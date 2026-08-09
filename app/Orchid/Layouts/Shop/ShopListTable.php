<?php

namespace App\Orchid\Layouts\Shop;

use Illuminate\Support\Facades\Request;
use Orchid\Support\Facades\Layout;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Select;
use Orchid\Screen\Layouts\Table;
use Orchid\Screen\TD;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Actions\Button;
use Carbon\Carbon;
use App\Models\Shop;

class ShopListTable extends Table
{
    /**
     * Data source.
     *
     * The name of the key to fetch it from the query.
     * The results of which will be elements of the table.
     *
     * @var string
     */
    protected $target = 'shops';

    /**
     * Get the table cells to be displayed.
     *
     * @return TD[]
     */
    protected function columns(): iterable
    {
        $filter = Request::get('filter');
        $regionID = isset($filter['region_id']) ? $filter['region_id'] : null;
        $cityName = isset($filter['city_id']) ? $filter['city_id'] : null;

        $cityes = \App\Models\City::all();
        $opt = [];
        foreach ($cityes as $city) {
            $opt[$city->name] = $city->name;
        }

        return [
            TD::make('id', 'Ид')->sort()->popover('Идентификационный номер в системе')->filter(),
            TD::make('region_id', 'Регион')
                ->render(function (Shop $shop) {
                    return $shop->region?->name ?? '';
                })
                ->sort()
                ->filter(Select::make()->options(\App\Models\Region::pluck('name', 'id')->toArray())->empty(), null)
                ->filterValue(function ($value) {
                    $region = \App\Models\Region::find($value);
                    return $region?->name ?? '';
                }),
            TD::make('city_id', 'Город')->render(function (Shop $shop) {
                return $shop->city?->name ?? '';
            })->sort()->filter(Select::make()->options(\App\Models\City::when($regionID, function ($query) use ($regionID) {
                return $query->where('region_id', $regionID);
            })->pluck('name', 'id')->toArray())->empty(), null)
            ->filterValue(function ($value) {
                $city = \App\Models\City::find($value);
                return $city?->name ?? '';
            }),
            TD::make('area_id', 'Район')->render(function (Shop $shop) {
                return $shop->area?->name ?? '';
            })->sort()->defaultHidden(),
            TD::make('', 'Метро')->render(function (Shop $shop) {
                return $shop->subways->pluck('name')->implode(', ');
            })->defaultHidden(),
            TD::make('municipality_id', 'Муниципалитет')->render(function (Shop $shop) {
                return $shop->municipality?->name ?? '';
            })->sort()->defaultHidden(),
            TD::make('name', 'Название')->sort()->filter(),
            TD::make('title', 'Заголовок')->filter(TD::FILTER_TEXT)->popover('Заголовок для карточки сервисного центра')->defaultHidden(),
            TD::make('phone', 'Телефон')->filter(TD::FILTER_TEXT),
            TD::make('whatsapp', 'Watsapp')->defaultHidden()->filter(TD::FILTER_TEXT),
            TD::make('telegram', 'Telegram')->defaultHidden()->filter(TD::FILTER_TEXT),
            TD::make('vk', 'VK')->filter(TD::FILTER_TEXT)->defaultHidden(),
            TD::make('emails', 'Почта')->render(function (Shop $shop) {
                $emails = json_decode($shop->emails);
                $emails = is_array($emails) ? array_filter($emails, fn ($email) => is_scalar($email) && trim((string) $email) !== '') : [];
                return implode(', ', $emails);
            })->filter(TD::FILTER_TEXT)->defaultHidden(),
            TD::make('address', 'Адрес')->filter(TD::FILTER_TEXT),
            TD::make('convenience_shop', 'Круглосуточный')->render(function (Shop $shop) {
                return $shop->convenience_shop ? 'да' : 'нет';
            })->sort()->defaultHidden()->width('70px'),
            TD::make('appraisal_online', 'Оценка онлайн')->render(function (Shop $shop) {
                return $shop->appraisal_online ? 'да' : 'нет';
            })->sort()->defaultHidden()->width('70px'),
            TD::make('pawnshop', 'Ломбард')->render(function (Shop $shop) {
                return $shop->pawnshop ? 'да' : 'нет';
            })->sort()->defaultHidden()->width('70px'),
            TD::make('average_rating', 'Средний рейтинг')->sort()->filter(TD::FILTER_NUMBER_RANGE)->defaultHidden()->width('70px'),
            TD::make('show', 'Статус')->render(function (Shop $shop) {
                return $shop->show ? 'Показывать' : 'Не показывать';
            })->sort()->defaultHidden(),
            TD::make('created_at', 'Дата добавления')->render(function (Shop $shop) {
                return Carbon::parse($shop->created_at)->format('d.m.Y H:i');;
            })->sort()->filter(TD::FILTER_DATE_RANGE)->defaultHidden(),
            TD::make('updated_at', 'Дата добавления')->render(function (Shop $shop) {
                return Carbon::parse($shop->updated_at)->format('d.m.Y H:i');;
            })->sort()->filter(TD::FILTER_DATE_RANGE)->defaultHidden(),

            TD::make('Действия')->render(function (Shop $shop) {
                $editLink = Link::make('')
                    ->route('platform.shop.edit', $shop->id)
                    ->icon('bs.pencil');

                $deleteButton = Button::make('')
                    ->method('removePost')
                    ->confirm(__('Are you sure you want to delete this post?'))
                    ->parameters(['post_id' => $shop->id])
                    ->icon('bs.trash'); // Используйте 'trash' вместо 'icon-trash'

                return '<div class="d-flex">' . $editLink . ' ' . $deleteButton . '</div>';
            }),
        ];
    }
}
