<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use App\Enums\BookCategory;

class Book extends Model
{
    protected $fillable = ['title', 'category', 'image', 'price', 'description', 'url'];

    protected $casts = [
        'category' => BookCategory::class,
    ];

    /**
     * 検索スコープ (renderをスッキリさせるため)
     * @param $query
     * @param $term 
     * @return Builder
     */
    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        return $query->when($term, function ($query, $term) {
            return $query->whereRaw('LOWER(title) LIKE ?', ['%' . strtolower($term) . '%']);
        });
    }

    /**
     * 【登録専用】
     * @param $data
     * @param $imagePath
     * @return App\Models\Book
     */
    public function createWithData(array $data, ?string $imagePath = null)
    {
        return $this->create([
            'title'       => $data['title'],
            'category'    => $data['category'],
            'image'       => $imagePath,
            'price'       => $data['price'],
            'description' => $data['description'],
            'url'         => $data['url'] ?? null,
        ]);
    }

    /**
     * 【更新専用】
     * @param $data
     * @param $imagePath
     * @return App\Models\Book
     */
    public function updateWithData(array $data, ?string $imagePath = null)
    {
        if ($imagePath) {
            // 先に配列の中身を書き換えておく
            $data['image'] = $imagePath;
        }
        // 配列を渡せば、内部で fill して save してくれる
        return $this->update($data);
    }

    public function toEditArray(): array
    {
        return [
            'Id'          => $this->id,
            'title'       => $this->title,
            'category'    => $this->category,
            'oldImage'    => $this->image,
            'price'       => $this->price,
            'description' => $this->description,
            'url'         => $this->url,
        ];
    }

}