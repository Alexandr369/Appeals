<?php

namespace Azuriom\Plugin\Appeals\Providers;

use Azuriom\Extensions\Plugin\BasePluginServiceProvider;
use Azuriom\Models\Permission;
use Azuriom\Plugin\Appeals\Http\Middleware\NavBadgeMiddleware;
use Azuriom\Plugin\Appeals\Models\Appeal;
use Illuminate\Support\Facades\Schema;

class AppealsServiceProvider extends BasePluginServiceProvider
{
    /**
     * The plugin's global HTTP middleware stack.
     */
    protected array $middleware = [
        NavBadgeMiddleware::class,
    ];

    /**
     * Register any plugin services.
     *
     * @return void
     */
    public function register()
    {
        $this->registerMiddlewares();
    }

    /**
     * Bootstrap any plugin services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadViews();

        $this->loadTranslations();

        $this->loadMigrations();

        $this->registerRouteDescriptions();

        $this->registerAdminNavigation();

        Permission::registerPermissions([
            'appeals.submit' => 'appeals::admin.permissions.submit',
            'appeals.edit' => 'appeals::admin.permissions.edit',
            'appeals.view' => 'appeals::admin.permissions.view',
            'appeals.manage' => 'appeals::admin.permissions.manage',
            'appeals.settings' => 'appeals::admin.permissions.settings',
        ]);
    }

    /**
     * Returns the routes that should be able to be added to the navbar.
     *
     * @return array
     */
    protected function routeDescriptions()
    {
        return [
            'appeals.index' => trans('appeals::messages.title'),
        ];
    }

    /**
     * Return the admin navigation routes to register in the dashboard.
     *
     * @return array
     */
    protected function adminNavigation()
    {
        $appealsLabel = trans('appeals::admin.nav.appeals');

        if (Schema::hasTable('appeals_appeals')) {
            $pendingCount = Appeal::active()->count();

            if ($pendingCount > 0) {
                // Rewritten into a real red badge by NavBadgeMiddleware, since Blade
                // escapes this label as plain text.
                $appealsLabel .= " %%APPEALS_PENDING_COUNT:{$pendingCount}%%";
            }
        }

        return [
            'appeals' => [
                'name' => trans('appeals::admin.nav.title'),
                'type' => 'dropdown',
                'icon' => 'bi bi-flag',
                'route' => 'appeals.admin.*',
                'items' => [
                    'appeals.admin.index' => [
                        'name' => $appealsLabel,
                        'permission' => 'appeals.view',
                    ],
                    'appeals.admin.settings' => [
                        'name' => trans('appeals::admin.nav.settings'),
                        'permission' => 'appeals.settings',
                    ],
                ],
            ],
        ];
    }
}
