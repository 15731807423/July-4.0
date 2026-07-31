<?php

namespace Translate\Controllers;

use Illuminate\Http\Response;
use Translate\Authentication;

class PublicKeyController
{
    public function show(): Response
    {
        return response(Authentication::publicKey(), 200, [
            'Content-Type' => 'text/plain; charset=utf-8',
            'Cache-Control' => 'public, max-age=300',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }
}
