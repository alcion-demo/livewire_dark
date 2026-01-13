<?php

namespace App\Enums;

enum BookCategory: string
{
    case Technical = '技術書';
    case Novel = '小説';
    case Business = 'ビジネス';
    case Other = 'その他';

    /**
     * ラベルを取得するメソッド（表示用）
     */
    public function label(): string
    {
        return $this->value;
    }
}
