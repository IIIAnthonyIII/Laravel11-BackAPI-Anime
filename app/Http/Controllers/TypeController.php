<?php

namespace App\Http\Controllers;

use App\Services\TypeService;
use App\Utils\Pagination;
use Illuminate\Http\Request;

class TypeController extends Controller {
    private $service;

    public function __construct() {
        $this->service = new TypeService();
        parent::__construct();
    }

    public function index() {
        try {
            $response = $this->service->getAll();
            $pagination = (request()->has('per_page')) ? new Pagination($response) : null;
            $type = $response['data'];
            $message = $response['message'];
            $this->setPagination($pagination);
            $this->setDataCorrect($type, $message, 200);
        } catch (\Exception $e) {
            $this->setError($e->getMessage(), $e->getCode());
        }
        return $this->returnData();
    }

    public function store(Request $request)
    {
        try {
            $type = $this->service->create($request);
            $this->setDataCorrect($type, 'Tipo creado!!!', 201);
        } catch (\Exception $e) {
            $this->setError($e->getMessage(), $e->getCode());
        }
        return $this->returnData();
    }

    public function getById(Request $request, $id) {
        try {
            $type = $this->service->getId($request, $id);
            $this->setDataCorrect($type, 'Tipo encontrado!!!', 200);
        } catch (\Exception $e) {
            $this->setError($e->getMessage(), $e->getCode());
        }
        return $this->returnData();
    }

    public function update(Request $request, $id) {
        try {
            $type = $this->service->update($request, $id);
            $this->setDataCorrect($type, 'Tipo actualizado!!!', 200);
        } catch (\Exception $e) {
            $this->setError($e->getMessage(), $e->getCode());
        }
        return $this->returnData();
    }

    public function activar($id) {
        try {
            $type = $this->service->activar($id);
            $this->setDataCorrect($type, 'Tipo  activado!!!', 200);
        } catch (\Exception $e) {
            $this->setError($e->getMessage(), $e->getCode());
        }
        return $this->returnData();
    }

    public function delete($id, Request $request) {
        try {
            $type = $this->service->delete($id, $request);
            $message = $request->permanent ? 'Tipo eliminado definitivamente!!!' : 'Tipo eliminado!!!';
            $this->setDataCorrect($type, $message, 200);
        } catch (\Exception $e) {
            $this->setError($e->getMessage(), $e->getCode());
        }
        return $this->returnData();
    }
}
