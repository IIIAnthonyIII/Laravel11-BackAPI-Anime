<?php
namespace App\Models\Traits;

trait Fields {
    public function scopeFields($query, $data) {
        try {
            if(!property_exists($this, 'allowedFields')) throw new \Exception('Por favor agrega la propiedad public $allowedFields en '.get_class($this), 500);
            if(is_null($data)){
                return;
            }
            $fields = array_map('trim', explode(',', $data));
            $dataFields = $query->select($fields);
            return $dataFields;
        } catch (\Exception $e) {
            throw $e;
        }
    }
}