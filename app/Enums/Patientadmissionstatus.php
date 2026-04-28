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

    // Check if patient is currently in treatment
    public function isInTreatment(): bool
    {
        return match($this) {
            self::ADMITTED,
            self::POST_SURGERY_DONE,
            self::FRESH_DONE => true,
            default => false,
        };
    }

    // Check if patient is ready for discharge
    public function isReadyForDischarge(): bool
    {
        return $this === self::FRESH_DONE;
    }

    // Check if patient needs approval
    public function needsApproval(): bool
    {
        return $this === self::RELEASE_PENDING;
    }

    // Get next possible statuses
    public function getNextPossibleStatuses(): array
    {
        return match($this) {
            self::ADMITTED => [self::POST_SURGERY_DONE],
            self::POST_SURGERY_DONE => [self::FRESH_DONE],
            self::FRESH_DONE => [self::DISCHARGED],
            self::DISCHARGED => [self::RELEASE_PENDING],
            self::RELEASE_PENDING => [self::RELEASED],
            self::RELEASED => [],
        };
    }

    // Get previous possible statuses for rejection
    public function getPreviousPossibleStatuses(): array
    {
        return match($this) {
            self::RELEASE_PENDING => [self::DISCHARGED],
            default => [],
        };
    }

    // Check if status is a final state
    public function isFinalState(): bool
    {
        return $this === self::RELEASED;
    }

    // Get status color for UI display
    public function getColor(): string
    {
        return match($this) {
            self::ADMITTED => 'blue',
            self::POST_SURGERY_DONE => 'orange',
            self::FRESH_DONE => 'green',
            self::DISCHARGED => 'yellow',
            self::RELEASE_PENDING => 'purple',
            self::RELEASED => 'gray',
        };
    }

    // Get status icon for UI display
    public function getIcon(): string
    {
        return match($this) {
            self::ADMITTED => 'hospital',
            self::POST_SURGERY_DONE => 'medical-services',
            self::FRESH_DONE => 'healing',
            self::DISCHARGED => 'exit-to-app',
            self::RELEASE_PENDING => 'approval',
            self::RELEASED => 'check-circle',
        };
    }

    // Get all active statuses (excluding released)
    public static function getActiveStatuses(): array
    {
        return [
            self::ADMITTED,
            self::POST_SURGERY_DONE,
            self::FRESH_DONE,
            self::DISCHARGED,
            self::RELEASE_PENDING,
        ];
    }

    // Get all treatment statuses
    public static function getTreatmentStatuses(): array
    {
        return [
            self::ADMITTED,
            self::POST_SURGERY_DONE,
            self::FRESH_DONE,
        ];
    }

    // Get all discharge-related statuses
    public static function getDischargeStatuses(): array
    {
        return [
            self::DISCHARGED,
            self::RELEASE_PENDING,
            self::RELEASED,
        ];
    }

    // Check if patient can be assigned to a specific queue
    public function canBeAssignedToQueue(string $queueType): bool
    {
        return match($queueType) {
            'post_surgery' => $this === self::ADMITTED,
            'fresh' => $this === self::POST_SURGERY_DONE,
            'discharge' => $this === self::FRESH_DONE,
            'release' => $this === self::DISCHARGED,
            'manager_approval' => $this === self::RELEASE_PENDING,
            'round' => $this->isInTreatment(),
            default => false,
        };
    }

    // Get status description with context
    public function getDescription(): string
    {
        return match($this) {
            self::ADMITTED => 'Patient has been admitted to the hospital',
            self::POST_SURGERY_DONE => 'Patient has completed post-surgery procedures',
            self::FRESH_DONE => 'Patient has completed fresh treatment procedures',
            self::DISCHARGED => 'Patient is ready to be discharged from hospital',
            self::RELEASE_PENDING => 'Patient discharge is pending manager approval',
            self::RELEASED => 'Patient has been released from hospital',
        };
    }

    // Get estimated days remaining in current status
    public function getEstimatedDays(): int
    {
        return match($this) {
            self::ADMITTED => 3,
            self::POST_SURGERY_DONE => 2,
            self::FRESH_DONE => 1,
            self::DISCHARGED => 0,
            self::RELEASE_PENDING => 0,
            self::RELEASED => 0,
        };
    }
}
