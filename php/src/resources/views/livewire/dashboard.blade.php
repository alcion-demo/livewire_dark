{{-- 必ず全体を一つの div で囲みます --}}
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        
        {{-- 統計カード --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            {{-- dark:bg-gray-800, dark:border-blue-400 などを追加 --}}
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6 border-l-4 border-blue-500 dark:border-blue-400">
                <div class="text-sm text-gray-500 dark:text-gray-400 uppercase font-bold">総登録数</div>
                <div class="text-3xl font-bold text-gray-800 dark:text-gray-100">{{ number_format($totalCount) }} 冊</div>
            </div>
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6 border-l-4 border-green-500 dark:border-green-400">
                <div class="text-sm text-gray-500 dark:text-gray-400 uppercase font-bold">蔵書合計金額</div>
                <div class="text-3xl font-bold text-gray-800 dark:text-gray-100">¥{{ number_format($totalPrice) }}</div>
            </div>
        </div>

        {{-- チャートセクション --}}
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6 mb-8">
            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4 border-b dark:border-gray-700 pb-2">月別登録推移</h3>
            <div class="h-64">
                <canvas id="registrationChart"></canvas>
            </div>
        </div>

        {{-- 最近登録された本 --}}
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6">
            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4 border-b dark:border-gray-700 pb-2">最近登録された本</h3>
            @if($recentBooks->isEmpty())
                <p class="text-gray-500 dark:text-gray-400">登録されている本はありません。</p>
            @else
                <ul class="divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach($recentBooks as $book)
                        <li class="py-3 flex justify-between items-center">
                            <div class="flex items-center">
                                @if($book->image)
                                    <img src="{{ Storage::url($book->image) }}" 
                                        class="w-16 h-20 object-cover rounded shadow-sm mr-4">
                                @else
                                    {{-- プレースホルダーの色も調整 --}}
                                    <div class="w-16 h-20 bg-gray-200 dark:bg-gray-700 rounded mr-4 flex items-center justify-center text-[10px] text-gray-400 dark:text-gray-500">
                                        No Image
                                    </div>
                                @endif
                                <div class="flex flex-col">
                                    <span class="text-[10px] text-indigo-500 dark:text-indigo-400 font-bold">{{ $book->created_at->format('n月') }}登録</span>
                                    <span class="text-gray-700 dark:text-gray-200 font-medium">{{ $book->title }}</span>
                                </div>
                            </div>
                            <span class="text-sm text-gray-400 dark:text-gray-500 font-bold">¥{{ number_format($book->price) }}</span>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
    </div>

    {{-- Script部分は変更なし --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        function initChart() {
            const ctx = document.getElementById('registrationChart');
            if (!ctx) return;

            const existingChart = Chart.getChart(ctx);
            if (existingChart) {
                existingChart.destroy();
            }

            // ダークモード判定（文字色などを動的に変えたい場合）
            const isDark = document.documentElement.classList.contains('dark');

            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: @json($labels),
                    datasets: [{
                        label: '登録冊数',
                        data: @json($counts),
                        borderColor: '#4F46E5',
                        tension: 0.3,
                        fill: true,
                        backgroundColor: 'rgba(79, 70, 229, 0.1)'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            ticks: { color: isDark ? '#9CA3AF' : '#6B7280' }, // 軸の文字色
                            grid: { color: isDark ? '#374151' : '#E5E7EB' }  // グリッドの色
                        },
                        x: {
                            ticks: { color: isDark ? '#9CA3AF' : '#6B7280' },
                            grid: { color: isDark ? '#374151' : '#E5E7EB' }
                        }
                    },
                    plugins: {
                        legend: {
                            labels: { color: isDark ? '#F3F4F6' : '#111827' }
                        }
                    }
                }
            });
        }

        document.addEventListener('DOMContentLoaded', initChart);
        document.addEventListener('livewire:navigated', initChart);
    </script>
</div>