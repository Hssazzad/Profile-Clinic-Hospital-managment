<?php

namespace App\Http\Controllers\Billing;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class PaynowController extends Controller
{
    public function index()
    {
        return view('billing.paynow.index');
    }

    public function list(Request $request)
    {
        $q = trim((string) $request->input('q', ''));
        $showAll = $request->boolean('show_all', false);
        $perPage = max(1, (int) $request->input('per_page', 20));
        $page = max(1, (int) $request->input('page', 1));
        $offset = ($page - 1) * $perPage;

        $query = DB::table('investigation_payments as inv')
            ->select($this->invoiceSelectColumns('inv'));

        if (!$showAll) {
            $query->where('inv.DueAmount', '>', 0);
        }

        if ($q !== '') {
            $query->where(function ($sub) use ($q) {
                $like = '%' . $q . '%';

                $sub->where('inv.BillNo', 'like', $like)
                    ->orWhere('inv.PatientName', 'like', $like)
                    ->orWhere('inv.PatientCode', 'like', $like)
                    ->orWhere('inv.MobileNo', 'like', $like);
            });
        }

        $total = (clone $query)->count();

        $rows = $query
            ->orderByDesc('inv.ID')
            ->offset($offset)
            ->limit($perPage)
            ->get()
            ->map(function ($row) {
                $row->NetBill = ((float) ($row->TotalBill ?? 0)) - ((float) ($row->Discount ?? 0));
                return $row;
            });

        return response()->json([
            'data' => $rows,
            'meta' => [
                'total' => $total,
                'per_page' => $perPage,
                'current_page' => $page,
                'last_page' => (int) ceil($total / $perPage),
                'from' => $total ? ($offset + 1) : 0,
                'to' => min($offset + $perPage, $total),
            ],
        ]);
    }

    public function searchPatient(Request $request)
    {
        $q = trim((string) $request->input('q', ''));

        $query = DB::table('patients as p')
            ->select(
                'p.id',
                'p.patientname',
                'p.patientcode',
                'p.age',
                'p.gender',
                'p.mobile_no'
            );

        if ($q !== '') {
            $query->where(function ($sub) use ($q) {
                $like = '%' . $q . '%';

                $sub->where('p.patientname', 'like', $like)
                    ->orWhere('p.patientcode', 'like', $like)
                    ->orWhere('p.mobile_no', 'like', $like);
            });
        }

        $rows = $query
            ->orderByDesc('p.id')
            ->limit(20)
            ->get();

        return response()->json(['data' => $rows]);
    }

    public function getDueInvoices(Request $request, $patientId)
    {
        $q = trim((string) $request->input('q', ''));
        $patientCode = trim((string) $request->input('patient_code', ''));
        $perPage = max(1, (int) $request->input('per_page', 50));

        $query = DB::table('investigation_payments as inv')
            ->select($this->invoiceSelectColumns('inv'))
            ->where('inv.DueAmount', '>', 0);

        if ($patientCode !== '') {
            $query->where('inv.PatientCode', $patientCode);
        } else {
            $patientIdColumn = $this->resolveExistingColumn('investigation_payments', ['PatientId', 'PatientID', 'patient_id']);

            if ($patientId !== '__ALL__' && $patientIdColumn) {
                $query->where('inv.' . $patientIdColumn, $patientId);
            }
        }

        if ($q !== '') {
            $query->where(function ($sub) use ($q) {
                $like = '%' . $q . '%';

                $sub->where('inv.BillNo', 'like', $like)
                    ->orWhere('inv.PatientName', 'like', $like)
                    ->orWhere('inv.PatientCode', 'like', $like)
                    ->orWhere('inv.MobileNo', 'like', $like);
            });
        }

        $rows = $query
            ->orderByDesc('inv.ID')
            ->limit($perPage)
            ->get()
            ->map(function ($row) {
                $row->NetBill = ((float) ($row->TotalBill ?? 0)) - ((float) ($row->Discount ?? 0));
                return $row;
            });

        return response()->json(['data' => $rows]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'invoice_id' => 'required|integer',
            'paying_amount' => 'required|numeric|min:1',
            'payment_date' => 'required|date',
            'payment_method' => 'required|in:cash,mobile_banking,card,bank_transfer,cheque',
            'collected_by' => 'nullable|string|max:100',
        ]);

        $invoice = DB::table('investigation_payments')
            ->where('ID', $request->invoice_id)
            ->first();

        if (!$invoice) {
            return response()->json([
                'success' => false,
                'message' => 'Invoice not found.',
            ], 404);
        }

        $paying = (float) $request->paying_amount;
        $due = (float) $invoice->DueAmount;

        if ($paying <= 0 || $paying > $due) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid amount. Due is ' . number_format($due, 0),
            ], 422);
        }

        $newDue = max(0, $due - $paying);
        $newPaid = (float) $invoice->PaidAmount + $paying;
        $newStatus = $newDue <= 0 ? 'paid' : 'partial';

        $paymentColumns = Schema::getColumnListing('investigation_payments');

        DB::beginTransaction();

        try {
            $update = [];

            $this->putIfColumnExists($update, $paymentColumns, 'PaidAmount', $newPaid);
            $this->putIfColumnExists($update, $paymentColumns, 'paid_amount', $newPaid);

            $this->putIfColumnExists($update, $paymentColumns, 'DueAmount', $newDue);
            $this->putIfColumnExists($update, $paymentColumns, 'due_amount', $newDue);

            $this->putIfColumnExists($update, $paymentColumns, 'Status', $newStatus);
            $this->putIfColumnExists($update, $paymentColumns, 'status', $newStatus);

            $this->putIfColumnExists($update, $paymentColumns, 'PaymentMethod', $request->payment_method);
            $this->putIfColumnExists($update, $paymentColumns, 'payment_method', $request->payment_method);

            $this->putIfColumnExists($update, $paymentColumns, 'CollectedBy', $request->input('collected_by') ?: ($invoice->CollectedBy ?? null));
            $this->putIfColumnExists($update, $paymentColumns, 'collected_by', $request->input('collected_by') ?: ($invoice->collected_by ?? null));

            $this->putIfColumnExists($update, $paymentColumns, 'PaymentDate', $request->payment_date);
            $this->putIfColumnExists($update, $paymentColumns, 'payment_date', $request->payment_date);

            $this->putIfColumnExists($update, $paymentColumns, 'fin_paid_amount', $newPaid);
            $this->putIfColumnExists($update, $paymentColumns, 'fin_due_amount', $newDue);
            $this->putIfColumnExists($update, $paymentColumns, 'payment_status', $newStatus);
            $this->putIfColumnExists($update, $paymentColumns, 'fin_last_payment_at', $request->payment_date);
            $this->putIfColumnExists($update, $paymentColumns, 'updated_at', now());

            DB::table('investigation_payments')
                ->where('ID', $request->invoice_id)
                ->update($update);

            DB::commit();

            return response()->json([
                'success' => true,
                'invoice_id' => (int) $request->invoice_id,
                'new_status' => $newStatus,
                'due_left' => $newDue,
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            \Log::error('PaynowController store: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed. Please try again.',
            ], 500);
        }
    }

    public function show($id)
    {
        $invoice = DB::table('investigation_payments')
            ->where('ID', $id)
            ->first();

        if (!$invoice) {
            abort(404);
        }

        $items = DB::table('investigation_payment_items')
            ->where('PaymentId', $id)
            ->get();

        return view('billing.payment_show', compact('invoice', 'items'));
    }

    public function print($id)
    {
        $payment = DB::table('investigation_payments')
            ->where('ID', $id)
            ->first();

        $items = DB::table('investigation_payment_items')
            ->where('PaymentId', $id)
            ->get();

        if (!$payment) {
            abort(404);
        }

        return view('billing.invoice_print', compact('payment', 'items'));
    }

    private function invoiceSelectColumns(string $alias = 'inv'): array
    {
        $prefix = $alias ? $alias . '.' : '';

        $columns = [
            $prefix . 'ID',
            $prefix . 'BillNo',
            $prefix . 'TotalBill',
            $prefix . 'Discount',
            $prefix . 'PaidAmount',
            $prefix . 'DueAmount',
            $prefix . 'Status',
            $prefix . 'PaymentDate',
            $prefix . 'PatientName',
            $prefix . 'PatientCode',
            $prefix . 'MobileNo',
        ];

        if (Schema::hasColumn('investigation_payments', 'InvoiceType')) {
            $columns[] = $prefix . 'InvoiceType';
        } else {
            $columns[] = DB::raw("'' as InvoiceType");
        }

        $patientIdColumn = $this->resolveExistingColumn('investigation_payments', ['PatientId', 'PatientID', 'patient_id']);
        if ($patientIdColumn) {
            $columns[] = $prefix . $patientIdColumn . ' as PatientId';
        } else {
            $columns[] = DB::raw('NULL as PatientId');
        }

        return $columns;
    }

    private function resolveExistingColumn(string $table, array $candidates): ?string
    {
        foreach ($candidates as $candidate) {
            if (Schema::hasColumn($table, $candidate)) {
                return $candidate;
            }
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
