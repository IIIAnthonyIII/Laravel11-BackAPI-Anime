<?php
namespace App\Models\Traits;
use Illuminate\Support\Str;

trait Sorts {
    public function scopeSorts($query, $sort) {
        try{
            if (!property_exists($this, 'allowedSorts')) throw new \Exception('Por favor agrega la propiedad public $allowedSorts en '.get_class($this), 500);
            if(is_null($sort)) {
                return;
            }
            $sortFields = array_map('trim', explode(',', $sort));
            foreach($sortFields as $field) {
                $direction = 'asc';
                if(Str::of($field)->startsWith('-')) {
                    $direction = 'desc';
                    $field = Str::of($field)->substr(1);
                }
                if(!collect($this->allowedSorts)->contains($field)) throw new \Exception('Parámetro inválido en sort, '.$field.' no fue encontrado', 400);
                $dataSort = $query->orderBy($field, $direction);
            }
            return $dataSort;
        } catch (\Exception $e) {
            throw $e;
        }
    }
}