<?php

namespace InetStudio\Access\Models\Traits;

use InetStudio\Access\Contracts\Models\AccessModelContract;

trait Accessable
{
    /**
     * Получаем все доступы.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function access()
    {
        return $this->morphMany(app(AccessModelContract::class), 'accessable');
    }

    public function getFieldAccess($field)
    {
        return $this->access->where('field', $field)->first();
    }

    public function getFieldAccessByKey($field, $key)
    {
        $access = $this->getFieldAccess($field);
        return ($access) ? $access->getJSONData('access', $key) : [];
    }

    public function removeAccess()
    {
        app(AccessModelContract::class)->where([
            'accessable_id' => $this->getKey(),
            'accessable_type' => $this->getMorphClass(),
        ])->delete();
    }

    public function addAccess($field, $access)
    {
        $this->access()->create([
            'field' => $field,
            'access' => $access,
        ]);
    }
}
