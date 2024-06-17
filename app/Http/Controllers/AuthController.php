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
  
  
class AuthController extends Controller
{
    private $service;
    public function __construct()
    {
        $this->service = new AuthService();
        parent::__construct();
    }

    public function login()
    {
        $credentials = request(['email', 'password']);
        if (!$token = auth()->attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        $data =  $this->respondWithToken($token)->original + ['user' => auth()->user()];
        return $data;
    }

    public function getById($id)
    {
        try {
            $usuario = $this->service->getId($id);
            $this->setDataCorrect($usuario, 'Usuario encontrado correctamente', 200);
        } catch (\Exception $e) {
            $this->setError($e->getMessage(), $e->getCode());
        }
        return $this->returnData();
    }

    public function changePassword(Request $request)
    {
        try {
            $User = $this->service->changePassword($request);
            $this->setPagination(null);
            $this->setDataCorrect($User, 'Contraseña cambiada', 200);
        } catch (\Exception $e) {
            $this->setError($e->getMessage(), $e->getCode());
        }
        return $this->returnData();
    }

    public function update(Request $request, $id)
    {
        try {
            $usuario = $this->service->update($request, $id);
            $this->setDataCorrect($usuario, 'Usuario actualizado correctamente', 200);
        } catch (\Exception $e) {
            $this->setError($e->getMessage(), $e->getCode());
        }
        return $this->returnData();
    }

    public function me()
    {
        return response()->json(auth()->user());
    }

    public function logout()
    {
        auth()->logout();
        return response()->json(['message' => 'Successfully logged out']);
    }

    public function checkToken(Request $request)
    {
        try {
            $token = JWTAuth::parseToken()->authenticate();
        } catch (TokenExpiredException $e) {
            $newToken = JWTAuth::parseToken()->refresh();
            $this->setDataCorrect($newToken, 'Token is Expired', 200);
        } catch (TokenInvalidException $e) {
            $this->setError('Token is Invalid', 401);
        } catch (JWTException $e) {
            $this->setError('Token is Missing', 401);
        } catch (Exception $e) {
            $this->setError('Something went wrong', 500);
        }
        return $this->returnData();
    }

    public function refresh()
    {
        return $this->respondWithToken(auth()->refresh());
    }

    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60
        ]);
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'surname' => 'required',
            'email' => 'required|string|email|max:100|unique:users',
            'password' => 'required|string|min:6',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        }
        $user = User::create(array_merge(
            $validator->validate(),
            ['password' => Hash::make($request->password)]
        ));
        return response()->json([
            'message' => '¡Usuario registrado exitosamente!',
            'user' => $user
        ], 201);
    }
}