<?php
namespace App\Models\Traits;

trait Search {
    public function scopeSearch($query, $data) {
        try{
            if ((auth()->user())) {
                $dataSearch = $query->whereHas('tags', function ($query) use ($data) {
                    $query->where('name', 'like', '%' . $data . '%');
                })->orWhere('title', 'like', '%' . $data . '%');
            } else {
                $dataSearch = $query->whereHas('tags', function ($query) use ($data) {
                    $query->where('name', 'like', '%' . $data . '%')->where('status', 'A');
                })->orWhere('title', 'like', '%' . $data . '%')->where('status', 'A');
            }
            return $dataSearch;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function scopeSearchName($query, $data){
        try{
            (auth()->user())
              ? $dataSearch = $query->where('name', 'like', '%' . $data . '%')
              : $dataSearch = $query->where('name', 'like', '%' . $data . '%')->where('status', 'A');
            return $dataSearch;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function scopeSearchAll($query, $data, $name){
        try{
            $dataSearch = $query->where('title', 'like', '%' . $data . '%')
                ->orWhere('description', 'like', '%' . $data . '%')
                ->orWhereHas('tags', function ($query) use ($data) {
                    $query->where('name', 'like', '%' . $data . '%')->where('status', 'A');
                });
            if($name=='manual' || $name=='section'){
                $dataSearch = $query->orWhereHas('categories', function ($query) use ($data) {
                    $query->where('name', 'like', '%' . $data . '%')->where('status', 'A');
                });
            }
            return $dataSearch;
        } catch (\Exception $e) {
            throw $e;
        }
    }
}