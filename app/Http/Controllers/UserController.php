<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;


class UserController extends Controller
{
    //show register form
    public function create(){
        return view('users.register');
    }


    //create new user|login user
    public function store(Request $request){
        $formField = $request->validate([
            'name' =>'required|string|max:255',
            'email' =>'required|string|email|max:255|unique:users',
            'password' =>'required|string|min:6|confirmed',
        ]);

        //Hash Password
        $formField['password'] = bcrypt($formField['password']);


        //create user
        $user = User::create($formField);

        //login
        auth()->login($user);

        return redirect('/')->with('message', 'User created and logged in');
    }

    //user logout
    public function logout(Request $request){
        auth()->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken(); //

        return redirect('/')->with('message', 'You have been logged out!');
    }

    //user login form
    public function login(Request $request){
        return view('users.login');
    }

    //login the user || authenticate the user
    public function authenticate(Request $request){
        // $request->validate([
        //     'email' =>'required|string|email|max:255',
        //     'password' =>'required|string|min:6|confirmed'
        // ]);

        // $user = User::where('email', $request->email)->first();

        // if(!$user){
        //     return redirect('/')->with('message', 'User not found');
        // }

        // if($user->attempt_login($request->only('email', 'password'), $request->remember)){
        //     return redirect('/')->with('message', 'Login successful');
        // }else{
        //     return redirect('/')->with('message', 'Login failed');
        // }

        $formField = $request->validate([
            'email' =>'required|string|email',
            'password' =>'required',
        ]);

        if(auth()->attempt($formField)){
            $request->session()->regenerate();

            return redirect('/')->with('message', 'Successfully login');
        }

        return back()->withErrors(['email' => 'Invalid Credentials'])->onlyInput('email');

    }

}
