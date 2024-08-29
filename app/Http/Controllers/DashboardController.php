<?php

namespace App\Http\Controllers;

use App\Http\Requests\DashboardRequest;
use App\Services\DashboardService;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function showDashboard()
    {
        // Path ke file JSON
        $jsonPath = public_path('combined_following_data.json');

        // Membaca isi file JSON
        if (File::exists($jsonPath)) {
            $jsonFile = File::get($jsonPath);
            $combinedData = json_decode($jsonFile, true);
        } else {
            $combinedData = []; // Jika file tidak ditemukan, set data kosong
        }

        // Validasi bahwa $combinedData adalah array
        if (!is_array($combinedData)) {
            $combinedData = [];
        }

        // Mengirim data ke view
        return view('dashboard', ['combinedData' => $combinedData]);
    }

    protected $dService;

    public function __construct(DashboardService $dService)
    {
        $this->dService = $dService;
    }

    /**
     * View - Dashboard
     *
     * - show dashboard main page
     * ---------------------------
     */
    public function dashboardView(Request $request)
    {
        $data = [
            'metaTitle' => 'Dashboard',
            'user'      => $request->user
        ];

        return view('pages/dashboard', $data);
    }

    /**
     * View - Profile Setting
     *
     * - show dashboard main page
     * ---------------------------
     */
    public function dashboardProfileView(Request $request)
    {
        $data = [
            'metaTitle' => 'Profile',
            'headTitle' => 'Edit Profile',
            'user'      => $request->user
        ];

        return view('pages/dashboard_profile', $data);
    }

    /**
     * API - Edit Profile
     * ---------------------------
     */
    public function editProfile(DashboardRequest $request)
    {
        try {
            return $this->dService->editProfile($request);
        }
        catch (\Throwable $th) {
            return response()->json(
                [
                    'message' => $th->getMessage(),
                    'data'    => $th->getCode(),
                ],
                is_int($th->getCode()) ? $th->getCode() : 500
            );
        }
    }
}
