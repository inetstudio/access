<?php

namespace InetStudio\AccessPackage\Fields\Services\Front;

use InetStudio\AdminPanel\Base\Services\BaseService;
use InetStudio\AccessPackage\Fields\Contracts\Models\FieldModelContract;
use InetStudio\AccessPackage\Fields\Contracts\Services\Front\ItemsServiceContract;

/**
 * Class ItemsService.
 */
class ItemsService extends BaseService implements ItemsServiceContract
{
    /**
     * ItemsService constructor.
     *
     * @param  FieldModelContract  $model
     */
    public function __construct(FieldModelContract $model)
    {
        parent::__construct($model);
    }
}
