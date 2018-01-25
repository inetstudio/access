<?php

namespace InetStudio\Access\Http\Controllers\Back\Traits;

/**
 * Trait AccessManipulationsTrait
 * @package InetStudio\Access\Http\Controllers\Back\Traits
 */
trait AccessManipulationsTrait
{
    /**
     * Сохраняем доступ.
     *
     * @param $item
     * @param $request
     */
    private function saveAccess($item, $request): void
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
