<?php

namespace App\Orchid\Layouts\Shop\Edit;

use Orchid\Screen\Fields\Input;
use App\Orchid\Fields\Title;
use App\Orchid\Fields\DynamicInput;
use App\Orchid\Layouts\Shop\Edit\ShopEditRow;
use App\Models\Shop;

class ShopContacts extends ShopEditRow
{
    /**
     * Used to create the title of a group of form elements.
     *
     * @var string|null
     */
    protected $title;

    public function getRow(Shop $shop): iterable
    {
        $row = [
            Title::make('Контакты')->class('pt-4'),
            Input::make('shop.zip')
                ->title('Индекс')
                ->value($shop->zip ?? ''),
            Input::make('shop.address')
                ->title('Адрес')
                ->value($shop->address ?? '')
                ->required(),

            Input::make('shop.phone')
                ->title('Телефон')
                ->value($shop->phone ?? '')
                ->mask([
                    'mask' => '999 999 9999',
                    'numericInput' => true
                ]),
            DynamicInput::make('shop.additional_phones')
                ->title('Дополнительные номера телефонов')
                ->value($shop->phone ?? '')
                ->mask([
                    'mask' => '999 999 9999',
                    'numericInput' => true
                ])
                ->values(json_decode($shop->additional_phones, true) ?? []),

            Input::make('shop.whatsapp')
                ->title('Whatsapp')
                ->value($shop->whatsapp ?? ''),
            Input::make('shop.telegram')
                ->title('Telegram')
                ->value($shop->telegram ?? ''),
            Input::make('shop.vk')
                ->title('VK')
                ->value($shop->vk ?? ''),
            DynamicInput::make('shop.more_socials')
                ->title('Дополнительные социальные сети')
                ->values(json_decode($shop->more_socials, true) ?? [])
                ->useNames('Название', 'Ссылка'),
            DynamicInput::make('shop.web')
                ->title('Сайты')
                ->values(json_decode($shop->web, true) ?? []),
            DynamicInput::make('shop.emails')
                ->title('Почта')
                ->values(json_decode($shop->emails, true) ?? []),

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
