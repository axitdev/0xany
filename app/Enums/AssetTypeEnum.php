<?php

namespace App\Enums;

enum AssetTypeEnum: string
{
    case TOKEN = 'token';
    case FIAT = 'fiat';
    case STABLECOIN = 'stablecoin';
}
