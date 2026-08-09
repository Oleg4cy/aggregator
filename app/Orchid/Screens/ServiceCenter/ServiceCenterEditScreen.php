<?php

namespace App\Orchid\Screens\ServiceCenter;

use App\Models\ServiceCenter;
use App\Models\ServiceCenterWorkingHour;
use App\Services\DayService;
use Carbon\Carbon;
use App\Orchid\Layouts\ServiceCenter\Edit\ServiceCenterEquipmentTypesAndBrands;
use Illuminate\Http\Request;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Screen;
use App\Orchid\Layouts\ServiceCenter\Edit\ServiceCenterNetwork;
use App\Orchid\Layouts\ServiceCenter\Edit\ServiceCenterContacts;
use App\Orchid\Layouts\ServiceCenter\Edit\ServiceCenterDescription;
use App\Orchid\Layouts\ServiceCenter\Edit\ServiceCenterLocation;
use App\Orchid\Layouts\ServiceCenter\Edit\ServiceCenterOptions;
use App\Orchid\Layouts\ServiceCenter\Edit\ServiceCenterWorkingHours;
use Orchid\Support\Facades\Layout;
use Orchid\Support\Facades\Toast;
use Orchid\Screen\Actions\ModalToggle;

class ServiceCenterEditScreen extends Screen
{
    public $serviceCenter;
    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query(ServiceCenter $serviceCenter): iterable
    {
        $this->serviceCenter = $serviceCenter;

        return [
            'serviceCenter' => $serviceCenter,
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
                ServiceCenterNetwork::class,
                ServiceCenterOptions::class,
            ]),
            Layout::columns([
                ServiceCenterLocation::class,
                ServiceCenterWorkingHours::class,
            ]),
            ServiceCenterDescription::class,
            Layout::columns([
                ServiceCenterContacts::class,
                ServiceCenterEquipmentTypesAndBrands::class,
            ]),
        ];
    }

    private function optionRules(): array
    {
        return [
            'serviceCenter.open_24_hours' => ['nullable', 'boolean'],
            'serviceCenter.online_estimate' => ['nullable', 'boolean'],
            'serviceCenter.warranty' => ['nullable', 'boolean'],
            'serviceCenter.onsite_repair' => ['nullable', 'boolean'],
            'serviceCenter.courier' => ['nullable', 'boolean'],
            'serviceCenter.original_parts' => ['nullable', 'boolean'],
            'serviceCenter.buyback' => ['nullable', 'boolean'],
            'serviceCenter.trade_in' => ['nullable', 'boolean'],
            'serviceCenter.buy_for_parts' => ['nullable', 'boolean'],
            'serviceCenter.show' => ['nullable', 'boolean'],
        ];
    }

    private function optionAttributes(): array
    {
        return [
            'serviceCenter.open_24_hours' => 'Круглосуточно',
            'serviceCenter.online_estimate' => 'Онлайн-оценка ремонта',
            'serviceCenter.warranty' => 'Гарантия на ремонт',
            'serviceCenter.onsite_repair' => 'Выезд мастера',
            'serviceCenter.courier' => 'Забор и доставка курьером',
            'serviceCenter.original_parts' => 'Оригинальные запчасти',
            'serviceCenter.buyback' => 'Выкуп техники',
            'serviceCenter.trade_in' => 'Trade-in',
            'serviceCenter.buy_for_parts' => 'Выкуп на запчасти',
            'serviceCenter.show' => 'Показывать в списке',
        ];
    }

    private function optionMessages(): array
    {
        return [
            'serviceCenter.*.boolean' => 'Поле «:attribute» должно иметь значение да или нет.',
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
            'serviceCenter.region_id' => ['required', 'integer', 'exists:regions,id'],
            'serviceCenter.city_id' => ['required', 'integer', 'exists:cities,id'],
            'serviceCenter.area_id' => ['nullable', 'integer', 'exists:areas,id'],
            'subways' => ['nullable', 'array'],
            'subways.*' => ['integer', 'exists:subways,id'],
            'serviceCenter.lat' => ['nullable', 'numeric'],
            'serviceCenter.long' => ['nullable', 'numeric'],
        ];
    }

    private function locationAttributes(): array
    {
        return [
            'serviceCenter.region_id' => 'Регион',
            'serviceCenter.city_id' => 'Город',
            'serviceCenter.area_id' => 'Район',
            'subways' => 'Метро',
            'subways.*' => 'Станция метро',
            'serviceCenter.lat' => 'Широта',
            'serviceCenter.long' => 'Долгота',
        ];
    }

    private function locationMessages(): array
    {
        return [
            'serviceCenter.*.required' => 'Поле «:attribute» обязательно.',
            'serviceCenter.*.integer' => 'Поле «:attribute» должно быть целым числом.',
            'serviceCenter.*.exists' => 'Выбранное значение поля «:attribute» некорректно.',
            'subways.array' => 'Поле «:attribute» должно содержать список значений.',
            'subways.*.integer' => 'Поле «:attribute» должно быть целым числом.',
            'subways.*.exists' => 'Выбранное значение поля «:attribute» некорректно.',
            'serviceCenter.*.numeric' => 'Поле «:attribute» должно быть числом.',
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
            'serviceCenter.zip' => ['nullable', 'string'],
            'serviceCenter.address' => ['nullable', 'string'],
            'serviceCenter.phone' => ['nullable', 'string'],
            'serviceCenter.additional_phones' => ['nullable', 'array'],
            'serviceCenter.additional_phones.*' => ['nullable', 'string'],
            'serviceCenter.whatsapp' => ['nullable', 'string'],
            'serviceCenter.telegram' => ['nullable', 'string'],
            'serviceCenter.vk' => ['nullable', 'string'],
            'serviceCenter.web' => ['nullable', 'array'],
            'serviceCenter.web.*' => ['nullable', 'string'],
            'serviceCenter.more_socials' => ['nullable', 'array'],
            'serviceCenter.more_socials.*.name' => ['nullable', 'string'],
            'serviceCenter.more_socials.*.value' => ['nullable', 'string'],
            'serviceCenter.emails' => ['nullable', 'array'],
            'serviceCenter.emails.*' => ['nullable', 'string'],
        ];
    }

    private function contactAttributes(): array
    {
        return [
            'serviceCenter.zip' => 'Индекс',
            'serviceCenter.address' => 'Адрес',
            'serviceCenter.phone' => 'Телефон',
            'serviceCenter.additional_phones' => 'Дополнительные номера телефонов',
            'serviceCenter.additional_phones.*' => 'Дополнительный номер телефона',
            'serviceCenter.whatsapp' => 'Whatsapp',
            'serviceCenter.telegram' => 'Telegram',
            'serviceCenter.vk' => 'VK',
            'serviceCenter.web' => 'Сайты',
            'serviceCenter.web.*' => 'Сайт',
            'serviceCenter.more_socials' => 'Дополнительные социальные сети',
            'serviceCenter.more_socials.*.name' => 'Название социальной сети',
            'serviceCenter.more_socials.*.value' => 'Ссылка на социальную сеть',
            'serviceCenter.emails' => 'Почта',
            'serviceCenter.emails.*' => 'Адрес электронной почты',
        ];
    }

    private function contactMessages(): array
    {
        return [
            'serviceCenter.*.string' => 'Поле «:attribute» должно быть строкой.',
            'serviceCenter.*.*.string' => 'Поле «:attribute» должно быть строкой.',
            'serviceCenter.*.*.*.string' => 'Поле «:attribute» должно быть строкой.',
            'serviceCenter.*.array' => 'Поле «:attribute» должно содержать список значений.',
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

    private function syncSubways(ServiceCenter $serviceCenter, array $subwayIds): void
    {
        $serviceCenter->subways()->sync($subwayIds);
    }

    private function workingHoursRules(): array
    {
        $rules = [
            'working_hours' => ['required', 'array', 'size:7'],
        ];

        foreach (range(1, 7) as $day) {
            $rules["working_hours.$day"] = ['required', 'array'];
            $rules["working_hours.$day.open"] = ['nullable', 'regex:/^([01][0-9]|2[0-3]):[0-5][0-9]$/'];
            $rules["working_hours.$day.close"] = ['nullable', 'regex:/^([01][0-9]|2[0-3]):[0-5][0-9]$/'];
            $rules["working_hours.$day.is_day_off"] = ['required', 'boolean'];
        }

        return $rules;
    }

    private function workingHoursAttributes(): array
    {
        $attributes = ['working_hours' => 'Режим работы'];

        foreach (range(1, 7) as $day) {
            $dayName = DayService::getDayByNum($day);
            $attributes["working_hours.$day.open"] = "Время открытия, $dayName";
            $attributes["working_hours.$day.close"] = "Время закрытия, $dayName";
            $attributes["working_hours.$day.is_day_off"] = "Выходной, $dayName";
        }

        return $attributes;
    }

    private function workingHoursMessages(): array
    {
        return [
            'working_hours.required' => 'Поле «:attribute» обязательно.',
            'working_hours.array' => 'Поле «:attribute» должно содержать список значений.',
            'working_hours.size' => 'Поле «:attribute» должно содержать семь дней.',
            'working_hours.*.required' => 'Поле «:attribute» обязательно.',
            'working_hours.*.array' => 'Поле «:attribute» должно содержать данные дня.',
            'working_hours.*.*.required' => 'Поле «:attribute» обязательно.',
            'working_hours.*.*.boolean' => 'Поле «:attribute» должно иметь значение да или нет.',
            'working_hours.*.*.regex' => 'Поле «:attribute» должно содержать корректное время.',
        ];
    }

    private function normalizeWorkingHours(array $validated): array
    {
        $normalized = [];

        foreach (range(1, 7) as $day) {
            $row = $validated['working_hours'][$day];
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

    private function syncWorkingHours(ServiceCenter $serviceCenter, array $workingHours): void
    {
        $existing = ServiceCenterWorkingHour::getByServiceCenterId($serviceCenter->id)->get()->keyBy('day_of_week');

        foreach (range(1, 7) as $day) {
            $mode = $existing->get($day) ?? new ServiceCenterWorkingHour();
            if (!$mode->exists) {
                $mode->service_center_id = $serviceCenter->id;
                $mode->day_of_week = $day;
            }
            $mode->is_open = $workingHours[$day]['is_open'];
            $mode->open_time = $workingHours[$day]['open_time'];
            $mode->close_time = $workingHours[$day]['close_time'];
            $mode->save();
        }
    }

    public function saveWorkingHours(ServiceCenter $serviceCenter, Request $request): void
    {
        $validated = $request->validate(
            $this->workingHoursRules(),
            $this->workingHoursMessages(),
            $this->workingHoursAttributes()
        );

        $this->syncWorkingHours($serviceCenter, $this->normalizeWorkingHours($validated));

        Toast::info('Режим работы сохранён.');
    }

    public function save(ServiceCenter $serviceCenter, Request $request): void
    {
        [$equipmentTypeIds, $brandIds] = $this->getEquipmentTypeAndBrandSelection($request);

        $validated = $request->validate([
            'serviceCenter.name' => ['nullable', 'string'],
            'serviceCenter.title' => ['nullable', 'string'],
            'serviceCenter.description' => ['nullable', 'string'],
            'serviceCenter.service_network_id' => ['nullable', 'integer', 'exists:service_networks,id'],
        ] + $this->optionRules() + $this->contactRules() + $this->locationRules() + $this->workingHoursRules(), array_merge($this->optionMessages(), $this->contactMessages(), $this->locationMessages(), $this->workingHoursMessages()), [
            'serviceCenter.name' => 'Название',
            'serviceCenter.title' => 'Заголовок',
            'serviceCenter.description' => 'Описание',
            'serviceCenter.service_network_id' => 'Сеть',
        ] + $this->optionAttributes() + $this->contactAttributes() + $this->locationAttributes() + $this->workingHoursAttributes());

        $workingHours = $this->normalizeWorkingHours($validated);

        $attributes = $validated['serviceCenter'] ?? [];
        $attributes = $this->normalizeOptionAttributes($attributes);
        $attributes = $this->normalizeContactAttributes($attributes, true);

        $attributes = $this->normalizeLocationAttributes($attributes);

        $serviceCenter->fill($attributes)->save();

        $subwayIds = $this->getSubwayIds($validated);
        $this->syncSubways($serviceCenter, $subwayIds);

        $this->syncEquipmentTypesAndBrands($serviceCenter, $equipmentTypeIds, $brandIds);

        $this->syncWorkingHours($serviceCenter, $workingHours);

        Toast::info('Изменения сохранены.');
    }

    public function saveOptions(ServiceCenter $serviceCenter, Request $request): void
    {
        $validated = $request->validate(
            $this->optionRules(),
            $this->optionMessages(),
            $this->optionAttributes()
        );

        $attributes = $validated['serviceCenter'] ?? [];
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

        $serviceCenter->fill($attributes)->save();

        Toast::info('Опции сохранены.');
    }

    public function saveContacts(ServiceCenter $serviceCenter, Request $request): void
    {
        $validated = $request->validate(
            $this->contactRules(),
            $this->contactMessages(),
            $this->contactAttributes()
        );

        $attributes = $this->normalizeContactAttributes($validated['serviceCenter'] ?? [], true);
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

        $serviceCenter->fill($attributes)->save();

        Toast::info('Контакты сохранены.');
    }

    public function saveDescription(ServiceCenter $serviceCenter, Request $request): void
    {
        $validated = $request->validate([
            'serviceCenter.name' => ['nullable', 'string'],
            'serviceCenter.title' => ['nullable', 'string'],
            'serviceCenter.description' => ['nullable', 'string'],
        ], [], [
            'serviceCenter.name' => 'Название',
            'serviceCenter.title' => 'Заголовок',
            'serviceCenter.description' => 'Описание',
        ]);

        $attributes = $validated['serviceCenter'] ?? [];
        $serviceCenter->fill([
            'name' => $attributes['name'] ?? null,
            'title' => $attributes['title'] ?? null,
            'description' => $attributes['description'] ?? null,
        ])->save();

        Toast::info('Описание сохранено.');
    }

    public function saveLocation(ServiceCenter $serviceCenter, Request $request): void
    {
        $validated = $request->validate(
            $this->locationRules(),
            $this->locationMessages(),
            $this->locationAttributes()
        );

        $attributes = $this->normalizeLocationAttributes($validated['serviceCenter'] ?? []);
        $attributes = array_intersect_key($attributes, array_flip([
            'region_id',
            'city_id',
            'area_id',
            'coord',
        ]));

        $serviceCenter->fill($attributes)->save();
        $this->syncSubways($serviceCenter, $this->getSubwayIds($validated));

        Toast::info('Местоположение сохранено.');
    }

    public function saveNetwork(ServiceCenter $serviceCenter, Request $request): void
    {
        $validated = $request->validate([
            'serviceCenter.service_network_id' => ['nullable', 'integer', 'exists:service_networks,id'],
        ], [], [
            'serviceCenter.service_network_id' => 'Сеть',
        ]);

        $serviceNetworkId = $validated['serviceCenter']['service_network_id'] ?? null;
        $serviceCenter->service_network_id = $serviceNetworkId === null || $serviceNetworkId === ''
            ? null
            : (int) $serviceNetworkId;
        $serviceCenter->save();

        Toast::info('Сеть сохранена.');
    }

    public function saveEquipmentTypesAndBrands(ServiceCenter $serviceCenter, Request $request): void
    {
        [$equipmentTypeIds, $brandIds] = $this->getEquipmentTypeAndBrandSelection($request);
        $this->syncEquipmentTypesAndBrands($serviceCenter, $equipmentTypeIds, $brandIds);

        Toast::info('Типы техники сохранены.');
    }

    private function getEquipmentTypeAndBrandSelection(Request $request): array
    {
        $validated = $request->validate([
            'equipment_type_id' => ['nullable', 'array'],
            'equipment_type_id.*' => ['integer', 'exists:equipment_types,id'],
            'brand_id' => ['nullable', 'array'],
            'brand_id.*' => ['integer', 'exists:brands,id'],
        ], [], [
            'equipment_type_id' => 'Типы техники',
            'equipment_type_id.*' => 'Тип техники',
            'brand_id' => 'Бренды',
            'brand_id.*' => 'Бренд',
        ]);

        $equipmentTypeIds = array_values(array_unique(array_map(
            'intval',
            $validated['equipment_type_id'] ?? []
        )));
        $brandIds = array_values(array_unique(array_map(
            'intval',
            $validated['brand_id'] ?? []
        )));

        return [$equipmentTypeIds, $brandIds];
    }

    private function syncEquipmentTypesAndBrands(
        ServiceCenter $serviceCenter,
        array $equipmentTypeIds,
        array $brandIds
    ): void {
        $serviceCenter->equipmentTypes()->sync($equipmentTypeIds);
        $serviceCenter->brands()->sync($brandIds);
    }

    public function edit(Request $request): void
    {
        \App\Helpers::log($request->all());
    }
}
