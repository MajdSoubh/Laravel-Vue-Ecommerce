<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Country extends Model
{
    use HasFactory;
    protected function states(): Attribute
    {
        return  Attribute::make(get: fn ($value) => json_decode($value));
    }
}
