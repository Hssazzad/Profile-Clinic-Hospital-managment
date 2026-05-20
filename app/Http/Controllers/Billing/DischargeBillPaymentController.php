<?php

namespace App\Http\Controllers\Billing;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DischargeBillPaymentController extends Controller
{
    // ============================================================
    // SECTION 1  MAIN PAGE (index)
    // ============================================================

    public function index(Request $request)
    {
        // We no longer need the patients list on the left side, 
        // as you requested to only show the confirmed bill list.

        // -- Past Investigation Bill payments (from investigation_payments table) --
        $pastPayments = DB::table('investigation_payments')
            ->when($request->search, function ($q) use ($request) {
                $q->where(function ($q2) use ($request) {
                    $q2->where('PatientName', 'like', '%' . $request->search . '%')
                       ->orWhere('BillNo',  'like', '%' . $request->search . '%')
                       ->orWhere('PatientCode','like', '%' . $request->search . '%')
                       ->orWhere('MobileNo',   'like', '%' . $request->search . '%');
                });
            })
            ->when($request->status_filter && $request->status_filter !== 'all', function ($q) use ($request) {
                $q->where('Status', $request->status_filter);
            })
            ->orderByDesc('ID')
            ->paginate(20, ['*'], 'page')
            ->withQueryString();

        // -- Summary stats ---------------------------------------
        $stats = [
            'total_bills'   => DB::table('investigation_payments')->count(),
            'total_due'     => DB::table('investigation_payments')->where('Status', '!=', 'paid')->sum('DueAmount'),
            'today_bills'   => DB::table('investigation_payments')->whereDate('PaymentDate', today())->count(),
            'today_revenue' => DB::table('investigation_payments')->whereDate('PaymentDate', today())->sum('PaidAmount'),
        ];

        return view('billing.discharge_bill_payment', compact(
            'pastPayments',
            'stats'
        ));
    }

    // ============================================================
    // SECTION 2  DETAIL / RECEIPT
    // ============================================================

    public function detail($id)
    {
        $payment = DB::table('investigation_payments')->where('ID', $id)->first();
        
        if (! $payment) {
            return response()->json(['success' => false, 'message' => 'Receipt not found.'], 404);
        }
        
        $items = DB::table('investigation_payment_items')->where('PaymentId', $id)->get();

        return response()->json(['success' => true, 'data' => $this->formatPayment($payment, $items)]);
    }

    // ============================================================
    // SECTION 3  DELETE
    // ============================================================

    public function destroy($id)
    {
        $payment = DB::table('investigation_payments')->where('ID', $id)->first();

        if (! $payment) {
            return response()->json(['success' => false, 'message' => 'Record not found.'], 404);
        }

        DB::beginTransaction();
        try {
            DB::table('investigation_payment_items')->where('PaymentId', $id)->delete();
            DB::table('investigation_payments')->where('ID', $id)->delete();
            DB::commit();
            return response()->json(['success' => true, 'message' => 'Deleted successfully.']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Delete failed.'], 500);
        }
    }


    // ============================================================
    // SECTION 4  FORMAT HELPER
    // ============================================================

    private function formatPayment($payment, $items): array
    {
        return [
            'id'              => $payment->ID,
            'receipt_no'      => $payment->BillNo,
            'admission_id'    => $payment->AdmissionId,
            'patient_id'      => $payment->PatientId,
            'patient_name'    => $payment->PatientName,
            'patient_age'     => $payment->PatientAge,
            'patient_code'    => $payment->PatientCode,
            'mobile_no'       => $payment->MobileNo,
            'payment_date'    => $payment->PaymentDate,
            'payment_method'  => $payment->PaymentMethod,
            'total_amount'    => (float) $payment->TotalBill,
            'gross_amount'    => (float) $payment->TotalBill, // Assuming TotalBill is gross for now
            'discount_amount' => (float) ($payment->Discount ?? 0),
            'paid_amount'     => (float) $payment->PaidAmount,
            'due_amount'      => (float) $payment->DueAmount,
            'status'          => $payment->Status ?? 'due',
            'received_by'     => $payment->CollectedBy,
            'notes'           => '', // Add to DB if needed
            'created_at'      => $payment->created_at,
            'tests'           => $items->map(fn($i) => [
                    'category'  => $i->CategoryName,
                    'test_name' => $i->ServiceName,
                    'price'     => (float) $i->UnitPrice,
                    'discount'  => 0,
                    'qty'       => (int)   $i->Quantity,
                    'subtotal'  => (float) $i->Amount,
                    'remarks'   => $i->Remarks,
                ])->toArray(),
        ];
    }
}