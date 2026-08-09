<?php

namespace App\Orchid\Layouts\ServiceCenter\Edit;

use Orchid\Screen\Field;
use Orchid\Screen\Layouts\Rows;
use Orchid\Screen\Actions\Button;
use App\Models\ServiceCenter;

abstract class ServiceCenterEditRow extends Rows
{
    /**
     * Used to create the title of a group of form elements.
     *
     * @var string|null
     */
    protected $title;

    abstract function getRow(ServiceCenter $serviceCenter): iterable;
    abstract function getMethod(): string;

    protected function getSaveMethod(): string
    {
        return 'save-' . $this->getMethod();
    }

    /**
     * Get the fields elements to be displayed.
     *
     * @return Field[]
     */
    protected function fields(): iterable
    {
        $serviceCenter = $this->query->get('serviceCenter');
        $rows = $this->getRow($serviceCenter);

        if ($serviceCenter->id) {
            $rows[] = Button::make('Сохранить')
                ->method($this->getSaveMethod())
                ->class('btn btn-success m-auto');
        }

        return $rows;
    }
}
