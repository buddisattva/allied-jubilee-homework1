<?php

namespace App\Services\User;

use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Foundation\Auth\RedirectsUsers;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

class BasicLoginService implements Contracts\LoginService
{
    use RedirectsUsers;

    /**
     * Validate the user login request.
     *
     * @param Request $request
     * @return bool
     * @throws ValidationException
     */
    public function validateLogin(Request $request): bool
    {
        $username = 'email';

        $validator = Validator::make($request->all(), [
            $username => 'required|string|email',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return true;
    }

    /**
     * Attempt to log the user into the application.
     *
     * @param Request $request
     * @return bool
     */
    public function attemptLogin(Request $request): bool
    {
        return $this->guard()->attempt($this->credentials($request));
    }

    /**
     * Send the response after the user was authenticated.
     *
     * @param Request $request
     * @param bool $isNewbie
     * @return RedirectResponse
     */
    public function sendLoginResponse(Request $request, bool $isNewbie)
    {
        $request->session()->regenerate();

        return redirect()->intended($this->redirectPath(), 302, [
            'Is-Newbie' => (int) $isNewbie
        ]);
    }

    /**
     * Get the failed login response instance.
     *
     * @param Request $request
     * @return Response
     *
     * @throws ValidationException
     */
    public function sendFailedLoginResponse(Request $request)
    {
        throw ValidationException::withMessages([
            'email' => [trans('auth.failed')],
        ]);
    }

    /**
     * Get the guard to be used during authentication.
     *
     * @return StatefulGuard
     */
    private function guard(): StatefulGuard
    {
        return Auth::guard();
    }

    /**
     * Get the needed authorization credentials from the request.
     *
     * @param Request $request
     * @return array
     */
    private function credentials(Request $request): array
    {
        return $request->only('email', 'password');
    }
}