<?php

namespace App\Enums;

enum PatientAdmissionStatus: string
{
    /*
    |--------------------------------------------------------------------------
    | STATUS VALUES
    |--------------------------------------------------------------------------
    | Full flow:
    |
    |   admitted
    |      ?
    |   post_surgery_done
    |      ?
    |   fresh_done
    |      ?
    |   discharged          ? Nurse discharge note ????
    |      ?
    |   release_pending     ? Nurse release submit ???
    |      ? (approve)      ? (reject)
    |   released            discharged  ? Nurse ???? submit ???? ?????
    |
    | Round Prescription ?? flow-? ????
    | admitted / post_surgery_done / fresh_done — ?? ??? status-?
    | ?????? ???? Round ?????? ?????
    |--------------------------------------------------------------------------
    */

    case ADMITTED          = 'admitted';
    case POST_SURGERY_DONE = 'post_surgery_done';
    case FRESH_DONE        = 'fresh_done';
    case DISCHARGED        = 'discharged';
    case RELEASE_PENDING   = 'release_pending';
    case RELEASED          = 'released';

    /*
    |--------------------------------------------------------------------------
    | FORWARD TRANSITION RULES
    |--------------------------------------------------------------------------
    | Normal flow-? ??? status ???? ??????? ?????? allowed?
    | Reject (release_pending ? discharged) backward transition —
    | ???? canTransitionTo-?? ???, ReleaseApprovalController-?
    | ????????? handle ??? intentionally?
    |--------------------------------------------------------------------------
    */

    public function canTransitionTo(self $next): bool
    {
        return match($this) {
            self::ADMITTED          => $next === self::POST_SURGERY_DONE,
            self::POST_SURGERY_DONE => $next === self::FRESH_DONE,
            self::FRESH_DONE        => $next === self::DISCHARGED,
            self::DISCHARGED        => $next === self::RELEASE_PENDING,
            self::RELEASE_PENDING   => $next === self::RELEASED,
            self::RELEASED          => false,
        };
    }

    /*
    |--------------------------------------------------------------------------
    | REJECT — Backward transition
    |--------------------------------------------------------------------------
    | Manager reject ???? release_pending ???? discharged-? ?????
    | ??? canTransitionTo-? ????? ???? ??? intentional backward move?
    | ReleaseApprovalController-?? reject() method ??? use ?????
    |--------------------------------------------------------------------------
    */

    public function canRejectBackTo(self $previous): bool
    {
        return match($this) {
            self::RELEASE_PENDING => $previous === self::DISCHARGED,
            default               => false,
        };
    }

    /*
    |--------------------------------------------------------------------------
    | PAGE QUEUES
    |--------------------------------------------------------------------------
    */

    // PostSurgery page — ???? list-? ????
    public static function postSurgeryQueue(): string
    {
        return self::ADMITTED->value;
    }

    // Fresh page — ???? list-? ????
    public static function freshQueue(): string
    {
        return self::POST_SURGERY_DONE->value;
    }

    // Discharge page — ???? list-? ????
    public static function dischargeQueue(): string
    {
        return self::FRESH_DONE->value;
    }

    // Release page (Nurse submit ????) — ???? list-? ????
    public static function releaseQueue(): string
    {
        return self::DISCHARGED->value;
    }

    // Manager Approval page — ???? list-? ????
    public static function managerApprovalQueue(): string
    {
        return self::RELEASE_PENDING->value;
    }

    // Round Prescription page — discharged/release_pending/released ???? ????
    public static function roundQueue(): array
    {
        return [
            self::ADMITTED->value,
            self::POST_SURGERY_DONE->value,
            self::FRESH_DONE->value,
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | HELPERS
    |--------------------------------------------------------------------------
    */

    // Patient ?? ???? hospital-? ????
    public function isActive(): bool
    {
        return $this !== self::RELEASED;
    }

    // Patient ?? ??? ?????
    public function isGone(): bool
    {
        return $this === self::RELEASED;
    }

    /*
    |--------------------------------------------------------------------------
    | LABEL — ????? ???
    |--------------------------------------------------------------------------
    */

    public function label(): string
    {
        return match($this) {
            self::ADMITTED          => '?????',
            self::POST_SURGERY_DONE => '?????-???????? ???????',
            self::FRESH_DONE        => '????? ???????????? ???????',
            self::DISCHARGED        => '???????? ?????? ??????',
            self::RELEASE_PENDING   => '????????? ??????? ????',
            self::RELEASED          => '??? ????',
        };
    }
}