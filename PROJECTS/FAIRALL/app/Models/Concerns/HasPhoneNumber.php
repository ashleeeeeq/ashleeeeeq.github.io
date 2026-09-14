<?php

namespace App\Models\Concerns;

trait HasPhoneNumber
{
    /**
     * Boot the trait and register a saving event to strip the leading "0"
     * from contact_number when the dial_code is +63.
     */
    protected static function bootHasPhoneNumber(): void
    {
        static::saving(function ($model) {
            if (
                isset($model->dial_code) &&
                $model->dial_code === '+63' &&
                isset($model->contact_number) &&
                is_string($model->contact_number) &&
                strlen($model->contact_number) > 0 &&
                $model->contact_number[0] === '0'
            ) {
                $model->contact_number = ltrim($model->contact_number, '0');
            }
        });
    }
}