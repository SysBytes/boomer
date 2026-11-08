<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use App\Models\User;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Get the needed authorization credentials from the request.
     * Adds the active = 1 constraint so inactive users cannot log in.
     */
    protected function credentials(Request $request): array
    {
        return array_merge($request->only($this->username(), 'password'), [
            'active' => 1,
        ]);
    }

    /**
     * Override failed login to show a specific message when the user
     * exists but is inactive, otherwise show the generic error.
     */
    protected function sendFailedLoginResponse(Request $request)
    {
        $user = User::where($this->username(), $request->{$this->username()})->first();

        if ($user && !$user->active) {
            throw ValidationException::withMessages([
                $this->username() => [__('Tu cuenta está desactivada. Contacta al administrador.')],
            ]);
        }

        throw ValidationException::withMessages([
            $this->username() => [trans('auth.failed')],
        ]);
    }

    /**
     * Where to redirect users after login.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  mixed  $user
     * @return mixed
     */
    protected function authenticated(Request $request, $user)
    {
        if ($user->role === 'technician' || $user->role === 'assistant' || $user->role === 'external') {
            return redirect()->route('dashboard');
        }

        return redirect()->intended('/backoffice');
    }

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->middleware('auth')->only('logout');
    }
}
