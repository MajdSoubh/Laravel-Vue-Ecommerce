<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;


    protected $guarded = [];

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }
    public function details()
    {
        return $this->hasOne(OrderDetail::class);
    }
    public function client()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function payment()
    {
        return $this->hasOne(Payment::class);
    }
}
