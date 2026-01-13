<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use App\Models\User;
use App\Models\Book;
use App\Livewire\BookIndex;
use Livewire\Livewire;

class BookFavoriteTest extends TestCase
{
    use RefreshDatabase;

    // テストコードのイメージ（Livewireを使っている場合）
    public function test_user_can_favorite_a_book()
    {
        $user = User::factory()->create();
        $book = Book::factory()->create();

        Livewire::actingAs($user)
            ->test(BookIndex::class)
            ->call('toggleFavorite', $book->id);

        $this->assertTrue($user->favoriteBooks->contains($book->id));
    }

    public function test_title_is_required()
    {
        // ログインした状態で
        $user = \App\Models\User::factory()->create();

        \Livewire\Livewire::actingAs($user)
            ->test(BookIndex::class) // 対象のコンポーネント
            ->set('title', '')                 // タイトルを「空」にする
            ->call('bookPost')                     // 保存メソッドを実行（実際のメソッド名に合わせてください）
            ->assertHasErrors(['title' => 'required']); // title属性にrequiredエラーがあるか確認
    }

    public function test_price_is_required()
    {
        // ログインした状態で
        $user = \App\Models\User::factory()->create();

        \Livewire\Livewire::actingAs($user)
            ->test(BookIndex::class) // 対象のコンポーネント
            ->set('price', '')                 // タイトルを「空」にする
            ->call('bookPost')                     // 保存メソッドを実行（実際のメソッド名に合わせてください）
            ->assertHasErrors(['price' => 'required']); // title属性にrequiredエラーがあるか確認
    }

    public function test_description_is_required()
    {
        // ログインした状態で
        $user = \App\Models\User::factory()->create();

        \Livewire\Livewire::actingAs($user)
            ->test(BookIndex::class) // 対象のコンポーネント
            ->set('description', '')                 // タイトルを「空」にする
            ->call('bookPost')                     // 保存メソッドを実行（実際のメソッド名に合わせてください）
            ->assertHasErrors(['description' => 'required']); // title属性にrequiredエラーがあるか確認
    }

    public function test_price_must_be_a_number()
    {
        $user = \App\Models\User::factory()->create();

        \Livewire\Livewire::actingAs($user)
            ->test(BookIndex::class)
            ->set('price', 'あいうえお') // ★ 数字以外の文字列をセット
            ->call('bookPost')           // メソッド名は実際の名称（addBook等）に
            ->assertHasErrors(['price' => 'numeric']); // ★ numericエラーが出るかチェック
    }

    public function test_user_can_unfavorite_a_book()
    {
        $user = User::factory()->create();
        $book = Book::factory()->create();

        // 最初にお気に入り済みの状態を作る
        $user->favoriteBooks()->attach($book->id);

        Livewire::actingAs($user)
            ->test(BookIndex::class)
            ->call('toggleFavorite', $book->id); // もう一度叩く

        // 解除されていることを確認
        $this->assertFalse($user->favoriteBooks->contains($book->id));
    }

    public function test_can_update_book_info()
    {
        $user = \App\Models\User::factory()->create();
        $book = \App\Models\Book::factory()->create([
            'title' => '古いタイトル',
            'price' => 1000,
        ]);

        \Livewire\Livewire::actingAs($user)
            ->test(BookIndex::class)
            // 1. 編集画面表示メソッド名は showEditBookModal
            ->call('showEditBookModal', $book->id) 
            ->set('title', '新しいタイトル')
            ->set('price', 2000)
            // 2. 更新メソッド updateBook に $book->id を渡す
            ->call('updateBook', $book->id) 
            ->assertHasNoErrors();

        // 3. DBの中身を確認
        $this->assertDatabaseHas('books', [
            'id'    => $book->id,
            'title' => '新しいタイトル',
            'price' => 2000,
        ]);
    }

    public function test_can_delete_book()
    {
        $user = \App\Models\User::factory()->create();
        $book = \App\Models\Book::factory()->create();

        \Livewire\Livewire::actingAs($user)
            ->test(BookIndex::class)
            ->call('deleteBook', $book->id); // 削除メソッドを実行

        // データベースから消えていることを確認
        $this->assertDatabaseMissing('books', [
            'id' => $book->id,
        ]);
    }
}
