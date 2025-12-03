<?php
declare(strict_types=1);

namespace App\Http\Middleware;

use Illuminate\Cookie\Middleware\EncryptCookies as Middleware;

class EncryptCookies extends Middleware
{
    /**
     * The cookies that should not be encrypted.
     *
     * @var string[]
     */
    protected $except = [];
}
