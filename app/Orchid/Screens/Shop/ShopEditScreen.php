<?php

namespace App\Orchid\Screens\Shop;

use App\Models\Shop;
use App\Orchid\Layouts\Shop\Edit\ShopCategories;
use Illuminate\Http\Request;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Screen;
use App\Orchid\Layouts\Shop\Edit\ShopChain;
use App\Orchid\Layouts\Shop\Edit\ShopContacts;
use App\Orchid\Layouts\Shop\Edit\ShopDescription;
use App\Orchid\Layouts\Shop\Edit\ShopLocation;
use App\Orchid\Layouts\Shop\Edit\ShopOptions;
use App\Orchid\Layouts\Shop\Edit\ShopWorkingMode;
use Orchid\Support\Facades\Layout;
use Orchid\Support\Facades\Toast;
use Orchid\Screen\Actions\ModalToggle;

class ShopEditScreen extends Screen
{
    public $shop;
    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query(Shop $shop): iterable
    {
        $this->shop = $shop;

        return [
            'shop' => $shop,
        ];
    }

    /**
     * The name of the screen displayed in the header.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return 'Редактирование';
    }

    /**
     * The screen's action buttons.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): iterable
    {
        return [
            Button::make('Сохранить все изменения')->icon('bs.floppy')->method('save')->turbo(false),
        ];
    }

    /**
     * The screen's layout elements.
     *
     * @return \Orchid\Screen\Layout[]|string[]
     */
    public function layout(): iterable
    {
        return [
            ShopChain::class,
            ShopLocation::class,
            ShopDescription::class,
            ShopContacts::class,
            ShopOptions::class,
            ShopWorkingMode::class,
            ShopCategories::class,
        ];
    }

    public function save(Shop $shop, Request $request): void
    {
        $validated = $request->validate([
            'shop.name' => ['nullable', 'string'],
            'shop.title' => ['nullable', 'string'],
            'shop.description' => ['nullable', 'string'],
            'shop.zip' => ['nullable', 'string'],
            'shop.address' => ['nullable', 'string'],
            'shop.phone' => ['nullable', 'string'],
            'shop.additional_phones' => ['nullable', 'array'],
            'shop.additional_phones.*' => ['nullable', 'string'],
            'shop.whatsapp' => ['nullable', 'string'],
            'shop.telegram' => ['nullable', 'string'],
            'shop.vk' => ['nullable', 'string'],
            'shop.web' => ['nullable', 'array'],
            'shop.web.*' => ['nullable', 'string'],
            'shop.more_socials' => ['nullable', 'array'],
            'shop.more_socials.*.name' => ['nullable', 'string'],
            'shop.more_socials.*.value' => ['nullable', 'string'],
            'shop.emails' => ['nullable', 'array'],
            'shop.emails.*' => ['nullable', 'string'],
            'shop.lat' => ['nullable', 'numeric'],
            'shop.long' => ['nullable', 'numeric'],
            'shop.convenience_shop' => ['nullable', 'boolean'],
            'shop.appraisal_online' => ['nullable', 'boolean'],
            'shop.pawnshop' => ['nullable', 'boolean'],
            'shop.show' => ['nullable', 'boolean'],
        ]);

        $attributes = $validated['shop'] ?? [];
        foreach (['additional_phones', 'web', 'more_socials', 'emails'] as $column) {
            if (array_key_exists($column, $attributes)) {
                $attributes[$column] = json_encode($attributes[$column]);
            }
        }

        if (array_key_exists('lat', $attributes) || array_key_exists('long', $attributes)) {
            $attributes['coord'] = json_encode([
                'lat' => $attributes['lat'] ?? null,
                'long' => $attributes['long'] ?? null,
            ]);
            unset($attributes['lat'], $attributes['long']);
        }

        $shop->fill($attributes)->save();

        Toast::info('Изменения сохранены.');
    }

    public function edit(Request $request): void
    {
        \App\Helpers::log($request->all());
    }
}
