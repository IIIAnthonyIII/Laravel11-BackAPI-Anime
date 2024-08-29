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
}
