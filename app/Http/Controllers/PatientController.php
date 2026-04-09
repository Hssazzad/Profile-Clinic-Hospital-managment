<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;    
use App\Models\ConfigDistrict;

class PatientController extends Controller
{
    /**
     * Display patient list with search
     */
    public function index(Request $request)
    {
        $q = $request->get('q');
        $patients = Patient::query()
            ->when($q, function ($query) use ($q) {
                $query->where('patientname', 'like', "%{$q}%")
                      ->orWhere('patientcode', 'like', "%{$q}%")
                      ->orWhere('mobile_no', 'like', "%{$q}%")
                      ->orWhere('nid_number', 'like', "%{$q}%");
            })
            ->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();
        
        return view('patients.create', compact('patients', 'q'));
    }

    /**
     * Show new patient form with auto-generated code
     */
    public function newpatient()
    {
        $districts = DB::table('configdistrict')
            ->select('code', 'name')
            ->orderBy('name')
            ->get();
        
        // YY (last 2 digits of year) => 26 for 2026
        $yy = (int) Carbon::now()->format('y');

        $newCode = DB::transaction(function () use ($yy) {
            // lock rows of this year to avoid duplicate in concurrent requests
            $lastCode = DB::table('configpatientcode')
                ->whereBetween('Code', [$yy * 100000, $yy * 100000 + 999])
                ->lockForUpdate()
                ->max('Code');

            $nextSerial = $lastCode ? (($lastCode % 100000) + 1) : 1;

            if ($nextSerial > 999) {
                abort(500, 'Yearly patient code limit reached (999).');
            }

            $code = ($yy * 100000) + $nextSerial;
            
            return $code;
        });

        // pass to view
        return view('patients.create', compact('newCode', 'districts'));
    }

    /**
     * Fetch reference persons by type (AJAX)
     */
    public function fetch_reference_person(Request $request)
    {
        $type = $request->reference_type;

        // Load from patient_ref table
        $rows = DB::table('patient_ref')
            ->select('ID', 'Name', 'Mobile')
            ->where('active', 1)
            ->where('ref_type', $type)   // OfficeEmployee / PCNurse / MidWife
            ->orderBy('Name')
            ->get();

        // return <option> list
        $html = '<option value="">-- Select Person --</option>';
        foreach ($rows as $r) {
            $html .= '<option value="'.$r->ID.'">'.$r->Name.' ('.$r->Mobile.')</option>';
        }

        return response($html);
    }

    /**
     * Store new reference person (AJAX)
     */
    public function store_reference_person(Request $request)
    {
        $data = $request->validate([
            'ref_type' => ['required', Rule::in(['OfficeEmployee','PCNurse','MidWife','Others'])],
            'Name'     => ['required', 'string', 'max:25'],
            'Mobile'   => ['nullable', 'string', 'max:15'],
        ]);

        // Simple code generate (you can change format)
        $code = strtoupper(substr($data['ref_type'], 0, 2)) . str_pad((string)rand(1, 9999), 4, '0', STR_PAD_LEFT);

        $id = DB::table('patient_ref')->insertGetId([
            'Code'    => $code,
            'ref_type'=> $data['ref_type'],
            'Name'    => $data['Name'],
            'Mobile'  => $data['Mobile'] ?? '',
            'active'  => 1,
        ]);

        return response()->json([
            'ok' => true,
            'id' => $id,
            'type' => $data['ref_type'],
        ]);
    }

    /**
     * Get all reference persons (unused, kept for compatibility)
     */
    public function refPersons(Request $request)
    {
        $rows = DB::table('patient_ref')
            ->select('ID as id', 'Name as name', 'Mobile as mobile')
            ->orderBy('Name')
            ->get();

        return response()->json($rows);
    }

    /**
     * Store new patient
     * ✅ Required: patientname, patientfather, mobile_no, date_of_birth, age, gender
     * ✅ Optional: all address fields (district, upozila, union, village)
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            // ✅ REQUIRED FIELDS
            'patientname'    => ['required', 'string', 'max:150'],
            'patientfather'  => ['required', 'string', 'max:70'],
            'mobile_no'      => ['required', 'digits:11'],
            'date_of_birth'  => ['required', 'date'],
            'age'            => ['required', 'string', 'min:1', 'max:150'],
            'gender'         => ['required', Rule::in(['Male','Female','Other'])],

            // ✅ OPTIONAL FIELDS
            'patienthusband' => ['nullable', 'string', 'max:70'],
            'photo'          => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,gif', 'max:2048'],

            // ✅ ADDRESS OPTIONAL
            'district'       => ['nullable', 'string', 'max:20'],
            'upozila'        => ['nullable', 'string', 'max:20'],
            'union'          => ['nullable', 'string', 'max:20'],
            'village'        => ['nullable', 'string', 'max:20'],
            'address'        => ['nullable', 'string', 'max:255'],

            // ✅ OPTIONAL CONTACT
            'spomobile_no'   => ['nullable', 'digits:11'],
            'relmobile_no'   => ['nullable', 'digits:11'],
            'nid_number'     => ['nullable', 'digits_between:10,17'],
            'email'          => ['nullable', 'email', 'max:120'],

            // ✅ OPTIONAL HEALTH INFO
            'blood_group'    => ['nullable', 'string', 'max:5'],
            'notes'          => ['nullable', 'string'],

            // ✅ OPTIONAL REFERENCE
            'reference_type'   => ['nullable', Rule::in(['Self','OfficeEmployee','PCNurse','MidWife','NOCOM','Others'])],
            'reference_person' => ['nullable', 'integer'],
            'reference_name'   => ['nullable', 'string', 'max:120'],
        ]);

        // ===== Reference Type Validation =====
        $type = $validated['reference_type'] ?? null;

        if ($type === 'Self' || $type === 'Others') {
            if (empty(trim((string)($validated['reference_name'] ?? '')))) {
                return back()->withErrors([
                    'reference_name' => 'Reference Name is required for Self/Others.'
                ])->withInput();
            }
            $validated['reference_person'] = null;

        } elseif (in_array($type, ['OfficeEmployee','PCNurse','MidWife'], true)) {

            if (empty($validated['reference_person'])) {
                return back()->withErrors([
                    'reference_person' => 'Please select Reference Person.'
                ])->withInput();
            }

            $ref = DB::table('patient_ref')
                ->select('ID','Name','Mobile','ref_type','active')
                ->where('ID', $validated['reference_person'])
                ->where('active', 1)
                ->first();

            if (!$ref) {
                return back()->withErrors([
                    'reference_person' => 'Invalid Reference Person selected.'
                ])->withInput();
            }

            if (!empty($ref->ref_type) && $ref->ref_type !== $type) {
                return back()->withErrors([
                    'reference_person' => 'Reference Person type mismatch.'
                ])->withInput();
            }

            $validated['reference_name'] = $ref->Name;

        } else {
            $validated['reference_person'] = null;
            $validated['reference_name'] = null;
        }

        // ===== Photo Upload =====
        if ($request->hasFile('photo')) {
            $file = $request->file('photo');

            // ensure folder exists
            if (!is_dir(public_path('uploads/photos'))) {
                mkdir(public_path('uploads/photos'), 0755, true);
            }

            $filename = time().'_'.uniqid().'.'.$file->getClientOriginalExtension();
            $file->move(public_path('uploads/photos'), $filename);

            // ✅ DB-তে শুধু path রাখুন (URL না)
            $validated['photo'] = 'uploads/photos/'.$filename;
        }

        // ===== Generate Patient Code =====
        $yy = (int) Carbon::now()->format('y');

        $patientCode = DB::transaction(function () use ($yy) {
            $lastCode = DB::table('configpatientcode')
                ->whereBetween('Code', [$yy * 100000, $yy * 100000 + 999])
                ->lockForUpdate()
                ->max('Code');

            $nextSerial = $lastCode ? (($lastCode % 100000) + 1) : 1;

            if ($nextSerial > 999) {
                abort(500, 'Yearly patient code limit reached (999).');
            }

            $code = ($yy * 100000) + $nextSerial;

            DB::table('configpatientcode')->insert([
                'Code'            => $code,
                'LastCreatedDate' => Carbon::today(),
            ]);

            return $code;
        });

        $validated['patientcode'] = $patientCode;

        // ===== Save Patient =====
        Patient::create($validated);

        return redirect()
            ->route('patients.newpatient')
            ->with('success', 'Patient has been saved successfully.');
    }

    /**
     * Search patients
     */
    public function searchpatient(Request $request)
    {
        $query = trim($request->get('q'));

        $patients = Patient::query()
            ->when($query, function ($q) use ($query) {
                $q->where('patientname', 'like', "%{$query}%")
                  ->orWhere('mobile_no', 'like', "%{$query}%")
                  ->orWhere('nid_number', 'like', "%{$query}%")
                  ->orWhere('relmobile_no', 'like', "%{$query}%")
                  ->orWhere('date_of_birth', 'like', "%{$query}%")
                  ->orWhere('patientcode', 'like', "%{$query}%");
            })
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

        return view('patients.search', compact('patients', 'query'));
    }

    /**
     * Show single patient
     */
    public function show(Patient $patient)
    {
        return view('patients.show', compact('patient'));
    }

    /**
     * Show edit patient form
     */
    public function editpatient($id)
    {
        $patient   = Patient::findOrFail($id);
        $districts = DB::table('configdistrict')
            ->orderBy('name')
            ->get(['code','name']);

        return view('patients.editpatient', compact('patient', 'districts'));
    }

    /**
     * Update patient
     * ✅ Required: patientname, patientfather, mobile_no, gender
     * ✅ Optional: all address fields (district, upozila, union, village)
     */
    public function updatepatient(Request $request, $id)
    {
        $patient = Patient::findOrFail($id);

        $validated = $request->validate([
            // ✅ REQUIRED
            'patientname'    => ['required', 'string', 'max:255'],
            'patientfather'  => ['required', 'string', 'max:255'],
            'mobile_no'      => ['required', 'digits:11'],
            'gender'         => ['required', 'in:Male,Female,Other'],

            // ✅ OPTIONAL
            'patienthusband' => ['nullable', 'string', 'max:255'],
            'spomobile_no'   => ['nullable', 'digits:11'],
            'relmobile_no'   => ['nullable', 'digits:11'],
            'blood_group'    => ['nullable', 'in:A+,A-,B+,B-,AB+,AB-,O+,O-'],
            'date_of_birth'  => ['nullable', 'date'],
            'age'            => ['nullable', 'string', 'max:150'],
            'nid_number'     => ['nullable', 'digits_between:10,17'],
            'email'          => ['nullable', 'email', 'max:120'],
            'notes'          => ['nullable', 'string', 'max:1000'],

            // ✅ ADDRESS OPTIONAL
            'district'       => ['nullable', 'string', 'max:20'],
            'upozila'        => ['nullable', 'string', 'max:20'],
            'union'          => ['nullable', 'string', 'max:20'],
            'village'        => ['nullable', 'string', 'max:20'],
            'address'        => ['nullable', 'string', 'max:255'],

            // ✅ PHOTO
            'photo'          => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        // ===== Photo upload (optional) =====
        if ($request->hasFile('photo')) {
            $file = $request->file('photo');

            // ensure folder exists
            if (!is_dir(public_path('uploads/photos'))) {
                mkdir(public_path('uploads/photos'), 0755, true);
            }

            $filename = time().'_'.uniqid().'.'.$file->getClientOriginalExtension();
            $file->move(public_path('uploads/photos'), $filename);

            $validated['photo'] = 'uploads/photos/'.$filename;
        }

        $patient->update($validated);

        return redirect()->route('patients.editpatient', $patient->id)
            ->with('success', 'Patient updated successfully!');
    }

    /**
     * Delete patient
     */
    public function destroy(Patient $patient)
    {
        $patient->delete();

        return redirect()
            ->route('patients.index')
            ->with('success', 'Patient deleted.');
    }

    /**
     * Fetch admission history for patient (AJAX)
     */
    public function admissionHistory($patientId)
    {
        $admissions = DB::table('nursing_admissions')
            ->where('patient_id', $patientId)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success'    => true,
            'admissions' => $admissions,
        ]);
    }
}