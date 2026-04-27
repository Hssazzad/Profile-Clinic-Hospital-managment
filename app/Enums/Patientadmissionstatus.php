<?php

namespace App\Enums;

enum PatientAdmissionStatus: string
{


    case ADMITTED          = 'admitted';
    case POST_SURGERY_DONE = 'post_surgery_done';
    case FRESH_DONE        = 'fresh_done';
    case DISCHARGED        = 'discharged';
    case RELEASE_PENDING   = 'release_pending';
    case RELEASED          = 'released';



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


    public function canRejectBackTo(self $previous): bool
    {
        return match($this) {
            self::RELEASE_PENDING => $previous === self::DISCHARGED,
            default               => false,
        };
    }



    // PostSurgery page � ???? list-? ????
    public static function postSurgeryQueue(): string
    {
        return self::ADMITTED->value;
    }

    // Fresh page � ???? list-? ????
    public static function freshQueue(): string
    {
        return self::POST_SURGERY_DONE->value;
    }

    // Discharge page � ???? list-? ????
    public static function dischargeQueue(): string
    {
        return self::FRESH_DONE->value;
    }

    // Release page (Nurse submit ????) � ???? list-? ????
    public static function releaseQueue(): string
    {
        return self::DISCHARGED->value;
    }

    // Manager Approval page � ???? list-? ????
    public static function managerApprovalQueue(): string
    {
        return self::RELEASE_PENDING->value;
    }

    // Round Prescription page � discharged/release_pending/released ???? ????
    public static function roundQueue(): array
    {
        return [
            self::ADMITTED->value,
            self::POST_SURGERY_DONE->value,
            self::FRESH_DONE->value,
        ];
    }


    // Patient  hospital
    public function isActive(): bool
    {
        return $this !== self::RELEASED;
    }

    // Patient hospital
    public function isGone(): bool
    {
        return $this === self::RELEASED;
    }



    public function label(): string
    {
        return match($this) {
            self::ADMITTED          => '????? ???????',
            self::POST_SURGERY_DONE => '?????-???????? ???????',
            self::FRESH_DONE        => '????? ???????????? ???????',
            self::DISCHARGED        => '???????? ?????? ??????',
            self::RELEASE_PENDING   => '????????? ??????? ????',
            self::RELEASED          => '??? ????',
        };
    }
}
