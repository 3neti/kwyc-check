<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;
use App\Enums\ChannelEnum;
use App\Enums\FormatEnum;

class Repository extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'channel', 'format', 'address', 'command'];

    protected $casts = [
        'channel' => ChannelEnum::class,
        'format' > FormatEnum::class,
    ];

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }
}
