<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class FreshMedicineService
{
    /**
     * Fetch available medicines from common_medicine table
     * Feeds "আরো Medicine যোগ করুন" section — user adds manually, fills dose/freq after
     *
     * @return \Illuminate\Support\Collection
     */
    public function getAvailableMedicinesForFresh()
    {
        return DB::table('common_medicine')
            ->where('active', 1)
            ->select(
                'id',
                'name',
                'code',
                'GroupName',
                'strength',
                DB::raw("'' as dose"),
                DB::raw("'' as route"),
                DB::raw("'' as frequency"),
                DB::raw("'' as duration"),
                DB::raw("'' as timing"),
                DB::raw("'' as note")
            )
            ->orderBy('GroupName')
            ->orderBy('name')
            ->get();
    }

    /**
     * Get ALL template_medicine rows where order_type = 'fresh prescription'
     * Used for auto-loading Selected Medicines when patient is selected
     *
     * @return \Illuminate\Support\Collection
     */
    public function getTemplateMedicinesForFreshPrescription()
    {
        return DB::table('template_medicine')
            ->where('active', 1)
            ->where('order_type', 'fresh prescription')
            ->select('id', 'name', 'strength', 'dose', 'route', 'frequency', 'duration', 'timing', 'note', 'group')
            ->orderBy('group')
            ->orderBy('name')
            ->get();
    }

    /**
     * Get medicines from template (existing functionality)
     *
     * @param string $templateId
     * @return \Illuminate\Support\Collection
     */
    public function getTemplateMedicines($templateId)
    {
        return DB::table('template_medicine')
            ->where('templeteid', $templateId)
            ->where('active', 1)
            ->orderBy('group')
            ->orderBy('name')
            ->get();
    }

    /**
     * Get medicines from a specific template for fresh prescription
     * (order_type = 'fresh prescription' only)
     *
     * @param string $templateId
     * @return \Illuminate\Support\Collection
     */
    public function getTemplateMedicinesForFresh($templateId)
    {
        return DB::table('template_medicine')
            ->where('templeteid', $templateId)
            ->where('active', 1)
            ->where('order_type', 'fresh prescription')
            ->orderBy('group')
            ->orderBy('name')
            ->get();
    }

    /**
     * Apply template and map medicines to common_medicine data
     *
     * @param string $templateId
     * @return array
     */
    public function applyTemplateWithCommonMedicines($templateId)
    {
        $templateMedicines = $this->getTemplateMedicinesForFresh($templateId);
        $commonMedicines   = $this->getAvailableMedicinesForFresh();

        $medicineMap = $commonMedicines->keyBy(function ($item) {
            return strtolower(trim($item->name));
        });

        $mappedMedicines = $templateMedicines->map(function ($med) use ($medicineMap) {
            $medicineName = trim($med->name ?? $med->group ?? '');
            if (empty($medicineName)) {
                return null;
            }

            $commonMed = $medicineMap->get(strtolower($medicineName));

            return [
                'medicine_name'      => $medicineName,
                'dose'               => $med->dose      ?? '',
                'route'              => $med->route      ?? '',
                'frequency'          => $med->frequency  ?? '',
                'duration'           => $med->duration   ?? '',
                'timing'             => $med->timing     ?? '',
                'remarks'            => $med->note       ?? '',
                'common_medicine_id' => $commonMed->id   ?? null,
                'medicine_code'      => $commonMed->code ?? '',
                'group_name'         => $commonMed->GroupName ?? '',
                'strength'           => $commonMed->strength  ?? '',
                'is_from_common'     => !is_null($commonMed),
            ];
        })->filter()->values();

        return [
            'template_medicines'         => $mappedMedicines,
            'available_medicines'        => $commonMedicines,
            'template_medicines_count'   => $mappedMedicines->count(),
            'available_medicines_count'  => $commonMedicines->count(),
        ];
    }

    /**
     * Search medicines from common_medicine table
     *
     * @param string $query
     * @return \Illuminate\Support\Collection
     */
    public function searchCommonMedicines($query = '')
    {
        $builder = DB::table('common_medicine')
            ->where('active', 1)
            ->select(
                'id', 'name', 'code', 'GroupName', 'strength',
                DB::raw("'' as dose"),
                DB::raw("'' as route"),
                DB::raw("'' as frequency"),
                DB::raw("'' as duration"),
                DB::raw("'' as timing"),
                DB::raw("'' as note")
            );

        if (!empty($query)) {
            $builder->where(function ($q) use ($query) {
                $q->where('name',      'like', "%{$query}%")
                  ->orWhere('code',      'like', "%{$query}%")
                  ->orWhere('GroupName', 'like', "%{$query}%");
            });
        }

        return $builder
            ->orderBy('GroupName')
            ->orderBy('name')
            ->limit(100)
            ->get();
    }

    /**
     * Get medicine details by ID from common_medicine
     *
     * @param int $medicineId
     * @return object|null
     */
    public function getCommonMedicineById($medicineId)
    {
        return DB::table('common_medicine')
            ->where('active', 1)
            ->where('id', $medicineId)
            ->first();
    }
}