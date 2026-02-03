<?php

namespace App\Http\Middleware;

use Closure;

class BlockBots
{
    public function handle($request, Closure $next)
    {
        // 1. jika kosong, langsung blok
        $ua = strtolower($request->header('User-Agent', ''));


        // 2. daftar kata yang menandakan bot
        $blocked = [
            'curl',
            'python',
            'wget',
            'scraper',
            'bot',
            'spider',
            'crawler',
            'go-http-client',
            'libwww-perl',
        ];

        // 3. cek User-Agent
        foreach ($blocked as $bad) {
            if (str_contains($ua, $bad)) {
                abort(403, 'Bot access blocked');
            }
        }

        // 4. jika aman → lanjut
        return $next($request);
    }
}
