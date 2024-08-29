<?php
namespace App\Services;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthService {
    public function getId($id) {
        try {
            $usuario = new User();
            (auth()->user()) 
              ? $query = $usuario->where('id', $id)->where('status', '!=', 'E')
              : throw new \Exception('No autorizado', 400);
            if (sizeof($query->get()) == 0) throw new \Exception('No existe este usuario o se encuentra eliminado', 400);
            return $query->first();
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function changePassword($data) {
        DB::beginTransaction();
        try {
            $validator = Validator::make($data->all(), [
                'password' => 'required',
                'new_password' => 'required',
            ]);
            if ($validator->fails()) {
                $error =  $validator->errors()->first();
                throw new \Exception($error, 400);
            }
            $user = auth()->user();
            $user = User::find(auth()->user()->id);
            if (Hash::check($data->password, $user->password)) {
                $user->password = Hash::make($data->new_password);
                $user->save();
            } else {
                throw new \Exception('La contraseña actual es incorrecta', 400);
            }
            DB::commit();
            return $user;
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    public function update($data, $id) {
        DB::beginTransaction();
        try {
            $validator = Validator::make($data->all(), [
                'name' => 'required|string',
                'status' => 'required|string|max:1'
            ]);
            if ($validator->fails()) {
                $error = $validator->errors()->first();
                throw new \Exception($error);
            }
            $usuario = User::find($id);
            if ($usuario == null) throw new \Exception('No existe el usuario', 404);
            $usuario->name = $data->name;
            //verificar que email no exista en otro usuario
            $user = User::where('email', $data->email)->where('id', '!=', $id)->first();
            if ($user != null) throw new \Exception('El email ya existe', 400);
            $usuario->email = $data->email;
            $usuario->surname = $data->surname;
            $usuario->status = $data->status;
            $usuario->updated_at = Carbon::now();
            $usuario->update();
            DB::commit();
            return $usuario;
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }
}
