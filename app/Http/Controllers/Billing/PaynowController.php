<?php

namespace App\Http\Controllers\Billing;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;

class PaynowController extends Controller
{
    // ─── Constants ────────────────────────────────────────────────
    private const PER_PAGE_DEFAULT  = 20;
    private const PER_PAGE_MAX      = 200;
    private const SEARCH_LIMIT      = 20;
    private const DUE_INVOICES_MAX  = 100;
    private const SCHEMA_CACHE_TTL  = 3600;
    private const SCHEMA_CACHE_KEY  = 'schema_cols_investigation_payments';

    // ─────────────────────────────────────────────────────────────
    // index
    // ─────────────────────────────────────────────────────────────
    public function index()
    {
        return view('billing.paynow.index');
    }

    // ─────────────────────────────────────────────────────────────
    // GET /Billing/Paynow/ajax/list
    // ─────────────────────────────────────────────────────────────
    public function list(Request $request)
    {
        $q       = trim((string) $request->input('q', ''));
        $showAll = $request->boolean('show_all', false);
        $perPage = min(
            max(1, (int) $request->input('per_page', self::PER_PAGE_DEFAULT)),
            self::PER_PAGE_MAX
        );
        $page   = max(1, (int) $request->input('page', 1));
        $offset = ($page - 1) * $perPage;

        $query = DB::table('investigation_payments as inv')
            ->select($this->invoiceSelectColumns('inv'));

        if (! $showAll) {
            $query->where('inv.DueAmount', '>', 0);
        }

        if ($q !== '') {
            $like = '%' . $q . '%';
            $query->where(function ($sub) use ($like) {
                $sub->where('inv.BillNo',       'like', $like)
                    ->orWhere('inv.PatientName', 'like', $like)
                    ->orWhere('inv.PatientCode', 'like', $like)
                    ->orWhere('inv.MobileNo',    'like', $like);
            });
        }

        $total = (clone $query)->count();

        $rows = $query
            ->orderByDesc('inv.ID')
            ->offset($offset)
            ->limit($perPage)
            ->get()
            ->map(fn($row) => $this->appendNetBill($row));

        return response()->json([
            'success' => true,
            'data'    => $rows,
            'meta'    => [
                'total'        => $total,
                'per_page'     => $perPage,
                'current_page' => $page,
                'last_page'    => (int) ceil($total / max($perPage, 1)),
                'from'         => $total ? ($offset + 1) : 0,
                'to'           => min($offset + $perPage, $total),
            ],
        ]);
    }

    // ─────────────────────────────────────────────────────────────
    // GET /Billing/Paynow/ajax/search-patient
    // ─────────────────────────────────────────────────────────────
    public function searchPatient(Request $request)
    {
        $q = trim((string) $request->input('q', ''));

        $query = DB::table('patients as p')
            ->select('p.id', 'p.patientname', 'p.patientcode', 'p.age', 'p.gender', 'p.mobile_no');

        if ($q !== '') {
            $like = '%' . $q . '%';
            $query->where(function ($sub) use ($like) {
                $sub->where('p.patientname',  'like', $like)
                    ->orWhere('p.patientcode', 'like', $like)
                    ->orWhere('p.mobile_no',   'like', $like);
            });
        }

        $rows = $query->orderByDesc('p.id')->limit(self::SEARCH_LIMIT)->get();

        return response()->json([
            'success' => true,
            'data'    => $rows,
        ]);
    }

    // ─────────────────────────────────────────────────────────────
    // GET /Billing/Paynow/ajax/due-invoices/{patientId}
    // ─────────────────────────────────────────────────────────────
    public function getDueInvoices(Request $request, $patientId)
    {
        $q           = trim((string) $request->input('q', ''));
        $patientCode = trim((string) $request->input('patient_code', ''));
        $perPage     = min(
            max(1, (int) $request->input('per_page', self::DUE_INVOICES_MAX)),
            self::PER_PAGE_MAX
        );

        $query = DB::table('investigation_payments as inv')
            ->select($this->invoiceSelectColumns('inv'))
            ->where('inv.DueAmount', '>', 0);

        if ($patientCode !== '') {
            $query->where('inv.PatientCode', $patientCode);
        } else {
            $patientIdColumn = $this->resolveExistingColumn(
                'investigation_payments',
                ['PatientId', 'PatientID', 'patient_id']
            );

            if ($patientId !== '__ALL__' && $patientIdColumn) {
                $query->where('inv.' . $patientIdColumn, $patientId);
            }
        }

        if ($q !== '') {
            $like = '%' . $q . '%';
            $query->where(function ($sub) use ($like) {
                $sub->where('inv.BillNo',       'like', $like)
                    ->orWhere('inv.PatientName', 'like', $like)
                    ->orWhere('inv.PatientCode', 'like', $like)
                    ->orWhere('inv.MobileNo',    'like', $like);
            });
        }

        $rows = $query
            ->orderBy('inv.ID')   // oldest first — bulk distribution-এর জন্য consistent order
            ->limit($perPage)
            ->get()
            ->map(fn($row) => $this->appendNetBill($row));

        return response()->json([
            'success' => true,
            'data'    => $rows,
        ]);
    }

    // ─────────────────────────────────────────────────────────────
    // POST /Billing/Paynow/store   (single invoice payment)
    // ─────────────────────────────────────────────────────────────
    public function store(Request $request)
    {
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'invoice_id'     => 'required|integer|min:1',
            'paying_amount'  => 'required|numeric|min:0.01',
            'payment_date'   => 'required|date',
            'payment_method' => 'required|in:cash,mobile_banking,card,bank_transfer,cheque',
            'collected_by'   => 'nullable|string|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
                'errors'  => $validator->errors(),
            ], 422);
        }

        $paymentColumns = Cache::remember(
            self::SCHEMA_CACHE_KEY,
            self::SCHEMA_CACHE_TTL,
            fn() => Schema::getColumnListing('investigation_payments')
        );

        DB::beginTransaction();

        try {
            $invoice = DB::table('investigation_payments')
                ->where('ID', $request->invoice_id)
                ->lockForUpdate()
                ->first();

            if (! $invoice) {
                DB::rollBack();
                return response()->json(['success' => false, 'message' => 'Invoice not found.'], 404);
            }

            $paying = (float) $request->paying_amount;
            $due    = (float) ($invoice->DueAmount ?? 0);

            if ($due <= 0) {
                DB::rollBack();
                return response()->json(['success' => false, 'message' => 'This invoice has already been fully paid.'], 422);
            }

            if ($paying <= 0 || $paying > $due) {
                DB::rollBack();
                return response()->json(['success' => false, 'message' => 'Invalid amount. Due is ' . number_format($due, 0)], 422);
            }

            $newDue    = max(0.0, $due - $paying);
            $newPaid   = ((float) ($invoice->PaidAmount ?? 0)) + $paying;
            $newStatus = $newDue <= 0 ? 'paid' : 'partial';
            $collectedBy = $request->input('collected_by')
                ?: ($invoice->CollectedBy ?? $invoice->collected_by ?? null);

            $update = [];
            $this->putIfColumnExists($update, $paymentColumns, 'PaidAmount',         $newPaid);
            $this->putIfColumnExists($update, $paymentColumns, 'paid_amount',         $newPaid);
            $this->putIfColumnExists($update, $paymentColumns, 'DueAmount',           $newDue);
            $this->putIfColumnExists($update, $paymentColumns, 'due_amount',          $newDue);
            $this->putIfColumnExists($update, $paymentColumns, 'Status',              $newStatus);
            $this->putIfColumnExists($update, $paymentColumns, 'status',              $newStatus);
            $this->putIfColumnExists($update, $paymentColumns, 'PaymentMethod',       $request->payment_method);
            $this->putIfColumnExists($update, $paymentColumns, 'payment_method',      $request->payment_method);
            $this->putIfColumnExists($update, $paymentColumns, 'CollectedBy',         $collectedBy);
            $this->putIfColumnExists($update, $paymentColumns, 'collected_by',        $collectedBy);
            $this->putIfColumnExists($update, $paymentColumns, 'PaymentDate',         $request->payment_date);
            $this->putIfColumnExists($update, $paymentColumns, 'payment_date',        $request->payment_date);
            $this->putIfColumnExists($update, $paymentColumns, 'fin_paid_amount',     $newPaid);
            $this->putIfColumnExists($update, $paymentColumns, 'fin_due_amount',      $newDue);
            $this->putIfColumnExists($update, $paymentColumns, 'payment_status',      $newStatus);
            $this->putIfColumnExists($update, $paymentColumns, 'fin_last_payment_at', $request->payment_date);
            $this->putIfColumnExists($update, $paymentColumns, 'updated_at',          now());

            DB::table('investigation_payments')->where('ID', $request->invoice_id)->update($update);

            DB::commit();

            DuelistFinalController::clearSummaryCache();

            return response()->json([
                'success'    => true,
                'invoice_id' => (int) $request->invoice_id,
                'new_status' => $newStatus,
                'due_left'   => $newDue,
            ]);

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('PaynowController@store failed: ' . $e->getMessage(), [
                'invoice_id' => $request->invoice_id,
                'amount'     => $request->paying_amount,
            ]);
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(), // TODO: remove before production
            ], 500);
        }
    }

    // ─────────────────────────────────────────────────────────────
    // POST /Billing/Paynow/store-bulk
    //
    // Bulk payment — একটি transaction-এ সব due invoice update।
    // Amount oldest invoice থেকে distribute হয়।
    // Response-এ updated invoice list থাকে (combined receipt-এর জন্য)।
    // ─────────────────────────────────────────────────────────────
    public function storeBulk(Request $request)
    {
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'patient_code'   => 'required|string|max:50',
            'paying_amount'  => 'required|numeric|min:0.01',
            'payment_date'   => 'required|date',
            'payment_method' => 'required|in:cash,mobile_banking,card,bank_transfer,cheque',
            'collected_by'   => 'nullable|string|max:100',
            'invoice_ids'    => 'required|array|min:1',
            'invoice_ids.*'  => 'integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
                'errors'  => $validator->errors(),
            ], 422);
        }

        $paymentColumns = Cache::remember(
            self::SCHEMA_CACHE_KEY,
            self::SCHEMA_CACHE_TTL,
            fn() => Schema::getColumnListing('investigation_payments')
        );

        DB::beginTransaction();

        try {
            // Oldest-first order — row lock সহ সব due invoice নিয়ে আসছি
            $invoices = DB::table('investigation_payments')
                ->whereIn('ID', $request->invoice_ids)
                ->where('PatientCode', $request->patient_code)
                ->where('DueAmount', '>', 0)
                ->orderBy('ID')      // oldest first
                ->lockForUpdate()
                ->get();

            if ($invoices->isEmpty()) {
                DB::rollBack();
                return response()->json(['success' => false, 'message' => 'No due invoice found.'], 422);
            }

            $totalDue = $invoices->sum(fn($inv) => (float) ($inv->DueAmount ?? 0));
            $paying   = (float) $request->paying_amount;

            if ($paying <= 0) {
                DB::rollBack();
                return response()->json(['success' => false, 'message' => 'Invalid amount.'], 422);
            }

            if ($paying > $totalDue) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Amount exceeds total due of ৳ ' . number_format($totalDue, 0),
                ], 422);
            }

            $remaining   = $paying;
            $collectedBy = $request->input('collected_by') ?: null;
            $updated     = [];   // receipt-এর জন্য return করব

            foreach ($invoices as $invoice) {
                if ($remaining <= 0) break;

                $due         = (float) ($invoice->DueAmount ?? 0);
                $allocate    = min($remaining, $due);
                $remaining  -= $allocate;

                $newDue    = max(0.0, $due - $allocate);
                $newPaid   = ((float) ($invoice->PaidAmount ?? 0)) + $allocate;
                $newStatus = $newDue <= 0 ? 'paid' : 'partial';
                $cb        = $collectedBy ?? ($invoice->CollectedBy ?? $invoice->collected_by ?? null);

                $row = [];
                $this->putIfColumnExists($row, $paymentColumns, 'PaidAmount',         $newPaid);
                $this->putIfColumnExists($row, $paymentColumns, 'paid_amount',         $newPaid);
                $this->putIfColumnExists($row, $paymentColumns, 'DueAmount',           $newDue);
                $this->putIfColumnExists($row, $paymentColumns, 'due_amount',          $newDue);
                $this->putIfColumnExists($row, $paymentColumns, 'Status',              $newStatus);
                $this->putIfColumnExists($row, $paymentColumns, 'status',              $newStatus);
                $this->putIfColumnExists($row, $paymentColumns, 'PaymentMethod',       $request->payment_method);
                $this->putIfColumnExists($row, $paymentColumns, 'payment_method',      $request->payment_method);
                $this->putIfColumnExists($row, $paymentColumns, 'CollectedBy',         $cb);
                $this->putIfColumnExists($row, $paymentColumns, 'collected_by',        $cb);
                $this->putIfColumnExists($row, $paymentColumns, 'PaymentDate',         $request->payment_date);
                $this->putIfColumnExists($row, $paymentColumns, 'payment_date',        $request->payment_date);
                $this->putIfColumnExists($row, $paymentColumns, 'fin_paid_amount',     $newPaid);
                $this->putIfColumnExists($row, $paymentColumns, 'fin_due_amount',      $newDue);
                $this->putIfColumnExists($row, $paymentColumns, 'payment_status',      $newStatus);
                $this->putIfColumnExists($row, $paymentColumns, 'fin_last_payment_at', $request->payment_date);
                $this->putIfColumnExists($row, $paymentColumns, 'updated_at',          now());

                DB::table('investigation_payments')->where('ID', $invoice->ID)->update($row);

                // Receipt-এর জন্য snapshot রাখা হচ্ছে
                $updated[] = [
                    'id'         => (int) $invoice->ID,
                    'bill_no'    => $invoice->BillNo    ?? '-',
                    'patient'    => $invoice->PatientName ?? '-',
                    'total_bill' => (float) ($invoice->TotalBill ?? 0),
                    'discount'   => (float) ($invoice->Discount  ?? 0),
                    'allocated'  => round($allocate, 2),
                    'new_paid'   => round($newPaid,   2),
                    'new_due'    => round($newDue,    2),
                    'status'     => $newStatus,
                ];
            }

            DB::commit();

            DuelistFinalController::clearSummaryCache();

            return response()->json([
                'success'       => true,
                'total_paid'    => round($paying - $remaining, 2),
                'invoices'      => $updated,
                'invoice_count' => count($updated),
            ]);

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('PaynowController@storeBulk failed: ' . $e->getMessage(), [
                'patient_code' => $request->patient_code,
                'amount'       => $request->paying_amount,
            ]);
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(), // TODO: remove before production
            ], 500);
        }
    }

    // ─────────────────────────────────────────────────────────────
    // show / print
    // ─────────────────────────────────────────────────────────────
    public function show(int $id)
    {
        $invoice = DB::table('investigation_payments')->where('ID', $id)->first();
        if (! $invoice) abort(404, 'Invoice not found.');
        $items = DB::table('investigation_payment_items')->where('PaymentId', $id)->get();
        return view('billing.payment_show', compact('invoice', 'items'));
    }

    public function print(int $id)
    {
        $payment = DB::table('investigation_payments')->where('ID', $id)->first();
        if (! $payment) abort(404, 'Payment not found.');
        $items = DB::table('investigation_payment_items')->where('PaymentId', $id)->get();
        return view('billing.invoice_print', compact('payment', 'items'));
    }

    // ─── Private helpers ──────────────────────────────────────────

    private function appendNetBill(object $row): object
    {
        $row->NetBill = ((float) ($row->TotalBill ?? 0)) - ((float) ($row->Discount ?? 0));
        return $row;
    }

    private function invoiceSelectColumns(string $alias = 'inv'): array
    {
        $p = $alias ? $alias . '.' : '';

        $columns = [
            $p . 'ID',
            $p . 'BillNo',
            $p . 'TotalBill',
            $p . 'Discount',
            $p . 'PaidAmount',
            $p . 'DueAmount',
            $p . 'Status',
            $p . 'PaymentDate',
            $p . 'PatientName',
            $p . 'PatientCode',
            $p . 'MobileNo',
        ];

        $columns[] = Schema::hasColumn('investigation_payments', 'InvoiceType')
            ? $p . 'InvoiceType'
            : DB::raw("'' as InvoiceType");

        $patientIdColumn = $this->resolveExistingColumn(
            'investigation_payments',
            ['PatientId', 'PatientID', 'patient_id']
        );

        $columns[] = $patientIdColumn
            ? $p . $patientIdColumn . ' as PatientId'
            : DB::raw('NULL as PatientId');

        return $columns;
    }

    private function resolveExistingColumn(string $table, array $candidates): ?string
    {
        foreach ($candidates as $candidate) {
            if (Schema::hasColumn($table, $candidate)) return $candidate;
        }
        return null;
    }

    private function putIfColumnExists(array &$row, array $columns, string $column, $value): void
    {
        if (in_array($column, $columns, true)) {
            $row[$column] = $value;
        }
    }
}