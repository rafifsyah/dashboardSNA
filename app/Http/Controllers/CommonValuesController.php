<?php
// File: app/Http/Controllers/CommonValuesController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CommonValuesController extends Controller
{
    public function show()
    {
        $commonValues = [];

        if (Storage::exists('public/common_values.json')) {
            $commonValues = json_decode(Storage::get('public/common_values.json'), true);

            // Check if the JSON is valid
            if (!$commonValues) {
                $commonValues = [];
            }
        }

        return view('common-values', [
            'commonValues' => $commonValues,
        ]);
    }
}
