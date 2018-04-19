<?php

namespace Betterde\Role\Providers;

use Betterde\Role\Commands\CreateRole;
use Betterde\Role\Commands\FlushRoleCache;
use Betterde\Role\Contracts\RoleContract;
use Illuminate\Support\ServiceProvider;

/**
 * Date: 19/04/2018
 * @author George
 * @package Betterde\Role\Providers
 */
class RoleServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        /**
         * 发布数据库迁移文件
         */
        $this->publishes([
            __DIR__.'/../../database/migrations/' => database_path('migrations'),
            __DIR__.'/../../config/role.php' => config_path('role.php')
        ], 'role');

        if ($this->app->runningInConsole()) {
            $this->commands([
                CreateRole::class,
                FlushRoleCache::class
            ]);
        }

        $this->registerModelBindings();
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * 绑定模型到接口
     *
     * Date: 19/04/2018
     * @author George
     */
    protected function registerModelBindings()
    {
        $this->app->bind(RoleContract::class, config('role.model'));
    }
}
