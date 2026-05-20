<?php

namespace App\Repositories\Billing\Contracts;

interface InvoiceRepositoryInterface
{
    public function createInvoice(array $data): int;
    public function createInvoiceItems(int $paymentId, array $items): void;
    public function getLastBillNumber(): ?string;
}