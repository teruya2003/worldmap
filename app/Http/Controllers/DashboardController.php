<?php

namespace App\Http\Controllers;

use App\Models\UserCountryStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * ダッシュボードを表示
     */
    public function index()
    {
        $user = Auth::user();
        
        // ユーザーの国ステータスを取得
        $userStatuses = UserCountryStatus::where('user_id', $user->id)->get();
        
        // 国IDをキーとした連想配列に変換
        $statusMap = $userStatuses->pluck('status', 'country_id')->toArray();
        
        return view('dashboard', compact('statusMap'));
    }
}
