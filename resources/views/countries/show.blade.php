<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ $country->name }} ({{ $country->name_en }})
            </h2>
            <a href="{{ route('dashboard') }}" class="text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 dark:hover:text-indigo-300">
                ← ダッシュボードに戻る
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            {{-- 国情報セクション --}}
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-8">
                <div class="relative">
                    @php
                        $countryImages = [
                            'JPN' => 'images/countries/japan.jpg',
                            'USA' => 'images/countries/usa.jpg',
                            'GBR' => 'images/countries/uk.jpg',
                            'FRA' => 'images/countries/france.jpg',
                            'DEU' => 'images/countries/germany.jpg',
                        ];
                        $imagePath = $countryImages[$country->code] ?? null;
                    @endphp
                    
                    @if($imagePath && file_exists(public_path($imagePath)))
                        <div class="h-96 bg-cover bg-center" style="background-image: url('{{ asset($imagePath) }}')">
                            <div class="absolute inset-0 bg-black bg-opacity-40"></div>
                            <div class="relative z-10 p-6 pb-8">
                                <h1 class="text-3xl font-bold text-white mb-2">{{ $country->name }}</h1>
                                <h2 class="text-xl text-white opacity-90 mb-4">{{ $country->name_en }}</h2>
                    @elseif($country->background_image)
                        <div class="h-96 bg-cover bg-center" style="background-image: url('{{ asset('storage/' . $country->background_image) }}')">
                            <div class="absolute inset-0 bg-black bg-opacity-40"></div>
                            <div class="relative z-10 p-6 pb-8">
                                <h1 class="text-3xl font-bold text-white mb-2">{{ $country->name }}</h1>
                                <h2 class="text-xl text-white opacity-90 mb-4">{{ $country->name_en }}</h2>
                    @else
                        <div class="h-96 bg-gradient-to-r from-blue-500 to-purple-600">
                            <div class="absolute inset-0 bg-black bg-opacity-20"></div>
                            <div class="relative z-10 p-6 pb-8">
                                <h1 class="text-3xl font-bold text-white mb-2">{{ $country->name }}</h1>
                                <h2 class="text-xl text-white opacity-90 mb-4">{{ $country->name_en }}</h2>
                    @endif
                        
                        {{-- 国ステータス --}}
                        <div class="mb-4">
                            @if($userStatus)
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                                    @if($userStatus->status === 'lived') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                                    @elseif($userStatus->status === 'stayed') bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200
                                    @elseif($userStatus->status === 'visited') bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200
                                    @elseif($userStatus->status === 'passed') bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200
                                    @else bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200
                                    @endif">
                                    {{ $userStatus->status_label }}
                                </span>
                            @else
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200">
                                    行ったことがない
                                </span>
                            @endif
                        </div>
                            </div>
                        </div>
                </div>
                
                <div class="p-6">
                    {{-- 国情報カード --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-6">
                        @if($country->capital)
                            <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                                <h3 class="font-medium text-gray-900 dark:text-gray-100 mb-2">首都</h3>
                                <p class="text-gray-600 dark:text-gray-300">{{ $country->capital }}</p>
                            </div>
                        @endif
                        
                        @if($country->population)
                            <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                                <h3 class="font-medium text-gray-900 dark:text-gray-100 mb-2">人口</h3>
                                <p class="text-gray-600 dark:text-gray-300">{{ number_format($country->population) }}人</p>
                            </div>
                        @endif
                        
                        @if($country->continent)
                            <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                                <h3 class="font-medium text-gray-900 dark:text-gray-100 mb-2">大陸</h3>
                                <p class="text-gray-600 dark:text-gray-300">{{ $country->continent }}</p>
                            </div>
                        @endif
                        
                        @if($country->currency)
                            <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                                <h3 class="font-medium text-gray-900 dark:text-gray-100 mb-2">通貨</h3>
                                <p class="text-gray-600 dark:text-gray-300">{{ $country->currency }}</p>
                            </div>
                        @endif
                        
                        @if($country->languages && is_array($country->languages))
                            <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                                <h3 class="font-medium text-gray-900 dark:text-gray-100 mb-2">公用語</h3>
                                <p class="text-gray-600 dark:text-gray-300">{{ implode(', ', $country->languages) }}</p>
                            </div>
                        @endif
                    </div>
                    
                    @if($country->description)
                        <div class="mb-6">
                            <h3 class="font-medium text-gray-900 dark:text-gray-100 mb-2">説明</h3>
                            <p class="text-gray-600 dark:text-gray-300">{{ $country->description }}</p>
                        </div>
                    @endif
                    
                    {{-- ステータス変更ボタン --}}
                    <div class="flex gap-2">
                        <button onclick="showStatusModal()" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                            ステータスを変更
                        </button>
                    </div>
                </div>
            </div>

            {{-- 写真セクション --}}
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">写真</h3>
                        <button onclick="showPhotoModal()" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
                            写真を追加
                        </button>
                    </div>
                    
                    @if($photos->count() > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6" id="photos-grid">
                            @foreach($photos as $photo)
                                <div class="photo-item bg-gray-50 dark:bg-gray-700 rounded-lg overflow-hidden">
                                    <img src="{{ Storage::url($photo->image_path) }}" alt="{{ $photo->caption }}" class="w-full h-48 object-cover">
                                    <div class="p-4">
                                        @if($photo->caption)
                                            <h4 class="font-medium text-gray-900 dark:text-gray-100 mb-2">{{ $photo->caption }}</h4>
                                        @endif
                                        @if($photo->description)
                                            <p class="text-sm text-gray-600 dark:text-gray-300 mb-2">{{ $photo->description }}</p>
                                        @endif
                                        @if($photo->location)
                                            <p class="text-xs text-gray-500 dark:text-gray-400 mb-2">📍 {{ $photo->location }}</p>
                                        @endif
                                        @if($photo->taken_at)
                                            <p class="text-xs text-gray-500 dark:text-gray-400 mb-3">{{ $photo->taken_at->format('Y年m月d日') }}</p>
                                        @endif
                                        <button onclick="deletePhoto({{ $photo->id }})" class="text-red-600 hover:text-red-800 text-sm">
                                            削除
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-12">
                            <svg class="w-16 h-16 mx-auto mb-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            <p class="text-gray-500 dark:text-gray-400">まだ写真がありません</p>
                            <p class="text-sm text-gray-400 dark:text-gray-500">「写真を追加」ボタンから最初の写真を追加しましょう</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- ステータス変更モーダル --}}
    <div id="status-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white dark:bg-gray-800">
            <div class="mt-3">
                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">ステータスを変更</h3>
                <form id="status-form">
                    <div class="space-y-2 mb-4">
                        <label class="flex items-center">
                            <input type="radio" name="status" value="lived" class="mr-3" {{ $userStatus && $userStatus->status === 'lived' ? 'checked' : '' }}>
                            <span class="inline-block w-3 h-3 bg-green-500 rounded-full mr-3"></span>
                            住んだことがある
                        </label>
                        <label class="flex items-center">
                            <input type="radio" name="status" value="stayed" class="mr-3" {{ $userStatus && $userStatus->status === 'stayed' ? 'checked' : '' }}>
                            <span class="inline-block w-3 h-3 bg-blue-500 rounded-full mr-3"></span>
                            宿泊したことがある
                        </label>
                        <label class="flex items-center">
                            <input type="radio" name="status" value="visited" class="mr-3" {{ $userStatus && $userStatus->status === 'visited' ? 'checked' : '' }}>
                            <span class="inline-block w-3 h-3 bg-yellow-500 rounded-full mr-3"></span>
                            日帰りで訪れたことがある
                        </label>
                        <label class="flex items-center">
                            <input type="radio" name="status" value="passed" class="mr-3" {{ $userStatus && $userStatus->status === 'passed' ? 'checked' : '' }}>
                            <span class="inline-block w-3 h-3 bg-orange-500 rounded-full mr-3"></span>
                            通ったことがある
                        </label>
                        <label class="flex items-center">
                            <input type="radio" name="status" value="not_visited" class="mr-3" {{ !$userStatus || $userStatus->status === 'not_visited' ? 'checked' : '' }}>
                            <span class="inline-block w-3 h-3 bg-gray-400 rounded-full mr-3"></span>
                            行ったことがない
                        </label>
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">メモ</label>
                        <textarea name="notes" rows="3" class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ $userStatus ? $userStatus->notes : '' }}</textarea>
                    </div>
                    <div class="flex justify-end gap-2">
                        <button type="button" onclick="hideStatusModal()" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                            キャンセル
                        </button>
                        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                            保存
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- 写真追加モーダル --}}
    <div id="photo-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white dark:bg-gray-800">
            <div class="mt-3">
                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">写真を追加</h3>
                <form id="photo-form" enctype="multipart/form-data">
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">画像ファイル</label>
                        <input type="file" name="image" accept="image/*" required class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">キャプション</label>
                        <input type="text" name="caption" class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">説明</label>
                        <textarea name="description" rows="3" class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">撮影場所</label>
                        <input type="text" name="location" class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">撮影日</label>
                        <input type="date" name="taken_at" class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                    <div class="flex justify-end gap-2">
                        <button type="button" onclick="hidePhotoModal()" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                            キャンセル
                        </button>
                        <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
                            追加
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        // ステータスモーダル
        function showStatusModal() {
            document.getElementById('status-modal').classList.remove('hidden');
        }

        function hideStatusModal() {
            document.getElementById('status-modal').classList.add('hidden');
        }

        // 写真モーダル
        function showPhotoModal() {
            document.getElementById('photo-modal').classList.remove('hidden');
        }

        function hidePhotoModal() {
            document.getElementById('photo-modal').classList.add('hidden');
            document.getElementById('photo-form').reset();
        }

        // ステータス更新
        document.getElementById('status-form').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            
            fetch('{{ route("countries.status.update", $country->id) }}', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
        });

        // 写真追加
        document.getElementById('photo-form').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            
            fetch('{{ route("countries.photos.store", $country->id) }}', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
        });

        // 写真削除
        function deletePhoto(photoId) {
            if (confirm('この写真を削除しますか？')) {
                fetch(`/photos/${photoId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Content-Type': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                });
            }
        }
    </script>
    @endpush
</x-app-layout>
