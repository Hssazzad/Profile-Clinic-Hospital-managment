<?php

namespace App\Http\Controllers\Billing;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    // GET /Billing/payment
    public function index(Request $request)
    {
        return view('billing.payment_index');
    }

    // GET /Billing/payment/{patientId}  ? invoice ID asl?
    public function show($patientId)
    {
        $invoice = DB::table('investigation_payments')
            ->where('ID', $patientId)
            ->first();

        if (!$invoice) abort(404);

        $items = DB::table('investigation_payment_items')
            ->where('PaymentId', $patientId)
            ->get();

        return view('billing.payment_show', compact('invoice', 'items'));
    }

    // POST /Billing/payment/store  — JSON response
    public function store(Request $request)
    {
        $request->validate([
            'invoice_id'     => 'required|integer',
            'paying_amount'  => 'required|numeric|min:1',
            'payment_date'   => 'required|date',
            'payment_method' => 'required|in:cash,mobile_banking,card,bank_transfer,cheque',
        ]);

        $invoice = DB::table('investigation_payments')
            ->where('ID', $request->invoice_id)
            ->first();

        if (!$invoice) {
            return response()->json(['success' => false, 'message' => 'Invoice not found.'], 404);
        }

        $paying = (float) $request->paying_amount;
        $due    = (float) $invoice->DueAmount;

        if ($paying <= 0 || $paying > $due) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid amount. Due is ? ' . number_format($due, 0),
            ], 422);
        }

        $newDue    = max(0, $due - $paying);
        $newPaid   = (float) $invoice->PaidAmount + $paying;
        $newStatus = $newDue <= 0 ? 'paid' : 'partial';

        DB::beginTransaction();
        try {
            DB::table('investigation_payments')
                ->where('ID', $request->invoice_id)
                ->update([
                    'PaidAmount'    => $newPaid,
                    'DueAmount'     => $newDue,
                    'Status'        => $newStatus,
                    'PaymentMethod' => $request->payment_method,
                    'CollectedBy'   => $request->input('collected_by') ?? $invoice->CollectedBy,
                    'updated_at'    => now(),
                ]);

            DB::commit();

            return response()->json([
                'success'    => true,
                'invoice_id' => (int) $request->invoice_id,
                'new_status' => $newStatus,
                'due_left'   => $newDue,
            ]);

        } catch (\Throwable $e) {
            DB::rollBack();
            \Log::error('PaymentController store: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed. Please try again.'], 500);
        }
    }

    // GET /Billing/payment/{id}/print
    public function printInvoice($id)
    {
        $payment = DB::table('investigation_payments')->where('ID', $id)->first();
        $items   = DB::table('investigation_payment_items')->where('PaymentId', $id)->get();

        if (!$payment) abort(404);

        return view('billing.invoice_print', compact('payment', 'items'));
    }
}