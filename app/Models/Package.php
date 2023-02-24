<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;

class Package extends Model
{
    use HasFactory;

    protected $fillable = ['code', 'name', 'price', 'channel'];

    protected $casts = [
        'price' => 'float',
    ];

    public function getRouteKeyName()
    {
        return 'code';
    }

    public function packageItems(): HasMany
    {
        return $this->hasMany(PackageItem::class);
    }
}
