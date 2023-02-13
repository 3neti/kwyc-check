<?php

namespace App\Models;

use Spatie\SchemalessAttributes\Casts\SchemalessAttributes;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use MOIREI\Vouchers\Models\Voucher as BaseVoucher;
use Illuminate\Database\Eloquent\Builder;
use MOIREI\Vouchers\VoucherScheme;

class Voucher extends BaseVoucher
{
    use HasFactory;

    /**
     * The attributes that should be cast to native types.
     *
     * @var array<string>
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'active_date' => 'datetime',
        'expires_at' => 'datetime',
        'can_redeem' => 'array',
        'cannot_redeem' => 'array',
        'quantity' => 'integer',
        'quantity_used' => 'json',
        'value' => 'decimal:2',
        'data' => SchemalessAttributes::class,
        'limit_scheme' => VoucherScheme::class,
    ];

    protected $schemalessAttributes = [
        'data',
    ];

    /**
     * Products related this voucher
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphToMany
     */
    public function campaigns(): MorphToMany
    {
        return $this->morphedByMany(
            config('vouchers.models.products'),
            'item',
            config('vouchers.tables.item_pivot_table', 'item_voucher'),
            'voucher_id',
        );
    }

    public function scopeWithData(): Builder
    {
        return $this->data->modelScope();
    }
}
