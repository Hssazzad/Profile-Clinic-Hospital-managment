<?php
// app/Http/Controllers/PrescriptionWizardController.php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rule;

class PrescriptionWizardController extends Controller
{
    private function rx(): array {
        return Session::get('rx', [
            'patient_id'       => null,
            'investigations'   => [],   // [['name' => 'CBC', 'note' => 'fasting']]
            'diagnosis'        => [],   // ['Typhoid', 'Dengue']
            'medicines'        => [],   // [['id'=>1,'name'=>'Paracetamol','strength'=>'500mg',...], ...]
            'meta'             => [
                'prescribed_on' => now()->toDateString(),
                'doctor_name'   => '',
                'doctor_reg_no' => '',
            ],
        ]);
    }
    private function putRx(array $rx): void {
        Session::put('rx', $rx);
    }
    public function reset() {
        Session::forget('rx');
        return redirect()->route('rx.step1');
    }

    // ----- STEP 1: PATIENT -----
    public function step1()
    {
        $rx = $this->rx();
        $patients = DB::table('patients')->select('id','patientname','patientcode','mobile_no')->orderBy('patientname')->get();
        return view('rx_wizard.step1_patient', compact('rx','patients'));
    }
    public function postStep1(Request $req)
    {
        $val = $req->validate([
            'patient_id' => ['required', Rule::exists('patients','id')],
        ]);
        $rx = $this->rx();
        $rx['patient_id'] = (int)$val['patient_id'];
        $this->putRx($rx);
        return redirect()->route('rx.step2');
    }

    // ----- STEP 2: INVESTIGATIONS -----
    public function step2()
    {
        $rx = $this->rx();
        if (!$rx['patient_id']) return redirect()->route('rx.step1');
        $investigations = DB::table('common_investigation')->select('name')->orderBy('name')->get();
        return view('rx_wizard.step2_investigations', compact('rx','investigations'));
    }
    public function postStep2(Request $req)
    {
        $val = $req->validate([
            'investigation'      => ['required','array','min:1'],
            'investigation.*'    => ['string','max:150'],
            'note_all'           => ['nullable','string','max:200'],
        ]);
        $rx = $this->rx();
        $rx['investigations'] = [];
        foreach ($val['investigation'] as $name) {
            $rx['investigations'][] = ['name'=>$name, 'note'=>$val['note_all'] ?? ''];
        }
        $this->putRx($rx);
        return redirect()->route('rx.step3');
    }

    // ----- STEP 3: DIAGNOSIS -----
    public function step3()
    {
        $rx = $this->rx();
        if (!$rx['patient_id']) return redirect()->route('rx.step1');
        if (empty($rx['investigations'])) return redirect()->route('rx.step2');
        $diagnoses = DB::table('diagnoses')->select('name')->orderBy('name')->get();
        return view('rx_wizard.step3_diagnosis', compact('rx','diagnoses'));
    }
    public function postStep3(Request $req)
    {
        $val = $req->validate([
            'diagnosis'   => ['required','array','min:1'],
            'diagnosis.*' => ['string','max:150'],
        ]);
        $rx = $this->rx();
        $rx['diagnosis'] = array_values(array_unique($val['diagnosis']));
        $this->putRx($rx);
        return redirect()->route('rx.step4');
    }

    // ----- STEP 4: MEDICINES -----
    public function step4()
    {
        $rx = $this->rx();
        if (!$rx['patient_id']) return redirect()->route('rx.step1');
        if (empty($rx['investigations'])) return redirect()->route('rx.step2');
        if (empty($rx['diagnosis'])) return redirect()->route('rx.step3');

        $medicines = DB::table('medicines')->select('id','name')->orderBy('name')->get();
        return view('rx_wizard.step4_medicines', compact('rx','medicines'));
    }
    public function postStep4(Request $req)
    {
        $val = $req->validate([
            'medicine_id'   => ['required','array','min:1'],
            'medicine_id.*' => ['integer', Rule::exists('medicines','id')],
            // optional “apply same details to all”
            'strength'      => ['nullable','string','max:100'],
            'dose'          => ['nullable','string','max:100'],
            'route'         => ['nullable','string','max:100'],
            'frequency'     => ['nullable','string','max:100'],
            'duration'      => ['nullable','string','max:100'],
            'timing'        => ['nullable','string','max:100'],
        ]);

        $idToName = DB::table('medicines')->whereIn('id',$val['medicine_id'])->pluck('name','id')->toArray();

        $rx = $this->rx();
        $rx['medicines'] = [];
        foreach ($val['medicine_id'] as $mid) {
            $rx['medicines'][] = [
                'id'        => (int)$mid,
                'name'      => $idToName[$mid] ?? ('#'.$mid),
                'strength'  => $val['strength']  ?? '',
                'dose'      => $val['dose']      ?? '',
                'route'     => $val['route']     ?? '',
                'frequency' => $val['frequency'] ?? '',
                'duration'  => $val['duration']  ?? '',
                'timing'    => $val['timing']    ?? '',
            ];
        }
        $this->putRx($rx);
        return redirect()->route('rx.review');
    }

    // ----- STEP 5: REVIEW & SAVE -----
    public function review()
    {
        $rx = $this->rx();
        if (!$rx['patient_id']) return redirect()->route('rx.step1');
        if (empty($rx['investigations'])) return redirect()->route('rx.step2');
        if (empty($rx['diagnosis'])) return redirect()->route('rx.step3');
        if (empty($rx['medicines'])) return redirect()->route('rx.step4');

        $patient = DB::table('patients')->select('id','name','code','mobile')->where('id',$rx['patient_id'])->first();
        return view('rx_wizard.step5_review', compact('rx','patient'));
    }

    public function finalize(Request $req)
    {
        $rx = $this->rx();
        $data = $req->validate([
            'prescribed_on' => ['required','date'],
            'doctor_name'   => ['nullable','string','max:150'],
            'doctor_reg_no' => ['nullable','string','max:100'],
        ]);
        $rx['meta'] = $data; $this->putRx($rx);

        // Persist everything in one transaction
        DB::transaction(function() use ($rx) {
            $pid = DB::table('prescriptions')->insertGetId([
                'patient_id'    => $rx['patient_id'],
                'prescribed_on' => $rx['meta']['prescribed_on'],
                'doctor_name'   => $rx['meta']['doctor_name'] ?? '',
                'doctor_reg_no' => $rx['meta']['doctor_reg_no'] ?? '',
                'created_at'    => now(),
                'updated_at'    => now(),
            ]);

            // pivot tables / detail rows (use your actual schema)
            foreach ($rx['investigations'] as $i) {
                DB::table('prescription_investigations')->insert([
                    'prescription_id' => $pid,
                    'name'            => $i['name'],
                    'note'            => $i['note'] ?? '',
                    'created_at'      => now(),
                    'updated_at'      => now(),
                ]);
            }
            foreach ($rx['diagnosis'] as $d) {
                DB::table('prescription_diagnoses')->insert([
                    'prescription_id' => $pid,
                    'name'            => $d,
                    'created_at'      => now(),
                    'updated_at'      => now(),
                ]);
            }
            foreach ($rx['medicines'] as $m) {
                DB::table('prescription_medicines')->insert([
                    'prescription_id' => $pid,
                    'medicine_id'     => $m['id'],
                    'medicine_name'   => $m['name'],
                    'strength'        => $m['strength'],
                    'dose'            => $m['dose'],
                    'route'           => $m['route'],
                    'frequency'       => $m['frequency'],
                    'duration'        => $m['duration'],
                    'timing'          => $m['timing'],
                    'created_at'      => now(),
                    'updated_at'      => now(),
                ]);
            }
        });

        Session::forget('rx');
        return redirect()->route('prescriptions.create')->with('success','Prescription saved.');
    }
}
