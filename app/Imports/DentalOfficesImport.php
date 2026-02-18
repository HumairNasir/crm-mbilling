<?php

namespace App\Imports;

use App\Models\DentalOffice;
use App\Models\State;
use App\Models\Region;
use App\Models\User;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;

class DentalOfficesImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnFailure
{
    use Importable, SkipsFailures;

    private $row_data; // Property to hold the current row's data for validation

    /**
     * CAPTURE ROW DATA
     * This runs before validation to let us access the whole row in the rules() method.
     */
    public function prepareForValidation($data, $index)
    {
        $this->row_data = $data;
        return $data;
    }

    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */

    // 3. ADD THIS FUNCTION TO DEFINE CSV SETTINGS
    public function getCsvSettings(): array
    {
        return [
            'input_encoding' => 'UTF-8', // Fixes weird character issues
            'delimiter' => ',', // Forces comma delimiter
            'enclosure' => '"', // Standard enclosure
        ];
    }

    public function model(array $row)
    {
        // --- 1. AUTO-MATCH STATE & REGION LOGIC ---
        $stateName = isset($row['state']) ? trim($row['state']) : null;
        $stateId = null;
        $regionId = null;

        if ($stateName) {
            // Find the State by Name (Case-insensitive)
            $state = State::where('name', 'LIKE', $stateName)->first();

            if ($state) {
                $stateId = $state->id;
                // Automatically grab the Region ID from the State record
                $regionId = $state->region_id;
            }
        }

        // Fallback: If State wasn't found but 'region' column exists
        if (!$regionId && isset($row['region'])) {
            $region = Region::where('name', 'LIKE', trim($row['region']))->first();
            if ($region) {
                $regionId = $region->id;
            }
        }

        // --- 2. AUTO-MATCH SALES REP LOGIC ---
        $salesRepId = null;
        if (isset($row['sales_rep']) && !empty($row['sales_rep'])) {
            $rep = User::where('name', 'LIKE', trim($row['sales_rep']))->first();
            if ($rep) {
                $salesRepId = $rep->id;
            }
        }

        // --- 3. CREATE THE RECORD ---
        // Note: Validation has already passed at this point
        return new DentalOffice([
            'name' => $row['name'],
            'dr_name' => $row['dr_name'] ?? null,
            'state_id' => $stateId,
            'region_id' => $regionId,
            'sales_rep_id' => $salesRepId,
            'email' => $row['email'] ?? null,
            'phone' => $row['phone'] ?? null,
            'address' => $row['address'] ?? null,
            'country' => 'United States',
            'receptive' => 'COLD', // Default status
        ]);
    }

    /**
     * VALIDATION RULES
     */
    public function rules(): array
    {
        return [
            'state' => 'required',
            'name' => [
                'required',
                function ($attribute, $value, $fail) {
                    // 1. Get Context from the captured row data
                    $stateName = isset($this->row_data['state']) ? trim($this->row_data['state']) : null;
                    $drName = isset($this->row_data['dr_name']) ? trim($this->row_data['dr_name']) : null;

                    // 2. Resolve State ID (Need to look it up to check DB uniqueness)
                    $state = State::where('name', 'LIKE', $stateName)->first();
                    $stateId = $state ? $state->id : null;

                    // 3. Check for Duplicates in the Database
                    // We check if a record exists with the same Name, Doctor, AND State.
                    $query = DentalOffice::where('name', $value)->where('state_id', $stateId);

                    if ($drName) {
                        $query->where('dr_name', $drName);
                    }

                    if ($query->exists()) {
                        $fail('DUPLICATE: This office already exists in this state.');
                    }
                },
            ],
        ];
    }
}
