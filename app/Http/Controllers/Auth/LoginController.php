<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\User\Contracts\RegisterService;
use Illuminate\Http\Request;
use App\Services\User\Contracts\LoginService;

class LoginController extends Controller
{
    private $loginService;
    private $registerService;

    public function __construct(LoginService $loginService, RegisterService $registerService)
    {
        $this->middleware('guest')->except('logout');

        $this->loginService = $loginService;
        $this->registerService = $registerService;
    }

    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $this->loginService->validateLogin($request);
        $isNewbie = false;

        $registerData = [
            'name' => $request->get('email'),
            'email' => $request->get('email'),
            'password' => $request->get('password')
        ];

        if (!$this->registerService->isRegistered($request->get('email')) &&
            $this->registerService->isCorrectRegisterData($registerData)) {
            // if the user is not registered and provides proper email and password
            $this->registerService->createAccount($registerData);
            $isNewbie = true;
        }

        if ($this->loginService->attemptLogin($request)) {
            $this->loginService->setIsNewbie($isNewbie);
            return $this->loginService->sendLoginResponse($request);
        }

        return $this->loginService->sendFailedLoginResponse($request);
    }

    public function logout(Request $request)
    {
        auth()->guard()->logout();

        $request->session()->invalidate();

        return redirect('/');
    }
}
