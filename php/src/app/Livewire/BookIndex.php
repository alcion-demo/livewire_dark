<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Book;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Storage;
use App\Services\BookService;

class BookIndex extends Component
{
    use WithFileUploads;
    use WithPagination;

    protected $paginationTheme = 'tailwind';

    public $liveModal = false;
    public $title;
    public $newImage;
    public $price;
    public $description;
    public $Id;
    public $oldImage;
    public $editWork = false;
    public $search = '';
    public $url;

    protected $rules = [
        'title' => 'required|string|min:3|max:255',
        'newImage' => 'nullable|image|max:1024',
        'price' => 'required|numeric|min:0',
        'description' => 'required|string|max:1000',
        'url' => 'nullable|url|max:2000',
    ];

    protected Book $bookModel;
    protected BookService $service;

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
        $this->reset(['title', 'newImage', 'price', 'description', 'url']);
        $this->liveModal = false;
    }

    /**
     * 画像登録画面
     */
    public function showBookModal(){
        $this->resetValidation();
        $this->reset(['title', 'newImage', 'price', 'description', 'oldImage', 'url']);
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

        $this->reset();
        $this->liveModal = false;

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
        $this->reset(['title', 'newImage', 'price', 'description', 'Id', 'oldImage', 'url']); // フォームフィールドと、編集関連のプロパティをリセット
        $this->editWork = false; // 編集モードを終了
    }

    /**
     * 一覧画面表示
     */
    public function render()
    {
        $booksQuery = Book::select('id', 'title', 'price', 'image', 'description', 'url', 'created_at');

        // 検索キーワードがある場合、WHERE句を追加
        if (!empty($this->search)) {
            $booksQuery->whereRaw('LOWER(title) LIKE ?', ['%' . strtolower($this->search) . '%']);
        }

        // ソートとページネーションを適用
        $books = $booksQuery->orderBy('id', 'DESC')->paginate(3);

        return view('livewire.book-index', [
            'books' => $books,
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

}