<?php

namespace App\Repositories\Billing\Contracts;

interface TemporaryBillRepositoryInterface
{
    public function create(array $data): int;

    public function findByPatientCode(?string $patientCode): array;

    public function deleteById(int $tmpId): bool;

    public function deleteByPatientCode(?string $patientCode): bool;
}