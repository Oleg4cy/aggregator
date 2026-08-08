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

    private function locationRules(): array
    {
        return [
            'shop.region_id' => ['required', 'integer', 'exists:regions,id'],
            'shop.city_id' => ['required', 'integer', 'exists:cities,id'],
            'shop.area_id' => ['nullable', 'integer', 'exists:areas,id'],
            'subways' => ['nullable', 'array'],
            'subways.*' => ['integer', 'exists:subways,id'],
            'shop.lat' => ['nullable', 'numeric'],
            'shop.long' => ['nullable', 'numeric'],
        ];
    }

    private function locationAttributes(): array
    {
        return [
            'shop.region_id' => 'Регион',
            'shop.city_id' => 'Город',
            'shop.area_id' => 'Район',
            'subways' => 'Метро',
            'subways.*' => 'Станция метро',
            'shop.lat' => 'Широта',
            'shop.long' => 'Долгота',
        ];
    }

    private function locationMessages(): array
    {
        return [
            'shop.*.required' => 'Поле «:attribute» обязательно.',
            'shop.*.integer' => 'Поле «:attribute» должно быть целым числом.',
            'shop.*.exists' => 'Выбранное значение поля «:attribute» некорректно.',
            'subways.array' => 'Поле «:attribute» должно содержать список значений.',
            'subways.*.integer' => 'Поле «:attribute» должно быть целым числом.',
            'subways.*.exists' => 'Выбранное значение поля «:attribute» некорректно.',
            'shop.*.numeric' => 'Поле «:attribute» должно быть числом.',
        ];
    }

    private function normalizeLocationAttributes(array $attributes): array
    {
        foreach (['region_id', 'city_id'] as $column) {
            if (array_key_exists($column, $attributes)) {
                $attributes[$column] = (int) $attributes[$column];
            }
        }

        if (array_key_exists('area_id', $attributes)) {
            $attributes['area_id'] = $attributes['area_id'] === null || $attributes['area_id'] === ''
                ? null
                : (int) $attributes['area_id'];
        }

        if (array_key_exists('lat', $attributes) || array_key_exists('long', $attributes)) {
            $attributes['coord'] = json_encode([
                'lat' => $attributes['lat'] ?? null,
                'long' => $attributes['long'] ?? null,
            ]);
            unset($attributes['lat'], $attributes['long']);
        }

        return $attributes;
    }

    private function getSubwayIds(array $validated): array
    {
        return array_values(array_unique(array_map(
            'intval',
            $validated['subways'] ?? []
        )));
    }

    private function syncSubways(Shop $shop, array $subwayIds): void
    {
        $shop->subways()->sync($subwayIds);
    }

    public function save(Shop $shop, Request $request): void
    {
        [$categoryIds, $subCategoryIds] = $this->getCategorySelection($request);

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
            'shop.convenience_shop' => ['nullable', 'boolean'],
            'shop.appraisal_online' => ['nullable', 'boolean'],
            'shop.pawnshop' => ['nullable', 'boolean'],
            'shop.show' => ['nullable', 'boolean'],
            'shop.chain_id' => ['nullable', 'integer', 'exists:chains,id'],
        ] + $this->locationRules(), $this->locationMessages(), [
            'shop.name' => 'Название',
            'shop.title' => 'Заголовок',
            'shop.description' => 'Описание',
            'shop.zip' => 'Индекс',
            'shop.address' => 'Адрес',
            'shop.phone' => 'Телефон',
            'shop.additional_phones' => 'Дополнительные номера телефонов',
            'shop.additional_phones.*' => 'Дополнительный номер телефона',
            'shop.whatsapp' => 'Whatsapp',
            'shop.telegram' => 'Telegram',
            'shop.vk' => 'VK',
            'shop.web' => 'Сайты',
            'shop.web.*' => 'Сайт',
            'shop.more_socials' => 'Дополнительные социальные сети',
            'shop.more_socials.*.name' => 'Название социальной сети',
            'shop.more_socials.*.value' => 'Ссылка на социальную сеть',
            'shop.emails' => 'Почта',
            'shop.emails.*' => 'Адрес электронной почты',
            'shop.convenience_shop' => 'Круглосуточный магазин',
            'shop.appraisal_online' => 'Оценка онлайн',
            'shop.pawnshop' => 'Ломбард',
            'shop.show' => 'Показывать в списке',
            'shop.chain_id' => 'Сеть',
        ] + $this->locationAttributes());

        $attributes = $validated['shop'] ?? [];
        if (array_key_exists('chain_id', $attributes)) {
            $attributes['chain_id'] = $attributes['chain_id'] === null || $attributes['chain_id'] === ''
                ? null
                : (int) $attributes['chain_id'];
        }
        foreach (['additional_phones', 'web', 'emails'] as $column) {
            if (array_key_exists($column, $attributes)) {
                $values = is_array($attributes[$column]) ? $attributes[$column] : [];
                $attributes[$column] = json_encode(array_values(array_filter(
                    $values,
                    fn ($value) => is_scalar($value) && trim((string) $value) !== ''
                )));
            }
        }

        if (array_key_exists('more_socials', $attributes)) {
            $moreSocials = [];
            $socialRows = is_array($attributes['more_socials']) ? $attributes['more_socials'] : [];
            foreach ($socialRows as $key => $row) {
                if (is_array($row)) {
                    $socialName = $row['name'] ?? '';
                    $socialValue = $row['value'] ?? '';
                } else {
                    $socialName = $key;
                    $socialValue = $row;
                }

                if (!is_scalar($socialName) || !is_scalar($socialValue)) {
                    continue;
                }

                $socialName = trim((string) $socialName);
                $socialValue = trim((string) $socialValue);
                if ($socialName === '' && $socialValue === '') {
                    continue;
                }

                $moreSocials[$socialName] = $socialValue;
            }
            $attributes['more_socials'] = json_encode($moreSocials);
        }

        $attributes = $this->normalizeLocationAttributes($attributes);

        $shop->fill($attributes)->save();

        $subwayIds = $this->getSubwayIds($validated);
        $this->syncSubways($shop, $subwayIds);

        $this->syncCategories($shop, $categoryIds, $subCategoryIds);

        Toast::info('Изменения сохранены.');
    }

    public function saveDescription(Shop $shop, Request $request): void
    {
        $validated = $request->validate([
            'shop.name' => ['nullable', 'string'],
            'shop.title' => ['nullable', 'string'],
            'shop.description' => ['nullable', 'string'],
        ], [], [
            'shop.name' => 'Название',
            'shop.title' => 'Заголовок',
            'shop.description' => 'Описание',
        ]);

        $attributes = $validated['shop'] ?? [];
        $shop->fill([
            'name' => $attributes['name'] ?? null,
            'title' => $attributes['title'] ?? null,
            'description' => $attributes['description'] ?? null,
        ])->save();

        Toast::info('Описание сохранено.');
    }

    public function saveLocation(Shop $shop, Request $request): void
    {
        $validated = $request->validate(
            $this->locationRules(),
            $this->locationMessages(),
            $this->locationAttributes()
        );

        $attributes = $this->normalizeLocationAttributes($validated['shop'] ?? []);
        $attributes = array_intersect_key($attributes, array_flip([
            'region_id',
            'city_id',
            'area_id',
            'coord',
        ]));

        $shop->fill($attributes)->save();
        $this->syncSubways($shop, $this->getSubwayIds($validated));

        Toast::info('Местоположение сохранено.');
    }

    public function saveChain(Shop $shop, Request $request): void
    {
        $validated = $request->validate([
            'shop.chain_id' => ['nullable', 'integer', 'exists:chains,id'],
        ], [], [
            'shop.chain_id' => 'Сеть',
        ]);

        $chainId = $validated['shop']['chain_id'] ?? null;
        $shop->chain_id = $chainId === null || $chainId === ''
            ? null
            : (int) $chainId;
        $shop->save();

        Toast::info('Сеть сохранена.');
    }

    public function saveCategories(Shop $shop, Request $request): void
    {
        [$categoryIds, $subCategoryIds] = $this->getCategorySelection($request);
        $this->syncCategories($shop, $categoryIds, $subCategoryIds);

        Toast::info('Категории сохранены.');
    }

    private function getCategorySelection(Request $request): array
    {
        $validated = $request->validate([
            'category_id' => ['nullable', 'array'],
            'category_id.*' => ['integer', 'exists:categories,id'],
            'sub_categories' => ['nullable', 'array'],
            'sub_categories.*' => ['integer', 'exists:sub_categories,id'],
        ], [], [
            'category_id' => 'Категории',
            'category_id.*' => 'Категория',
            'sub_categories' => 'Подкатегории',
            'sub_categories.*' => 'Подкатегория',
        ]);

        $categoryIds = array_values(array_unique(array_map(
            'intval',
            $validated['category_id'] ?? []
        )));
        $subCategoryIds = array_values(array_unique(array_map(
            'intval',
            $validated['sub_categories'] ?? []
        )));

        return [$categoryIds, $subCategoryIds];
    }

    private function syncCategories(
        Shop $shop,
        array $categoryIds,
        array $subCategoryIds
    ): void {
        $shop->categories()->sync($categoryIds);
        $shop->subCategories()->sync($subCategoryIds);
    }

    public function edit(Request $request): void
    {
        \App\Helpers::log($request->all());
    }
}
