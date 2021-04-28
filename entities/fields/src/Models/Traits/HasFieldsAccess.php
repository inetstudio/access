<?php

namespace InetStudio\AccessPackage\Fields\Models\Traits;

use ArrayAccess;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Contracts\Container\BindingResolutionException;
use InetStudio\AccessPackage\Fields\Contracts\Models\FieldModelContract;

/**
 * Trait HasFieldsAccess.
 */
trait HasFieldsAccess
{
    use HasFieldsAccessCollection;

    /**
     * The queued custom fields.
     *
     * @var array
     */
    protected $queuedFieldsAccess = [];

    /**
     * Get FieldsAccess class name.
     *
     * @return string
     *
     * @throws BindingResolutionException
     */
    public function getFieldAccessClassName(): string
    {
        $model = app()->make(FieldModelContract::class);

        return get_class($model);
    }

    /**
     * Получаем все доступы к полям материала.
     *
     * @return MorphMany
     *
     * @throws BindingResolutionException
     */
    public function fields_access(): MorphMany
    {
        $className = $this->getFieldAccessClassName();

        return $this->morphMany($className, 'model');
    }

    /**
     * Attach the given custom to the model.
     *
     * @param  int|string|array|ArrayAccess|FieldModelContract  $fieldsAccess
     *
     * @throws BindingResolutionException
     */
    public function setFieldsAccessAttribute($fieldsAccess): void
    {
        if (! $this->exists) {
            $this->queuedFieldsAccess = $fieldsAccess;

            return;
        }

        $this->attachFieldsAccess($fieldsAccess);
    }

    /**
     * Boot the HasFieldsAccess trait for a model.
     */
    public static function bootHasFieldsAccess()
    {
        static::created(
            function (Model $customizableModel) {
                if ($customizableModel->queuedFieldsAccess) {
                    $customizableModel->attachFieldsAccess($customizableModel->queuedFieldsAccess);
                    $customizableModel->queuedFieldsAccess = [];
                }
            }
        );

        static::deleted(
            function (Model $customizableModel) {
                $customizableModel->syncFieldsAccess(null);
            }
        );
    }

    /**
     * Get the custom list.
     *
     * @return array
     *
     * @throws BindingResolutionException
     */
    public function getFieldsAccessList(): array
    {
        return $this->fields_access()
            ->pluck('access', 'field')
            ->toArray();
    }

    /**
     * Получаем доступ к полю.
     *
     * @param  string  $field
     * @param $default
     * @param  bool  $returnObject
     *
     * @return mixed|null
     *
     * @throws BindingResolutionException
     */
    public function getFieldAccess(string $field, $default = null, bool $returnObject = false)
    {
        $builder = $this->fields_access()
            ->where('field', $field);

        if ($returnObject) {
            return $builder->withTrashed()->first();
        } else {
            $fieldsAccess = $builder->first();
        }

        return ($fieldsAccess) ? $fieldsAccess->access : $default;
    }

    /**
     * Получаем доступ к полю по ключу.
     *
     * @param  string  $field
     * @param  string  $key
     * @param $default
     * @param  bool  $returnObject
     *
     * @return mixed|null
     *
     * @throws BindingResolutionException
     */
    public function getFieldAccessByKey(string $field, string $key = '', $default = null, bool $returnObject = false)
    {
        $access = $this->getFieldAccess($field, $default, $returnObject);

        if ($key == '' || $returnObject) {
            return $access;
        }

        return Arr::get(Arr::wrap($access), $key);
    }

    /**
     * Scope query with all the given custom fields.
     *
     * @param  Builder  $query
     * @param  int|string|array|ArrayAccess|FieldModelContract  $fieldsAccess
     *
     * @return Builder
     *
     * @throws BindingResolutionException
     */
    public function scopeWithAllFieldsAccess(Builder $query, $fieldsAccess): Builder
    {
        $fieldsAccess = $this->isFieldsAccessStringBased($fieldsAccess)
            ? $fieldsAccess : $this->hydrateFieldsAccess($fieldsAccess)->pluck('field')->toArray();

        collect($fieldsAccess)->each(
            function ($fieldsAccessItem) use ($query) {
                $query->whereHas(
                    'fields_access',
                    function (Builder $query) use ($fieldsAccessItem) {
                        return $query->where('field', $fieldsAccessItem);
                    }
                );
            }
        );

        return $query;
    }

    /**
     * Scope query with any of the given custom fields.
     *
     * @param  Builder  $query
     * @param  int|string|array|ArrayAccess|FieldModelContract  $fieldsAccess
     *
     * @return Builder
     *
     * @throws BindingResolutionException
     */
    public function scopeWithAnyFieldAccess(Builder $query, $fieldsAccess): Builder
    {
        $fieldsAccess = $this->isFieldsAccessStringBased($fieldsAccess)
            ? $fieldsAccess : $this->hydrateFieldsAccess($fieldsAccess)->pluck('field')->toArray();

        return $query->whereHas(
            'fields_access',
            function (Builder $query) use ($fieldsAccess) {
                $query->whereIn('field', (array) $fieldsAccess);
            }
        );
    }

    /**
     * Scope query with any of the given custom fields.
     *
     * @param  Builder  $query
     * @param  int|string|array|ArrayAccess|FieldModelContract  $fieldsAccess
     *
     * @return Builder
     *
     * @throws BindingResolutionException
     */
    public function scopeWithFieldsAccess(Builder $query, $fieldsAccess): Builder
    {
        return $this->scopeWithAnyFieldAccess($query, $fieldsAccess);
    }

    /**
     * Scope query without the given custom fields.
     *
     * @param  Builder  $query
     * @param  int|string|array|ArrayAccess|FieldModelContract  $fieldsAccess
     *
     * @return Builder
     *
     * @throws BindingResolutionException
     */
    public function scopeWithoutFieldsAccess(Builder $query, $fieldsAccess): Builder
    {
        $fieldsAccess = $this->isFieldsAccessStringBased($fieldsAccess)
            ? $fieldsAccess : $this->hydrateFieldsAccess($fieldsAccess)->pluck('field')->toArray();

        return $query->whereDoesntHave(
            'fields_access',
            function (Builder $query) use ($fieldsAccess) {
                $query->whereIn('field', (array) $fieldsAccess);
            }
        );
    }

    /**
     * Scope query without any custom fields.
     *
     * @param  Builder  $query
     *
     * @return Builder
     */
    public function scopeWithoutAnyFieldAccess(Builder $query): Builder
    {
        return $query->doesntHave('fields_access');
    }

    /**
     * Attach the given custom to the model.
     *
     * @param  int|string|array|ArrayAccess|FieldModelContract  $fieldsAccess
     *
     * @return $this
     *
     * @throws BindingResolutionException
     */
    public function attachFieldsAccess($fieldsAccess): self
    {
        static::$dispatcher->dispatch('inetstudio.fields_access.attaching', [$this, $fieldsAccess]);

        foreach ($fieldsAccess as $field => $access) {
            $this->updateFieldAccess($field, $access);
        }

        static::$dispatcher->dispatch('inetstudio.fields_access.attached', [$this, $fieldsAccess]);

        return $this;
    }

    /**
     * Sync the given custom to the model.
     *
     * @param  int|string|array|ArrayAccess|FieldModelContract|null  $fieldsAccess
     *
     * @return $this
     *
     * @throws BindingResolutionException
     */
    public function syncFieldsAccess($fieldsAccess): self
    {
        static::$dispatcher->dispatch('inetstudio.fields_access.syncing', [$this, $fieldsAccess]);

        foreach ($fieldsAccess ?? [] as $field => $access) {
            if (empty($access)) {
                $this->deleteFieldAccess($field);
            } else {
                $this->updateFieldAccess($field, $access);
            }
        }

        static::$dispatcher->dispatch('inetstudio.fields_access.synced', [$this, $fieldsAccess]);

        return $this;
    }

    /**
     * Detach the given custom from the model.
     *
     * @param  int|string|array|ArrayAccess|FieldModelContract  $fieldsAccess
     *
     * @return $this
     *
     * @throws BindingResolutionException
     */
    public function detachFieldsAccess($fieldsAccess): self
    {
        static::$dispatcher->dispatch('inetstudio.fields_access.detaching', [$this, $fieldsAccess]);

        $this->deleteAllFieldsAccess();

        static::$dispatcher->dispatch('inetstudio.fields_access.detached', [$this, $fieldsAccess]);

        return $this;
    }

    /**
     * Hydrate custom fields.
     *
     * @param  int|string|array|ArrayAccess|FieldModelContract  $fieldsAccess
     *
     * @return Collection
     *
     * @throws BindingResolutionException
     */
    protected function hydrateFieldsAccess($fieldsAccess): Collection
    {
        $isFieldsAccessStringBased = $this->isFieldsAccessStringBased($fieldsAccess);
        $isFieldsAccessIntBased = $this->isFieldsAccessIntBased($fieldsAccess);
        $field = $isFieldsAccessStringBased ? 'field' : 'id';
        $className = $this->getFieldAccessClassName();

        return $isFieldsAccessStringBased || $isFieldsAccessIntBased
            ? $className::query()->whereIn($field, (array) $fieldsAccess)->get() : collect($fieldsAccess);
    }

    /**
     * Обновляем доступ к полю.
     *
     * @param  string  $field
     * @param $newAccess
     *
     * @return mixed
     *
     * @throws BindingResolutionException
     */
    protected function updateFieldAccess($field, $newAccess)
    {
        $fieldsAccess = $this->getFieldAccess($field, null, true);

        if ($fieldsAccess === null) {
            return $this->addFieldAccess($field, $newAccess);
        }

        if ($fieldsAccess->trashed()) {
            $fieldsAccess->restore();
        }

        return $fieldsAccess->update(
            [
                'access' => $newAccess,
            ]
        );
    }

    /**
     * Добавляем доступ к полю.
     *
     * @param  string  $field
     * @param $access
     *
     * @return mixed
     *
     * @throws BindingResolutionException
     */
    protected function addFieldAccess($field, $access)
    {
        if (! $access) {
            return false;
        }

        $existing = $this->fields_access()
            ->where('field', $field)
            ->where('access', $access)
            ->exists();

        if ($existing) {
            return false;
        }

        return $this->fields_access()->create(
            [
                'field' => $field,
                'access' => $access,
            ]
        );
    }

    /**
     * Удаляем доступ к полю.
     *
     * @param  string  $field
     *
     * @return mixed
     *
     * @throws BindingResolutionException
     */
    protected function deleteFieldAccess($field)
    {
        return $this->fields_access()
            ->where('field', $field)
            ->delete();
    }

    /**
     * Удаляем все доступы к полю.
     *
     * @return mixed
     *
     * @throws BindingResolutionException
     */
    protected function deleteAllFieldsAccess()
    {
        return $this->fields_access()->delete();
    }
}
