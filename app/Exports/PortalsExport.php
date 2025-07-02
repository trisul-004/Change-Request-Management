<?php

namespace App\Exports;

use App\Models\Portal;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class PortalsExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    public function collection()
    {
        return Portal::all();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Name',
            'URL',
            'IP Address',
            'Description',
            'Client',
            'Developer',
            'Status',
            'Managed By',
            'Created At',
            'Updated At'
        ];
    }

    public function map($portal): array
    {
        return [
            $portal->id,
            $portal->name,
            $portal->url,
            $portal->ip_address,
            $portal->description,
            $portal->client,
            $portal->developer,
            $portal->status,
            $portal->managed_by,
            $portal->created_at->format('Y-m-d H:i:s'),
            $portal->updated_at->format('Y-m-d H:i:s'),
        ];
    }
}
