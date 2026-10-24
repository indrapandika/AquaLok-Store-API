<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->product_id,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'price' => (float) $this->price,
            'stock' => (int) $this->stock,
            'img_url' => $this->img
                ? asset('storage/' . $this->img)
                : asset($this->fallbackImagePath()),
            'category' => [
                'id' => $this->category_id,
                'name' => $this->category?->category,
            ],
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }

    private function fallbackImagePath(): string
    {
        $slug = (string) $this->slug;
        $image = match (true) {
            str_contains($slug, 'cupang') => 'cupang.jpg',
            str_contains($slug, 'arwana') => 'arwana.jpg',
            str_contains($slug, 'mas-koki') => 'mas koki.jpg',
            str_contains($slug, 'guppy') => 'guppy.jpg',
            str_contains($slug, 'betok') => 'king betok.jpg',
            str_contains($slug, 'toman') => 'toman.jpg',
            str_contains($slug, 'akari') => 'akari.jpg',
            str_contains($slug, 'cacing') => 'cacing.jpg',
            default => 'cupang.jpg',
        };

        return 'images/products/' . $image;
    }
}
