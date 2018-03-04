<?php

namespace InetStudio\Access\Providers;

use Illuminate\Support\ServiceProvider;
use InetStudio\Access\Models\AccessModel;
use InetStudio\Access\Console\Commands\SetupCommand;
use InetStudio\Access\Contracts\Models\AccessModelContract;

/**
 * Class AccessServiceProvider
 * @package InetStudio\Products\Providers
 */
class AccessServiceProvider extends ServiceProvider
{
    /**
     * Загрузка сервиса.
     *
     * @return void
     */
    public function boot(): void
    {
        $this->registerConsoleCommands();
        $this->registerPublishes();
        $this->registerViews();
    }

    /**
     * Регистрация привязки в контейнере.
     *
     * @return void
     */
    public function register(): void
    {
        $this->registerBindings();
    }

    /**
     * Регистрация команд.
     *
     * @return void
     */
    protected function registerConsoleCommands(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                SetupCommand::class,
            ]);
        }
    }

    /**
     * Регистрация ресурсов.
     *
     * @return void
     */
    protected function registerPublishes(): void
    {
        if ($this->app->runningInConsole()) {
            if (! class_exists('CreateAccessTables')) {
                $timestamp = date('Y_m_d_His', time());
                $this->publishes([
                    __DIR__.'/../../database/migrations/create_access_tables.php.stub' => database_path('migrations/'.$timestamp.'_create_access_tables.php'),
                ], 'migrations');
            }
        }
    }

    /**
     * Регистрация представлений.
     *
     * @return void
     */
    protected function registerViews(): void
    {
        $this->loadViewsFrom(__DIR__.'/../../resources/views', 'admin.module.access');
    }

    /**
     * Регистрация привязок, алиасов и сторонних провайдеров сервисов.
     *
     * @return void
     */
    public function registerBindings(): void
    {
        $this->app->bind(AccessModelContract::class, AccessModel::class);

        // Services
        $this->app->bind('InetStudio\Access\Contracts\Services\Back\AccessServiceContract', 'InetStudio\Access\Services\Back\AccessService');
    }
}
