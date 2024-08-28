<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DataController extends Controller
{
    /public function index()
    {
        // Path ke file JSON
        $centralityFilePath = public_path('centrality_measures.json');
        $graphFilePath = public_path('graph_output.json');
        
        // Membaca file JSON
        $centralityJson = file_get_contents($centralityFilePath);
        $graphJson = file_get_contents($graphFilePath);
        
        // Mengonversi JSON menjadi array PHP
        $centralityData = json_decode($centralityJson, true);
        $graphData = json_decode($graphJson, true);
        
        // Mengirim data ke view
        return view('data', [
            'centrality' => $centralityData,
            'graph' => $graphData
        ]);
    }
}
