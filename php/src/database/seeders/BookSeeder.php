<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use App\Models\Book;
use Carbon\Carbon;
use App\Enums\BookCategory;

class BookSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // storage/app/public/books 内のファイルを取得
        $files = Storage::disk('public')->files('books');

        if (empty($files)) {
            $this->command->error('画像ファイルが books フォルダに見つかりませんでした。');
            return;
        }

        $categories = BookCategory::cases();

        for ($i = 1; $i <= 10; $i++) {
            $randomDate = Carbon::now()->subDays(rand(0, 150));
            
            //フォルダ内の画像からランダムに1つ選ぶ
            $imagePath = $files[array_rand($files)];

            // Enumからランダムに1つ選ぶ
            $randomCategory = $categories[array_rand($categories)];

            Book::create([
                'title' => 'テスト書籍 ' . $i,
                'category'    => $randomCategory,
                'price' => rand(500, 5000),
                'description' => "カテゴリー: {$randomCategory->value} のテストデータです。",
                'image' => $imagePath, // 例: 'books/filename.jpg'
                'created_at' => $randomDate,
                'updated_at' => $randomDate,
            ]);
        }
    }
}