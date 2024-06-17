<?php
namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use App\Services\UserService;
use App\Utils\Pagination;
use Illuminate\Http\Request;

class UserController extends Controller {
    private $service;

    public function __construct() {
        $this->service = new UserService();
        parent::__construct();
    }

    public function index(Request $request) {
        try {
            $response = $this->service->getAll($request);
            $pagination = (isset($_GET['per_page'])) ? new Pagination($response) : null;
            $users = $response['data'];
            $message = $response['message'];
            $this->setPagination($pagination);
            $this->setDataCorrect($users, $message, 200);
        } catch (\Exception $e) {
            $this->setError($e->getMessage(), $e->getCode());
        }
        return $this->returnData();
    }

    public function delete($id) {
        try {
            $user = $this->service->delete($id);
            $this->setDataCorrect($user, 'Usuario eliminado!!!', 200);
        } catch (\Exception $e) {
            $this->setError($e->getMessage(), $e->getCode());
        }
        return $this->returnData();
    }

    public function activar($id) {
        try {
            $user = $this->service->activar($id);
            $this->setDataCorrect($user, 'Usuario activado!!!', 200);
        } catch (\Exception $e) {
            $this->setError($e->getMessage(), $e->getCode());
        }
        return $this->returnData();
    }

    // public function store(Request $request)
    // {
    //     try {
    //         $manual = $this->service->create($request);
    //         $this->setDataCorrect($manual, 'Manual creado correctamente', 201);
    //     } catch (\Exception $e) {
    //         $this->setError($e->getMessage(), $e->getCode());
    //     }
    //     return $this->returnData();
    // }

    // public function getById(Request $request, $id)
    // {
    //     try {
    //         $manual = $this->service->getId($request, $id);
    //         $this->setDataCorrect($manual, 'Manual encontrado', 200);
    //     } catch (\Exception $e) {
    //         $this->setError($e->getMessage(), $e->getCode());
    //     }
    //     return $this->returnData();
    // }

    // public function update(Request $request, $id)
    // {
    //     try {
    //         $manual = $this->service->update($request, $id);
    //         $this->setDataCorrect($manual, 'Manual actualizado correctamente', 200);
    //     } catch (\Exception $e) {
    //         $this->setError($e->getMessage(), $e->getCode());
    //     }
    //     return $this->returnData();
    // }

    // public function deleteStatusE($id)
    // {
    //     try {

    //         $this->service->deleteStatusE($id);
    //         $this->setDataCorrect(null, 'Manual  eliminado', 200);
    //     } catch (\Exception $e) {
    //         $this->setError($e->getMessage(), $e->getCode());
    //     }
    //     return $this->returnData();
    // }
}
