<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'user_id',
    ];

    

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function productUser()
    {
        return $this->hasOneThrough(User::class, product::class);
    }

    public function messages()
    {
        return $this->hasMany(Message::class);
    }
}
