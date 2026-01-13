@php
use App\Enums\BookCategory;
@endphp

<div class="max-w-6xl mx-auto py-8 px-4">
    {{-- ★修正：メッセージ表示エリアを最上部へ移動し、Livewireの更新に強くする --}}
    @if (session()->has('message'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)"
            class="mb-4 bg-green-50 dark:bg-green-900/30 border-l-4 border-green-400 p-4 shadow-sm">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-green-700 dark:text-green-400">{{ session('message') }}</p>
                </div>
            </div>
        </div>
    @endif

    {{-- 上部：検索と登録 --}}
    <div class="mb-6 flex justify-between items-center bg-white dark:bg-gray-800 p-4 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700">
        <div class="flex items-center flex-1">
            {{-- 検索窓 --}}
            <div class="relative w-1/3">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
                <input type="text" wire:model.live="search" placeholder="タイトルで検索..."
                    class="block w-full pl-10 border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 transition placeholder-gray-400">
            </div>

            {{-- お気に入りフィルターエリア --}}
            <div class="ml-4 flex items-center">
                <button wire:click="toggleFavoriteFilter" 
                    class="inline-flex items-center px-4 py-2 rounded-lg text-sm font-bold transition-all shadow-sm border
                    {{ $showOnlyFavorites 
                        ? 'bg-red-500 text-white border-red-500 hover:bg-red-600' 
                        : 'bg-white dark:bg-gray-700 text-gray-600 dark:text-gray-300 border-gray-200 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-600' 
                    }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2 {{ $showOnlyFavorites ? 'fill-current' : 'fill-none' }}" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                    </svg>
                    {{ $showOnlyFavorites ? '全表示に戻す' : 'お気に入り件数' }}
                    @if($favoriteCount > 0)
                        <span class="ml-4 px-2 py-0.5 text-xs rounded-full {{ $showOnlyFavorites ? 'bg-white/20 text-white' : 'bg-gray-600 text-white' }}">
                            {{ $favoriteCount }}
                        </span>
                    @endif
                </button>

                @if($favoriteCount > 0)
                    <button 
                        wire:click="clearAllFavorites" 
                        wire:confirm="すべてのお気に入りを解除してもよろしいですか？"
                        class="ml-6 text-xs text-red-400 hover:text-red-300 underline whitespace-nowrap"
                    >
                        全解除
                    </button>
                @endif
            </div>
        </div>

        <x-button style="background-color: #6366f1;" class="text-white shadow-md transform hover:-translate-y-0.5 transition border-none" wire:click="showBookModal">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 4v16m8-8H4"></path></svg>
            新規登録
        </x-button>
    </div>

    {{-- メインコンテンツ：テーブル --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-xl overflow-hidden border border-gray-100 dark:border-gray-700">
        {{-- ★元々ここにあった session メッセージ表示を上に移動しました --}}

        <table class="w-full text-left border-collapse table-auto">
            <thead class="bg-gray-50 dark:bg-gray-700/50 border-b border-gray-100 dark:border-gray-700">
                <tr>
                    <th class="w-16 p-4 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">ID</th>
                    <th class="p-4 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">書籍情報</th>
                    <th class="p-4 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">カテゴリー</th>
                    <th class="p-4 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider text-right">価格</th>
                    <th class="p-4 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">説明</th>
                    <th class="p-4 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider text-center">操作</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                @forelse($books as $book)
                <tr wire:key="book-{{ $book->id }}" class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
                    <td class="p-4 text-sm text-gray-500 dark:text-gray-400">#{{ $book->id }}</td>
                    <td class="pl-4 pr-2 py-4">
                        <div class="flex items-center space-x-4">
                            <div class="h-16 w-12 flex-shrink-0">
                                @if($book->image)
                                    <img class="h-16 w-12 object-cover rounded shadow-sm border border-gray-100 dark:border-gray-600" 
                                        src="{{ Storage::url($book->image) }}" alt="{{ $book->title }}">
                                @else
                                    <div class="h-16 w-12 bg-gray-100 dark:bg-gray-700 rounded flex items-center justify-center border border-gray-200 dark:border-gray-600">
                                        <span class="text-[10px] text-gray-400 font-bold text-center leading-tight">No<br>Image</span>
                                    </div>
                                @endif
                            </div>
                            <div class="flex-shrink">
                                <div class="text-sm font-bold text-gray-900 dark:text-gray-200">
                                    @if($book->url)
                                        <a href="{{ $book->url }}" target="_blank" rel="noopener noreferrer" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-300 hover:underline inline-flex items-center whitespace-nowrap">
                                            <span>{{ $book->title }}</span>
                                            <svg class="w-3 h-3 ml-1 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                                        </a>
                                    @else
                                        <span class="text-gray-500 dark:text-gray-400 block whitespace-nowrap">{{ $book->title }}</span>
                                        <span class="text-[10px] font-normal text-gray-400 dark:text-gray-500 italic">URLなし</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </td>
                    <td class="pl-2 pr-4 py-4 text-sm font-semibold text-gray-700 dark:text-gray-300">{{ $book->category->value ?? '未設定' }}</td>
                    <td class="p-4 text-sm font-semibold text-gray-700 dark:text-gray-300 text-right">¥{{ number_format($book->price) }}</td>
                    <td class="p-4">
                        <p class="text-sm text-gray-600 dark:text-gray-400 line-clamp-2 w-64">{!! nl2br(e($book->description)) !!}</p>
                    </td>
                    <td class="p-4 text-center">
                        <div class="flex justify-center items-center space-x-3">
                            <button wire:click="toggleFavorite({{ $book->id }})" 
                                class="p-2 rounded-full transition-all transform active:scale-125 focus:outline-none {{ $book->favoritedBy->contains(auth()->id()) ? 'bg-red-50 dark:bg-red-900/20 text-red-500' : 'bg-gray-100 dark:bg-gray-700 text-gray-300 dark:text-gray-500 hover:bg-gray-200 dark:hover:bg-gray-600' }}">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 fill-current" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd" />
                                </svg>
                            </button>
                            <button wire:click="showEditBookModal({{ $book->id }})" class="px-4 py-1.5 bg-emerald-500 text-white rounded-lg hover:bg-emerald-600 shadow-sm text-sm font-bold transition-colors">編集</button>
                            <button wire:click="deleteBook({{ $book->id }})" wire:confirm="本当に削除しますか？" class="px-4 py-1.5 bg-rose-400 text-white rounded-lg hover:bg-rose-500 shadow-sm text-sm font-bold transition-colors">削除</button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="p-12 text-center">
                        <div class="flex flex-col items-center justify-center text-gray-500 dark:text-gray-400">
                            <svg class="w-12 h-12 mb-4 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 9.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            @if($showOnlyFavorites)
                                <p class="text-lg font-medium">お気に入りはまだありません</p>
                                <p class="text-sm">気になる本のハートアイコンを押して追加してみましょう！</p>
                            @elseif($search)
                                <p class="text-lg font-medium">「{{ $search }}」に一致する本は見つかりませんでした</p>
                            @else
                                <p class="text-lg font-medium">本が登録されていません</p>
                            @endif
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

        {{-- ページネーション --}}
        <div class="p-4 bg-gray-50 dark:bg-gray-700/50 border-t border-gray-100 dark:border-gray-700 pagination-dark-fix">
            {{ $books->onEachSide(1)->links() }}
        </div>
    </div>

    {{-- モーダル --}}
    <x-dialog-modal wire:model="liveModal">
        <x-slot name="title">
            <h2 class="text-xl font-bold {{ $editWork ? 'text-emerald-500' : 'text-indigo-500 dark:text-indigo-400' }}">
                {{ $editWork ? '書籍の編集' : '新しい書籍の登録' }}
            </h2>
        </x-slot>
        <x-slot name="content">
            <div class="space-y-4">
                <div>
                    <x-label for="title" value="タイトル" class="font-bold mb-1 dark:text-gray-300" />
                    <x-input type="text" id="title" wire:model.lazy="title" class="w-full" />
                    @error('title') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
                </div>
                <div>
                    <x-label for="category" value="カテゴリー" class="font-bold mb-1 dark:text-gray-300" />
                    <select id="category" wire:model="category" class="w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg">
                        <option value="">選択してください</option>
                        @foreach(BookCategory::cases() as $cat)
                            <option value="{{ $cat->value }}">{{ $cat->value }}</option>
                        @endforeach
                    </select>
                    @error('category') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
                </div>
                <div class="flex gap-4 items-end">
                    <div class="w-1/3">
                        @if ($newImage)
                            <img src="{{ $newImage->temporaryUrl() }}" class="w-32 h-40 object-cover rounded-lg ring-4 ring-indigo-100">
                        @elseif ($oldImage)
                            <img src="{{ Storage::url($oldImage) }}" class="w-32 h-40 object-cover rounded-lg">
                        @else
                            <div class="w-full h-40 bg-gray-100 dark:bg-gray-700 rounded-lg flex items-center justify-center text-gray-400 border border-dashed dark:border-gray-600 text-xs">No Image</div>
                        @endif
                    </div>
                    <div class="flex-1">
                        <x-label for="image" value="書籍画像" class="font-bold mb-1 dark:text-gray-300" />
                        <input type="file" id="image" wire:model="newImage" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100" />
                    </div>
                </div>
                <div>
                    <x-label for="price" value="価格" class="font-bold mb-1 dark:text-gray-300" />
                    <x-input type="text" id="price" wire:model.lazy="price" class="w-full" />
                    @error('price') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
                </div>
                <div>
                    <x-label for="description" value="説明文" class="font-bold mb-1 dark:text-gray-300" />
                    <textarea id="description" rows="4" wire:model="description" class="w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg"></textarea>
                    @error('description') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
                </div>
            </div>
        </x-slot>
        <x-slot name="footer">
            <x-button class="!bg-indigo-600 mr-3" wire:click="{{ $editWork ? 'updateBook('.$Id.')' : 'bookPost' }}">
                {{ $editWork ? '更新する' : '登録する' }}
            </x-button>
            <x-secondary-button wire:click="closeBookModal">キャンセル</x-secondary-button>
        </x-slot>
    </x-dialog-modal>
</div>