<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class FileUploadController extends Controller
{
    public function uploadFollowersFollowing(Request $request)
    {
        try {
            $followersFile = $request->file('followers');
            $followingFile = $request->file('following');

            // Membaca dan mendecode data JSON dari file yang diunggah
            $followersData = json_decode(file_get_contents($followersFile->getRealPath()), true);
            $followingData = json_decode(file_get_contents($followingFile->getRealPath()), true);

            // Periksa kesalahan decoding JSON
            if ($followersData === null || $followingData === null) {
                return response()->json(["error" => "Error decoding JSON files"], 400);
            }

            // Ekstrak nilai dari followers.json
            $followersValues = [];
            foreach ($followersData as $entry) {
                if (isset($entry['string_list_data'][0]['value'])) {
                    $followersValues[$entry['string_list_data'][0]['value']] = true;
                }
            }

            // Ekstrak nilai dari following.json
            $followingValues = [];
            foreach ($followingData['relationships_following'] as $entry) {
                if (isset($entry['string_list_data'][0]['value'])) {
                    $followingValues[$entry['string_list_data'][0]['value']] = true;
                }
            }

            // Temukan nilai yang sama
            $commonValues = array_intersect_key($followersValues, $followingValues);

            // Simpan nilai yang sama ke file JSON baru
            $outputFile = env("PATH_SIMPAN_UPLOAD") . "/common_values.json";
            file_put_contents($outputFile, json_encode(array_keys($commonValues), JSON_PRETTY_PRINT));

            // Redirect ke halaman dengan hasil
            return response()->json([
                "message" => "berhasil"
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage(),
            ],500);
        }
    }

    public function getFollowersFollowing(Request $request)
    {
        try {
            // Simpan nilai yang sama ke file JSON baru
            $outputFile = env("PATH_SIMPAN_UPLOAD") . "/common_values.json";
            return response(file_get_contents($outputFile), 200)->header('Content-Type', mime_content_type($outputFile));
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage(),
            ],500);
        }
    }
}
