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

class DentalOfficesImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnFailure
{
    use Importable, SkipsFailures;

    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
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

        // Optional Fallback: If State wasn't found but 'region' column exists in Excel
        if (!$regionId && isset($row['region'])) {
            $region = Region::where('name', 'LIKE', trim($row['region']))->first();
            if ($region) {
                $regionId = $region->id;
            }
        }

        // --- 2. AUTO-MATCH SALES REP LOGIC (Optional) ---
        $salesRepId = null;
        if (isset($row['sales_rep']) && !empty($row['sales_rep'])) {
            $rep = User::where('name', 'LIKE', trim($row['sales_rep']))->first();
            if ($rep) {
                $salesRepId = $rep->id;
            }
        }

        // --- 3. CREATE THE RECORD ---
        return new DentalOffice([
            'name' => $row['name'],
            'dr_name' => $row['dr_name'] ?? null,
            'state_id' => $stateId,
            'region_id' => $regionId,
            'sales_rep_id' => $salesRepId,
            'email' => $row['email'] ?? null,
            'phone' => $row['phone'] ?? null,
            'address' => $row['address'] ?? null,
            'country' => 'United States', // Hardcoded as requested
            'receptive' => 'COLD',
        ]);
    }

    /**
     * VALIDATION RULES
     * This also handles the SKIP logic for duplicates
     */
    public function rules(): array
    {
        return [
            'state' => 'required',
            'name' => [
                'required',
                function ($attribute, $value, $fail) {
                    // Get the data for the SPECIFIC row currently being validated
                    // We use request('dr_name') as a fallback but we need the row context
                    $data = request()->all();

                    // Better approach: Look up State ID based on the row's 'state' value
                    $stateName = isset($this->row_data['state']) ? trim($this->row_data['state']) : null;
                    $state = \App\Models\State::where('name', 'LIKE', $stateName)->first();
                    $stateId = $state->id ?? null;

                    $drName = $this->row_data['dr_name'] ?? null;

                    $exists = \App\Models\DentalOffice::where('name', $value)
                        ->where('dr_name', $drName)
                        ->where('state_id', $stateId)
                        ->exists();

                    if ($exists) {
                        $fail('DUPLICATE: This office already exists in this state for this doctor.');
                    }
                },
            ],
        ];
    }

    // Add this helper to capture row data for the validation rule
    private $row_data;
    public function prepareForValidation($data, $index)
    {
        $this->row_data = $data;
        return $data;
    }
}
