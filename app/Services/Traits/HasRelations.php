<?php
namespace App\Services\Traits;

trait HasRelations {
    public function addRelations($relation, $data, $model, $action) {
        try {
            $dataStatusError = [];
            foreach ($data as $name) {
                $element = $model::where('name', $name)->first();
                if ($element == null) {
                    $element = new $model();
                    $element->name = $name;
                    $element->save();
                }
                if ($element->status == 'E' || $element->status == 'I') $dataStatusError[] = $element->name;
                if ($action == 'create') $relation->attach($element->id, ['user_create' => auth()->user()->id]);
                if ($action == 'update') $relation->attach($element->id, ['user_create' => auth()->user()->id, 'user_modifies' => auth()->user()->id]);
            }
            if ($dataStatusError != null) throw new \Exception('No están activos los siguientes elementos: ' . implode(', ', $dataStatusError), 404);
        } catch (\Exception $e) {
            throw $e;
        }
    }
}