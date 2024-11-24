<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function login(Request $request)
    {
        $validatedData = $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);

        if (!Auth::attempt($validatedData)) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $user = Auth::user()->with('guru');
        return response()->json(['success' => true]);
    }
}
