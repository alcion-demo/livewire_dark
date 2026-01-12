@php
use App\Enums\BookCategory;
@endphp

<div class="max-w-6xl mx-auto py-8 px-4">
    {{-- 上部：検索と登録 --}}
    <div class="mb-6 flex justify-between items-center bg-white dark:bg-gray-800 p-4 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700">
        <div class="relative w-1/3">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
            </div>
            <input type="text" wire:model.live="search" placeholder="タイトルで検索..."
                class="block w-full pl-10 border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 transition placeholder-gray-400">
            
            @if($search)
                <button wire:click="clearSearch" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            @endif
        </div>

        {{-- ボタンの色を Jetstream 標準に戻しました --}}
        <x-button style="background-color: #6366f1;" class="text-white shadow-md transform hover:-translate-y-0.5 transition border-none" wire:click="showBookModal">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 4v16m8-8H4"></path></svg>
            新規登録
        </x-button>
    </div>

    {{-- メインコンテンツ：テーブル --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-xl overflow-hidden border border-gray-100 dark:border-gray-700">
        @if (session()->has('message'))
            <div class="bg-green-50 dark:bg-green-900/30 border-l-4 border-green-400 p-4 m-4">
                <p class="text-green-700 dark:text-green-400">{{ session('message') }}</p>
            </div>
        @endif

        <table class="w-full text-left">
            <thead class="bg-gray-50 dark:bg-gray-700/50 border-b border-gray-100 dark:border-gray-700">
                <tr>
                    <th class="p-4 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">ID</th>
                    <th class="p-4 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">書籍情報</th>
                    <th class="p-4 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">カテゴリー</th>
                    <th class="p-4 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">価格</th>
                    <th class="p-4 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">説明</th>
                    <th class="p-4 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider text-center">操作</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                @forelse($books as $book)
                {{-- wire:key を追加して画像の再描画を確実にします --}}
                <tr wire:key="book-{{ $book->id }}" class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
                    <td class="p-4 text-sm text-gray-500 dark:text-gray-400">#{{ $book->id }}</td>
                    <td class="p-4">
                        <div class="flex items-center">
                            <div class="h-16 w-12 flex-shrink-0">
                                @if($book->image)
                                    <img class="h-16 w-12 object-cover rounded shadow-sm border border-gray-100" 
                                        src="{{ Storage::url($book->image) }}" alt="{{ $book->title }}">
                                @else
                                    <div class="h-16 w-12 bg-gray-100 dark:bg-gray-700 rounded flex items-center justify-center border border-gray-200 dark:border-gray-600">
                                        <span class="text-[10px] text-gray-400 font-bold text-center leading-tight">No<br>Image</span>
                                    </div>
                                @endif
                            </div>
                            <div class="ml-4 flex-1 min-w-0">
                                <div class="text-sm font-bold text-gray-900 dark:text-gray-200 truncate w-48" title="{{ $book->title }}">
                                    @if($book->url)
                                        <a href="{{ $book->url }}" target="_blank" rel="noopener noreferrer" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-300 hover:underline inline-flex items-center">
                                            {{ $book->title }}
                                            <svg class="w-3 h-3 ml-1 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                                        </a>
                                    @else
                                        <span class="text-gray-500 dark:text-gray-400">{{ $book->title }}</span>
                                        <span class="text-[10px] font-normal text-gray-400 dark:text-gray-500 ml-1">URLなし</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </td>
                    <td class="p-4 text-sm font-semibold text-gray-700 dark:text-gray-300">
                        @if($book->category instanceof \App\Enums\BookCategory)
                            {{-- Enumオブジェクトとして認識されている場合 --}}
                            {{ $book->category->value }}
                        @else
                            {{-- 文字列（または数値）として入っている場合でもそのまま出す --}}
                            {{ $book->category ?? '未設定' }}
                        @endif
                    </td>

                    <td class="p-4 text-sm font-semibold text-gray-700 dark:text-gray-300">
                        ¥{{ number_format($book->price) }}
                    </td>

                    <td class="p-4">
                        <p class="text-sm text-gray-600 dark:text-gray-400 line-clamp-2 w-64">{!! nl2br(e($book->description)) !!}</p>
                    </td>

                    <td class="p-4 text-center">
                        <div class="flex justify-center space-x-3">
                            <button wire:click="showEditBookModal({{ $book->id }})" 
                                class="px-4 py-1.5 bg-emerald-500 text-white rounded-lg hover:bg-emerald-600 shadow-sm hover:shadow active:transform active:scale-95 transition-all text-sm font-bold tracking-wider">
                                編集
                            </button>
                            <button wire:click="deleteBook({{ $book->id }})" wire:confirm="本当に削除しますか？" 
                                class="px-4 py-1.5 bg-rose-400 text-white rounded-lg hover:bg-rose-500 shadow-sm hover:shadow active:transform active:scale-95 transition-all text-sm font-bold tracking-wider">
                                削除
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="p-8 text-center text-gray-500 dark:text-gray-400">
                        検索結果が見つかりませんでした。
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

        {{-- ページネーション --}}
        <div class="p-4 bg-gray-50 dark:bg-gray-700/50 border-t border-gray-100 dark:border-gray-700 pagination-dark-fix">
            {{ $books->onEachSide(1)->links('vendor.pagination.tailwind2') }}
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
                {{-- タイトル --}}
                <div>
                    <x-label for="title" value="タイトル" class="font-bold mb-1 dark:text-gray-300" />
                    <x-input type="text" id="title" wire:model.lazy="title" class="w-full" />
                    @error('title') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
                </div>

                {{-- カテゴリー：ダークモード完全対応版 --}}
                <div>
                    <div>
                        <x-label for="category" value="カテゴリー" class="font-bold mb-1 dark:text-gray-300" />
                        <select id="category" wire:model="category" 
                            class="w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 transition">
                            <option value="">選択してください</option>
                                @foreach(BookCategory::cases() as $cat)
                                    {{-- $cat->value で '技術書' などの文字列が取得できます --}}
                                    <option value="{{ $cat->value }}">{{ $cat->value }}</option>
                                @endforeach
                        </select>
                    </div>
                    @error('category') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
                </div>

                {{-- 画像プレビューとアップロード --}}
                <div class="flex gap-4 items-end">
                    <div class="w-1/3">
                    @if ($newImage)
                        <p class="text-xs text-indigo-500 dark:text-indigo-400 font-bold mb-1">プレビュー:</p>
                        <img src="{{ $newImage->temporaryUrl() }}" class="w-32 h-40 object-cover rounded-lg ring-4 ring-indigo-100 dark:ring-indigo-900/30">
                    @elseif ($oldImage)
                        <p class="text-xs text-gray-400 mb-1">現在の画像:</p>
                        <img src="{{ Storage::url($oldImage) }}" class="w-32 h-40 object-cover rounded-lg">
                    @else
                        <div class="w-full h-40 bg-gray-100 dark:bg-gray-700 rounded-lg flex items-center justify-center text-gray-400 dark:text-gray-500 border border-dashed dark:border-gray-600">No Image</div>
                    @endif
                    </div>
                    <div class="flex-1">
                        <x-label for="image" value="書籍画像" class="font-bold mb-1 dark:text-gray-300" />
                        <input type="file" id="image" wire:model="newImage" 
                            class="w-full text-sm text-gray-500 dark:text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 dark:file:bg-indigo-900/50 file:text-indigo-700 dark:file:text-indigo-300 hover:file:bg-indigo-100 dark:hover:file:bg-indigo-900/70" />
                        @error('newImage') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
                    </div>
                </div>

                {{-- 価格 --}}
                <div>
                    <x-label for="price" value="価格" class="font-bold mb-1 dark:text-gray-300" />
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-500 dark:text-gray-400">¥</span>
                        <x-input type="text" id="price" wire:model.lazy="price" class="w-full pl-7" />
                    </div>
                    @error('price') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
                </div>

                {{-- URL --}}
                <div>
                    <x-label for="url" value="参考URL" class="font-bold mb-1 dark:text-gray-300" />
                    <x-input type="text" id="url" wire:model="url" placeholder="https://amazon..." class="w-full" />
                    @error('url') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
                </div>

                {{-- 説明文 --}}
                <div>
                    <x-label for="description" value="説明文" class="font-bold mb-1 dark:text-gray-300" />
                    <textarea id="description" rows="4" wire:model="description" 
                        class="w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500"></textarea>
                    @error('description') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
                </div>
            </div>
        </x-slot>

        <x-slot name="footer">
            @if ($editWork)
                <x-button class="!bg-emerald-600 hover:!bg-emerald-700 mr-3 !text-white !border-none shadow-md" wire:click="updateBook({{ $Id }})">
                    更新する
                </x-button>
            @else
                <x-button class="!bg-indigo-600 hover:!bg-indigo-700 mr-3 !text-white !border-none shadow-md" wire:click="bookPost">
                    登録する
                </x-button>
            @endif
            <x-secondary-button wire:click="$toggle('liveModal')" class="dark:!bg-gray-700 dark:!text-gray-300 dark:!border-gray-600">
                キャンセル
            </x-secondary-button>
        </x-slot>
    </x-dialog-modal>
</div>