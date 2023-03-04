<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Verifiable;
use App\Traits\HasMobile;
use App\Traits\HasData;

class Contact extends Model
{
    use HasFactory, Verifiable, HasMobile, HasData;

    /**
     * The attributes that are mass assignable.
     *
     * @var string<int, string>
     */
    protected $fillable = ['mobile', 'handle'];
}
