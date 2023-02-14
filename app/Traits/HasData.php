<?php

namespace App\Traits;

use Spatie\SchemalessAttributes\Casts\SchemalessAttributes;

trait HasData
{
    protected $schemalessAttributes = [
        'data',
    ];

    public function initializeHasData()
    {
        $this->fillable = array_merge(
            $this->fillable, [
                'data'
            ]
        );

        $this->casts = array_merge(
            $this->casts, [
                'data' => SchemalessAttributes::class,
            ]
        );
    }
}
