<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Type extends Model
{
    use HasFactory;

    // protected $connection = 'DB2';
    protected $table = "anime";
    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
