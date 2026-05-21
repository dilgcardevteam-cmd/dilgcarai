<?php

namespace App\Http\Controllers;

use App\Models\Notebook;
use App\Models\Region;
use App\Models\Province;
use App\Models\CityMunicipality;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SystemSettingsController extends Controller
{
    /**
     * Display the system settings page.
     */
    public function index(): View
    {
        return view('settings.index', [
            'settings' => [
                'system_name' => 'NoteGov AI DILG',
                'organization' => 'Department of the Interior and Local Government',
                'timezone' => 'Asia/Manila',
                'language' => 'English',
                'maintenance_mode' => false,
            ],
            'featuredNotebooks' => Notebook::where('is_featured', true)->latest()->get(),
            'allNotebooks' => Notebook::latest()->get(),
            'storageUsage' => [
                'used' => 45.8, // Example GB
                'total' => 100, // Example GB
                'percentage' => 45.8,
            ],
            'psgcCounts' => [
                'regions' => Region::count(),
                'provinces' => Province::count(),
                'cities' => CityMunicipality::count(),
            ]
        ]);
    }

    /**
     * Update system settings.
     */
    public function update(Request $request)
    {
        // Logic to update settings (e.g., in a settings table or .env)
        return back()->with('status', 'Settings updated successfully.');
    }

    /**
     * Import PSGC data from CSV.
     */
    public function importPsgc(Request $request)
    {
        $request->validate([
            'psgc_csv' => 'required|file|mimes:csv,txt',
            'import_mode' => 'required|string|in:upsert,insert_only,update_only,refresh',
        ]);

        ini_set('max_execution_time', 300); // 5 minutes max
        set_time_limit(300);

        $mode = $request->import_mode;

        if ($mode === 'refresh') {
            CityMunicipality::truncate();
            Province::truncate();
            Region::truncate();
        }

        $file = $request->file('psgc_csv');
        $handle = fopen($file->getRealPath(), 'r');
        
        if ($handle === false) {
            return back()->withErrors(['psgc_csv' => 'Failed to read the uploaded file.']);
        }

        // Skip header if it exists
        $header = fgetcsv($handle);
        
        $rowCount = 0;
        $errors = [];
        $batchSize = 100;
        $batchCount = 0;

        try {
            while (($data = fgetcsv($handle)) !== false) {
                // Expected CSV structure: Region, Province, City/Municipality
                $regionName = trim($data[0] ?? '');
                $provinceName = trim($data[1] ?? '');
                $cityName = trim($data[2] ?? '');

                if (empty($regionName)) {
                    continue;
                }

                // Handle Region
                $region = Region::where('name', $regionName)->first();
                if (!$region) {
                    if ($mode === 'upsert' || $mode === 'insert_only' || $mode === 'refresh') {
                        $region = Region::create(['name' => $regionName]);
                    } else {
                        continue; // Skip if update_only and doesn't exist
                    }
                }

                if (!empty($provinceName)) {
                    // Handle Province
                    $province = Province::where('name', $provinceName)->where('region_id', $region->id)->first();
                    if (!$province) {
                        if ($mode === 'upsert' || $mode === 'insert_only' || $mode === 'refresh') {
                            $province = Province::create(['name' => $provinceName, 'region_id' => $region->id]);
                        } else {
                            continue;
                        }
                    } else if ($mode === 'upsert' || $mode === 'update_only') {
                        $province->update(['region_id' => $region->id]);
                    }

                    if (!empty($cityName)) {
                        // Handle City
                        $city = CityMunicipality::where('name', $cityName)->where('province_id', $province->id)->first();
                        if (!$city) {
                            if ($mode === 'upsert' || $mode === 'insert_only' || $mode === 'refresh') {
                                CityMunicipality::create(['name' => $cityName, 'province_id' => $province->id]);
                            }
                        } else if ($mode === 'upsert' || $mode === 'update_only') {
                            $city->update(['province_id' => $province->id]);
                        }
                    }
                }
                $rowCount++;
                $batchCount++;

                // Commit batch to database periodically
                if ($batchCount % $batchSize === 0) {
                    // Optional: can add database flush/transaction commit here if using transactions
                }
            }
        } catch (\Exception $e) {
            fclose($handle);
            return back()->withErrors(['psgc_csv' => 'Import failed: ' . $e->getMessage()]);
        }

        fclose($handle);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'status' => 'success',
                'message' => "Import Successfully! $rowCount rows of PSGC data have been processed.",
                'counts' => [
                    'regions' => Region::count(),
                    'provinces' => Province::count(),
                    'cities' => CityMunicipality::count(),
                ],
                'errors' => $errors
            ]);
        }

        return back()->with('status', "Import Successfully! $rowCount rows of PSGC data have been processed.");
    }
}
