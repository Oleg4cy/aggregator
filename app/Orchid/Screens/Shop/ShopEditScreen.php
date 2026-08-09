<?php

namespace App\Orchid\Screens\Shop;

use App\Models\Shop;
use App\Models\ShopWorkingMode as ShopWorkingModeModel;
use App\Services\DayService;
use Carbon\Carbon;
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
            Layout::columns([
                ShopChain::class,
                ShopOptions::class,
            ]),
            Layout::columns([
                ShopLocation::class,
                ShopWorkingMode::class,
            ]),
            ShopDescription::class,
            Layout::columns([
                ShopContacts::class,
                ShopCategories::class,
            ]),
        ];
    }

    private function optionRules(): array
    {
        return [
            'shop.open_24_hours' => ['nullable', 'boolean'],
            'shop.online_estimate' => ['nullable', 'boolean'],
            'shop.warranty' => ['nullable', 'boolean'],
            'shop.onsite_repair' => ['nullable', 'boolean'],
            'shop.courier' => ['nullable', 'boolean'],
            'shop.original_parts' => ['nullable', 'boolean'],
            'shop.buyback' => ['nullable', 'boolean'],
            'shop.trade_in' => ['nullable', 'boolean'],
            'shop.buy_for_parts' => ['nullable', 'boolean'],
            'shop.show' => ['nullable', 'boolean'],
        ];
    }

    private function optionAttributes(): array
    {
        return [
            'shop.open_24_hours' => 'Круглосуточно',
            'shop.online_estimate' => 'Онлайн-оценка ремонта',
            'shop.warranty' => 'Гарантия на ремонт',
            'shop.onsite_repair' => 'Выезд мастера',
            'shop.courier' => 'Забор и доставка курьером',
            'shop.original_parts' => 'Оригинальные запчасти',
            'shop.buyback' => 'Выкуп техники',
            'shop.trade_in' => 'Trade-in',
            'shop.buy_for_parts' => 'Выкуп на запчасти',
            'shop.show' => 'Показывать в списке',
        ];
    }

    private function optionMessages(): array
    {
        return [
            'shop.*.boolean' => 'Поле «:attribute» должно иметь значение да или нет.',
        ];
    }

    private function normalizeOptionAttributes(array $attributes): array
    {
        foreach (['open_24_hours', 'online_estimate', 'warranty', 'onsite_repair', 'courier', 'original_parts', 'buyback', 'trade_in', 'buy_for_parts', 'show'] as $column) {
            if (array_key_exists($column, $attributes)) {
                $attributes[$column] = in_array($attributes[$column], [true, 1, '1'], true);
            }
        }

        return $attributes;
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

    private function contactRules(): array
    {
        return [
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
        ];
    }

    private function contactAttributes(): array
    {
        return [
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
        ];
    }

    private function contactMessages(): array
    {
        return [
            'shop.*.string' => 'Поле «:attribute» должно быть строкой.',
            'shop.*.*.string' => 'Поле «:attribute» должно быть строкой.',
            'shop.*.*.*.string' => 'Поле «:attribute» должно быть строкой.',
            'shop.*.array' => 'Поле «:attribute» должно содержать список значений.',
        ];
    }

    private function normalizeContactAttributes(array $attributes, bool $clearMissingDynamic = false): array
    {
        $dynamicColumns = ['additional_phones', 'web', 'emails', 'more_socials'];
        if ($clearMissingDynamic) {
            foreach ($dynamicColumns as $column) {
                if (!array_key_exists($column, $attributes)) {
                    $attributes[$column] = [];
                }
            }
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

    private function workingModeRules(): array
    {
        $rules = [
            'working_mode' => ['required', 'array', 'size:7'],
        ];

        foreach (range(1, 7) as $day) {
            $rules["working_mode.$day"] = ['required', 'array'];
            $rules["working_mode.$day.open"] = ['nullable', 'regex:/^([01][0-9]|2[0-3]):[0-5][0-9]$/'];
            $rules["working_mode.$day.close"] = ['nullable', 'regex:/^([01][0-9]|2[0-3]):[0-5][0-9]$/'];
            $rules["working_mode.$day.is_day_off"] = ['required', 'boolean'];
        }

        return $rules;
    }

    private function workingModeAttributes(): array
    {
        $attributes = ['working_mode' => 'Режим работы'];

        foreach (range(1, 7) as $day) {
            $dayName = DayService::getDayByNum($day);
            $attributes["working_mode.$day.open"] = "Время открытия, $dayName";
            $attributes["working_mode.$day.close"] = "Время закрытия, $dayName";
            $attributes["working_mode.$day.is_day_off"] = "Выходной, $dayName";
        }

        return $attributes;
    }

    private function workingModeMessages(): array
    {
        return [
            'working_mode.required' => 'Поле «:attribute» обязательно.',
            'working_mode.array' => 'Поле «:attribute» должно содержать список значений.',
            'working_mode.size' => 'Поле «:attribute» должно содержать семь дней.',
            'working_mode.*.required' => 'Поле «:attribute» обязательно.',
            'working_mode.*.array' => 'Поле «:attribute» должно содержать данные дня.',
            'working_mode.*.*.required' => 'Поле «:attribute» обязательно.',
            'working_mode.*.*.boolean' => 'Поле «:attribute» должно иметь значение да или нет.',
            'working_mode.*.*.regex' => 'Поле «:attribute» должно содержать корректное время.',
        ];
    }

    private function normalizeWorkingMode(array $validated): array
    {
        $normalized = [];

        foreach (range(1, 7) as $day) {
            $row = $validated['working_mode'][$day];
            $normalized[$day] = [
                'is_open' => !(bool) $row['is_day_off'],
                'open_time' => $this->normalizeWorkingTime($row['open'] ?? null),
                'close_time' => $this->normalizeWorkingTime($row['close'] ?? null),
            ];
        }

        return $normalized;
    }

    private function normalizeWorkingTime(?string $time): ?string
    {
        if ($time === null || $time === '') {
            return null;
        }

        return Carbon::createFromFormat('H:i', $time)->format('H:i:s');
    }

    private function syncWorkingMode(Shop $shop, array $workingMode): void
    {
        $existing = ShopWorkingModeModel::getByShopID($shop->id)->get()->keyBy('day_of_week');

        foreach (range(1, 7) as $day) {
            $mode = $existing->get($day) ?? new ShopWorkingModeModel();
            if (!$mode->exists) {
                $mode->shop_id = $shop->id;
                $mode->day_of_week = $day;
            }
            $mode->is_open = $workingMode[$day]['is_open'];
            $mode->open_time = $workingMode[$day]['open_time'];
            $mode->close_time = $workingMode[$day]['close_time'];
            $mode->save();
        }
    }

    public function saveWorkingMode(Shop $shop, Request $request): void
    {
        $validated = $request->validate(
            $this->workingModeRules(),
            $this->workingModeMessages(),
            $this->workingModeAttributes()
        );

        $this->syncWorkingMode($shop, $this->normalizeWorkingMode($validated));

        Toast::info('Режим работы сохранён.');
    }

    public function save(Shop $shop, Request $request): void
    {
        [$categoryIds, $subCategoryIds] = $this->getCategorySelection($request);

        $validated = $request->validate([
            'shop.name' => ['nullable', 'string'],
            'shop.title' => ['nullable', 'string'],
            'shop.description' => ['nullable', 'string'],
            'shop.chain_id' => ['nullable', 'integer', 'exists:chains,id'],
        ] + $this->optionRules() + $this->contactRules() + $this->locationRules() + $this->workingModeRules(), array_merge($this->optionMessages(), $this->contactMessages(), $this->locationMessages(), $this->workingModeMessages()), [
            'shop.name' => 'Название',
            'shop.title' => 'Заголовок',
            'shop.description' => 'Описание',
            'shop.chain_id' => 'Сеть',
        ] + $this->optionAttributes() + $this->contactAttributes() + $this->locationAttributes() + $this->workingModeAttributes());

        $workingMode = $this->normalizeWorkingMode($validated);

        $attributes = $validated['shop'] ?? [];
        $attributes = $this->normalizeOptionAttributes($attributes);
        if (array_key_exists('chain_id', $attributes)) {
            $attributes['chain_id'] = $attributes['chain_id'] === null || $attributes['chain_id'] === ''
                ? null
                : (int) $attributes['chain_id'];
        }
        $attributes = $this->normalizeContactAttributes($attributes, true);

        $attributes = $this->normalizeLocationAttributes($attributes);

        $shop->fill($attributes)->save();

        $subwayIds = $this->getSubwayIds($validated);
        $this->syncSubways($shop, $subwayIds);

        $this->syncCategories($shop, $categoryIds, $subCategoryIds);

        $this->syncWorkingMode($shop, $workingMode);

        Toast::info('Изменения сохранены.');
    }

    public function saveOptions(Shop $shop, Request $request): void
    {
        $validated = $request->validate(
            $this->optionRules(),
            $this->optionMessages(),
            $this->optionAttributes()
        );

        $attributes = $validated['shop'] ?? [];
        $attributes = array_intersect_key($attributes, array_flip([
            'open_24_hours',
            'online_estimate',
            'warranty',
            'onsite_repair',
            'courier',
            'original_parts',
            'buyback',
            'trade_in',
            'buy_for_parts',
            'show',
        ]));
        $attributes = $this->normalizeOptionAttributes($attributes);

        $shop->fill($attributes)->save();

        Toast::info('Опции сохранены.');
    }

    public function saveContacts(Shop $shop, Request $request): void
    {
        $validated = $request->validate(
            $this->contactRules(),
            $this->contactMessages(),
            $this->contactAttributes()
        );

        $attributes = $this->normalizeContactAttributes($validated['shop'] ?? [], true);
        $attributes = array_intersect_key($attributes, array_flip([
            'zip',
            'address',
            'phone',
            'additional_phones',
            'whatsapp',
            'telegram',
            'vk',
            'more_socials',
            'web',
            'emails',
        ]));

        $shop->fill($attributes)->save();

        Toast::info('Контакты сохранены.');
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

        Toast::info('Типы техники сохранены.');
    }

    private function getCategorySelection(Request $request): array
    {
        $validated = $request->validate([
            'category_id' => ['nullable', 'array'],
            'category_id.*' => ['integer', 'exists:categories,id'],
            'sub_categories' => ['nullable', 'array'],
            'sub_categories.*' => ['integer', 'exists:sub_categories,id'],
        ], [], [
            'category_id' => 'Типы техники',
            'category_id.*' => 'Тип техники',
            'sub_categories' => 'Бренды',
            'sub_categories.*' => 'Бренд',
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
