<?php

namespace InetStudio\Access\Services\Back;

use InetStudio\Access\Contracts\Services\Back\AccessServiceContract;

/**
 * Class AccessService.
 */
class AccessService implements AccessServiceContract
{
    /**
     * Присваиваем доступы объекту.
     *
     * @param $request
     *
     * @param $item
     */
    public function attachToObject($request, $item)
    {
        if ($request->filled('access')) {
            $item->removeAccess();

            foreach ($request->get('access') as $field => $access) {
                $item->addAccess($field, $access);
            }
        } else {
            $item->removeAccess();
        }
    }
}
