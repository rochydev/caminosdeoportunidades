<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ContractType;
use App\Models\DisabilityType;
use App\Models\JobCategory;
use App\Models\ModalityType;
use App\Models\WorkdayType;

class CatalogController extends Controller
{
    public function formData()
    {
        return response()->json([
            'categories'    => JobCategory::orderBy('name')->get(['id', 'name']),
            'contractTypes' => ContractType::orderBy('name')->get(['id', 'name']),
            'workdayTypes'  => WorkdayType::orderBy('name')->get(['id', 'name']),
            'modalityTypes' => ModalityType::orderBy('name')->get(['id', 'name']),
            'disabilityTypes' => DisabilityType::orderBy('name')->get(['id', 'name']),
        ]);
    }
}
