<?php

namespace InetStudio\Access\Providers;

use Illuminate\Support\ServiceProvider;

/**
 * Class AccessBindingsServiceProvider.
 */
class AccessBindingsServiceProvider extends ServiceProvider
{
    /**
    * @var  bool
    */
    protected $defer = true;

    /**
    * @var  array
    */
    public $bindings = [
        'InetStudio\Access\Contracts\Models\AccessModelContract' => 'InetStudio\Access\Models\AccessModel',
        'InetStudio\Access\Contracts\Services\Back\AccessServiceContract' => 'InetStudio\Access\Services\Back\AccessService',
    ];

    /**
     * Получить сервисы от провайдера.
     *
     * @return  array
     */
    public function provides()
    {
        return array_keys($this->bindings);
    }
}
