<?php

namespace CodeZero\RouteKeyExists;

use Illuminate\Contracts\Validation\Rule;

class RouteKeyExists implements Rule
{
    /**
     * The model to validate.
     *
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $model;

    /**
     * The attribute being validated.
     *
     * @var string
     */
    protected $attribute;

    /**
     * Replace the route key in the request
     * with the actual database value?
     *
     * @var string|bool
     */
    protected $replaceAttribute = false;

    /**
     * Attribute with the actual database value
     * to add to the request.
     *
     * @var string|bool
     */
    protected $addAttribute = false;

    /**
     * Create a new rule instance.
     *
     * @param string $model
     *
     * @return static
     */
    public static function model($model)
    {
        return new static($model);
    }

    /**
     * Create a new rule instance.
     *
     * @param string $model
     */
    public function __construct($model)
    {
        $this->model = new $model;
    }

    /**
     * Replace the route key in the request with the actual database value.
     * If an attribute name is provided, the original request attribute
     * name will be removed and replaced with the new one.
     *
     * @param string|null $attribute
     *
     * @return $this
     */
    public function replace($attribute = null)
    {
        $this->replaceAttribute = $attribute ?: true;

        return $this;
    }

    /**
     * Add the given attribute with the actual
     * database value to the request.
     *
     * @param string $attribute
     *
     * @return $this
     */
    public function add($attribute)
    {
        $this->addAttribute = $attribute;

        return $this;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute
     * @param string $value
     *
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $this->attribute = $attribute;

        if ( ! $model = $this->model->resolveRouteBinding($value)) {
            return false;
        }

        $this->updateRequest($model);

        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return trans('validation.exists', [
            'attribute' => $this->attribute,
        ]);
    }

    /**
     * Update the request attributes if needed.
     *
     * @param \Illuminate\Database\Eloquent\Model $model
     *
     * @return void
     */
    protected function updateRequest($model)
    {
        $actualKey = $model->{$model->getRouteKeyName()};

        if ($this->replaceAttribute === true) {
            $this->mergeRequest($this->attribute, $actualKey);
        }

        if (is_string($this->replaceAttribute)) {
            $this->mergeRequest($this->replaceAttribute, $actualKey);
            $this->removeFromRequest($this->attribute);
        }

        if (is_string($this->addAttribute)) {
            $this->mergeRequest($this->addAttribute, $actualKey);
        }
    }

    /**
     * Merge the request attributes with the given key / value pair.
     *
     * @param string $key
     * @param mixed $value
     *
     * @return void
     */
    protected function mergeRequest($key, $value)
    {
        request()->merge([
            $key => $value,
        ]);
    }

    /**
     * Remove an attribute from the request.
     *
     * @param mixed $attribute
     *
     * @return void
     */
    protected function removeFromRequest($attribute)
    {
        $attributes = request()->all();

        if (isset($attributes[$attribute])) {
            unset($attributes[$attribute]);
        }

        request()->replace($attributes);
    }
}
