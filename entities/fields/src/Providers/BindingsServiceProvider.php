<?php

namespace InetStudio\AccessPackage\Fields\Providers;

use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;

/**
 * Class BindingsServiceProvider.
 */
class BindingsServiceProvider extends BaseServiceProvider implements DeferrableProvider
{
    /**
    * @var  array
    */
    public $bindings = [
        'InetStudio\AccessPackage\Fields\Contracts\Models\FieldModelContract' => 'InetStudio\AccessPackage\Fields\Models\FieldModel',
        'InetStudio\AccessPackage\Fields\Contracts\Services\Back\ItemsServiceContract' => 'InetStudio\AccessPackage\Fields\Services\Back\ItemsService',
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
