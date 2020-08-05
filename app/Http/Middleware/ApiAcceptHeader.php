<?php

namespace App\Http\Middleware;

use Closure;

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
