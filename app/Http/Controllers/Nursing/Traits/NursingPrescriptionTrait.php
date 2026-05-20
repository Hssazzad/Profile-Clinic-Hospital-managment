<?php

namespace App\Http\Controllers\Nursing\Traits;

use Illuminate\Support\Facades\DB;

/**
 * NursingPrescriptionTrait
 *
 * AdmissionController, PostSurgeryController, FreshController —
 * ???????? duplicate ??? ?? methods ?????
 * ??? ?? ????????? Logic ????? change ??????
 */
trait NursingPrescriptionTrait
{
    /* ------------------------------------------
       On Admission prescriptions (latest 5)
    ------------------------------------------ */
    private function getPreviousAdmissionPrescriptions($patientId): array
    {
        $rows = DB::table('nursing_admissions')
            ->where('patient_id', $patientId)
            ->where('admission_type', 'on_admission')
            ->orderByDesc('id')
            ->limit(5)
            ->get();

        $result = [];
        foreach ($rows as $row) {
            $lines = [];

            $meds = DB::table('nursing_admission_medicines')
                ->where('nursing_admission_id', $row->id)
                ->get();

            foreach ($meds as $m) {
                $parts = array_filter([
                    $m->medicine_name ?? $m->name ?? null,
                    $m->dose      ?? null,
                    $m->route     ?? null,
                    $m->frequency ?? null,
                    !empty($m->duration) ? ('× ' . $m->duration) : null,
                    !empty($m->timing)   ? ('(' . $m->timing . ')') : null,
                ]);
                if (!empty($parts)) {
                    $lines[] = implode(' ', $parts);
                }
            }

            if (empty($lines) && !empty($row->notes)) {
                $lines = array_values(array_filter(
                    preg_split('/\r\n|\r|\n/', trim((string) $row->notes))
                ));
            }

            $result[] = [
                'id'     => $row->id,
                'date'   => $row->rx_date ?? $row->admission_date ?? $row->created_at,
                'doctor' => null,
                'type'   => 'on_admission',
                'lines'  => array_values($lines),
            ];
        }

        return $result;
    }

    /* ------------------------------------------
       Fresh prescriptions (latest 3)
    ------------------------------------------ */
    private function getFreshPrescriptions($patientId): array
    {
        $rows = DB::table('nursing_fresh_prescriptions')
            ->where('patient_id', $patientId)
            ->orderByDesc('id')
            ->limit(3)
            ->get();

        $result = [];
        foreach ($rows as $row) {
            $lines = [];

            $meds = DB::table('nursing_fresh_medicines')
                ->where('fresh_prescription_id', $row->id)
                ->get();

            foreach ($meds as $m) {
                $parts = array_filter([
                    $m->medicine_name ?? null,
                    $m->dose      ?? null,
                    $m->route     ?? null,
                    $m->frequency ?? null,
                    !empty($m->duration) ? ('× ' . $m->duration) : null,
                ]);
                if (!empty($parts)) {
                    $lines[] = implode(' ', $parts);
                }
            }

            if (empty($lines) && !empty($row->rx_text)) {
                $lines = array_values(array_filter(
                    preg_split('/\r\n|\r|\n/', trim((string) $row->rx_text))
                ));
            }

            $result[] = [
                'id'     => $row->id,
                'date'   => $row->prescription_date ?? $row->created_at,
                'doctor' => $row->doctor_name ?? null,
                'type'   => 'fresh',
                'lines'  => array_values($lines),
            ];
        }

        return $result;
    }

    /* ------------------------------------------
       Post Surgery prescriptions (latest 5)
    ------------------------------------------ */
    private function getPostSurgeryPrescriptions($patientId): array
    {
        $rows = DB::table('nursing_postsurgery_prescriptions')
            ->where('patient_id', $patientId)
            ->orderByDesc('id')
            ->limit(5)
            ->get();

        $result = [];
        foreach ($rows as $row) {
            $lines = [];

            $meds = DB::table('nursing_postsurgery_medicines')
                ->where('postsurgery_prescription_id', $row->id)
                ->get();

            foreach ($meds as $m) {
                $parts = array_filter([
                    $m->medicine_name ?? null,
                    $m->strength  ?? null,
                    $m->dose      ?? null,
                    $m->route     ?? null,
                    $m->frequency ?? null,
                    !empty($m->duration) ? ('× ' . $m->duration) : null,
                    !empty($m->timing)   ? ('(' . $m->timing . ')') : null,
                ]);
                if (!empty($parts)) {
                    $lines[] = implode(' ', $parts);
                }
            }

            if (empty($lines) && !empty($row->notes)) {
                $lines = array_values(array_filter(
                    preg_split('/\r\n|\r|\n/', trim((string) $row->notes))
                ));
            }

            $result[] = [
                'id'     => $row->id,
                'date'   => $row->prescription_date ?? $row->created_at,
                'doctor' => null,
                'type'   => 'post-surgery',
                'lines'  => array_values($lines),
            ];
        }

        return $result;
    }

    /* ------------------------------------------
       Template table ???? safe fetch
       Exception ??? empty collection return
    ------------------------------------------ */
    private function safeFetch(string $table, $tplCode, string $orderBy = 'id')
    {
        try {
            return DB::table($table)
                ->when($tplCode, fn($q) => $q->where('templateid', $tplCode))
                ->orderBy($orderBy)
                ->get();
        } catch (\Exception $e) {
            return collect();
        }
    }
}