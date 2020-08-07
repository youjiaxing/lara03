<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Str;

class ApiAcceptHeader
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure                 $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (!Str::startsWith($request->path(), 'api/')) {
            return $next($request);
        }

        $defaultType = "application/json";
        $allowTypes = ["json", "xml"];
        $needModified = true;

        $acceptHeader = $request->header('Accept');
        if (!empty($acceptHeader)) {
            foreach ($allowTypes as $type) {
                if (strpos($acceptHeader, $type) !== false) {
                    $needModified = false;
                    break;
                }
            }
        }

        if ($needModified) {
            $request->headers->set("Accept", $defaultType);
        }

        return $next($request);
    }
}
