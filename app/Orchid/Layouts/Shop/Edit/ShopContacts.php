<?php

namespace App\Orchid\Layouts\Shop\Edit;

use Orchid\Screen\Fields\Input;
use App\Orchid\Fields\Title;
use App\Orchid\Fields\DynamicInput;
use App\Orchid\Layouts\Shop\Edit\ShopEditRow;
use App\Models\ServiceCenter;

class ShopContacts extends ShopEditRow
{
    /**
     * Used to create the title of a group of form elements.
     *
     * @var string|null
     */
    protected $title;

    public function getRow(ServiceCenter $serviceCenter): iterable
    {
        $row = [
            Title::make('Контакты')->class('pt-4'),
            Input::make('shop.zip')
                ->title('Индекс')
                ->value($serviceCenter->zip ?? ''),
            Input::make('shop.address')
                ->title('Адрес')
                ->value($serviceCenter->address ?? '')
                ->required(),

            Input::make('shop.phone')
                ->title('Телефон')
                ->value($serviceCenter->phone ?? '')
                ->mask([
                    'mask' => '999 999 9999',
                    'numericInput' => true
                ]),
            DynamicInput::make('shop.additional_phones')
                ->title('Дополнительные номера телефонов')
                ->value($serviceCenter->phone ?? '')
                ->mask([
                    'mask' => '999 999 9999',
                    'numericInput' => true
                ])
                ->values(json_decode($serviceCenter->additional_phones, true) ?? []),

            Input::make('shop.whatsapp')
                ->title('Whatsapp')
                ->value($serviceCenter->whatsapp ?? ''),
            Input::make('shop.telegram')
                ->title('Telegram')
                ->value($serviceCenter->telegram ?? ''),
            Input::make('shop.vk')
                ->title('VK')
                ->value($serviceCenter->vk ?? ''),
            DynamicInput::make('shop.more_socials')
                ->title('Дополнительные социальные сети')
                ->values(json_decode($serviceCenter->more_socials, true) ?? [])
                ->useNames('Название', 'Ссылка'),
            DynamicInput::make('shop.web')
                ->title('Сайты')
                ->values(json_decode($serviceCenter->web, true) ?? []),
            DynamicInput::make('shop.emails')
                ->title('Почта')
                ->values(json_decode($serviceCenter->emails, true) ?? []),

        ];

        return $row;
    }

    public function getMethod(): string
    {
        return 'contacts';
    }

    protected function getSaveMethod(): string
    {
        return 'saveContacts';
    }
}
