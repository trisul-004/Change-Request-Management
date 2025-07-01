<?php

namespace App\Imports;

use App\Models\Portal;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\Log;

class PortalsImport implements ToModel, WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        Log::info('Importing row:', $row);
        
        try {
            return new Portal([
                'name' => $row['name'] ?? null,
                'url' => $row['url'] ?? null,
                'ip_address' => $row['ip_address'] ?? null,
                'description' => $row['descripti_on'] ?? $row['description'] ?? null,
                'client' => $row['client'] ?? null,
                'status' => $row['status'] ?? 'active',
                'developer' => $row['developer'] ?? null,
            ]);
        } catch (\Exception $e) {
            Log::error('Error importing row: ' . $e->getMessage(), $row);
            throw $e;
        }
    }
}
