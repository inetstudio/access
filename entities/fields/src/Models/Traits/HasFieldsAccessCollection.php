<?php

namespace InetStudio\AccessPackage\Fields\Models\Traits;

use ArrayAccess;
use Illuminate\Support\Collection;
use InetStudio\AccessPackage\Fields\Contracts\Models\FieldModelContract;

/**
 * Trait HasFieldsAccessCollection.
 */
trait HasFieldsAccessCollection
{
    /**
     * Determine if the model has any the given custom fields.
     *
     * @param  int|string|array|ArrayAccess|FieldModelContract  $fieldsAccess
     *
     * @return bool
     */
    public function hasFieldsAccess($fieldsAccess): bool
    {
        if ($this->isFieldsAccessStringBased($fieldsAccess)) {
            return ! $this->fields_access->pluck('field')->intersect((array) $fieldsAccess)->isEmpty();
        }

        if ($this->isFieldsAccessIntBased($fieldsAccess)) {
            return ! $this->fields_access->pluck('id')->intersect((array) $fieldsAccess)->isEmpty();
        }

        if ($fieldsAccess instanceof FieldModelContract) {
            return $this->fields_access->contains('field', $fieldsAccess['field']);
        }

        if ($fieldsAccess instanceof Collection) {
            return ! $fieldsAccess->intersect($this->fields_access->pluck('field'))->isEmpty();
        }

        return false;
    }

    /**
     * Determine if the model has any the given custom fields.
     *
     * @param  int|string|array|ArrayAccess|FieldModelContract  $fieldsAccess
     *
     * @return bool
     */
    public function hasAnyCustomField($fieldsAccess): bool
    {
        return $this->hasFieldsAccess($fieldsAccess);
    }

    /**
     * Determine if the model has all of the given custom fields.
     *
     * @param  int|string|array|ArrayAccess|FieldModelContract  $fieldsAccess
     *
     * @return bool
     */
    public function hasAllFieldsAccess($fieldsAccess): bool
    {
        if ($this->isFieldsAccessStringBased($fieldsAccess)) {
            $fieldsAccess = (array) $fieldsAccess;

            return $this->fields_access->pluck('field')->intersect($fieldsAccess)->count() == count($fieldsAccess);
        }

        if ($this->isFieldsAccessIntBased($fieldsAccess)) {
            $fieldsAccess = (array) $fieldsAccess;

            return $this->fields_access->pluck('id')->intersect($fieldsAccess)->count() == count($fieldsAccess);
        }

        if ($fieldsAccess instanceof FieldModelContract) {
            return $this->fields_access->contains('field', $fieldsAccess['field']);
        }

        if ($fieldsAccess instanceof Collection) {
            return $this->fields_access->intersect($fieldsAccess)->count() == $fieldsAccess->count();
        }

        return false;
    }

    /**
     * Determine if the given fields are string based.
     *
     * @param  int|string|array|ArrayAccess|FieldModelContract  $fieldsAccess
     *
     * @return bool
     */
    protected function isFieldsAccessStringBased($fieldsAccess): bool
    {
        return is_string($fieldsAccess) || (is_array($fieldsAccess) && isset($fieldsAccess[0]) && is_string($fieldsAccess[0]));
    }

    /**
     * Determine if the given fields are integer based.
     *
     * @param  int|string|array|ArrayAccess|FieldModelContract  $fieldsAccess
     *
     * @return bool
     */
    protected function isFieldsAccessIntBased($fieldsAccess): bool
    {
        return is_int($fieldsAccess) || (is_array($fieldsAccess) && isset($fieldsAccess[0]) && is_int($fieldsAccess[0]));
    }
}
