<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomDetail extends Model
{
    use HasFactory;
    protected $fillable = [
        'transaction_id',
        'type',
        'vehicleRegNumber',
        'entity',
        'status',
        'is_deleted'
    ];

    public function user(){
        $this->belongsTo(User::class);
    }
}
