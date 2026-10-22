<?php

namespace Azuriom\Plugin\Appeals\Http\Middleware;

use Azuriom\Plugin\Appeals\Models\Appeal;
use Azuriom\Plugin\Appeals\Support\SanctionSourceManager;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

/**
 * Shows a small banner telling a banned/muted player they can appeal, on every
 * front-end page, not just the plugin's own. Azuriom's front layout gives
 * plugins no hook to inject markup site-wide, so this works the same way as
 * NavBadgeMiddleware: splice real HTML into the already-rendered response,
 * right after the opening <body> tag.
 */
class SanctionBannerMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        if ($request->is('admin*') || $request->routeIs('appeals.*') || ! setting('appeals.show_banner', true)) {
            return $response;
        }

        $user = $request->user();

        if ($user === null) {
            return $response;
        }

        if (! str_contains((string) $response->headers->get('Content-Type'), 'text/html')) {
            return $response;
        }

        $content = $response->getContent();

        if (! is_string($content) || ! str_contains($content, '<body')) {
            return $response;
        }

        $banner = $this->bannerFor($user);

        if ($banner === null) {
            return $response;
        }

        $response->setContent(preg_replace_callback(
            '/<body([^>]*)>/',
            fn ($matches) => '<body'.$matches[1].'>'.$banner,
            $content,
            1
        ));

        return $response;
    }

    /**
     * Renders the banner for this user, cached briefly since checking the
     * external sanction source means an extra database connection.
     */
    private function bannerFor($user): ?string
    {
        $html = Cache::remember("appeals.banner.{$user->id}", now()->addMinutes(2), function () use ($user) {
            if (! $user->can('appeals.submit')) {
                return '';
            }

            $activeAppeal = Appeal::where('user_id', $user->id)->active()->first();

            if ($activeAppeal !== null) {
                return view('appeals::banner', [
                    'message' => trans('appeals::messages.banner.pending'),
                    'url' => route('appeals.show', $activeAppeal),
                    'cta' => trans('appeals::messages.banner.view'),
                ])->render();
            }

            $sanctionSource = (new SanctionSourceManager())->make();

            if (! $sanctionSource->isConfigured()) {
                return '';
            }

            $allowedTypes = explode(',', (string) setting('appeals.appealable_types', 'ban,mute'));
            $sanctions = array_intersect_key($sanctionSource->findActiveSanctions($user), array_flip($allowedTypes));

            if ($sanctions === []) {
                return '';
            }

            return view('appeals::banner', [
                'message' => trans('appeals::messages.banner.sanctioned'),
                'url' => route('appeals.create'),
                'cta' => trans('appeals::messages.banner.appeal'),
            ])->render();
        });

        return $html !== '' ? $html : null;
    }
}
