<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class FixLocationsSeeder extends Seeder
{
    private const SORT_ORDER_TO_BBS = [
        10 => '10', // Barishal
        20 => '20', // Chattogram
        30 => '30', // Dhaka
        40 => '40', // Khulna
        50 => '45', // Mymensingh
        60 => '50', // Rajshahi
        70 => '55', // Rangpur
        80 => '60', // Sylhet
    ];

    public function run(): void
    {
        $path = database_path('data/bd_locations_bangla.json');
        if (!File::exists($path)) {
            $this->command->error("File not found: {$path}");
            return;
        }

        $raw = json_decode(File::get($path), true);
        $bbsToDivisionTitle = [];
        foreach ($raw['divisions_bn'] as $row) {
            $bbsToDivisionTitle[(string) $row['value']] = $row['title'];
        }

        $sqlLines = [];
        $sqlLines[] = "-- Bangladesh Divisions, Districts, Upazilas Data Update (UTF-8)";
        $sqlLines[] = "SET NAMES utf8mb4;";
        $sqlLines[] = "SET CHARACTER SET utf8mb4;";
        $sqlLines[] = "";

        // 1. Update Divisions
        $divisions = DB::table('divisions')->orderBy('sort_order')->get();
        foreach ($divisions as $div) {
            $bbs = self::SORT_ORDER_TO_BBS[(int) $div->sort_order] ?? null;
            if ($bbs && isset($bbsToDivisionTitle[$bbs])) {
                $bnName = $bbsToDivisionTitle[$bbs];
                DB::table('divisions')->where('id', $div->id)->update([
                    'name' => $bnName,
                    'updated_at' => now(),
                ]);
                $escaped = addslashes($bnName);
                $sqlLines[] = "UPDATE `divisions` SET `name` = '{$escaped}' WHERE `id` = {$div->id};";
            }
        }
        $sqlLines[] = "";

        // 2. Update Districts in place
        // The districts were inserted per division in order of sort_order
        $districtBbsToId = [];
        foreach ($divisions as $div) {
            $bbs = self::SORT_ORDER_TO_BBS[(int) $div->sort_order] ?? null;
            $districtRows = $raw['districts_bn'][$bbs] ?? [];
            $existingDistricts = DB::table('districts')
                ->where('division_id', $div->id)
                ->orderBy('sort_order')
                ->orderBy('id')
                ->get();

            foreach ($districtRows as $sort => $districtRow) {
                $districtBbs = (string) $districtRow['value'];
                $target = $existingDistricts[$sort] ?? null;
                if ($target) {
                    $bnName = $districtRow['title'];
                    DB::table('districts')->where('id', $target->id)->update([
                        'name' => $bnName,
                        'updated_at' => now(),
                    ]);
                    $districtBbsToId[$districtBbs] = $target->id;
                    $escaped = addslashes($bnName);
                    $sqlLines[] = "UPDATE `districts` SET `name` = '{$escaped}' WHERE `id` = {$target->id};";
                }
            }
        }
        $sqlLines[] = "";

        // 3. Update Upazilas in place
        foreach ($raw['upazilas_bn'] as $districtKey => $upazRows) {
            $districtPk = $districtBbsToId[(string) $districtKey] ?? null;
            if ($districtPk === null) {
                continue;
            }

            $existingUpazilas = DB::table('upazilas')
                ->where('district_id', $districtPk)
                ->orderBy('sort_order')
                ->orderBy('id')
                ->get();

            foreach ($upazRows as $sort => $upazRow) {
                $target = $existingUpazilas[$sort] ?? null;
                if ($target) {
                    $bnName = $upazRow['title'];
                    DB::table('upazilas')->where('id', $target->id)->update([
                        'name' => $bnName,
                        'updated_at' => now(),
                    ]);
                    $escaped = addslashes($bnName);
                    $sqlLines[] = "UPDATE `upazilas` SET `name` = '{$escaped}' WHERE `id` = {$target->id};";
                }
            }
        }

        // Save generated SQL file
        $sqlPath = database_path('seeders/bangladesh_locations_update.sql');
        File::put($sqlPath, implode("\n", $sqlLines));
        echo "Locations updated successfully. SQL saved to: {$sqlPath}\n";
    }
}
