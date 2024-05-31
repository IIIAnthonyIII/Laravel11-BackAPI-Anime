<?php

namespace App\Utils;

class ApiResponse
{
    public $statusCode;
    public $message;
    public $data;
    public $pagination;

    public function __construct(
        $data = null,
        $pagination = null,
        $statusCode = 200,
        $message = 'Se ha procesado correctamente'
    ) {
        $this->data = $data;
        $this->pagination = $pagination;
        $this->message = $message;
        $this->statusCode = $statusCode;
    }
    public function setData($data)
    {
        $this->data = $data;
    }

    public function setStatusCode($statusCode)
    {
        $this->statusCode = $statusCode;
    }

    public function setMessage($message)
    {
        $this->message = $message;
    }

    public function setError($message, $statusCode)
    {
        $this->message = $message;
        $this->statusCode = $statusCode;
    }

    public function setPagination($pagination){
        $this->pagination = $pagination;
    }

    public function setDataCorrect($data, $message, $status)
    {
        $this->data = $data;
        $this->message = $message;
        $this->statusCode = $status;
    }

    public function returnData()
    {
        return response()->json(
            [
                'data' => $this->data,
                'pagination' => $this->pagination,
                'message' => $this->message,
                'statusCode' => $this->statusCode,
            ],
            $this->statusCode);
    }
}