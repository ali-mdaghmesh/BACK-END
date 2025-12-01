<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    
    function register(Request $request) {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone_number' => 'required|string|max:15|unique:users,phone_number',
            'password' => 'required|string|min:8|confirmed',
        ]);
        $user=User::create([
           
        ]); 

    }




}
