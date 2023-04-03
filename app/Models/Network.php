<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Network extends Model
{
    use HasFactory;

    protected $fillable = [
        'referral_id',
        'user_id',
        'reffered_id',
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
     public function reffered()
    {
        return $this->belongsTo(User::class,'reffered_id');
    }

}
