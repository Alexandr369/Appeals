<?php

namespace Azuriom\Plugin\Appeals\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

/**
 * Azuriom's admin sidebar renders plugin nav item labels as escaped text
 * (`{{ $subItem['name'] }}` in admin/layouts/admin.blade.php), so a plugin cannot
 * inject real markup through the adminNavigation() array alone: any HTML put in
 * the label would show up as literal text instead of being rendered.
 *
 * This middleware rewrites one small text marker we control (added to the label
 * in AppealsServiceProvider::adminNavigation()) into a real red badge, directly
 * in the already-rendered admin HTML. It never touches anything else, and if the
 * marker is not found it does nothing.
 */
class NavBadgeMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        if (! $request->is('admin*')) {
            return $response;
        }

        $content = $response->getContent();

        if (! is_string($content) || ! str_contains($content, '%%APPEALS_PENDING_COUNT:')) {
            return $response;
        }

        $response->setContent(preg_replace_callback(
            '/%%APPEALS_PENDING_COUNT:(\d+)%%/',
            fn ($matches) => '<span class="badge rounded-pill bg-danger ms-1">'.$matches[1].'</span>',
            $content
        ));

        return $response;
    }
}
