<?php
namespace App\Http\Controllers\Auth;

class AdminLoginController extends BaseLoginController
{
    protected string $guard = 'web';
    protected string $redirectTo = '/admin/dashboard';
    protected string $view = 'auth.login-admin';
}
