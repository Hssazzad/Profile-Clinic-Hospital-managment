<?php

namespace App\Services\Billing;

use App\Http\Requests\Billing\CreateInvoiceRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class InvoiceService
{
    /**
     * Create a new invoice with full business logic
     */
    public function createInvoice(CreateInvoiceRequest $request)
    {
        // Step 1: ডাটা নিয়ে গণনা করা
        $totalBill  = (float) $request->total_bill;
        $discount   = (float) $request->input('discount', 0);
        $paidAmount = (float) $request->paid_amount;

        $netAmount   = max(0, $totalBill - $discount);           // নেট বিল = মোট বিল - ডিসকাউন্ট
        $minRequired = ceil($netAmount * 0.25);                  // কমপক্ষে ২৫% দিতে হবে

        // ==================== বিজনেস রুল চেক ====================
        if ($paidAmount < $minRequired) {
            throw new \InvalidArgumentException(
                'Minimum 25% required. Please pay at least ৳ ' . number_format($minRequired, 0) . '.'
            );
        }

        // পেমেন্ট স্ট্যাটাস নির্ধারণ (paid / partial / due)
        $dueAmount = max(0, $netAmount - $paidAmount);
        $status    = $this->determinePaymentStatus($paidAmount, $netAmount);

        // ==================== ডাটাবেস ট্রানজেকশন শুরু ====================
        DB::beginTransaction();

        try {
            // Bill No জেনারেট করা (INV-00001, INV-00002 ...)
            $billNo = $this->generateBillNumber();

            // মূল Invoice টেবিলে ডাটা সংরক্ষণ
            $paymentId = DB::table('investigation_payments')->insertGetId([
                'BillNo'        => $billNo,
                'PatientId'     => $request->patient_id,
                'PatientCode'   => $request->patient_code,
                'PatientName'   => $request->patient_name,
                'PatientAge'    => $request->patient_age,
                'MobileNo'      => $request->mobile_no,
                'AdmissionId'   => $request->admission_id,
                'TotalBill'     => $totalBill,
                'Discount'      => $discount,
                'PaidAmount'    => $paidAmount,
                'DueAmount'     => $dueAmount,
                'PaymentDate'   => $request->payment_date,
                'PaymentMethod' => $request->payment_method,
                'CollectedBy'   => $request->collected_by,
                'Status'        => $status,
                'created_at'    => now(),
                'updated_at'    => now(),
            ]);

            // Invoice এর আইটেমগুলো (ডাক্তার ফি, টেস্ট ইত্যাদি) সংরক্ষণ
            $this->insertPaymentItems($paymentId, $request->validated('items'));

            // Temporary Table থেকে ডাটা মুছে ফেলা
            $this->clearTemporaryBillItems($request->patient_code);

            // সব ঠিক থাকলে Commit করা
            DB::commit();

            return [
                'success'    => true,
                'payment_id' => $paymentId,
                'bill_no'    => $billNo,
            ];

        } catch (\Throwable $e) {
            // কোনো সমস্যা হলে সব পরিবর্তন Rollback করে দেয়া
            DB::rollBack();

            Log::error('Invoice Creation Failed', [
                'patient_id' => $request->patient_id,
                'error'      => $e->getMessage(),
                'trace'      => $e->getTraceAsString()
            ]);

            throw $e;   // Controller এ এই এরর পাঠিয়ে দেয়া হচ্ছে
        }
    }


    /**
     * নতুন Bill Number তৈরি করা (Race Condition Safe)
     */
    private function generateBillNumber(): string
    {
        return DB::transaction(function () {
            $lastBillNo = DB::table('investigation_payments')
                            ->lockForUpdate()           // অন্য কেউ একই সময়ে এডিট করতে পারবে না
                            ->orderByDesc('ID')
                            ->value('BillNo');

            $nextNum = $lastBillNo ? (intval(substr($lastBillNo, 4)) + 1) : 1;
            return 'INV-' . str_pad($nextNum, 5, '0', STR_PAD_LEFT);
        });
    }


    /**
     * Invoice এর আইটেমগুলো (Multiple Items) একসাথে Insert করা
     */
    private function insertPaymentItems(int $paymentId, array $items): void
    {
        $itemsToInsert = [];

        foreach ($items as $item) {
            if (empty(trim($item['service_name'] ?? ''))) {
                continue;
            }

            $itemsToInsert[] = [
                'PaymentId'    => $paymentId,
                'CategoryCode' => $item['category'] ?? null,
                'CategoryName' => $item['category_name'] ?? null,
                'ServiceName'  => $item['service_name'],
                'UnitPrice'    => $item['unit_price'] ?? 0,
                'Quantity'     => $item['quantity'] ?? 1,
                'Amount'       => $item['amount'] ?? 0,
                'Remarks'      => $item['remarks'] ?? null,
                'created_at'   => now(),
                'updated_at'   => now(),
            ];
        }

        if (!empty($itemsToInsert)) {
            DB::table('investigation_payment_items')->insert($itemsToInsert);
        }
    }


    /**
     * Temporary Bill Table থেকে এই পেশেন্টের সব ডাটা মুছে ফেলা
     */
    private function clearTemporaryBillItems(?string $patientCode): void
    {
        if ($patientCode) {
            DB::table('tbl_bill_tmp')
                ->where('PatientCode', $patientCode)
                ->delete();
        }
    }


    /**
     * পেমেন্টের স্ট্যাটাস নির্ধারণ করা
     */
    private function determinePaymentStatus(float $paidAmount, float $netAmount): string
    {
        if ($paidAmount >= $netAmount) {
            return 'paid';
        } elseif ($paidAmount > 0) {
            return 'partial';
        }
        return 'due';
    }
}