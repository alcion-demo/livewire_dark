<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Book;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Storage;
use App\Services\BookService;
use App\Enums\BookCategory;
use Illuminate\Validation\Rules\Enum;

class BookIndex extends Component
{
    use WithFileUploads;
    use WithPagination;

    protected $paginationTheme = 'tailwind';

    public $liveModal = false;
    public $title;
    public $category;
    public $newImage;
    public $price;
    public $description;
    public $Id;
    public $oldImage;
    public $editWork = false;
    public $search = '';
    public $url;
    public $showOnlyFavorites = false;

    protected Book $bookModel;
    protected BookService $service;

    /**
     * バリデーション
     * Enum使用の為(cast処理あるのでfrom無)
     * @return array
     */
    protected function rules()
    {
        return [
            'title' => 'required|string|min:3|max:255',
            'category' => ['required', new Enum(BookCategory::class)],
            'newImage' => 'nullable|image|max:1024',
            'price' => 'required|numeric|min:0',
            'description' => 'required|string|max:1000',
            'url' => 'nullable|url|max:2000',
        ];
    }

    /**
     * Livewire の boot メソッドで一括注入
     * construct
     */
    public function boot(Book $book, BookService $service)
    {
        $this->bookModel = $book;
        $this->service = $service;
    }

    /**
     * 登録画面
     * @param App\Services\BookService
     */
    public function bookPost(BookService $service){
        $this->validate();

        $imagePath = null;
        $imagePath = $this->newImage ? $this->service->uploadImage($this->newImage) : null;

        $this->bookModel->createWithData($this->all(), $imagePath);
        $this->reset(['title', 'category', 'newImage', 'price', 'description', 'url']);
        $this->liveModal = false;

        session()->flash('message', '書籍が正常に登録されました！');
    }

    /**
     * 画像登録画面
     */
    public function showBookModal(){
        $this->resetValidation();
        $this->reset(['title', 'category', 'newImage', 'price', 'description', 'url', 'Id', 'oldImage']);
        $this->editWork = false;
        $this->liveModal = true;
    }

    /**
     * 編集画面表示
     * @param $id
     * @return view
     */
    public function showEditBookModal($id){
        $book = $this->bookModel->findOrFail($id);

        // これだけで OK！
        $this->fill($book->toEditArray());

        $this->editWork = true;
        $this->liveModal = true;
    }

    /**
     * 編集画面更新
     * @param $id
     * @return view
     */
    public function updateBook($Id)
    {
        $this->validate();
        $book = $this->bookModel->findOrFail($Id);

        $imagePath = $this->newImage ? $this->service->uploadImage($this->newImage) : null;
        if ($imagePath) {
            $this->service->deleteImage($book->image); // 古いのは消す
        }

        // Model のメソッドを呼ぶだけ！
        $book->updateWithData($this->all(), $imagePath);

        $this->reset(['title', 'category','newImage', 'price', 'description', 'oldImage', 'url']);
        $this->liveModal = false;

        session()->flash('message', '書籍が正常に更新されました！');

    }

    /**
     * 実際に書籍を削除するメソッド
     * @param $id
     * @return view
     */
    public function deleteBook($id)
    {
        $book = Book::findOrFail($id);
        if ($book->image) {
            Storage::disk('public')->delete($book->image);
        }

        $book->delete();
        session()->flash('message', '書籍が正常に削除されました！');

    }

    /**
     * 編集画面終了
     */
    public function closeBookModal()
    {
        $this->liveModal = false;
        $this->resetValidation();
        $this->reset(['title','category', 'newImage', 'price', 'description', 'Id', 'oldImage', 'url']); // フォームフィールドと、編集関連のプロパティをリセット
        $this->editWork = false; // 編集モードを終了
    }

    /**
     * 一覧画面表示
     */
    public function render()
    {
        $booksQuery = Book::with('favoritedBy')
        ->select('id', 'category', 'title', 'price', 'image', 'description', 'url', 'created_at');

        // 検索キーワードがある場合、WHERE句を追加
        if (!empty($this->search)) {
            $booksQuery->whereRaw('LOWER(title) LIKE ?', ['%' . strtolower($this->search) . '%']);
        }

        // お気に入り件数を取得
            $favoriteCount = Book::whereHas('favoritedBy', function ($q) {
                $q->where('user_id', auth()->id());
            })->count();

        // ★ お気に入りフィルターがONの場合、お気に入りが付加されている本だけに絞り込む
        if ($this->showOnlyFavorites) {
            $booksQuery->whereHas('favoritedBy', function ($q) {
                $q->where('user_id', auth()->id());
            });
        }

        // ソートとページネーションを適用
        $books = $booksQuery->orderBy('id', 'DESC')->paginate(3);

        return view('livewire.book-index', [
            'books' => $books,
            'favoriteCount' => $favoriteCount,
        ]);
    }

    /**
     * 検索窓初期化
     */
    public function clearSearch()
    {
        $this->reset('search');
        $this->resetPage();
    }

    public function toggleFavorite($bookId)
    {
        // ログインユーザーのお気に入りを切り替え（attach/detach を自動で行う toggle メソッドが便利です）
        auth()->user()->favoriteBooks()->toggle($bookId);
    }

    /**
     * フィルターの切り替え
     */
    public function toggleFavoriteFilter()
    {
        $this->showOnlyFavorites = !$this->showOnlyFavorites;
        $this->resetPage(); // ページネーションを1ページ目に戻す
    }

    /**
     * ログインユーザーのお気に入りをすべて解除する
     */
    public function clearAllFavorites()
    {
        // ログイン中のユーザーのお気に入り本とのリレーションを空にする
        auth()->user()->favoriteBooks()->detach();

        // 画面を更新（お気に入りフィルター中なら一覧が空になる）
        $this->resetPage(); 
        session()->flash('message', 'すべてのお気に入りを解除しました。');
    }

}