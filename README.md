# 書籍管理アプリケーション  

<img alt="Static Badge" src="https://img.shields.io/badge/wsl2-w?style=plastic&logo=linux&logoColor=000000&labelColor=%23FCC624&color=%23FCC624"> <img alt="Static Badge" src="https://img.shields.io/badge/ubuntu-u?style=plastic&logo=ubuntu&logoColor=%23ffffff&labelColor=%23E95420&color=%23E95420"> <img alt="Static Badge" src="https://img.shields.io/badge/alpine-l?style=plastic&logo=alpinelinux&logoColor=%23ffffff&labelColor=%230D597F&color=%230D597F">  
<img alt="Static Badge" src="https://img.shields.io/badge/Docker-d?style=plastic&logo=docker&logoColor=%23ffffff&labelColor=%232496ED&color=%232496ED">
<img alt="Static Badge" src="https://img.shields.io/badge/NGINX-n?style=plastic&logo=nginx&logoColor=%23ffffff">
<img alt="Static Badge" src="https://img.shields.io/badge/MySQL-m?style=plastic&logo=mysql&logoColor=%23ffffff&labelColor=%234479A1&color=%234479A1">
<img alt="Static Badge" src="https://img.shields.io/badge/php-p?style=plastic&logo=php&logoColor=%23ffffff&labelColor=%23777BB4&color=%23777BB4">
<img alt="Static Badge" src="https://img.shields.io/badge/livewire-w?style=plastic&logo=livewire&logoColor=%23ffffff&labelColor=%234E56A6&color=%234E56A6">  
<img alt="Static Badge" src="https://img.shields.io/badge/Laravel12-l?style=plastic&logo=laravel&logoColor=%23ffffff&labelColor=%23FF2D20&color=%23FF2D20">
<img alt="Static Badge" src="https://img.shields.io/badge/tailwind-%20?style=plastic&logo=tailwindcss&logoColor=ffffff&color=%2306B6D4">
<img alt="Static Badge" src="https://img.shields.io/badge/Chart.js-c?style=plastic&logo=chart.js&logoColor=%23FF6384&labelColor=%23000000&color=%23000000">
<img alt="Static Badge" src="https://img.shields.io/badge/vite-v?style=plastic&logo=vite&logoColor=%23ffffff&labelColor=%23646CFF&color=%23646CFF">
<img alt="Static Badge" src="https://img.shields.io/badge/npm-n?style=plastic&logo=npm&logoColor=%23ffffff&labelColor=%23CB3837&color=%23CB3837">
<img alt="Static Badge" src="https://img.shields.io/badge/-jetstream?style=plastic&logo=jetstream&label=jetstream&labelColor=c1c1c1&color=c1c1c1">  

## プロジェクト概要

- Laravel フレームワークを使ったモダンな Web アプリケーションのサンプルです。
- 認証（Fortify / Jetstream）や Livewire を使ったインタラクティブな UI、ファイル保存（画像）などの機能を含みます。

## 学習・検証目的
- 本プロジェクトは、Laravel における認証基盤（Jetstream）と
Livewire を用いたサーバーサイド駆動 UI の理解を目的とした学習用アプリケーションです。

## 技術選定の背景
- フロントエンド専業を目指すのではなく、Laravel を軸としたバックエンド開発を主目的としているため、
  JavaScript 依存を最小限に抑えつつ、UI のインタラクションを実現できる Livewire を採用しました。
- npm / Vite によるビルドや依存管理は行い、フロントエンド資産を「読める・触れる」状態は維持しています。

## 主な機能
- ユーザー認証（登録、ログイン、二要素認証の導入箇所あり）
- Book モデルによる書籍管理（CRUD）
- Livewire を使ったリアクティブな画面（例: 一部のコンポーネントは `app/Livewire` にあります）
- 画像やファイルのアップロードと保存（`public/storage` 配下に配置）
- 日本化語対応（`lang/ja.json`）

## 使用技術
| カテゴリ | 使用技術 |
| :--- | :--- |
| **Backend** | Laravel 12, Jetstream, PHP_CodeSniffer |
| **Frontend** | Livewire, Tailwind CSS, Node.js |
| **Infrastructure** | Docker Compose (App / Node / MySQL / Nginx) |
| **OS Environment** | WSL2 (Ubuntu / Alpine Linux) |
| **Database** | MySQL 8.x |

## セットアップ手順

### 1. インフラのビルドと起動
```
docker compose build
docker compose up -d
```

### 2. バックエンド初期化
```
docker compose exec app ash
composer install
chown -R www-data:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache
```
#### 3. 認証基盤インストール
```
composer require laravel/jetstream
php artisan jetstream:install livewire --dark

```
#### 4. フロントエンド依存関係  
```
npm install
npm run dev
```
#### 5. マイグレーション
```
php artisan migrate
```
※ npm コマンドは Node がインストールされた app コンテナ内で実行しています。

## ディレクトリ構成（主要部分）

- `app/` - アプリケーションのソースコード
  - `Models/` - Eloquent モデル（例: `Book.php`, `User.php`）
  - `Http/Controllers/` - ルーティングで呼ばれるコントローラ
  - `Livewire/` - Livewire コンポーネント（例: `BookIndex.php`, `Dashboard.php`）
  - `Providers/` - サービスプロバイダ（Fortify/Jetstream の設定など）
- `bootstrap/` - フレームワーク初期化コード
- `config/` - アプリ設定（データベース、認証、キャッシュ等）
- `database/` - マイグレーション、ファクトリ、シーダー
  - `migrations/` - テーブル定義（例: `create_books_table.php`、`add_url_to_books_table.php`）
- `public/` - 公開ディレクトリ（`index.php`, `storage`, `images`）
- `resources/` - ビュー、CSS、JS、Markdown 等のソース
- `routes/` - ルーティング定義 (`web.php`, `api.php`) 
- `tests/` - テストコード
- `vendor/` - Composer 依存ライブラリ（自動生成）

## 設計・実装の特徴

- MVC（Model-View-Controller）構成を基本にしています。
  - `Models`（データ） ⇄ `Controllers`（処理） ⇄ `resources/views`（表示）という流れを意識します。
- Eloquent（ORM）を使い、データベース操作はモデルメソッドやクエリビルダで行います。
- Livewire によるサーバーサイド駆動のインタラクティブ UI
  - JavaScript をたくさん書かなくても、PHP 側のコンポーネントで状態管理とイベント処理ができます。
- Fortify / Jetstream による認証基盤
  - ユーザー登録、ログイン、パスワードリセット、二要素認証などの機能を組み込みやすくしています。
- マイグレーションでスキーマ変更を管理
  - `database/migrations/*` をコミットすることで、他の開発者と DB 構造を共有できます。
- ローカライズ対応
  - `lang/` フォルダに日本語リソースが含まれており、多言語対応の例になっています。

## 今後の改善予定
- Livewire コンポーネントの責務整理
- バリデーション・エラーハンドリングの改善