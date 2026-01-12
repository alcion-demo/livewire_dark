<?php

namespace App\Services;

use App\Models\Book;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;

class BookService
{
    /**
     * 画像をアップロードし、保存したパスを返す
     * @param UploadedFile $file
     * @return filepath
     */
    public function uploadImage(UploadedFile $file): string
    {
        $fileName = date('Ymd_His') . '_' . $file->getClientOriginalName();
        return $file->storeAs('books', $fileName, 'public');
    }

    /**
     * 古い画像を削除する
     * @param string $path
     */
    public function deleteImage(?string $path): void
    {
        if ($path && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }
}