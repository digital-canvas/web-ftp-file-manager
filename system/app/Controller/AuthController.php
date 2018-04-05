<?php

namespace App\Controller;

/**
 * Class AuthController
 *
 * @package App\Controller
 */
class AuthController
{
    /**
     * @return \Illuminate\Contracts\View\View|\Illuminate\View\Factory
     */
    public function showLogin()
    {
        $messages = session()->getFlashBag()->get('login');

        return view('auth.login', compact('messages'));
    }

    /**
     * @return \Illuminate\Http\RedirectResponse
     */
    public function login()
    {
        $logged_in = auth()->login(request('username'), request('password'));
        if($logged_in){
            return redirect()->route('home');
        }

        session()->getFlashBag()->add('login', 'Invalid FTP credentials');

        return redirect()->back();
    }

    public function logout()
    {
        auth()->logout();
        return redirect()->route('login');
    }
}
