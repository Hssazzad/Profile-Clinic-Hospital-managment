<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Nursing\NursingController;
use App\Http\Controllers\Nursing\AdmissionController;
use App\Http\Controllers\Nursing\PostSurgeryController;
use App\Http\Controllers\Nursing\FreshController;
use App\Http\Controllers\Nursing\RoundPrescriptionController;
use App\Http\Controllers\Nursing\DischargeController;
use App\Http\Controllers\Nursing\ReleaseController;
use App\Http\Controllers\Nursing\ReleaseApprovalController;
use App\Http\Controllers\TemplateController;

// ═══════════════════════════════════════════════════════════════════════════════
// PUBLIC ROUTES
// ═══════════════════════════════════════════════════════════════════════════════

Route::get('/test-nursing-public', function () {
    return "Nursing routes are working!";
});

Route::get('/nursing/admission/template/data/{title}', [TemplateController::class, 'getTemplateData'])
    ->name('nursing.admission.template.data.public');

// ═══════════════════════════════════════════════════════════════════════════════
// AUTHENTICATED ROUTES
// ═══════════════════════════════════════════════════════════════════════════════
Route::middleware(['web', 'auth'])->group(function () {

    // ─────────────────────────────────────────────────────────────────────────
    // PREFIX: nursing (lowercase)
    // ─────────────────────────────────────────────────────────────────────────
    Route::prefix('nursing')->group(function () {

        // ── Dashboard & General ──────────────────────────────────────────────
        Route::get('/index',      [NursingController::class, 'index'])->name('nursing.index');
        Route::get('/create',     [NursingController::class, 'create'])->name('nursing.create');
        Route::get('/Presurgery', [NursingController::class, 'Presurgery'])->name('nursing.presurgery');
        Route::get('/presurgery', [NursingController::class, 'Presurgery']);

        // ── Round Prescription ───────────────────────────────────────────────
        Route::get('/RoundPrescription',                               [RoundPrescriptionController::class, 'index'])->name('nursing.round_prescription');
        Route::get('/roundprescription',                               [RoundPrescriptionController::class, 'index']);
        Route::get('/roundprescription/data',                          [RoundPrescriptionController::class, 'getData'])->name('nursing.round_prescription.data');
        Route::get('/roundprescription/patient-admission/{patientId}', [RoundPrescriptionController::class, 'getPatientAdmissionData'])->name('nursing.round_prescription.patient');
        Route::post('/roundprescription/store',                        [RoundPrescriptionController::class, 'store'])->name('nursing.round_prescription.store');
        
        // ✅ roundprescription/detail/{id} — for viewing prescription details
        Route::get('/roundprescription/detail/{id}', [RoundPrescriptionController::class, 'detail'])->name('nursing.round_prescription.detail');
        
        // ✅ roundprescription/patient-history/{patientId} — for patient history
        Route::get('/roundprescription/patient-history/{patientId}', [RoundPrescriptionController::class, 'patientHistory'])->name('nursing.round_prescription.patient_history');

        // ── Discharge ────────────────────────────────────────────────────────
        Route::get('/Discharge',                           [DischargeController::class, 'index'])->name('nursing.discharge');
        Route::get('/discharge',                           [DischargeController::class, 'index']);
        Route::get('/discharge/patient-data/{patientId}', [DischargeController::class, 'getPatientData'])->name('nursing.discharge.patient');
        Route::post('/discharge/store',                    [DischargeController::class, 'store'])->name('nursing.discharge.store');

        // ── Release Patients (Nurse) ─────────────────────────────────────────
        Route::get('/Releasepatients',                          [ReleaseController::class, 'index'])->name('nursing.release_patients');
        Route::get('/releasepatients',                          [ReleaseController::class, 'index']);
        Route::get('/releasepatients/patient-data/{patientId}', [ReleaseController::class, 'getPatientData'])->name('nursing.release.patient');
        Route::post('/releasepatients/store',                   [ReleaseController::class, 'store'])->name('nursing.release.store');

        // ── Release Approval (Manager) ───────────────────────────────────────
        Route::get('/release-approval',          [ReleaseApprovalController::class, 'index'])->name('nursing.release_approval');
        Route::post('/release-approval/approve', [ReleaseApprovalController::class, 'approve'])->name('nursing.release_approval.approve');
        Route::post('/release-approval/reject',  [ReleaseApprovalController::class, 'reject'])->name('nursing.release_approval.reject');

        // ── On Admission ─────────────────────────────────────────────────────
		
        Route::get('/Onaddmission',              [AdmissionController::class, 'index'])->name('nursing.on_admission');
        Route::get('/onaddmission',              [AdmissionController::class, 'index']);
        Route::get('/admission/select',          [AdmissionController::class, 'selectPatient'])->name('nursing.admission.select');
        Route::get('/admission/create',          [AdmissionController::class, 'create'])->name('nursing.admission.create');
        Route::post('/admission/store',          [AdmissionController::class, 'store'])->name('nursing.admission.store');
        Route::post('/admission/apply-template', [AdmissionController::class, 'applyTemplate'])->name('nursing.admission.apply_template');

        Route::get('/admission/template',            [AdmissionController::class, 'template'])->name('nursing.admission.template');
        Route::get('/admission/template/data/{id?}', [AdmissionController::class, 'getTemplateData'])->name('nursing.admission.template.data');

        // ✅ admission/detail/{id} — 404 fix
        Route::get('/admission/detail/{id}',         [AdmissionController::class, 'detail'])->name('nursing.admission.detail');
        Route::get('/admission/{id}',                [AdmissionController::class, 'show'])->name('nursing.admission.show');
        Route::get('/admission/data/{patientId}',    [AdmissionController::class, 'getAdmissionData'])->name('nursing.admission.data');
        Route::put('/admission/{id}',                [AdmissionController::class, 'update'])->name('nursing.admission.update');
        Route::delete('/admission/{id}',             [AdmissionController::class, 'destroy'])->name('nursing.admission.destroy');

        Route::get('/template/medicine/{id}', [TemplateController::class, 'getTemplateMedicines'])->name('nursing.template.medicine.get');

        Route::get('/prescriptions',           [AdmissionController::class, 'index'])->name('prescriptions.index');
        Route::get('/prescriptions/{id}/edit', [AdmissionController::class, 'edit'])->name('prescriptions.edit');

        // ── Post Surgery ─────────────────────────────────────────────────────
        Route::get('/PostSurgery', [PostSurgeryController::class, 'index'])->name('nursing.postsurgery');
        Route::get('/postsurgery', [PostSurgeryController::class, 'index']);

        Route::get('/PostSurgery/patient-admission/{patientId}', [PostSurgeryController::class, 'getPatientAdmissionData']);
        Route::get('/postsurgery/patient-admission/{patientId}', [PostSurgeryController::class, 'getPatientAdmissionData']);

        Route::post('/PostSurgery/store', [PostSurgeryController::class, 'storePrescription'])->name('nursing.postsurgery.store');
        Route::post('/postsurgery/store', [PostSurgeryController::class, 'storePrescription']);

        Route::post('/postsurgery/apply-template',         [PostSurgeryController::class, 'applyTemplate'])->name('nursing.postsurgery.apply_template');
        Route::get('/postsurgery/template/{id}',           [PostSurgeryController::class, 'getTemplateData'])->name('nursing.postsurgery.template.data');
        Route::get('/postsurgery/pre-operation-medicines', [PostSurgeryController::class, 'getPreOperationMedicines'])->name('nursing.postsurgery.pre-operation-medicines');
        
        // ✅ postsurgery/detail/{id} — for viewing prescription details
        Route::get('/postsurgery/detail/{id}', [PostSurgeryController::class, 'detail'])->name('nursing.postsurgery.detail');

        // ── Fresh ────────────────────────────────────────────────────────────
        Route::get('/Fresh', [FreshController::class, 'index'])->name('nursing.fresh');
        Route::get('/fresh', [FreshController::class, 'index']);

        Route::post('/Fresh/store', [FreshController::class, 'storePrescription'])->name('nursing.fresh.store');
        Route::post('/fresh/store', [FreshController::class, 'storePrescription']);

        Route::get('/fresh/patient-admission/{patientId}', [FreshController::class, 'getPatientAdmissionData'])->name('nursing.fresh.patient_admission');
        Route::get('/fresh/post-operation-medicines',      [FreshController::class, 'getPostOperationMedicines'])->name('nursing.fresh.post-operation-medicines');
        Route::get('/fresh/template/{id}',                 [FreshController::class, 'getTemplateData'])->name('nursing.fresh.template.data');
        
        // ✅ fresh/detail/{id} — for viewing prescription details
        Route::get('/fresh/detail/{id}', [FreshController::class, 'detail'])->name('nursing.fresh.detail');

    }); // end prefix nursing

    // ─────────────────────────────────────────────────────────────────────────
    // ✅ admission/detail — prefix ছাড়া direct route (browser console এ এই URL দেখা যাচ্ছে)
    // ─────────────────────────────────────────────────────────────────────────
    Route::prefix('admission')->group(function () {
        Route::get('/detail/{id}',      [AdmissionController::class, 'detail'])->name('admission.detail');
        Route::get('/show/{id}',        [AdmissionController::class, 'show'])->name('admission.show');
        Route::get('/data/{patientId}', [AdmissionController::class, 'getAdmissionData'])->name('admission.data');
    });

    // ─────────────────────────────────────────────────────────────────────────
    // PREFIX: Nursing (PascalCase aliases)
    // ─────────────────────────────────────────────────────────────────────────
    Route::prefix('Nursing')->group(function () {
        Route::get('/Onaddmission',      [AdmissionController::class, 'index']);
        Route::get('/Presurgery',        [NursingController::class, 'Presurgery']);
        Route::get('/PostSurgery',       [PostSurgeryController::class, 'index']);
        Route::get('/PostSurgery/patient-admission/{patientId}', [PostSurgeryController::class, 'getPatientAdmissionData']);
        Route::post('/PostSurgery/store',[PostSurgeryController::class, 'storePrescription']);
        Route::get('/Fresh',             [FreshController::class, 'index']);
        Route::post('/Fresh/store',      [FreshController::class, 'storePrescription']);
        Route::get('/RoundPrescription', [RoundPrescriptionController::class, 'index']);
        Route::get('/Discharge',         [DischargeController::class, 'index']);
        Route::get('/Releasepatients',   [ReleaseController::class, 'index']);
        Route::get('/ReleaseApproval',          [ReleaseApprovalController::class, 'index']);
        Route::post('/ReleaseApproval/approve', [ReleaseApprovalController::class, 'approve']);
        Route::post('/ReleaseApproval/reject',  [ReleaseApprovalController::class, 'reject']);
    });

}); // end auth middleware group