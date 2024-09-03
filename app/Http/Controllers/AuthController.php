<?php
namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\AuthService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Facades\JWTAuth;
use Exception;

class AuthController extends Controller {
    private $service;
    
    public function __construct() {
        $this->service = new AuthService();
        parent::__construct();
    }

    public function register(Request $request) {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'surname' => 'required',
            'email' => 'required|string|email|max:100|unique:users',
            'password' => 'required|string|min:6',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        }
        $user = User::factory()->create(array_merge(
            $validator->validate(),
            ['password' => Hash::make($request->password)]
        ));
        return response()->json([
            'message' => 'Usuario registrado!!!',
            'user' => $user
        ], 201);
    }

    public function login() {
        $credentials = request(['email', 'password']);
        if (!$token = auth()->attempt($credentials)) {
            return response()->json(['error' => 'Credenciales incorrectas!'], 401);
        }
        $data =  $this->respondWithToken($token)->original + ['message' => 'Bienvenid@ ' . auth()->user()->name . '!'];
        return $data;
    }

    public function getById($id) {
        try {
            $usuario = $this->service->getId($id);
            $this->setDataCorrect($usuario, 'Usuario encontrado!!!', 200);
        } catch (\Exception $e) {
            $this->setError($e->getMessage(), $e->getCode());
        }
        return $this->returnData();
    }

    public function changePassword(Request $request) {
        try {
            $user = $this->service->changePassword($request);
            $this->setPagination(null);
            $this->setDataCorrect($user, 'Contraseña actualizada!!!', 200);
        } catch (\Exception $e) {
            $this->setError($e->getMessage(), $e->getCode());
        }
        return $this->returnData();
    }

    public function update(Request $request, $id) {
        try {
            $usuario = $this->service->update($request, $id);
            $this->setDataCorrect($usuario, 'El usuario actualizado!!!', 200);
        } catch (\Exception $e) {
            $this->setError($e->getMessage(), $e->getCode());
        }
        return $this->returnData();
    }

    public function me() {
        return response()->json(auth()->user());
    }

    public function logout() {
        auth()->logout();
        return response()->json(['message' => 'Usuario deslogueado!!!']);
    }

    public function checkToken(Request $request) {
        try {
            $token = JWTAuth::parseToken()->authenticate();
        } catch (TokenExpiredException $e) {
            $newToken = JWTAuth::parseToken()->refresh();
            $this->setDataCorrect($newToken, 'Token is Expired', 200);
        } catch (TokenInvalidException $e) {
            $this->setError('Token invalido!!!', 401);
        } catch (JWTException $e) {
            $this->setError('Token is Missing', 401);
        } catch (Exception $e) {
            $this->setError('Something went wrong', 500);
        }
        return $this->returnData();
    }

    public function refresh() {
        return $this->respondWithToken(auth()->refresh());
    }

    protected function respondWithToken($token) {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60
        ]);
    }
}