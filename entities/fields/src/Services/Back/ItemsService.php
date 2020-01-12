<?php

namespace InetStudio\AccessPackage\Fields\Services\Back;

use Illuminate\Http\Request;
use InetStudio\AdminPanel\Base\Services\BaseService;
use InetStudio\AccessPackage\Fields\Contracts\Models\FieldModelContract;
use InetStudio\AccessPackage\Fields\Contracts\Services\Back\ItemsServiceContract;

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

    /**
     * Присваиваем доступы к полям объекта.
     *
     * @param $fields
     * @param $item
     */
    public function attachToObject($fields, $item): void
    {
        if ($fields instanceof Request) {
            $fields = $fields->input('access.fields', []);
        } else {
            $fields = (array) $fields;
        }

        if (! empty($fields)) {
            $item->syncFieldsAccess($fields);
        } else {
            $item->detachFieldsAccess($item->fields_access);
        }
    }
}
