<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BookResource extends JsonResource
{
    /**
     * =========1=========
     * Transformasikan resource menjadi array.
     * Pastikan untuk menyertakan semua atribut model Book.
     */
    public function toArray(Request $request): array
    {
        return [
            'title' => $this->title,
            'id' => $this->id,
            'author' => $this->author,
            'published_year' => $this->published_year,
            'is_available' => (bool) $this->is_available,
        ];
    }
}