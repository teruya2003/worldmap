<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('WorldMap - 訪れた国の記録') }}
        </h2>
    </x-slot>

    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    
    <!-- カスタムCSS -->
    <style>
        .custom-popup {
            z-index: 1000 !important;
        }
        .leaflet-popup {
            z-index: 1000 !important;
        }
        .leaflet-popup-pane {
            z-index: 1000 !important;
        }
        #world-map {
            background-color: #f8fafc;
        }
        .leaflet-container {
            background-color: #f8fafc !important;
        }
    </style>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            {{-- 世界地図 --}}
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-8">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">世界地図</h3>
                    <div id="world-map" class="w-full h-96 border border-gray-200 dark:border-gray-700 rounded-lg"></div>
                </div>
            </div>

            {{-- 統計情報 --}}
            <div class="mb-8 grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="text-sm font-medium text-gray-500 dark:text-gray-400">訪れた国の総数</div>
                        <div class="text-2xl font-bold text-gray-900 dark:text-gray-100" id="visited-count">0</div>
                    </div>
                </div>
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="text-sm font-medium text-gray-500 dark:text-gray-400">住んだことがある</div>
                        <div class="text-2xl font-bold text-green-600" id="lived-count">0</div>
                    </div>
                </div>
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="text-sm font-medium text-gray-500 dark:text-gray-400">宿泊したことがある</div>
                        <div class="text-2xl font-bold text-blue-600" id="stayed-count">0</div>
                    </div>
                </div>
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="text-sm font-medium text-gray-500 dark:text-gray-400">日帰りで訪れたことがある</div>
                        <div class="text-2xl font-bold text-yellow-600" id="visited-day-count">0</div>
                    </div>
                </div>
            </div>

            {{-- 国検索機能 --}}
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">国を検索</h3>
                    <div class="flex gap-4">
                        <input type="text" id="country-search" placeholder="国名を入力してください" 
                               class="flex-1 rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <button id="search-btn" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            検索
                        </button>
                    </div>
                    <div id="search-results" class="mt-4 hidden">
                        {{-- 検索結果がここに表示されます --}}
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- 国ステータス変更モーダル --}}
    <div id="country-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden" style="z-index: 9999;">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white dark:bg-gray-800">
            <div class="mt-3">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100" id="modal-country-name">国名</h3>
                    <button id="modal-close" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                
                <div class="mb-4">
                    <p class="text-sm text-gray-600 dark:text-gray-400">この国の訪問状況を選択してください</p>
                </div>
                
                <div class="space-y-2 mb-6">
                    <button class="w-full text-left px-4 py-3 rounded-md hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300 border border-gray-200 dark:border-gray-600 transition-colors" data-status="lived">
                        <div class="flex items-center">
                            <span class="inline-block w-4 h-4 bg-green-500 rounded-full mr-3"></span>
                            <div>
                                <div class="font-medium">住んだことがある</div>
                                <div class="text-sm text-gray-500 dark:text-gray-400">長期間居住していた</div>
                            </div>
                        </div>
                    </button>
                    <button class="w-full text-left px-4 py-3 rounded-md hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300 border border-gray-200 dark:border-gray-600 transition-colors" data-status="stayed">
                        <div class="flex items-center">
                            <span class="inline-block w-4 h-4 bg-blue-500 rounded-full mr-3"></span>
                            <div>
                                <div class="font-medium">宿泊したことがある</div>
                                <div class="text-sm text-gray-500 dark:text-gray-400">1泊以上滞在した</div>
                            </div>
                        </div>
                    </button>
                    <button class="w-full text-left px-4 py-3 rounded-md hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300 border border-gray-200 dark:border-gray-600 transition-colors" data-status="visited">
                        <div class="flex items-center">
                            <span class="inline-block w-4 h-4 bg-yellow-500 rounded-full mr-3"></span>
                            <div>
                                <div class="font-medium">日帰りで訪れたことがある</div>
                                <div class="text-sm text-gray-500 dark:text-gray-400">1日以内の訪問</div>
                            </div>
                        </div>
                    </button>
                    <button class="w-full text-left px-4 py-3 rounded-md hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300 border border-gray-200 dark:border-gray-600 transition-colors" data-status="passed">
                        <div class="flex items-center">
                            <span class="inline-block w-4 h-4 bg-orange-500 rounded-full mr-3"></span>
                            <div>
                                <div class="font-medium">通ったことがある</div>
                                <div class="text-sm text-gray-500 dark:text-gray-400">通過しただけ</div>
                            </div>
                        </div>
                    </button>
                    <button class="w-full text-left px-4 py-3 rounded-md hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300 border border-gray-200 dark:border-gray-600 transition-colors" data-status="not_visited">
                        <div class="flex items-center">
                            <span class="inline-block w-4 h-4 bg-gray-400 rounded-full mr-3"></span>
                            <div>
                                <div class="font-medium">行ったことがない</div>
                                <div class="text-sm text-gray-500 dark:text-gray-400">未訪問</div>
                            </div>
                        </div>
                    </button>
                </div>
                
                <div class="flex justify-end gap-2">
                    <a href="#" id="modal-detail-link" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 text-sm transition-colors">
                        詳細ページへ
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Leaflet JavaScript -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    
    <script>
        // サーバーから取得したユーザーのステータス
        const userStatuses = @json($statusMap ?? []);
        
        // 国データ（拡張版 - 世界の主要国）
        const countriesData = [
            // アジア
            { id: 1, name: '日本', name_en: 'Japan', code: 'JPN', continent: 'Asia', status: 'not_visited', lat: 36.2048, lng: 138.2529 },
            { id: 2, name: '中国', name_en: 'China', code: 'CHN', continent: 'Asia', status: 'visited', lat: 35.8617, lng: 104.1954 },
            { id: 15, name: '韓国', name_en: 'South Korea', code: 'KOR', continent: 'Asia', status: 'visited', lat: 35.9078, lng: 127.7669 },
            { id: 16, name: 'タイ', name_en: 'Thailand', code: 'THA', continent: 'Asia', status: 'not_visited', lat: 15.8700, lng: 100.9925 },
            { id: 17, name: 'ベトナム', name_en: 'Vietnam', code: 'VNM', continent: 'Asia', status: 'not_visited', lat: 14.0583, lng: 108.2772 },
            { id: 18, name: 'インドネシア', name_en: 'Indonesia', code: 'IDN', continent: 'Asia', status: 'not_visited', lat: -0.7893, lng: 113.9213 },
            { id: 19, name: 'フィリピン', name_en: 'Philippines', code: 'PHL', continent: 'Asia', status: 'not_visited', lat: 12.8797, lng: 121.7740 },
            { id: 10, name: 'インド', name_en: 'India', code: 'IND', continent: 'Asia', status: 'not_visited', lat: 20.5937, lng: 78.9629 },
            { id: 25, name: 'パキスタン', name_en: 'Pakistan', code: 'PAK', continent: 'Asia', status: 'not_visited', lat: 30.3753, lng: 69.3451 },
            { id: 24, name: 'イラン', name_en: 'Iran', code: 'IRN', continent: 'Asia', status: 'not_visited', lat: 32.4279, lng: 53.6880 },
            { id: 23, name: 'サウジアラビア', name_en: 'Saudi Arabia', code: 'SAU', continent: 'Asia', status: 'not_visited', lat: 23.8859, lng: 45.0792 },
            { id: 22, name: 'トルコ', name_en: 'Turkey', code: 'TUR', continent: 'Asia', status: 'not_visited', lat: 38.9637, lng: 35.2433 },
            { id: 26, name: 'マレーシア', name_en: 'Malaysia', code: 'MYS', continent: 'Asia', status: 'not_visited', lat: 4.2105, lng: 101.9758 },
            { id: 27, name: 'シンガポール', name_en: 'Singapore', code: 'SGP', continent: 'Asia', status: 'not_visited', lat: 1.3521, lng: 103.8198 },
            { id: 28, name: 'バングラデシュ', name_en: 'Bangladesh', code: 'BGD', continent: 'Asia', status: 'not_visited', lat: 23.6850, lng: 90.3563 },
            { id: 29, name: 'ミャンマー', name_en: 'Myanmar', code: 'MMR', continent: 'Asia', status: 'not_visited', lat: 21.9162, lng: 95.9560 },
            { id: 30, name: 'スリランカ', name_en: 'Sri Lanka', code: 'LKA', continent: 'Asia', status: 'not_visited', lat: 7.8731, lng: 80.7718 },
            { id: 31, name: 'ネパール', name_en: 'Nepal', code: 'NPL', continent: 'Asia', status: 'not_visited', lat: 28.3949, lng: 84.1240 },
            { id: 32, name: 'アフガニスタン', name_en: 'Afghanistan', code: 'AFG', continent: 'Asia', status: 'not_visited', lat: 33.9391, lng: 67.7100 },
            { id: 33, name: 'イラク', name_en: 'Iraq', code: 'IRQ', continent: 'Asia', status: 'not_visited', lat: 33.2232, lng: 43.6793 },
            { id: 34, name: 'イスラエル', name_en: 'Israel', code: 'ISR', continent: 'Asia', status: 'not_visited', lat: 31.0461, lng: 34.8516 },
            { id: 35, name: 'アラブ首長国連邦', name_en: 'United Arab Emirates', code: 'ARE', continent: 'Asia', status: 'not_visited', lat: 23.4241, lng: 53.8478 },
            { id: 36, name: 'カタール', name_en: 'Qatar', code: 'QAT', continent: 'Asia', status: 'not_visited', lat: 25.3548, lng: 51.1839 },
            { id: 37, name: 'クウェート', name_en: 'Kuwait', code: 'KWT', continent: 'Asia', status: 'not_visited', lat: 29.3117, lng: 47.4818 },
            { id: 38, name: 'レバノン', name_en: 'Lebanon', code: 'LBN', continent: 'Asia', status: 'not_visited', lat: 33.8547, lng: 35.8623 },
            { id: 39, name: 'ヨルダン', name_en: 'Jordan', code: 'JOR', continent: 'Asia', status: 'not_visited', lat: 30.5852, lng: 36.2384 },
            { id: 40, name: 'シリア', name_en: 'Syria', code: 'SYR', continent: 'Asia', status: 'not_visited', lat: 34.8021, lng: 38.9968 },
            { id: 41, name: 'カザフスタン', name_en: 'Kazakhstan', code: 'KAZ', continent: 'Asia', status: 'not_visited', lat: 48.0196, lng: 66.9237 },
            { id: 42, name: 'ウズベキスタン', name_en: 'Uzbekistan', code: 'UZB', continent: 'Asia', status: 'not_visited', lat: 41.3775, lng: 64.5853 },
            { id: 43, name: 'モンゴル', name_en: 'Mongolia', code: 'MNG', continent: 'Asia', status: 'not_visited', lat: 46.8625, lng: 103.8467 },
            { id: 44, name: '北朝鮮', name_en: 'North Korea', code: 'PRK', continent: 'Asia', status: 'not_visited', lat: 40.3399, lng: 127.5101 },
            { id: 45, name: '台湾', name_en: 'Taiwan', code: 'TWN', continent: 'Asia', status: 'not_visited', lat: 23.6978, lng: 120.9605 },
            { id: 46, name: '香港', name_en: 'Hong Kong', code: 'HKG', continent: 'Asia', status: 'not_visited', lat: 22.3193, lng: 114.1694 },
            { id: 47, name: 'マカオ', name_en: 'Macau', code: 'MAC', continent: 'Asia', status: 'not_visited', lat: 22.1987, lng: 113.5439 },
            
            // ヨーロッパ
            { id: 4, name: 'フランス', name_en: 'France', code: 'FRA', continent: 'Europe', status: 'stayed', lat: 46.2276, lng: 2.2137 },
            { id: 5, name: 'イギリス', name_en: 'United Kingdom', code: 'GBR', continent: 'Europe', status: 'passed', lat: 55.3781, lng: -3.4360 },
            { id: 6, name: 'ドイツ', name_en: 'Germany', code: 'DEU', continent: 'Europe', status: 'visited', lat: 51.1657, lng: 10.4515 },
            { id: 7, name: 'イタリア', name_en: 'Italy', code: 'ITA', continent: 'Europe', status: 'stayed', lat: 41.8719, lng: 12.5674 },
            { id: 8, name: 'スペイン', name_en: 'Spain', code: 'ESP', continent: 'Europe', status: 'not_visited', lat: 40.4637, lng: -3.7492 },
            { id: 9, name: 'ロシア', name_en: 'Russia', code: 'RUS', continent: 'Europe', status: 'not_visited', lat: 61.5240, lng: 105.3188 },
            { id: 48, name: 'オランダ', name_en: 'Netherlands', code: 'NLD', continent: 'Europe', status: 'not_visited', lat: 52.1326, lng: 5.2913 },
            { id: 49, name: 'ベルギー', name_en: 'Belgium', code: 'BEL', continent: 'Europe', status: 'not_visited', lat: 50.5039, lng: 4.4699 },
            { id: 50, name: 'スイス', name_en: 'Switzerland', code: 'CHE', continent: 'Europe', status: 'not_visited', lat: 46.8182, lng: 8.2275 },
            { id: 51, name: 'オーストリア', name_en: 'Austria', code: 'AUT', continent: 'Europe', status: 'not_visited', lat: 47.5162, lng: 14.5501 },
            { id: 52, name: 'ポーランド', name_en: 'Poland', code: 'POL', continent: 'Europe', status: 'not_visited', lat: 51.9194, lng: 19.1451 },
            { id: 53, name: 'チェコ', name_en: 'Czech Republic', code: 'CZE', continent: 'Europe', status: 'not_visited', lat: 49.8175, lng: 15.4730 },
            { id: 54, name: 'ハンガリー', name_en: 'Hungary', code: 'HUN', continent: 'Europe', status: 'not_visited', lat: 47.1625, lng: 19.5033 },
            { id: 55, name: 'スウェーデン', name_en: 'Sweden', code: 'SWE', continent: 'Europe', status: 'not_visited', lat: 60.1282, lng: 18.6435 },
            { id: 56, name: 'ノルウェー', name_en: 'Norway', code: 'NOR', continent: 'Europe', status: 'not_visited', lat: 60.4720, lng: 8.4689 },
            { id: 57, name: 'デンマーク', name_en: 'Denmark', code: 'DNK', continent: 'Europe', status: 'not_visited', lat: 56.2639, lng: 9.5018 },
            { id: 58, name: 'フィンランド', name_en: 'Finland', code: 'FIN', continent: 'Europe', status: 'not_visited', lat: 61.9241, lng: 25.7482 },
            { id: 59, name: 'ポルトガル', name_en: 'Portugal', code: 'PRT', continent: 'Europe', status: 'not_visited', lat: 39.3999, lng: -8.2245 },
            { id: 60, name: 'ギリシャ', name_en: 'Greece', code: 'GRC', continent: 'Europe', status: 'not_visited', lat: 39.0742, lng: 21.8243 },
            { id: 61, name: 'アイルランド', name_en: 'Ireland', code: 'IRL', continent: 'Europe', status: 'not_visited', lat: 53.4129, lng: -8.2439 },
            { id: 62, name: 'アイスランド', name_en: 'Iceland', code: 'ISL', continent: 'Europe', status: 'not_visited', lat: 64.9631, lng: -19.0208 },
            { id: 63, name: 'ルクセンブルク', name_en: 'Luxembourg', code: 'LUX', continent: 'Europe', status: 'not_visited', lat: 49.8153, lng: 6.1296 },
            { id: 64, name: 'モナコ', name_en: 'Monaco', code: 'MCO', continent: 'Europe', status: 'not_visited', lat: 43.7384, lng: 7.4246 },
            { id: 65, name: 'リヒテンシュタイン', name_en: 'Liechtenstein', code: 'LIE', continent: 'Europe', status: 'not_visited', lat: 47.1660, lng: 9.5554 },
            { id: 66, name: 'アンドラ', name_en: 'Andorra', code: 'AND', continent: 'Europe', status: 'not_visited', lat: 42.5462, lng: 1.6016 },
            { id: 67, name: 'サンマリノ', name_en: 'San Marino', code: 'SMR', continent: 'Europe', status: 'not_visited', lat: 43.9424, lng: 12.4578 },
            { id: 68, name: 'バチカン', name_en: 'Vatican City', code: 'VAT', continent: 'Europe', status: 'not_visited', lat: 41.9029, lng: 12.4534 },
            { id: 69, name: 'マルタ', name_en: 'Malta', code: 'MLT', continent: 'Europe', status: 'not_visited', lat: 35.9375, lng: 14.3754 },
            { id: 70, name: 'キプロス', name_en: 'Cyprus', code: 'CYP', continent: 'Europe', status: 'not_visited', lat: 35.1264, lng: 33.4299 },
            
            // 北アメリカ
            { id: 3, name: 'アメリカ', name_en: 'United States', code: 'USA', continent: 'North America', status: 'visited', lat: 39.8283, lng: -98.5795 },
            { id: 13, name: 'カナダ', name_en: 'Canada', code: 'CAN', continent: 'North America', status: 'not_visited', lat: 56.1304, lng: -106.3468 },
            { id: 14, name: 'メキシコ', name_en: 'Mexico', code: 'MEX', continent: 'North America', status: 'not_visited', lat: 23.6345, lng: -102.5528 },
            { id: 71, name: 'グアテマラ', name_en: 'Guatemala', code: 'GTM', continent: 'North America', status: 'not_visited', lat: 15.7835, lng: -90.2308 },
            { id: 72, name: 'キューバ', name_en: 'Cuba', code: 'CUB', continent: 'North America', status: 'not_visited', lat: 21.5218, lng: -77.7812 },
            { id: 73, name: 'ジャマイカ', name_en: 'Jamaica', code: 'JAM', continent: 'North America', status: 'not_visited', lat: 18.1096, lng: -77.2975 },
            { id: 74, name: 'ハイチ', name_en: 'Haiti', code: 'HTI', continent: 'North America', status: 'not_visited', lat: 18.9712, lng: -72.2852 },
            { id: 75, name: 'ドミニカ共和国', name_en: 'Dominican Republic', code: 'DOM', continent: 'North America', status: 'not_visited', lat: 18.7357, lng: -70.1627 },
            { id: 76, name: 'コスタリカ', name_en: 'Costa Rica', code: 'CRI', continent: 'North America', status: 'not_visited', lat: 9.7489, lng: -83.7534 },
            { id: 77, name: 'パナマ', name_en: 'Panama', code: 'PAN', continent: 'North America', status: 'not_visited', lat: 8.5380, lng: -80.7821 },
            { id: 78, name: 'ニカラグア', name_en: 'Nicaragua', code: 'NIC', continent: 'North America', status: 'not_visited', lat: 12.2658, lng: -85.2072 },
            { id: 79, name: 'ホンジュラス', name_en: 'Honduras', code: 'HND', continent: 'North America', status: 'not_visited', lat: 15.2000, lng: -86.2419 },
            { id: 80, name: 'エルサルバドル', name_en: 'El Salvador', code: 'SLV', continent: 'North America', status: 'not_visited', lat: 13.7942, lng: -88.8965 },
            { id: 81, name: 'ベリーズ', name_en: 'Belize', code: 'BLZ', continent: 'North America', status: 'not_visited', lat: 17.1899, lng: -88.4976 },
            
            // 南アメリカ
            { id: 11, name: 'ブラジル', name_en: 'Brazil', code: 'BRA', continent: 'South America', status: 'not_visited', lat: -14.2350, lng: -51.9253 },
            { id: 82, name: 'アルゼンチン', name_en: 'Argentina', code: 'ARG', continent: 'South America', status: 'not_visited', lat: -38.4161, lng: -63.6167 },
            { id: 83, name: 'チリ', name_en: 'Chile', code: 'CHL', continent: 'South America', status: 'not_visited', lat: -35.6751, lng: -71.5430 },
            { id: 84, name: 'ペルー', name_en: 'Peru', code: 'PER', continent: 'South America', status: 'not_visited', lat: -9.1900, lng: -75.0152 },
            { id: 85, name: 'コロンビア', name_en: 'Colombia', code: 'COL', continent: 'South America', status: 'not_visited', lat: 4.5709, lng: -74.2973 },
            { id: 86, name: 'ベネズエラ', name_en: 'Venezuela', code: 'VEN', continent: 'South America', status: 'not_visited', lat: 6.4238, lng: -66.5897 },
            { id: 87, name: 'エクアドル', name_en: 'Ecuador', code: 'ECU', continent: 'South America', status: 'not_visited', lat: -1.8312, lng: -78.1834 },
            { id: 88, name: 'ボリビア', name_en: 'Bolivia', code: 'BOL', continent: 'South America', status: 'not_visited', lat: -16.2902, lng: -63.5887 },
            { id: 89, name: 'パラグアイ', name_en: 'Paraguay', code: 'PRY', continent: 'South America', status: 'not_visited', lat: -23.4425, lng: -58.4438 },
            { id: 90, name: 'ウルグアイ', name_en: 'Uruguay', code: 'URY', continent: 'South America', status: 'not_visited', lat: -32.5228, lng: -55.7658 },
            { id: 91, name: 'ガイアナ', name_en: 'Guyana', code: 'GUY', continent: 'South America', status: 'not_visited', lat: 4.8604, lng: -58.9302 },
            { id: 92, name: 'スリナム', name_en: 'Suriname', code: 'SUR', continent: 'South America', status: 'not_visited', lat: 3.9193, lng: -56.0278 },
            { id: 93, name: 'フランス領ギアナ', name_en: 'French Guiana', code: 'GUF', continent: 'South America', status: 'not_visited', lat: 3.9339, lng: -53.1258 },
            
            // アフリカ
            { id: 20, name: '南アフリカ', name_en: 'South Africa', code: 'ZAF', continent: 'Africa', status: 'not_visited', lat: -30.5595, lng: 22.9375 },
            { id: 21, name: 'エジプト', name_en: 'Egypt', code: 'EGY', continent: 'Africa', status: 'not_visited', lat: 26.0975, lng: 30.0444 },
            { id: 94, name: 'ナイジェリア', name_en: 'Nigeria', code: 'NGA', continent: 'Africa', status: 'not_visited', lat: 9.0820, lng: 8.6753 },
            { id: 95, name: 'ケニア', name_en: 'Kenya', code: 'KEN', continent: 'Africa', status: 'not_visited', lat: -0.0236, lng: 37.9062 },
            { id: 96, name: 'モロッコ', name_en: 'Morocco', code: 'MAR', continent: 'Africa', status: 'not_visited', lat: 31.6295, lng: -7.9811 },
            { id: 97, name: 'アルジェリア', name_en: 'Algeria', code: 'DZA', continent: 'Africa', status: 'not_visited', lat: 28.0339, lng: 1.6596 },
            { id: 98, name: 'チュニジア', name_en: 'Tunisia', code: 'TUN', continent: 'Africa', status: 'not_visited', lat: 33.8869, lng: 9.5375 },
            { id: 99, name: 'リビア', name_en: 'Libya', code: 'LBY', continent: 'Africa', status: 'not_visited', lat: 26.3351, lng: 17.2283 },
            { id: 100, name: 'スーダン', name_en: 'Sudan', code: 'SDN', continent: 'Africa', status: 'not_visited', lat: 12.8628, lng: 30.2176 },
            { id: 101, name: 'エチオピア', name_en: 'Ethiopia', code: 'ETH', continent: 'Africa', status: 'not_visited', lat: 9.1450, lng: 40.4897 },
            { id: 102, name: 'ガーナ', name_en: 'Ghana', code: 'GHA', continent: 'Africa', status: 'not_visited', lat: 7.9465, lng: -1.0232 },
            { id: 103, name: 'タンザニア', name_en: 'Tanzania', code: 'TZA', continent: 'Africa', status: 'not_visited', lat: -6.3690, lng: 34.8888 },
            { id: 104, name: 'ウガンダ', name_en: 'Uganda', code: 'UGA', continent: 'Africa', status: 'not_visited', lat: 1.3733, lng: 32.2903 },
            { id: 105, name: 'コンゴ民主共和国', name_en: 'Democratic Republic of the Congo', code: 'COD', continent: 'Africa', status: 'not_visited', lat: -4.0383, lng: 21.7587 },
            { id: 106, name: 'コンゴ共和国', name_en: 'Republic of the Congo', code: 'COG', continent: 'Africa', status: 'not_visited', lat: -0.2280, lng: 15.8277 },
            { id: 107, name: 'カメルーン', name_en: 'Cameroon', code: 'CMR', continent: 'Africa', status: 'not_visited', lat: 7.3697, lng: 12.3547 },
            { id: 108, name: 'セネガル', name_en: 'Senegal', code: 'SEN', continent: 'Africa', status: 'not_visited', lat: 14.4974, lng: -14.4524 },
            { id: 109, name: 'マリ', name_en: 'Mali', code: 'MLI', continent: 'Africa', status: 'not_visited', lat: 17.5707, lng: -3.9962 },
            { id: 110, name: 'ブルキナファソ', name_en: 'Burkina Faso', code: 'BFA', continent: 'Africa', status: 'not_visited', lat: 12.2383, lng: -1.5616 },
            { id: 111, name: 'ニジェール', name_en: 'Niger', code: 'NER', continent: 'Africa', status: 'not_visited', lat: 17.6078, lng: 8.0817 },
            { id: 112, name: 'チャド', name_en: 'Chad', code: 'TCD', continent: 'Africa', status: 'not_visited', lat: 15.4542, lng: 18.7322 },
            { id: 113, name: '中央アフリカ', name_en: 'Central African Republic', code: 'CAF', continent: 'Africa', status: 'not_visited', lat: 6.6111, lng: 20.9394 },
            { id: 114, name: 'ガボン', name_en: 'Gabon', code: 'GAB', continent: 'Africa', status: 'not_visited', lat: -0.8037, lng: 11.6094 },
            { id: 115, name: '赤道ギニア', name_en: 'Equatorial Guinea', code: 'GNQ', continent: 'Africa', status: 'not_visited', lat: 1.6508, lng: 10.2679 },
            { id: 116, name: 'サントメ・プリンシペ', name_en: 'Sao Tome and Principe', code: 'STP', continent: 'Africa', status: 'not_visited', lat: 0.1864, lng: 6.6131 },
            { id: 117, name: 'アンゴラ', name_en: 'Angola', code: 'AGO', continent: 'Africa', status: 'not_visited', lat: -11.2027, lng: 17.8739 },
            { id: 118, name: 'ザンビア', name_en: 'Zambia', code: 'ZMB', continent: 'Africa', status: 'not_visited', lat: -13.1339, lng: 27.8493 },
            { id: 119, name: 'ジンバブエ', name_en: 'Zimbabwe', code: 'ZWE', continent: 'Africa', status: 'not_visited', lat: -19.0154, lng: 29.1549 },
            { id: 120, name: 'ボツワナ', name_en: 'Botswana', code: 'BWA', continent: 'Africa', status: 'not_visited', lat: -22.3285, lng: 24.6849 },
            { id: 121, name: 'ナミビア', name_en: 'Namibia', code: 'NAM', continent: 'Africa', status: 'not_visited', lat: -22.9576, lng: 18.4904 },
            { id: 122, name: 'レソト', name_en: 'Lesotho', code: 'LSO', continent: 'Africa', status: 'not_visited', lat: -29.6100, lng: 28.2336 },
            { id: 123, name: 'スワジランド', name_en: 'Eswatini', code: 'SWZ', continent: 'Africa', status: 'not_visited', lat: -26.5225, lng: 31.4659 },
            { id: 124, name: 'マダガスカル', name_en: 'Madagascar', code: 'MDG', continent: 'Africa', status: 'not_visited', lat: -18.7669, lng: 46.8691 },
            { id: 125, name: 'モーリシャス', name_en: 'Mauritius', code: 'MUS', continent: 'Africa', status: 'not_visited', lat: -20.3484, lng: 57.5522 },
            { id: 126, name: 'セーシェル', name_en: 'Seychelles', code: 'SYC', continent: 'Africa', status: 'not_visited', lat: -4.6796, lng: 55.4920 },
            { id: 127, name: 'コモロ', name_en: 'Comoros', code: 'COM', continent: 'Africa', status: 'not_visited', lat: -11.8750, lng: 43.8722 },
            { id: 128, name: 'ジブチ', name_en: 'Djibouti', code: 'DJI', continent: 'Africa', status: 'not_visited', lat: 11.8251, lng: 42.5903 },
            { id: 129, name: 'ソマリア', name_en: 'Somalia', code: 'SOM', continent: 'Africa', status: 'not_visited', lat: 5.1521, lng: 46.1996 },
            { id: 130, name: 'エリトリア', name_en: 'Eritrea', code: 'ERI', continent: 'Africa', status: 'not_visited', lat: 15.1794, lng: 39.7823 },
            { id: 131, name: 'ルワンダ', name_en: 'Rwanda', code: 'RWA', continent: 'Africa', status: 'not_visited', lat: -1.9403, lng: 29.8739 },
            { id: 132, name: 'ブルンジ', name_en: 'Burundi', code: 'BDI', continent: 'Africa', status: 'not_visited', lat: -3.3731, lng: 29.9189 },
            { id: 133, name: 'マラウィ', name_en: 'Malawi', code: 'MWI', continent: 'Africa', status: 'not_visited', lat: -13.2543, lng: 34.3015 },
            { id: 134, name: 'モザンビーク', name_en: 'Mozambique', code: 'MOZ', continent: 'Africa', status: 'not_visited', lat: -18.6657, lng: 35.5296 },
            { id: 135, name: 'ザンビア', name_en: 'Zambia', code: 'ZMB', continent: 'Africa', status: 'not_visited', lat: -13.1339, lng: 27.8493 },
            { id: 136, name: 'マラウイ', name_en: 'Malawi', code: 'MWI', continent: 'Africa', status: 'not_visited', lat: -13.2543, lng: 34.3015 },
            { id: 137, name: 'モザンビーク', name_en: 'Mozambique', code: 'MOZ', continent: 'Africa', status: 'not_visited', lat: -18.6657, lng: 35.5296 },
            { id: 138, name: 'モーリタニア', name_en: 'Mauritania', code: 'MRT', continent: 'Africa', status: 'not_visited', lat: 21.0079, lng: -10.9408 },
            { id: 139, name: 'ガンビア', name_en: 'Gambia', code: 'GMB', continent: 'Africa', status: 'not_visited', lat: 13.4432, lng: -15.3101 },
            { id: 140, name: 'ギニアビサウ', name_en: 'Guinea-Bissau', code: 'GNB', continent: 'Africa', status: 'not_visited', lat: 11.8037, lng: -15.1804 },
            { id: 141, name: 'ギニア', name_en: 'Guinea', code: 'GIN', continent: 'Africa', status: 'not_visited', lat: 9.6412, lng: -10.0800 },
            { id: 142, name: 'シエラレオネ', name_en: 'Sierra Leone', code: 'SLE', continent: 'Africa', status: 'not_visited', lat: 8.4606, lng: -11.7799 },
            { id: 143, name: 'リベリア', name_en: 'Liberia', code: 'LBR', continent: 'Africa', status: 'not_visited', lat: 6.4281, lng: -9.4295 },
            { id: 144, name: 'コートジボワール', name_en: 'Ivory Coast', code: 'CIV', continent: 'Africa', status: 'not_visited', lat: 7.5400, lng: -5.5471 },
            { id: 145, name: 'トーゴ', name_en: 'Togo', code: 'TGO', continent: 'Africa', status: 'not_visited', lat: 8.6195, lng: 0.8248 },
            { id: 146, name: 'ベナン', name_en: 'Benin', code: 'BEN', continent: 'Africa', status: 'not_visited', lat: 9.3077, lng: 2.3158 },
            { id: 147, name: 'ニジェール', name_en: 'Niger', code: 'NER', continent: 'Africa', status: 'not_visited', lat: 17.6078, lng: 8.0817 },
            { id: 148, name: 'チャド', name_en: 'Chad', code: 'TCD', continent: 'Africa', status: 'not_visited', lat: 15.4542, lng: 18.7322 },
            { id: 149, name: '中央アフリカ', name_en: 'Central African Republic', code: 'CAF', continent: 'Africa', status: 'not_visited', lat: 6.6111, lng: 20.9394 },
            { id: 150, name: 'ガボン', name_en: 'Gabon', code: 'GAB', continent: 'Africa', status: 'not_visited', lat: -0.8037, lng: 11.6094 },
            { id: 151, name: '赤道ギニア', name_en: 'Equatorial Guinea', code: 'GNQ', continent: 'Africa', status: 'not_visited', lat: 1.6508, lng: 10.2679 },
            { id: 152, name: 'サントメ・プリンシペ', name_en: 'Sao Tome and Principe', code: 'STP', continent: 'Africa', status: 'not_visited', lat: 0.1864, lng: 6.6131 },
            { id: 153, name: 'アンゴラ', name_en: 'Angola', code: 'AGO', continent: 'Africa', status: 'not_visited', lat: -11.2027, lng: 17.8739 },
            { id: 154, name: 'ザンビア', name_en: 'Zambia', code: 'ZMB', continent: 'Africa', status: 'not_visited', lat: -13.1339, lng: 27.8493 },
            { id: 155, name: 'ジンバブエ', name_en: 'Zimbabwe', code: 'ZWE', continent: 'Africa', status: 'not_visited', lat: -19.0154, lng: 29.1549 },
            { id: 156, name: 'ボツワナ', name_en: 'Botswana', code: 'BWA', continent: 'Africa', status: 'not_visited', lat: -22.3285, lng: 24.6849 },
            { id: 157, name: 'ナミビア', name_en: 'Namibia', code: 'NAM', continent: 'Africa', status: 'not_visited', lat: -22.9576, lng: 18.4904 },
            { id: 158, name: 'レソト', name_en: 'Lesotho', code: 'LSO', continent: 'Africa', status: 'not_visited', lat: -29.6100, lng: 28.2336 },
            { id: 159, name: 'スワジランド', name_en: 'Eswatini', code: 'SWZ', continent: 'Africa', status: 'not_visited', lat: -26.5225, lng: 31.4659 },
            { id: 160, name: 'マダガスカル', name_en: 'Madagascar', code: 'MDG', continent: 'Africa', status: 'not_visited', lat: -18.7669, lng: 46.8691 },
            { id: 161, name: 'モーリシャス', name_en: 'Mauritius', code: 'MUS', continent: 'Africa', status: 'not_visited', lat: -20.3484, lng: 57.5522 },
            { id: 162, name: 'セーシェル', name_en: 'Seychelles', code: 'SYC', continent: 'Africa', status: 'not_visited', lat: -4.6796, lng: 55.4920 },
            { id: 163, name: 'コモロ', name_en: 'Comoros', code: 'COM', continent: 'Africa', status: 'not_visited', lat: -11.8750, lng: 43.8722 },
            { id: 164, name: 'ジブチ', name_en: 'Djibouti', code: 'DJI', continent: 'Africa', status: 'not_visited', lat: 11.8251, lng: 42.5903 },
            { id: 165, name: 'ソマリア', name_en: 'Somalia', code: 'SOM', continent: 'Africa', status: 'not_visited', lat: 5.1521, lng: 46.1996 },
            { id: 166, name: 'エリトリア', name_en: 'Eritrea', code: 'ERI', continent: 'Africa', status: 'not_visited', lat: 15.1794, lng: 39.7823 },
            { id: 167, name: 'ルワンダ', name_en: 'Rwanda', code: 'RWA', continent: 'Africa', status: 'not_visited', lat: -1.9403, lng: 29.8739 },
            { id: 168, name: 'ブルンジ', name_en: 'Burundi', code: 'BDI', continent: 'Africa', status: 'not_visited', lat: -3.3731, lng: 29.9189 },
            { id: 169, name: 'マラウィ', name_en: 'Malawi', code: 'MWI', continent: 'Africa', status: 'not_visited', lat: -13.2543, lng: 34.3015 },
            { id: 170, name: 'モザンビーク', name_en: 'Mozambique', code: 'MOZ', continent: 'Africa', status: 'not_visited', lat: -18.6657, lng: 35.5296 },
            { id: 171, name: 'ザンビア', name_en: 'Zambia', code: 'ZMB', continent: 'Africa', status: 'not_visited', lat: -13.1339, lng: 27.8493 },
            { id: 172, name: 'マラウイ', name_en: 'Malawi', code: 'MWI', continent: 'Africa', status: 'not_visited', lat: -13.2543, lng: 34.3015 },
            { id: 173, name: 'モザンビーク', name_en: 'Mozambique', code: 'MOZ', continent: 'Africa', status: 'not_visited', lat: -18.6657, lng: 35.5296 },
            { id: 174, name: 'モーリタニア', name_en: 'Mauritania', code: 'MRT', continent: 'Africa', status: 'not_visited', lat: 21.0079, lng: -10.9408 },
            { id: 175, name: 'ガンビア', name_en: 'Gambia', code: 'GMB', continent: 'Africa', status: 'not_visited', lat: 13.4432, lng: -15.3101 },
            { id: 176, name: 'ギニアビサウ', name_en: 'Guinea-Bissau', code: 'GNB', continent: 'Africa', status: 'not_visited', lat: 11.8037, lng: -15.1804 },
            { id: 177, name: 'ギニア', name_en: 'Guinea', code: 'GIN', continent: 'Africa', status: 'not_visited', lat: 9.6412, lng: -10.0800 },
            { id: 178, name: 'シエラレオネ', name_en: 'Sierra Leone', code: 'SLE', continent: 'Africa', status: 'not_visited', lat: 8.4606, lng: -11.7799 },
            { id: 179, name: 'リベリア', name_en: 'Liberia', code: 'LBR', continent: 'Africa', status: 'not_visited', lat: 6.4281, lng: -9.4295 },
            { id: 180, name: 'コートジボワール', name_en: 'Ivory Coast', code: 'CIV', continent: 'Africa', status: 'not_visited', lat: 7.5400, lng: -5.5471 },
            { id: 181, name: 'トーゴ', name_en: 'Togo', code: 'TGO', continent: 'Africa', status: 'not_visited', lat: 8.6195, lng: 0.8248 },
            { id: 182, name: 'ベナン', name_en: 'Benin', code: 'BEN', continent: 'Africa', status: 'not_visited', lat: 9.3077, lng: 2.3158 },
            { id: 183, name: 'ニジェール', name_en: 'Niger', code: 'NER', continent: 'Africa', status: 'not_visited', lat: 17.6078, lng: 8.0817 },
            { id: 184, name: 'チャド', name_en: 'Chad', code: 'TCD', continent: 'Africa', status: 'not_visited', lat: 15.4542, lng: 18.7322 },
            { id: 185, name: '中央アフリカ', name_en: 'Central African Republic', code: 'CAF', continent: 'Africa', status: 'not_visited', lat: 6.6111, lng: 20.9394 },
            { id: 186, name: 'ガボン', name_en: 'Gabon', code: 'GAB', continent: 'Africa', status: 'not_visited', lat: -0.8037, lng: 11.6094 },
            { id: 187, name: '赤道ギニア', name_en: 'Equatorial Guinea', code: 'GNQ', continent: 'Africa', status: 'not_visited', lat: 1.6508, lng: 10.2679 },
            { id: 188, name: 'サントメ・プリンシペ', name_en: 'Sao Tome and Principe', code: 'STP', continent: 'Africa', status: 'not_visited', lat: 0.1864, lng: 6.6131 },
            { id: 189, name: 'アンゴラ', name_en: 'Angola', code: 'AGO', continent: 'Africa', status: 'not_visited', lat: -11.2027, lng: 17.8739 },
            { id: 190, name: 'ザンビア', name_en: 'Zambia', code: 'ZMB', continent: 'Africa', status: 'not_visited', lat: -13.1339, lng: 27.8493 },
            { id: 191, name: 'ジンバブエ', name_en: 'Zimbabwe', code: 'ZWE', continent: 'Africa', status: 'not_visited', lat: -19.0154, lng: 29.1549 },
            { id: 192, name: 'ボツワナ', name_en: 'Botswana', code: 'BWA', continent: 'Africa', status: 'not_visited', lat: -22.3285, lng: 24.6849 },
            { id: 193, name: 'ナミビア', name_en: 'Namibia', code: 'NAM', continent: 'Africa', status: 'not_visited', lat: -22.9576, lng: 18.4904 },
            { id: 194, name: 'レソト', name_en: 'Lesotho', code: 'LSO', continent: 'Africa', status: 'not_visited', lat: -29.6100, lng: 28.2336 },
            { id: 195, name: 'スワジランド', name_en: 'Eswatini', code: 'SWZ', continent: 'Africa', status: 'not_visited', lat: -26.5225, lng: 31.4659 },
            { id: 196, name: 'マダガスカル', name_en: 'Madagascar', code: 'MDG', continent: 'Africa', status: 'not_visited', lat: -18.7669, lng: 46.8691 },
            { id: 197, name: 'モーリシャス', name_en: 'Mauritius', code: 'MUS', continent: 'Africa', status: 'not_visited', lat: -20.3484, lng: 57.5522 },
            { id: 198, name: 'セーシェル', name_en: 'Seychelles', code: 'SYC', continent: 'Africa', status: 'not_visited', lat: -4.6796, lng: 55.4920 },
            { id: 199, name: 'コモロ', name_en: 'Comoros', code: 'COM', continent: 'Africa', status: 'not_visited', lat: -11.8750, lng: 43.8722 },
            { id: 200, name: 'ジブチ', name_en: 'Djibouti', code: 'DJI', continent: 'Africa', status: 'not_visited', lat: 11.8251, lng: 42.5903 },
            
            // オセアニア
            { id: 12, name: 'オーストラリア', name_en: 'Australia', code: 'AUS', continent: 'Oceania', status: 'not_visited', lat: -25.2744, lng: 133.7751 },
            { id: 201, name: 'ニュージーランド', name_en: 'New Zealand', code: 'NZL', continent: 'Oceania', status: 'not_visited', lat: -40.9006, lng: 174.8860 },
            { id: 202, name: 'パプアニューギニア', name_en: 'Papua New Guinea', code: 'PNG', continent: 'Oceania', status: 'not_visited', lat: -6.3150, lng: 143.9555 },
            { id: 203, name: 'フィジー', name_en: 'Fiji', code: 'FJI', continent: 'Oceania', status: 'not_visited', lat: -16.5780, lng: 179.4144 },
            { id: 204, name: 'ソロモン諸島', name_en: 'Solomon Islands', code: 'SLB', continent: 'Oceania', status: 'not_visited', lat: -9.6457, lng: 160.1562 },
            { id: 205, name: 'バヌアツ', name_en: 'Vanuatu', code: 'VUT', continent: 'Oceania', status: 'not_visited', lat: -15.3767, lng: 166.9592 },
            { id: 206, name: 'サモア', name_en: 'Samoa', code: 'WSM', continent: 'Oceania', status: 'not_visited', lat: -13.7590, lng: -172.1046 },
            { id: 207, name: 'トンガ', name_en: 'Tonga', code: 'TON', continent: 'Oceania', status: 'not_visited', lat: -21.1789, lng: -175.1982 },
            { id: 208, name: 'キリバス', name_en: 'Kiribati', code: 'KIR', continent: 'Oceania', status: 'not_visited', lat: -3.3704, lng: -168.7340 },
            { id: 209, name: 'ツバル', name_en: 'Tuvalu', code: 'TUV', continent: 'Oceania', status: 'not_visited', lat: -7.1095, lng: 177.6493 },
            { id: 210, name: 'ナウル', name_en: 'Nauru', code: 'NRU', continent: 'Oceania', status: 'not_visited', lat: -0.5228, lng: 166.9315 },
            { id: 211, name: 'パラオ', name_en: 'Palau', code: 'PLW', continent: 'Oceania', status: 'not_visited', lat: 7.5150, lng: 134.5825 },
            { id: 212, name: 'マーシャル諸島', name_en: 'Marshall Islands', code: 'MHL', continent: 'Oceania', status: 'not_visited', lat: 7.1315, lng: 171.1845 },
            { id: 213, name: 'ミクロネシア', name_en: 'Micronesia', code: 'FSM', continent: 'Oceania', status: 'not_visited', lat: 7.4256, lng: 150.5508 },
            { id: 214, name: 'クック諸島', name_en: 'Cook Islands', code: 'COK', continent: 'Oceania', status: 'not_visited', lat: -21.2367, lng: -159.7777 },
            { id: 215, name: 'ニウエ', name_en: 'Niue', code: 'NIU', continent: 'Oceania', status: 'not_visited', lat: -19.0544, lng: -169.8672 },
            { id: 216, name: 'トケラウ', name_en: 'Tokelau', code: 'TKL', continent: 'Oceania', status: 'not_visited', lat: -8.9674, lng: -171.8559 },
            { id: 217, name: 'ピトケアン諸島', name_en: 'Pitcairn Islands', code: 'PCN', continent: 'Oceania', status: 'not_visited', lat: -24.7036, lng: -127.4393 },
            { id: 218, name: 'フランス領ポリネシア', name_en: 'French Polynesia', code: 'PYF', continent: 'Oceania', status: 'not_visited', lat: -17.6797, lng: -149.4068 },
            { id: 219, name: 'ニューカレドニア', name_en: 'New Caledonia', code: 'NCL', continent: 'Oceania', status: 'not_visited', lat: -20.9043, lng: 165.6180 },
            { id: 220, name: 'ウォリス・フツナ', name_en: 'Wallis and Futuna', code: 'WLF', continent: 'Oceania', status: 'not_visited', lat: -13.7687, lng: -177.1561 },
            { id: 221, name: 'アメリカ領サモア', name_en: 'American Samoa', code: 'ASM', continent: 'Oceania', status: 'not_visited', lat: -14.2710, lng: -170.1322 },
            { id: 222, name: 'グアム', name_en: 'Guam', code: 'GUM', continent: 'Oceania', status: 'not_visited', lat: 13.4443, lng: 144.7937 },
            { id: 223, name: '北マリアナ諸島', name_en: 'Northern Mariana Islands', code: 'MNP', continent: 'Oceania', status: 'not_visited', lat: 17.3308, lng: 145.3846 },
            { id: 224, name: 'ウェーク島', name_en: 'Wake Island', code: 'UMI', continent: 'Oceania', status: 'not_visited', lat: 19.2823, lng: 166.6470 },
            { id: 225, name: 'ジョンストン島', name_en: 'Johnston Atoll', code: 'UMI', continent: 'Oceania', status: 'not_visited', lat: 16.7295, lng: -169.5336 },
            { id: 226, name: 'ミッドウェー島', name_en: 'Midway Islands', code: 'UMI', continent: 'Oceania', status: 'not_visited', lat: 28.2072, lng: -177.3733 },
            { id: 227, name: 'ハワイ', name_en: 'Hawaii', code: 'USA', continent: 'Oceania', status: 'not_visited', lat: 19.8968, lng: -155.5828 },
            { id: 228, name: 'アラスカ', name_en: 'Alaska', code: 'USA', continent: 'North America', status: 'not_visited', lat: 64.2008, lng: -149.4937 },
            { id: 229, name: 'グリーンランド', name_en: 'Greenland', code: 'GRL', continent: 'North America', status: 'not_visited', lat: 71.7069, lng: -42.6043 },
            { id: 230, name: 'フェロー諸島', name_en: 'Faroe Islands', code: 'FRO', continent: 'Europe', status: 'not_visited', lat: 61.8926, lng: -6.9118 },
            { id: 231, name: 'スバルバル諸島', name_en: 'Svalbard', code: 'SJM', continent: 'Europe', status: 'not_visited', lat: 77.8750, lng: 20.9752 },
            { id: 232, name: 'ヤンマイエン島', name_en: 'Jan Mayen', code: 'SJM', continent: 'Europe', status: 'not_visited', lat: 70.9756, lng: -8.6670 },
            { id: 233, name: 'ブーベ島', name_en: 'Bouvet Island', code: 'BVT', continent: 'Antarctica', status: 'not_visited', lat: -54.4208, lng: 3.3464 },
            { id: 234, name: 'ハード島・マクドナルド諸島', name_en: 'Heard Island and McDonald Islands', code: 'HMD', continent: 'Antarctica', status: 'not_visited', lat: -53.0818, lng: 73.5042 },
            { id: 235, name: 'フランス領南方・南極地域', name_en: 'French Southern and Antarctic Lands', code: 'ATF', continent: 'Antarctica', status: 'not_visited', lat: -49.2804, lng: 69.3486 },
            { id: 236, name: '南極', name_en: 'Antarctica', code: 'ATA', continent: 'Antarctica', status: 'not_visited', lat: -75.2509, lng: -0.0713 },
            { id: 237, name: '南ジョージア・南サンドイッチ諸島', name_en: 'South Georgia and the South Sandwich Islands', code: 'SGS', continent: 'Antarctica', status: 'not_visited', lat: -54.4296, lng: -36.5879 },
            { id: 238, name: 'フォークランド諸島', name_en: 'Falkland Islands', code: 'FLK', continent: 'South America', status: 'not_visited', lat: -51.7963, lng: -59.5236 },
            { id: 239, name: '南極', name_en: 'Antarctica', code: 'ATA', continent: 'Antarctica', status: 'not_visited', lat: -75.2509, lng: -0.0713 },
            { id: 240, name: '南極', name_en: 'Antarctica', code: 'ATA', continent: 'Antarctica', status: 'not_visited', lat: -75.2509, lng: -0.0713 },
            { id: 241, name: '南極', name_en: 'Antarctica', code: 'ATA', continent: 'Antarctica', status: 'not_visited', lat: -75.2509, lng: -0.0713 },
            { id: 242, name: '南極', name_en: 'Antarctica', code: 'ATA', continent: 'Antarctica', status: 'not_visited', lat: -75.2509, lng: -0.0713 },
            { id: 243, name: '南極', name_en: 'Antarctica', code: 'ATA', continent: 'Antarctica', status: 'not_visited', lat: -75.2509, lng: -0.0713 },
            { id: 244, name: '南極', name_en: 'Antarctica', code: 'ATA', continent: 'Antarctica', status: 'not_visited', lat: -75.2509, lng: -0.0713 },
            { id: 245, name: '南極', name_en: 'Antarctica', code: 'ATA', continent: 'Antarctica', status: 'not_visited', lat: -75.2509, lng: -0.0713 },
            { id: 246, name: '南極', name_en: 'Antarctica', code: 'ATA', continent: 'Antarctica', status: 'not_visited', lat: -75.2509, lng: -0.0713 },
            { id: 247, name: '南極', name_en: 'Antarctica', code: 'ATA', continent: 'Antarctica', status: 'not_visited', lat: -75.2509, lng: -0.0713 },
            { id: 248, name: '南極', name_en: 'Antarctica', code: 'ATA', continent: 'Antarctica', status: 'not_visited', lat: -75.2509, lng: -0.0713 },
            { id: 249, name: '南極', name_en: 'Antarctica', code: 'ATA', continent: 'Antarctica', status: 'not_visited', lat: -75.2509, lng: -0.0713 },
            { id: 250, name: '南極', name_en: 'Antarctica', code: 'ATA', continent: 'Antarctica', status: 'not_visited', lat: -75.2509, lng: -0.0713 }
        ];

        // サーバーから取得したステータスで国データを更新
        const countries = countriesData.map(country => ({
            ...country,
            status: userStatuses[country.id] || country.status
        }));

        let currentCountryId = null;
        let map = null;
        let countryLayer = null;
        let savedZoom = 2;
        let savedCenter = [20, 0];

        // 統計情報を更新
        function updateStats() {
            const stats = countries.reduce((acc, country) => {
                if (country.status !== 'not_visited') {
                    acc.visited++;
                }
                acc[country.status]++;
                return acc;
            }, { visited: 0, lived: 0, stayed: 0, visited: 0, passed: 0, not_visited: 0 });

            document.getElementById('visited-count').textContent = stats.visited;
            document.getElementById('lived-count').textContent = stats.lived;
            document.getElementById('stayed-count').textContent = stats.stayed;
            document.getElementById('visited-day-count').textContent = stats.visited;
        }

        // 国の色を取得
        function getCountryColor(status) {
            const colors = {
                'lived': '#10b981',
                'stayed': '#3b82f6',
                'visited': '#eab308',
                'passed': '#f97316',
                'not_visited': '#9ca3af'
            };
            return colors[status] || '#9ca3af';
        }

        // Leaflet地図を初期化
        function initMap() {
            // 地図を初期化（背景レイヤーなし）
            map = L.map('world-map', {
                zoomControl: true,
                attributionControl: false
            }).setView(savedCenter, savedZoom);

            // 地図の移動・ズーム時に位置を保存
            map.on('moveend zoomend', function() {
                savedCenter = map.getCenter();
                savedZoom = map.getZoom();
            });

            // 国境データを読み込み
            loadCountryData();

            // 凡例を追加
            const legend = L.control({position: 'bottomleft'});
            legend.onAdd = function(map) {
                const div = L.DomUtil.create('div', 'bg-white p-3 rounded shadow-lg');
                div.innerHTML = `
                    <h4 class="font-bold mb-2">凡例</h4>
                    <div class="space-y-1 text-sm">
                        <div class="flex items-center">
                            <div class="w-3 h-3 bg-green-500 rounded-full mr-2"></div>
                            <span>住んだことがある</span>
                        </div>
                        <div class="flex items-center">
                            <div class="w-3 h-3 bg-blue-500 rounded-full mr-2"></div>
                            <span>宿泊したことがある</span>
                        </div>
                        <div class="flex items-center">
                            <div class="w-3 h-3 bg-yellow-500 rounded-full mr-2"></div>
                            <span>日帰りで訪れた</span>
                        </div>
                        <div class="flex items-center">
                            <div class="w-3 h-3 bg-orange-500 rounded-full mr-2"></div>
                            <span>通ったことがある</span>
                        </div>
                        <div class="flex items-center">
                            <div class="w-3 h-3 bg-gray-400 rounded-full mr-2"></div>
                            <span>行ったことがない</span>
                        </div>
                    </div>
                `;
                return div;
            };
            legend.addTo(map);
        }

        // 国境データを読み込み
        async function loadCountryData() {
            try {
                // Natural Earth Dataから国境データを取得
                const response = await fetch('https://raw.githubusercontent.com/holtzy/D3-graph-gallery/master/DATA/world.geojson');
                const worldData = await response.json();
                
                // 既存の国境レイヤーを削除
                if (countryLayer) {
                    map.removeLayer(countryLayer);
                }
                
                // すべての国をcountries配列に追加（まだ存在しない場合のみ）
                worldData.features.forEach(feature => {
                    const countryName = feature.properties.NAME || feature.properties.name;
                    const existingCountry = countries.find(c => c.name_en === countryName);
                    
                    if (!existingCountry) {
                        // 新しい国を追加
                        const newCountry = {
                            id: countries.length + 1,
                            name: countryName,
                            name_en: countryName,
                            code: feature.properties.ISO_A3 || feature.properties.iso_a3 || 'UNK',
                            continent: feature.properties.CONTINENT || feature.properties.continent || 'Unknown',
                            status: 'not_visited',
                            lat: 0,
                            lng: 0
                        };
                        countries.push(newCountry);
                    }
                });

                // 国境レイヤーを作成（すべての国を含める）
                countryLayer = L.geoJSON(worldData.features, {
                    style: function(feature) {
                        const countryName = feature.properties.NAME || feature.properties.name;
                        const country = countries.find(c => c.name_en === countryName);
                        const status = country ? country.status : 'not_visited';
                        
                        return {
                            fillColor: getCountryColor(status),
                            weight: 1,
                            opacity: 1,
                            color: '#333',
                            dashArray: '',
                            fillOpacity: 0.8
                        };
                    },
                    onEachFeature: function(feature, layer) {
                        const countryName = feature.properties.NAME || feature.properties.name;
                        const country = countries.find(c => c.name_en === countryName);
                        
                        // すべての国でクリック可能にする
                        const popup = L.popup({
                            className: 'custom-popup',
                            closeButton: true,
                            autoClose: true,
                            closeOnClick: false
                        }).setContent(`
                            <div class="text-center">
                                <h3 class="font-bold text-lg">${country ? country.name : countryName}</h3>
                                <p class="text-sm text-gray-600">${countryName}</p>
                                <p class="text-xs text-gray-500 mt-1">${country ? country.continent : '未登録の国'}</p>
                                <button onclick="showCountryModal('${countryName}', '${countryName}')" 
                                        class="mt-2 px-3 py-1 bg-indigo-600 text-white rounded text-sm hover:bg-indigo-700">
                                    詳細を表示
                                </button>
                            </div>
                        `);
                        
                        layer.bindPopup(popup);
                        
                        layer.on('click', function(e) {
                            e.originalEvent.stopPropagation();
                            showCountryModal(countryName, countryName);
                        });
                    }
                }).addTo(map);

                // 初回のみ地図を国境に合わせて調整
                if (savedZoom === 2 && savedCenter[0] === 20 && savedCenter[1] === 0) {
                    map.fitBounds(countryLayer.getBounds());
                } else {
                    // 保存された位置とズームレベルを維持
                    map.setView(savedCenter, savedZoom);
                }
                
            } catch (error) {
                console.error('Error loading country data:', error);
                // フォールバック: 簡易的な長方形を使用
                loadSimpleCountryData();
            }
        }

        // フォールバック用の簡易データ
        function loadSimpleCountryData() {
            const countryBoundaries = {
                "type": "FeatureCollection",
                "features": [
                    {
                        "type": "Feature",
                        "properties": {"name": "Japan", "name_en": "Japan", "code": "JPN"},
                        "geometry": {
                            "type": "Polygon",
                            "coordinates": [[[129.0, 31.0], [146.0, 31.0], [146.0, 46.0], [129.0, 46.0], [129.0, 31.0]]]
                        }
                    },
                    {
                        "type": "Feature", 
                        "properties": {"name": "United States", "name_en": "United States", "code": "USA"},
                        "geometry": {
                            "type": "Polygon",
                            "coordinates": [[[-125.0, 25.0], [-66.0, 25.0], [-66.0, 49.0], [-125.0, 49.0], [-125.0, 25.0]]]
                        }
                    },
                    {
                        "type": "Feature",
                        "properties": {"name": "China", "name_en": "China", "code": "CHN"},
                        "geometry": {
                            "type": "Polygon", 
                            "coordinates": [[[73.0, 18.0], [135.0, 18.0], [135.0, 54.0], [73.0, 54.0], [73.0, 18.0]]]
                        }
                    },
                    {
                        "type": "Feature",
                        "properties": {"name": "France", "name_en": "France", "code": "FRA"},
                        "geometry": {
                            "type": "Polygon",
                            "coordinates": [[[-5.0, 42.0], [8.0, 42.0], [8.0, 51.0], [-5.0, 51.0], [-5.0, 42.0]]]
                        }
                    },
                    {
                        "type": "Feature",
                        "properties": {"name": "Germany", "name_en": "Germany", "code": "DEU"},
                        "geometry": {
                            "type": "Polygon",
                            "coordinates": [[[6.0, 47.0], [15.0, 47.0], [15.0, 55.0], [6.0, 55.0], [6.0, 47.0]]]
                        }
                    },
                    {
                        "type": "Feature",
                        "properties": {"name": "Italy", "name_en": "Italy", "code": "ITA"},
                        "geometry": {
                            "type": "Polygon",
                            "coordinates": [[[7.0, 36.0], [19.0, 36.0], [19.0, 47.0], [7.0, 47.0], [7.0, 36.0]]]
                        }
                    },
                    {
                        "type": "Feature",
                        "properties": {"name": "United Kingdom", "name_en": "United Kingdom", "code": "GBR"},
                        "geometry": {
                            "type": "Polygon",
                            "coordinates": [[[-8.0, 50.0], [2.0, 50.0], [2.0, 61.0], [-8.0, 61.0], [-8.0, 50.0]]]
                        }
                    },
                    {
                        "type": "Feature",
                        "properties": {"name": "Spain", "name_en": "Spain", "code": "ESP"},
                        "geometry": {
                            "type": "Polygon",
                            "coordinates": [[[-9.0, 36.0], [4.0, 36.0], [4.0, 44.0], [-9.0, 44.0], [-9.0, 36.0]]]
                        }
                    },
                    {
                        "type": "Feature",
                        "properties": {"name": "Russia", "name_en": "Russia", "code": "RUS"},
                        "geometry": {
                            "type": "Polygon",
                            "coordinates": [[[20.0, 41.0], [180.0, 41.0], [180.0, 82.0], [20.0, 82.0], [20.0, 41.0]]]
                        }
                    },
                    {
                        "type": "Feature",
                        "properties": {"name": "India", "name_en": "India", "code": "IND"},
                        "geometry": {
                            "type": "Polygon",
                            "coordinates": [[[68.0, 7.0], [97.0, 7.0], [97.0, 36.0], [68.0, 36.0], [68.0, 7.0]]]
                        }
                    },
                    {
                        "type": "Feature",
                        "properties": {"name": "Brazil", "name_en": "Brazil", "code": "BRA"},
                        "geometry": {
                            "type": "Polygon",
                            "coordinates": [[[-74.0, -34.0], [-35.0, -34.0], [-35.0, 5.0], [-74.0, 5.0], [-74.0, -34.0]]]
                        }
                    },
                    {
                        "type": "Feature",
                        "properties": {"name": "Australia", "name_en": "Australia", "code": "AUS"},
                        "geometry": {
                            "type": "Polygon",
                            "coordinates": [[[113.0, -44.0], [154.0, -44.0], [154.0, -10.0], [113.0, -10.0], [113.0, -44.0]]]
                        }
                    },
                    {
                        "type": "Feature",
                        "properties": {"name": "Canada", "name_en": "Canada", "code": "CAN"},
                        "geometry": {
                            "type": "Polygon",
                            "coordinates": [[[-141.0, 42.0], [-53.0, 42.0], [-53.0, 83.0], [-141.0, 83.0], [-141.0, 42.0]]]
                        }
                    },
                    {
                        "type": "Feature",
                        "properties": {"name": "Mexico", "name_en": "Mexico", "code": "MEX"},
                        "geometry": {
                            "type": "Polygon",
                            "coordinates": [[[-118.0, 15.0], [-87.0, 15.0], [-87.0, 33.0], [-118.0, 33.0], [-118.0, 15.0]]]
                        }
                    },
                    {
                        "type": "Feature",
                        "properties": {"name": "South Korea", "name_en": "South Korea", "code": "KOR"},
                        "geometry": {
                            "type": "Polygon",
                            "coordinates": [[[125.0, 33.0], [132.0, 33.0], [132.0, 39.0], [125.0, 39.0], [125.0, 33.0]]]
                        }
                    }
                ]
            };

            // 既存の国境レイヤーを削除
            if (countryLayer) {
                map.removeLayer(countryLayer);
            }
            
            // 国境レイヤーを作成
            countryLayer = L.geoJSON(countryBoundaries, {
                style: function(feature) {
                    const country = countries.find(c => c.name_en === feature.properties.name_en);
                    const status = country ? country.status : 'not_visited';
                    
                    return {
                        fillColor: getCountryColor(status),
                        weight: 1,
                        opacity: 1,
                        color: '#333',
                        dashArray: '',
                        fillOpacity: 0.8
                    };
                },
                onEachFeature: function(feature, layer) {
                    const country = countries.find(c => c.name_en === feature.properties.name_en);
                    console.log('Processing country:', feature.properties.name_en, 'Found:', !!country);
                    
                    if (country) {
                        const popup = L.popup({
                            className: 'custom-popup',
                            closeButton: true,
                            autoClose: true,
                            closeOnClick: false
                        }).setContent(`
                            <div class="text-center">
                                <h3 class="font-bold text-lg">${country.name}</h3>
                                <p class="text-sm text-gray-600">${country.name_en}</p>
                                <p class="text-xs text-gray-500 mt-1">${country.continent}</p>
                                <button onclick="showCountryModal(${country.id}, '${country.name}')" 
                                        class="mt-2 px-3 py-1 bg-indigo-600 text-white rounded text-sm hover:bg-indigo-700">
                                    詳細を表示
                                </button>
                            </div>
                        `);
                        
                        layer.bindPopup(popup);
                        
                        layer.on('click', function(e) {
                            console.log('Country clicked:', country.name);
                            e.originalEvent.stopPropagation();
                            showCountryModal(country.id, country.name);
                        });
                    }
                }
            }).addTo(map);

            // 初回のみ地図を国境に合わせて調整
            if (savedZoom === 2 && savedCenter[0] === 20 && savedCenter[1] === 0) {
                map.fitBounds(countryLayer.getBounds());
            } else {
                // 保存された位置とズームレベルを維持
                map.setView(savedCenter, savedZoom);
            }
        }

        // 国モーダルを表示
        function showCountryModal(countryId, countryName) {
            // 国名で国を検索（IDが数値でない場合）
            let country = null;
            if (typeof countryId === 'number') {
                country = countries.find(c => c.id === countryId);
            } else {
                country = countries.find(c => c.name_en === countryName);
            }
            
            currentCountryId = country ? country.id : countryName;
            document.getElementById('modal-country-name').textContent = countryName;
            
            // 国が見つかった場合は詳細ページリンクを表示
            const detailLink = document.getElementById('modal-detail-link');
            if (country) {
                detailLink.href = `/countries/${country.id}`;
                detailLink.style.display = 'block';
            } else {
                detailLink.style.display = 'none';
            }
            
            document.getElementById('country-modal').classList.remove('hidden');
        }

        // 国ステータスを更新
        function updateCountryStatus(status) {
            if (!currentCountryId) return;
            
            // IDまたは国名で国を検索
            const country = countries.find(c => c.id === currentCountryId || c.name_en === currentCountryId);
            if (country) {
                // サーバーにステータスを保存
                fetch(`/countries/${country.id}/status`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        status: status
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // 成功した場合のみローカル状態を更新
                        country.status = status;
                        updateStats();
                        
                        // 国境データを再読み込み
                        loadCountryData();
                        
                        document.getElementById('country-modal').classList.add('hidden');
                    } else {
                        alert('ステータスの更新に失敗しました');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('エラーが発生しました');
                });
            }
        }

        // 検索機能
        function searchCountries(query) {
            const results = countries.filter(country => 
                country.name.includes(query) || country.name_en.toLowerCase().includes(query.toLowerCase())
            );
            
            const resultsContainer = document.getElementById('search-results');
            if (results.length === 0) {
                resultsContainer.innerHTML = '<p class="text-gray-500 dark:text-gray-400">該当する国が見つかりませんでした。</p>';
            } else {
                resultsContainer.innerHTML = results.map(country => `
                    <div class="flex items-center justify-between p-3 border border-gray-200 dark:border-gray-600 rounded-md mb-2">
                        <div>
                            <div class="font-medium text-gray-900 dark:text-gray-100">${country.name}</div>
                            <div class="text-sm text-gray-500 dark:text-gray-400">${country.name_en}</div>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="inline-block w-3 h-3 rounded-full" style="background-color: ${getCountryColor(country.status)}"></span>
                            <button onclick="showCountryModal(${country.id}, '${country.name}')" 
                                    class="px-3 py-1 bg-indigo-600 text-white rounded text-sm hover:bg-indigo-700">
                                編集
                            </button>
                        </div>
                    </div>
                `).join('');
            }
            resultsContainer.classList.remove('hidden');
        }

        // イベントリスナー
        document.addEventListener('DOMContentLoaded', function() {
            updateStats();
            initMap();

            // モーダル関連
            document.getElementById('modal-close').addEventListener('click', function() {
                document.getElementById('country-modal').classList.add('hidden');
            });

            document.querySelectorAll('[data-status]').forEach(button => {
                button.addEventListener('click', function() {
                    updateCountryStatus(this.dataset.status);
                });
            });

            // 検索機能
            document.getElementById('search-btn').addEventListener('click', function() {
                const query = document.getElementById('country-search').value.trim();
                if (query) {
                    searchCountries(query);
                }
            });

            document.getElementById('country-search').addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    const query = this.value.trim();
                    if (query) {
                        searchCountries(query);
                    }
                }
            });
        });
    </script>
</x-app-layout>