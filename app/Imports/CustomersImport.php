<?php

namespace App\Imports;

use App\Customer;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class CustomersImport implements ToCollection, WithStartRow, WithChunkReading
{
    protected $duplicates = false;
    protected $skippedRows = 0;
    protected $importedRows = 0;

    public function collection(Collection $rows)
    {
        if (count($rows) > 2000) {
            throw new \Exception('Excel import limit is 2000 rows only');
        }

        foreach ($rows as $row) {
            // Clean phone number
            $phone = isset($row[3]) ? preg_replace('/[^0-9]/', '', $row[3]) : null;
            $phone = $phone ? preg_replace('/^\+?92|\|1/', '', $phone) : null;

            // Check for duplicate phone
            if ($phone && Customer::where('primary_phone', $phone)->exists()) {
                $this->duplicates = true;
                $this->logSkippedRow($row, 'Duplicate phone: ' . $phone);
                $this->skippedRows++;
                continue;
            }

            // Split full name into first_name and last_name
            $fullName = isset($row[0]) && is_string($row[0]) ? trim($row[0]) : null;
            $firstName = null;
            $lastName = null;

            if ($fullName) {
                $nameParts = explode(' ', $fullName, 2); // Split on first space
                $firstName = $nameParts[0];
                $lastName = isset($nameParts[1]) ? $nameParts[1] : null;
            }

            // Clean and validate other data
            $email = isset($row[2]) && is_string($row[2]) && filter_var($row[2], FILTER_VALIDATE_EMAIL) ? trim($row[2]) : null;
            $dob = null;

            if (isset($row[1]) && is_numeric($row[1])) {
                try {
                    $dob = Carbon::createFromDate(1899, 12, 30)->addDays($row[1])->format('Y-m-d');
                } catch (\Exception $e) {
                    $dob = null;
                }
            }

            // Validate row data
            $validator = Validator::make([
                'first_name' => $firstName,
                'primary_phone' => $phone,
                'email' => $email,
            ], [
                'first_name' => 'required|string|max:255',
                'primary_phone' => 'nullable|string|max:20|unique:customers,primary_phone',
                'email' => 'nullable|email|unique:customers,email|max:255',
            ]);

            if ($validator->fails()) {
                $this->logSkippedRow($row, 'Validation failed: ' . json_encode($validator->errors()->all()));
                $this->skippedRows++;
                continue;
            }

            // Create customer
            Customer::create([
                'first_name' => $firstName,
                'last_name' => $lastName,
                'date_of_birth' => $dob,
                'email' => $email,
                'primary_phone' => $phone,
                'created_at' => Carbon::now(),
            ]);

            $this->importedRows++;
        }
    }

    public function startRow(): int
    {
        return 2; // Skip header row
    }

    public function chunkSize(): int
    {
        return 1000; // Process 1000 rows per chunk
    }

    public function getImportSummary()
    {
        return [
            'duplicates' => $this->duplicates,
            'skippedRows' => $this->skippedRows,
            'importedRows' => $this->importedRows,
        ];
    }

    protected function logSkippedRow($row, $reason)
    {
        $rowData = [
            'full_name' => $row[0] ?? 'N/A',
            'dob' => $row[1] ?? 'N/A',
            'email' => $row[2] ?? 'N/A',
            'primary_phone' => $row[3] ?? 'N/A',
            'reason' => $reason,
            'timestamp' => now()->toDateTimeString(),
        ];
        Log::channel('skipped')->info('Skipped Row: ' . json_encode($rowData));
    }
}