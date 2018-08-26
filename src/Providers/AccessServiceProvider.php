<?php

namespace InetStudio\Access\Providers;

use Collective\Html\FormBuilder;
use Illuminate\Support\ServiceProvider;
use InetStudio\Access\Console\Commands\SetupCommand;

/**
 * Class AccessServiceProvider.
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
        $this->registerFormComponents();
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
     * Регистрация компонентов форм.
     *
     * @return void
     */
    protected function registerFormComponents()
    {
        FormBuilder::component('access', 'admin.module.access::back.forms.blocks.access', ['name' => null, 'value' => null, 'attributes' => null]);
    }
}
