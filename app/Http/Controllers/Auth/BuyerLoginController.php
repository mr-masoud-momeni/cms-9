<?php
namespace App\Http\Controllers\Auth;

class BuyerLoginController extends BaseLoginController
{
    protected string $guard = 'buyer';
    protected string $redirectTo = '/buyer/dashboard';
}
