<?php
namespace App\Models\Traits;

trait Embed {
    public function scopeEmbed($query, $data) {
        try {
            if (is_null($data)) {
                return;
            }
            $dataEmbed = $query->with($data);
            return $dataEmbed;
        } catch (\Exception $e) {
            throw $e;
        }
    }
}