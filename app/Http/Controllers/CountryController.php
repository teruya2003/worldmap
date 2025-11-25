<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Models\UserCountryStatus;
use App\Models\CountryPhoto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class CountryController extends Controller
{
    /**
     * 国詳細ページを表示
     */
    public function show(Country $country)
    {
        $user = Auth::user();
        
        // ユーザーの国ステータスを取得
        $userStatus = UserCountryStatus::where('user_id', $user->id)
            ->where('country_id', $country->id)
            ->first();
        
        // 国の写真を取得
        $photos = CountryPhoto::where('country_id', $country->id)
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();
        
        return view('countries.show', compact('country', 'userStatus', 'photos'));
    }

    /**
     * 国ステータスを更新
     */
    public function updateStatus(Request $request, Country $country)
    {
        $request->validate([
            'status' => 'required|in:lived,stayed,visited,passed,not_visited'
        ]);

        $user = Auth::user();
        
        UserCountryStatus::updateOrCreate(
            [
                'user_id' => $user->id,
                'country_id' => $country->id
            ],
            [
                'status' => $request->status,
                'notes' => $request->notes ?? null
            ]
        );

        return response()->json(['success' => true]);
    }

    /**
     * 写真を保存
     */
    public function storePhoto(Request $request, Country $country)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'caption' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:1000',
            'location' => 'nullable|string|max:255',
            'taken_at' => 'nullable|date'
        ]);

        $user = Auth::user();
        
        // 画像を保存
        $imagePath = $request->file('image')->store('country-photos', 'public');
        
        // データベースに保存
        $photo = CountryPhoto::create([
            'user_id' => $user->id,
            'country_id' => $country->id,
            'image_path' => $imagePath,
            'caption' => $request->caption,
            'description' => $request->description,
            'location' => $request->location,
            'taken_at' => $request->taken_at
        ]);

        return response()->json(['success' => true, 'photo' => $photo]);
    }

    /**
     * 写真を削除
     */
    public function deletePhoto(CountryPhoto $photo)
    {
        $user = Auth::user();
        
        // 自分の写真のみ削除可能
        if ($photo->user_id !== $user->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        // ファイルを削除
        Storage::disk('public')->delete($photo->image_path);
        
        // データベースから削除
        $photo->delete();
        
        return response()->json(['success' => true]);
    }
}
