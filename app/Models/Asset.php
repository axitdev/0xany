<?php

namespace App\Models;

use App\Enums\AssetTypeEnum;
use Database\Factories\AssetFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Asset extends Model
{
    /** @use HasFactory<AssetFactory> */
    use HasFactory;

    use HasUuids;
    use SoftDeletes;

    protected $fillable = [
        'name',
        'symbol',
        'type',
        'decimals',
        'logo',
        'description',
        'website',
        'twitter',
        'discord',
        'telegram',
    ];

    protected function casts(): array
    {
        return [
            'type' => AssetTypeEnum::class,
        ];
    }
}
