<?php
namespace App\Http\Controllers;
use App\Services\AnimeService;
use App\Utils\Pagination;
use Illuminate\Http\Request;

class AnimeController extends Controller {
    private $service;

    public function __construct() {
        $this->service = new AnimeService();
        parent::__construct();
    }

    public function index(Request $request) {
        try {
            $response = $this->service->getAll($request);
            $pagination = (isset($_GET['per_page'])) ? new Pagination($response) : null;
            $anime = $response['data'];
            $message = $response['message'];
            $this->setPagination($pagination);
            $this->setDataCorrect($anime, $message, 200);
        } catch (\Exception $e) {
            $this->setError($e->getMessage(), $e->getCode());
        }
        return $this->returnData();
    }

    public function store(Request $request)
    {
        try {
            $anime = $this->service->create($request);
            $this->setDataCorrect($anime, 'Anime creado!!!', 201);
        } catch (\Exception $e) {
            $this->setError($e->getMessage(), $e->getCode());
        }
        return $this->returnData();
    }

    public function getById(Request $request, $id) {
        try {
            $anime = $this->service->getId($request, $id);
            $this->setDataCorrect($anime, 'Anime encontrado!!!', 200);
        } catch (\Exception $e) {
            $this->setError($e->getMessage(), $e->getCode());
        }
        return $this->returnData();
    }

    public function update(Request $request, $id) {
        try {
            $anime = $this->service->update($request, $id);
            $this->setDataCorrect($anime, 'Anime actualizado!!!', 200);
        } catch (\Exception $e) {
            $this->setError($e->getMessage(), $e->getCode());
        }
        return $this->returnData();
    }

    public function delete($id) {
        try {
            $anime = $this->service->delete($id);
            $this->setDataCorrect($anime, 'Anime eliminado!!!', 200);
        } catch (\Exception $e) {
            $this->setError($e->getMessage(), $e->getCode());
        }
        return $this->returnData();
    }

    public function activar($id) {
        try {
            $anime = $this->service->activar($id);
            $this->setDataCorrect($anime, 'Anime  activado!!!', 200);
        } catch (\Exception $e) {
            $this->setError($e->getMessage(), $e->getCode());
        }
        return $this->returnData();
    }

    public function deleteStatusE($id) {
        try {
            $this->service->deleteStatusE($id);
            $this->setDataCorrect(null, 'Anime  eliminado definitivamente!!!', 200);
        } catch (\Exception $e) {
            $this->setError($e->getMessage(), $e->getCode());
        }
        return $this->returnData();
    }
}