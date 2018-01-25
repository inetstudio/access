<?php

namespace InetStudio\Access\Contracts\Models;

/**
 * Interface AccessModelContract
 * @package InetStudio\Access\Contracts\Models
 */
interface AccessModelContract
{
    /**
     * Полиморфное отношение с остальными моделями.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function accessable();
}
