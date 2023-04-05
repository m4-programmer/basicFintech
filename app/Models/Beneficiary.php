<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Beneficiary extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'beneficiary_id',
    ];
    public function user()
    {
        return $this->belongsTo(User::class,'beneficiary_id');
    }
}
