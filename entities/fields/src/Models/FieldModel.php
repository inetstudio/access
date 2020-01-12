<?php

namespace InetStudio\AccessPackage\Fields\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use InetStudio\AdminPanel\Models\Traits\HasJSONColumns;
use InetStudio\AccessPackage\Fields\Contracts\Models\FieldModelContract;
use InetStudio\AdminPanel\Base\Models\Traits\Scopes\BuildQueryScopeTrait;

/**
 * Class FieldModel.
 */
class FieldModel extends Model implements FieldModelContract
{
    use SoftDeletes;
    use HasJSONColumns;
    use BuildQueryScopeTrait;

    /**
     * Тип сущности.
     */
    const ENTITY_TYPE = 'access_field';

    /**
     * Связанная с моделью таблица.
     *
     * @var string
     */
    protected $table = 'access_fields';

    /**
     * Атрибуты, для которых разрешено массовое назначение.
     *
     * @var array
     */
    protected $fillable = [
        'field', 'access',
    ];

    /**
     * Атрибуты, которые должны быть преобразованы к базовым типам.
     *
     * @var array
     */
    protected $casts = [
        'access' => 'array',
    ];

    /**
     * Атрибуты, которые должны быть преобразованы в даты.
     *
     * @var array
     */
    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    /**
     * Загрузка модели.
     */
    protected static function boot()
    {
        parent::boot();

        self::$buildQueryScopeDefaults['columns'] = [
            'id',
            'accessable_id',
            'accessable_type',
            'field',
            'access',
        ];
    }

    /**
     * Сеттер атрибута accessable_type.
     *
     * @param $value
     */
    public function setAccessableTypeAttribute($value)
    {
        $this->attributes['accessable_type'] = trim(strip_tags($value));
    }

    /**
     * Сеттер атрибута accessable_id.
     *
     * @param $value
     */
    public function setAccessableIdAttribute($value)
    {
        $this->attributes['accessable_id'] = (int) trim(strip_tags($value));
    }

    /**
     * Сеттер атрибута field.
     *
     * @param $value
     */
    public function setFieldAttribute($value)
    {
        $this->attributes['field'] = trim(strip_tags($value));
    }

    /**
     * Сеттер атрибута access.
     *
     * @param $value
     */
    public function setAccessInfoAttribute($value)
    {
        $this->attributes['access'] = json_encode((array) $value);
    }

    /**
     * Геттер атрибута type.
     *
     * @return string
     */
    public function getTypeAttribute(): string
    {
        return self::ENTITY_TYPE;
    }

    /**
     * Полиморфное отношение с остальными моделями.
     *
     * @return MorphTo
     */
    public function accessable(): MorphTo
    {
        return $this->morphTo();
    }
}
