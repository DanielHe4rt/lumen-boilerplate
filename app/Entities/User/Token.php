<?php


namespace App\Entities\User;


use Illuminate\Database\Eloquent\Model;

class Token extends Model
{
    protected $fillable = [
        'id', 'type', 'data', 'used', 'expires_at'
    ];

    protected $casts = [
        'used' => 'boolean'
    ];
}
