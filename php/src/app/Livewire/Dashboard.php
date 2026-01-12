<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Book;

class Dashboard extends Component
{
    /**
     * ダッシュボード表示
     * @return view
     */
    public function render()
    {
        // 統計データの取得
        $totalCount = \App\Models\Book::count();
        $totalPrice = \App\Models\Book::sum('price');
        $recentBooks = \App\Models\Book::orderBy('created_at', 'desc')->take(5)->get();

        // グラフデータの取得（直近6ヶ月）
        $monthlyData = \App\Models\Book::selectRaw("DATE_FORMAT(created_at, '%Y-%m') as month, count(*) as count")
            ->where('created_at', '>=', now()->subMonths(6))
            ->groupBy('month')
            ->orderBy('month', 'asc')
            ->get();

        return view('livewire.dashboard', [
            'totalCount' => $totalCount,
            'totalPrice' => $totalPrice,
            'recentBooks' => $recentBooks,
            'labels' => $monthlyData->pluck('month')->toArray(), // 配列にする
            'counts' => $monthlyData->pluck('count')->toArray(), // 配列にする
        ]);
    }
}