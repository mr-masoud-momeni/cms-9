<?php
namespace App\Http\Controllers\Auth;

class ShopAdminLoginController extends BaseLoginController
{
    protected string $guard = 'shop_admin';
    protected string $redirectTo = '/shop/dashboard';
    protected string $view = 'auth.login-shop';
}
