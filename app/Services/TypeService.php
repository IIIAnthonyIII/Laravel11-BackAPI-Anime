<?php
namespace App\Services;
use App\Models\Type;
use App\Services\Traits\Relations;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class TypeService {
    use Relations;

    //../api/type?per_page=5&page=1
    public function getAll() {
        try {
            $query = $this->getDataQuery();
            if (request()->has('per_page')) {
                if (request()->get('per_page') <= 0) throw new \Exception('El parámetro per_page debe ser mayor a 0', 400);
                $type = $query->paginate(request()->get('per_page'))->toArray();
            } else {
                $type['data'] = $query->get()->toArray();
            }
            $type['message'] = (sizeof($type['data']) == 0) ? 'No hay tipos' : 'Tipos encontrados';
            return $type;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    //../api/type?status=A,I,E
    private function getDataQuery() {
        if (auth()->check()) {
            if (request()->has('status')) {
                $query = new Type();
                $statuses = array_map('trim', explode(',', request()->get('status')));
                foreach ($statuses as $status) {
                    $query =  $query->orWhere('status', $status);
                }
            } else {
                $query = Type::where('status', '!=', 'E');
            }
        } else {
            $query = Type::where('status', 'A');
        }
        if (request()->query->count() > 0) $query = $this->parametersGet($query);
        return $query;
    }

    private function parametersGet($query) {
        $type = new Type();
        $params = [
            'fields' => 'fields',
            'embed' => 'embed',
            'sort' => 'sort'
        ];
        foreach ($params as $param => $method) {
            if (request()->has($param)) $query = $type->{$method}($query, request()->get($param));
        }
        $queryParamsCopy = request()->query();
        $excludedParams = [
            'fields', 'embed', 'sort', 'search', 
            'per_page', 'page', 'status'
        ];
        foreach ($excludedParams as $param) {
            unset($queryParamsCopy[$param]);
        }
        if (count($queryParamsCopy) > 0) $query = $type->parameters($query, $queryParamsCopy);
        return $query;
    }

    public function getId($data, $id) {
        try {
            $type = new Type();
            $query = (auth()->user())
              ? $type->where('id', $id)->where('status', '!=', 'E')
              : $type->where('id', $id)->where('status', 'A');
            if (isset($data->fields)) $query = $type->fields($query, $data->fields);
            if (isset($_GET['embed'])) $query = $type->embed($query, $_GET['embed']);
            return $query->first();
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function create($data) {
        DB::beginTransaction();
        try {
            $validator = Validator::make($data->all(), [
                'name'  => 'required|string',
                'color' => 'required|string'
            ]);
            if ($validator->fails()) {
                $error = $validator->errors()->first();
                throw new \Exception($error, 400);
            }
            $typeName = Type::where('name', $data->name)->first();
            if ($typeName != null) throw new \Exception('El tipo ya existe, por favor ingrese uno nuevo', 409);
            $type = new Type();
            $type->name = $data->name;
            $type->color = $data->color;
            $type->user_create = auth()->user()->id;
            $type->save();
            DB::commit();
            return  $type;
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    public function update($data, $id) {
        DB::beginTransaction();
        try {
            $validator = Validator::make($data->all(), [
                'name'  => 'required|string',
                'color' => 'required|string'
            ]);
            if ($validator->fails()) {
                $error = $validator->errors()->first();
                throw new \Exception($error);
            }
            $type = Type::find($id);
            if ($type == null) throw new \Exception('No existe este tipo', 404);
            if ($type->name != $data->name) {
                $typeName = Type::where('name', $data->name)->first();
                if ($typeName != null) throw new \Exception('El tipo ya existe, por favor ingrese uno nuevo', 409);
            }
            $type->name = $data->name;
            $type->color = $data->color;
            $type->user_modifies = auth()->user()->id;
            $type->update();
            DB::commit();
            return $type;
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    public function activar($id) {
        DB::beginTransaction();
        try {
            $query = Type::find($id);
            if ($query == null) throw new \Exception('No existe este tipo', 404);
            $query->status = 'A';
            $query->update();
            DB::commit();
            return $query;
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    public function delete($id, $data) {
        try {
            $query = Type::find($id);
            if ($query == null) throw new \Exception('No existe este tipo', 404);
            $query->status = $data->permanent ? 'E' : 'I';
            $query->user_delete = auth()->user()->id;
            $query->date_delete = Carbon::now();
            $query->update();
            return $query;
        } catch (\Exception $e) {
            throw $e;
        }
    }
}