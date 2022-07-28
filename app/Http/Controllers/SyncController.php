<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseFormatter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SyncController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        return ResponseFormatter::success(['user' => $user], 'Data fetched successfully');
    }
}
