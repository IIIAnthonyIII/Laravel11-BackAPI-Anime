<?php
namespace App\Utils;
class Pagination {
    public $total;
    public $per_page;
    public $current_page;
    public $last_page;
    public $first_page_url;
    public $last_page_url;
    public $next_page_url;
    public $prev_page_url;
    public $path;
    public $from;
    public $to;
    
    public function __construct($response) {
        $this->total = $response['total'];
        $this->per_page = $response['per_page'];
        $this->current_page = $response['current_page'];
        $this->last_page = $response['last_page'];
        $this->first_page_url = $response['first_page_url'];
        $this->last_page_url = $response['last_page_url'];
        $this->next_page_url = $response['next_page_url'];
        $this->prev_page_url = $response['prev_page_url'];
        $this->path = $response['path'];
        $this->from = $response['from'];
        $this->to = $response['to'];
    }
}