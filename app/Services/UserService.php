<?php

namespace App\Services;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class UserService{
    private function getDataQuery($data) {
        // if ((auth()->user())) {
            // if (isset($_GET['status'])) {
            //     $query = new User();
            //     $porciones = array_map('trim', explode(',', $_GET['status']));
            //     foreach ($porciones as $status) {
            //         $query =  $query->orWhere("status", $status);
            //     }
            // } else {
                $query = User::where('status', '!=', 'E');
            // }
        // } else {
        //     $query =  User::where(function ($query) {
        //         $query->where('status', '=', 'A');
        //     });
        // }
        // if (isset($_GET) && count($_GET) > 0) {
        //     $query = $this->parametersGet($query, $data);
        // }
        // if (sizeof($query->get()) == 0) {
        //     throw new \Exception('No hay usuarios registrados', 400);
        // }
        return $query;
    }

    public function getAll($data) {
        try {
            $query = $this->getDataQuery($data);
            if (isset($_GET['per_page'])) {
                ($data->per_page <= 0) ? throw new \Exception('El parámetro per_page debe ser mayor a 0', 400) :
                $user = $query->paginate($data->per_page)->toArray();
            } else {
                $user['data'] = $query->get()->toArray();
            }
            $user['message'] = (sizeof($user['data']) == 0) ? 'No hay usuarios' : 'Usuarios encontrados';
            return $user;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    // private function parametersGet($query, $data)
    // {
    //     $category = new Category();
    //     $categoryQuery = $query;

    //     if (isset($data->fields)) {
    //         $categoryQuery = $category->fields($query, $data->fields);
    //     }

    //     if (isset($_GET['embed'])) {
    //         $categoryQuery = $category->embed($query, $_GET['embed']);
    //     }

    //     if (isset($data->search)) {
    //         $categoryQuery = $category->searchName($query, $data->search);
    //     }

    //     if (isset($data->sort)) {
    //         $categoryQuery = $category->sort($query, $data->sort);
    //     }

    //     unset($_GET['fields'], $_GET['embed'], $_GET['sort'], $_GET['per_page'], $_GET['page'], $_GET['status'], $_GET['search']);
    //     if (isset($_GET) && count($_GET) > 0) {
    //         $categoryQuery = $category->parameters($query, $_GET);
    //     }

    //     return $categoryQuery;
    // }

    public function delete($id) {
        try {
            $user = User::find($id);
            if ($user == null) {
                throw new \Exception('No existe este usuario', 404);
            }
            $user->status = 'I';
            // $user->user_delete = auth()->user()->id;
            // $user->date_delete = Carbon::now();
            $user->update();
            return $user;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function activar($id) {
        DB::beginTransaction();
        try {
            $query = User::find($id);
            if ($query == null) {
                throw new \Exception('No existe esta categoria', 404);
            }
            $query->status = 'A';
            $query->update();
            DB::commit();
            return $query;
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    public function deleteStatusE($id) {
        try {
            $query = User::find($id);
            if ($query == null) {
                throw new \Exception('No existe este usuario', 404);
            }
            $query->status = 'E';
            // $query->date_delete = Carbon::now();
            // $query->user_delete = auth()->user()->id;
            $query->update();
            return $query;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    // public function getId($data, $id)
    // {
    //     try {
    //         $category = new Category();
    //         if ((auth()->user())) {
    //             $query = $category->where('id', $id)->where('status', '!=', 'E');
    //         } else {
    //             $query = $category->where('id', $id)->where('status', 'A');
    //         }
    //         if (isset($data->fields)) {
    //             $query = $category->fields($query, $data->fields);
    //         }
    //         if (isset($_GET['embed'])) {
    //             $query = $category->embed($query, $_GET['embed']);
    //         }
    //         if (sizeof($query->get()) == 0) {
    //             throw new \Exception('No existe esta categoría o no está activa', 400);
    //         }
    //         return $query->first();
    //     } catch (\Exception $e) {
    //         throw $e;
    //     }
    // }

    // public function update($data, $id)
    // {
    //     DB::beginTransaction();
    //     try {
    //         $validator = Validator::make($data->all(), [
    //             'name' => 'required|string|max:255',
    //             'status' => 'required|string|max:1'
    //         ]);
    //         if ($validator->fails()) {
    //             $error =  $validator->errors()->first();
    //             throw new \Exception($error);
    //         }
    //         $category = Category::find($id);
    //         if ($category == null) {
    //             throw new \Exception('No existe esta categoria', 404);
    //         }
    //         $category->name = $data->name;
    //         $category->status = $data->status;
    //         $category->user_modifies = auth()->user()->id;
    //         $category->update();
    //         DB::commit();
    //         return $category;
    //     } catch (\Exception $e) {
    //         DB::rollback();
    //         throw $e;
    //     }
    // }
}