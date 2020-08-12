<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\User\Contracts\RegisterService;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use App\Services\User\Contracts\LoginService;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    private $loginService;
    private $registerService;

    public function __construct(LoginService $loginService, RegisterService $registerService)
    {
        $this->middleware('guest')->except('logout');

        $this->loginService = $loginService;
        $this->registerService = $registerService;
    }

    public function login(Request $request)
    {
        $this->loginService->validateLogin($request);

        $registerData = [
            'name' => $request->get('email'),
            'email' => $request->get('email'),
            'password' => $request->get('password')
        ];
        if (!$this->registerService->isRegistered($request->get('email')) &&
            $this->registerService->isCorrectRegisterData($registerData)) {
            // if the user is not registered and provides proper email and password
            $this->registerService->createAccount($registerData);
        }

        if ($this->loginService->attemptLogin($request)) {
            return $this->loginService->sendLoginResponse($request);
        }

        return $this->loginService->sendFailedLoginResponse($request);
    }
}
