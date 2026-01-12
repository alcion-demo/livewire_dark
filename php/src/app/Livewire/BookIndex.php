<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Book;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Storage;

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

    /**
     * 登録画面
     */
    public function bookPost(){
        $this->validate();

        $imagePath = null;
        if ($this->newImage) {
            $originalFileName = $this->newImage->getClientOriginalName();
            $fileNameToStore = date('Ymd_His') . '_' . $originalFileName;

            $this->newImage->storeAs('books', $fileNameToStore, 'public');

            $imagePath = 'books/' . $fileNameToStore;
        }

        Book::create([
            'title' => $this->title,
            'image' => $imagePath,
            'price' => $this->price,
            'description' => $this->description,
            'url' => $this->url,
        ]);

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
        $book = Book::findOrFail($id);
        $this->Id = $book->id;
        $this->title = $book->title;
        $this->oldImage = $book->image;
        $this->price = $book->price;
        $this->description = $book->description;
        $this->editWork = true;
        $this->liveModal = true;
        $this->url = $book->url;
    }

    /**
     * 編集画面更新
     * @param $id
     * @return view
     */
    public function updateBook($Id)
    {
        $this->validate();
        $book = Book::findOrFail($Id);

        // 更新用データの準備（画像以外の項目）
        $updateData = [
            'title'       => $this->title,
            'price'       => $this->price,
            'description' => $this->description,
            'url'         => $this->url,
        ];

        // 新しい画像がアップロードされている場合のみ処理
        if ($this->newImage) {
            // 古い画像が存在すれば削除（ストレージの肥大化防止）
            if ($book->image && Storage::disk('public')->exists($book->image)) {
                Storage::disk('public')->delete($book->image);
            }

            $originalFileName = $this->newImage->getClientOriginalName();
            $fileNameToStore = date('Ymd_His') . '_' . $originalFileName;
            $imagePath = $this->newImage->storeAs('books', $fileNameToStore, 'public');

            // 更新用データに新しいパスをセット
            $updateData['image'] = $imagePath;
        }

        $book->update($updateData);

        $this->reset(['title', 'newImage', 'price', 'description', 'Id', 'oldImage', 'url']);
        $this->resetValidation();
        $this->liveModal = false;
        $this->editWork = false;

        session()->flash('message', '書籍情報を更新しました！');

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