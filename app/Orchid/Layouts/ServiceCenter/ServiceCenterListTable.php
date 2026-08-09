<?php

namespace App\Orchid\Layouts\ServiceCenter;

use Illuminate\Support\Facades\Request;
use Orchid\Support\Facades\Layout;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Select;
use Orchid\Screen\Layouts\Table;
use Orchid\Screen\TD;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Actions\Button;
use Carbon\Carbon;
use App\Models\ServiceCenter;

class ServiceCenterListTable extends Table
{
    /**
     * Data source.
     *
     * The name of the key to fetch it from the query.
     * The results of which will be elements of the table.
     *
     * @var string
     */
    protected $target = 'serviceCenters';

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
                ->render(function (ServiceCenter $serviceCenter) {
                    return $serviceCenter->region?->name ?? '';
                })
                ->sort()
                ->filter(Select::make()->options(\App\Models\Region::pluck('name', 'id')->toArray())->empty(), null)
                ->filterValue(function ($value) {
                    $region = \App\Models\Region::find($value);
                    return $region?->name ?? '';
                }),
            TD::make('city_id', 'Город')->render(function (ServiceCenter $serviceCenter) {
                return $serviceCenter->city?->name ?? '';
            })->sort()->filter(Select::make()->options(\App\Models\City::when($regionID, function ($query) use ($regionID) {
                return $query->where('region_id', $regionID);
            })->pluck('name', 'id')->toArray())->empty(), null)
            ->filterValue(function ($value) {
                $city = \App\Models\City::find($value);
                return $city?->name ?? '';
            }),
            TD::make('area_id', 'Район')->render(function (ServiceCenter $serviceCenter) {
                return $serviceCenter->area?->name ?? '';
            })->sort()->defaultHidden(),
            TD::make('', 'Метро')->render(function (ServiceCenter $serviceCenter) {
                return $serviceCenter->subways->pluck('name')->implode(', ');
            })->defaultHidden(),
            TD::make('municipality_id', 'Муниципалитет')->render(function (ServiceCenter $serviceCenter) {
                return $serviceCenter->municipality?->name ?? '';
            })->sort()->defaultHidden(),
            TD::make('name', 'Название')->sort()->filter(),
            TD::make('title', 'Заголовок')->filter(TD::FILTER_TEXT)->popover('Заголовок для карточки сервисного центра')->defaultHidden(),
            TD::make('phone', 'Телефон')->filter(TD::FILTER_TEXT),
            TD::make('whatsapp', 'Watsapp')->defaultHidden()->filter(TD::FILTER_TEXT),
            TD::make('telegram', 'Telegram')->defaultHidden()->filter(TD::FILTER_TEXT),
            TD::make('vk', 'VK')->filter(TD::FILTER_TEXT)->defaultHidden(),
            TD::make('emails', 'Почта')->render(function (ServiceCenter $serviceCenter) {
                $emails = json_decode($serviceCenter->emails);
                $emails = is_array($emails) ? array_filter($emails, fn ($email) => is_scalar($email) && trim((string) $email) !== '') : [];
                return implode(', ', $emails);
            })->filter(TD::FILTER_TEXT)->defaultHidden(),
            TD::make('address', 'Адрес')->filter(TD::FILTER_TEXT),
            TD::make('open_24_hours', 'Круглосуточно')->render(function (ServiceCenter $serviceCenter) {
                return $serviceCenter->open_24_hours ? 'да' : 'нет';
            })->sort()->defaultHidden()->width('70px'),
            TD::make('online_estimate', 'Онлайн-оценка ремонта')->render(function (ServiceCenter $serviceCenter) {
                return $serviceCenter->online_estimate ? 'да' : 'нет';
            })->sort()->defaultHidden()->width('70px'),
            TD::make('warranty', 'Гарантия')->render(function (ServiceCenter $serviceCenter) {
                return $serviceCenter->warranty ? 'да' : 'нет';
            })->sort()->defaultHidden()->width('70px'),
            TD::make('onsite_repair', 'Выезд мастера')->render(function (ServiceCenter $serviceCenter) {
                return $serviceCenter->onsite_repair ? 'да' : 'нет';
            })->sort()->defaultHidden()->width('70px'),
            TD::make('courier', 'Курьер')->render(function (ServiceCenter $serviceCenter) {
                return $serviceCenter->courier ? 'да' : 'нет';
            })->sort()->defaultHidden()->width('70px'),
            TD::make('original_parts', 'Оригинальные запчасти')->render(function (ServiceCenter $serviceCenter) {
                return $serviceCenter->original_parts ? 'да' : 'нет';
            })->sort()->defaultHidden()->width('70px'),
            TD::make('buyback', 'Выкуп')->render(function (ServiceCenter $serviceCenter) {
                return $serviceCenter->buyback ? 'да' : 'нет';
            })->sort()->defaultHidden()->width('70px'),
            TD::make('trade_in', 'Trade-in')->render(function (ServiceCenter $serviceCenter) {
                return $serviceCenter->trade_in ? 'да' : 'нет';
            })->sort()->defaultHidden()->width('70px'),
            TD::make('buy_for_parts', 'Выкуп на запчасти')->render(function (ServiceCenter $serviceCenter) {
                return $serviceCenter->buy_for_parts ? 'да' : 'нет';
            })->sort()->defaultHidden()->width('70px'),
            TD::make('average_rating', 'Средний рейтинг')->sort()->filter(TD::FILTER_NUMBER_RANGE)->defaultHidden()->width('70px'),
            TD::make('show', 'Статус')->render(function (ServiceCenter $serviceCenter) {
                return $serviceCenter->show ? 'Показывать' : 'Не показывать';
            })->sort()->defaultHidden(),
            TD::make('created_at', 'Дата добавления')->render(function (ServiceCenter $serviceCenter) {
                return Carbon::parse($serviceCenter->created_at)->format('d.m.Y H:i');;
            })->sort()->filter(TD::FILTER_DATE_RANGE)->defaultHidden(),
            TD::make('updated_at', 'Дата добавления')->render(function (ServiceCenter $serviceCenter) {
                return Carbon::parse($serviceCenter->updated_at)->format('d.m.Y H:i');;
            })->sort()->filter(TD::FILTER_DATE_RANGE)->defaultHidden(),

            TD::make('Действия')->render(function (ServiceCenter $serviceCenter) {
                $editLink = Link::make('')
                    ->route('platform.service-centers.edit', $serviceCenter->id)
                    ->icon('bs.pencil');

                $deleteButton = Button::make('')
                    ->method('removePost')
                    ->confirm(__('Are you sure you want to delete this post?'))
                    ->parameters(['post_id' => $serviceCenter->id])
                    ->icon('bs.trash'); // Используйте 'trash' вместо 'icon-trash'

                return '<div class="d-flex">' . $editLink . ' ' . $deleteButton . '</div>';
            }),
        ];
    }
}
