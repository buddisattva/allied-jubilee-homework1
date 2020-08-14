<?php

namespace App\Services\User;

use App\Services\User\Contracts\LoginService;
use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Foundation\Auth\RedirectsUsers;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

class BasicLoginService implements LoginService
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
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email',
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
     * @param array $data
     * @return bool
     */
    public function attemptLogin(array $data): bool
    {
        return $this->guard()->attempt($data);
    }

    /**
     * Send the response after the user was authenticated.
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function sendLoginResponse(Request $request)
    {
        $request->session()->regenerate();

        return redirect()->intended($this->redirectPath());
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
     * Set isNewbie in session.
     * @param bool $isNewbie
     * @return bool
     */
    public function setIsNewbie(bool $isNewbie): bool
    {
        session()->put('isNewbie', $isNewbie);

        return true;
    }
}