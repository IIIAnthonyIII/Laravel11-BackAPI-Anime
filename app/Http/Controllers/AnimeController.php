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

    public function index() {
        try {
            $response = $this->service->getAll();
            $pagination = (request()->has('per_page')) ? new Pagination($response) : null;
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

    public function activar($id) {
        try {
            $anime = $this->service->activar($id);
            $this->setDataCorrect($anime, 'Anime  activado!!!', 200);
        } catch (\Exception $e) {
            $this->setError($e->getMessage(), $e->getCode());
        }
        return $this->returnData();
    }

    public function delete($id, Request $request) {
        try {
            $anime = $this->service->delete($id, $request);
            $message = $request->permanent ? 'Anime eliminado definitivamente!!!' : 'Anime eliminado!!!';
            $this->setDataCorrect($anime, $message, 200);
        } catch (\Exception $e) {
            $this->setError($e->getMessage(), $e->getCode());
        }
        return $this->returnData();
    }
}