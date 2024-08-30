<?php
namespace App\Models;
use App\Models\Traits\Embed;
use App\Models\Traits\Fields;
use App\Models\Traits\Parameters;
use App\Models\Traits\Sorts;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Anime extends Model {
    use HasFactory;
    use Sorts, Embed, Fields, Parameters;

    public $allowedSorts = ['id', 'title', 'name', 'episodes', 'dateOfIssue', 'status', 'created_at', 'updated_at'];
    public $allowedParameters = ['id', 'title', 'name', 'episodes', 'dateOfIssue', 'status', 'created_at', 'updated_at'];
    public $allowedFields = ['id', 'title', 'name', 'episodes', 'dateOfIssue', 'status', 'created_at', 'updated_at'];

    // protected $connection = 'DB2';
    protected $table = "anime";
    protected $guarded = [];

    public function user() {
        return $this->belongsTo(User::class);
    }

    // .../api/anime?sort=-id
    public function sort($query, $sort) {
        return $this->scopeSorts($query, $sort);
    }

    // .../api/anime?embed=type
    public function embed($query, $embed) {
        return $this->scopeEmbed($query, $embed);
    }

    //Parametros
    public function parameters($query, $parameters) {
        return $this->scopeParameters($query, $parameters);
    }

    // .../api/anime?fields=id,title,episodes
    public function fields($query, $fields) {
        return $this->scopeFields($query, $fields);
    }

    //Buscar por nombre y etiqueta
    // public function search($query, $search)
    // {
    //     return $this->scopeSearch($query, $search);
    // }

    // //Buscar por slug
    // public function searchBySlug($query, $slug)
    // {
    //     return $this->scopeSearchBySlug($query, $slug);
    // }

    // //Busqueda general
    // public function searchAll($query, $search, $name)
    // {
    //     return $this->scopeSearchAll($query, $search, $name);
    // }
}
