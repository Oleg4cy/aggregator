<?php

namespace App\Orchid\Fields;

use Orchid\Screen\Field;
use Illuminate\Database\Eloquent\Collection;

class SelectRelation extends Field
{
    public $attributes = [
        'edit' => false,
        'rows' => false,
        'controller' => '',
        'inputsGroups' => [] ,
        'sorting' => null,
    ];

    /**
     * The field view.
     *
     * @var string
     */
    public $view = 'orchid.fields.select-relation';

    /**
     * Values for the dynamic input.
     *
     * @var array
     */

    /**
     * Set the values for the dynamic input.
     *
     * @param array $values
     *
     * @return $this
     */
    public function type(string $type)
    {
        $this->attributes['type'] = $type;

        return $this;
    }

    public function edit(bool $edit)
    {
        $this->attributes['edit'] = $edit;

        return $this;
    }

    public function inputsGroups(array $groups)
    {
        $this->attributes['inputsGroups'] = $groups;

        return $this;
    }

    public function controller(string $controller)
    {
        $this->attributes['controller'] = $controller;

        return $this;
    }

    public function sorting(array $options, string $default, string $label)
    {
        $this->attributes['sorting'] = [
            'options' => $options,
            'default' => $default,
            'label' => $label,
        ];

        return $this;
    }

    public function setRows()
    {
        $this->attributes['rows'] = true;

        return $this;
    }
}
