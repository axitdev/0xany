<?php

namespace App\Exports;

use App\Models\Asset;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class AssetsExport implements FromCollection, WithHeadings
{
    use Exportable;

    public function collection(): Collection
    {
        return Asset::all();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Asset name',
            'Symbol',
            'Type',
            'Decimals',
            'Logo',
            'Description',
            'Website',
            'Twitter',
            'Discord',
            'Telegram',
            'Created at',
            'Updated at',
            'Deleted at',
        ];
    }
}
