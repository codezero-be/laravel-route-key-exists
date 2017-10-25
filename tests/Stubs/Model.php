<?php

namespace CodeZero\RouteKeyExists\Tests\Stubs;

use Illuminate\Foundation\Auth\User as EloquentModel;

class Model extends EloquentModel
{
    protected $guarded= [];

    protected $table = 'users';

    /**
     * Get the route key for the model.
     *
     * @return string
     */
    public function getRouteKeyName()
    {
        return 'id';
    }

    /**
     * Get the value of the model's route key.
     *
     * @return mixed
     */
    public function getRouteKey()
    {
        $id = parent::getRouteKey();

        return "foo-{$id}";
    }

    /**
     * Retrieve the model for a bound value.
     *
     * @param mixed $value
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function resolveRouteBinding($value)
    {
        $id = (int) str_replace('foo-', '', $value);

        return parent::resolveRouteBinding($id);
    }
}
