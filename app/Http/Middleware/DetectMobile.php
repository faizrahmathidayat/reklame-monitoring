<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;

class DetectMobile
{
    public function handle(Request $request, Closure $next)
    {
        $ua       = $request->header('User-Agent', '');
        $isMobile = (bool) preg_match('/Mobile|Android|iPhone|iPad|iPod|Windows Phone|BlackBerry|Opera Mini/i', $ua);

        // Allow manual override via query string: ?view=mobile or ?view=desktop
        if ($request->query('view') === 'mobile')   $isMobile = true;
        if ($request->query('view') === 'desktop')  $isMobile = false;

        View::share('isMobile', $isMobile);

        return $next($request);
    }
}
