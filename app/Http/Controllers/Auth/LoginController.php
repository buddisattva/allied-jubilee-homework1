<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\User\Contracts\RegisterService;
use App\Services\User\FacebookService;
use Illuminate\Http\Request;
use App\Services\User\Contracts\LoginService;

class LoginController extends Controller
{
    private $loginService;
    private $registerService;
    private $facebookService;

    public function __construct(
        LoginService $loginService,
        RegisterService $registerService,
        FacebookService $facebookService
    )
    {
        $this->middleware('guest')->except('logout');

        $this->loginService = $loginService;
        $this->registerService = $registerService;
        $this->facebookService = $facebookService;
    }

    public function showLoginForm()
    {
        return view('auth.login', ['facebook' => $this->facebookService->getLoginUrl()]);
    }

    public function login(Request $request)
    {
        $this->loginService->validateLogin($request);
        $isNewbie = false;

        $email = $request->get('email');
        $password = $request->get('password');
        $registerData = [
            'name' => $email,
            'email' => $email,
            'password' => $password
        ];

        if (!$this->registerService->isRegistered($email) &&
            $this->registerService->isCorrectRegisterData($registerData)) {
            // if the user is not registered and provides proper email and password
            $this->registerService->createAccount($registerData);
            $isNewbie = true;
        }

        if ($this->loginService->attemptLogin([
            'email' => $email,
            'password' => $password,
        ])) {
            $this->loginService->setIsNewbie($isNewbie);
            return $this->loginService->sendLoginResponse($request);
        }

        return $this->loginService->sendFailedLoginResponse($request);
    }

    public function facebookLoginCallback(Request $request)
    {
        if ($accessToken = $this->facebookService->getAccessToken($request)) {
            $facebookUser = $this->facebookService->getUser($accessToken);
            $email = $facebookUser->getField('email');
            $password = hash('haval256,5', $facebookUser->getField('id'));
            $registerData = [
                'name' => $facebookUser->getField('name'),
                'email' => $email,
                'password' => $password
            ];

            $isNewbie = false;

            if (!$this->registerService->isRegistered($email)) {
                $this->registerService->createAccount($registerData);
                $isNewbie = true;
            }

            if ($this->loginService->attemptLogin([
                'email' => $email,
                'password' => $password
            ])) {
                $this->loginService->setIsNewbie($isNewbie);
                return $this->loginService->sendLoginResponse($request);
            }

            return $this->loginService->sendFailedLoginResponse($request);
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
