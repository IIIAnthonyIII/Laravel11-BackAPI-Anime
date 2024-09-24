<?php

namespace App\Models;

use App\Models\Traits\Embed;
use App\Models\Traits\Fields;
use App\Models\Traits\Parameters;
use App\Models\Traits\Sorts;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Type extends Model {
    use HasFactory;
    use Sorts, Embed, Fields, Parameters;

    public $allowedSorts = ['id', 'name', 'status', 'created_at', 'updated_at'];
    public $allowedParameters = ['id', 'name', 'status', 'created_at', 'updated_at'];
    public $allowedFields = ['id', 'name', 'status', 'created_at', 'updated_at'];

    // protected $connection = 'DB2';
    protected $table = "type";
    protected $guarded = [];

    public function user() {
        return $this->belongsTo(User::class, "id");
    }
    
    public function animes () {
        return $this->hasMany(Anime::class)->where('status', '!=', 'E');
    }

    // .../api/type?sort=-id
    public function sort($query, $sort) {
        return $this->scopeSorts($query, $sort);
    }

    // .../api/type?embed=user
    public function embed($query, $embed) {
        return $this->scopeEmbed($query, $embed);
    }

    //Parametros
    public function parameters($query, $parameters) {
        return $this->scopeParameters($query, $parameters);
    }

    // .../api/type?fields=id,title,episodes
    public function fields($query, $fields) {
        return $this->scopeFields($query, $fields);
    }
}
