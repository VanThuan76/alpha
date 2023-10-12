<?php
namespace App\Admin\Helpers;

abstract class DatabaseHelper
{
    public static function getRecordByField($model, $field, $value)
    {
        if (class_exists($model)) {
            return $model::where($field, $value)->first();
        }
        return null;
    }
    public static function getValueByField($model, $field, $value = null)
    {
        if (class_exists($model)) {
            $record = $model::find($field);
            if ($record && !is_null($value)) {
                return $record->{$value};
            }else{
                return $record;
            }
        }
        return null;
    }
    public static function getOptionsForSelect($model, $labelColumn, $valueColumn, $additionalFilters = [])
    {
        if (class_exists($model)) {
            $query = $model::query();

            foreach ($additionalFilters as $filter) {
                list($field, $operator, $value) = $filter;
                $query->where($field, $operator, $value);
            }

            $options = $query->pluck($labelColumn, $valueColumn)->toArray();

            return $options;
        }

        return [];
    }
}

