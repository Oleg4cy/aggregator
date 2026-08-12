<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use \App\Models\ServiceCenter;
use \App\Services\TitleService;
use \App\Http\Controllers\CookieController;
use \App\Http\Controllers\LocationController;
use \App\Constants\CookieConstants;

class ServiceCenterController extends Controller
{
    private $perPage = 5;

    public function index(Request $request): View
    {
        $serviceCenters = ServiceCenter::filter()->get();
        // $serviceCenters = $this->getServiceCenterListPaginated($request->input('page', 1));
        $title = TitleService::homePage($request, $serviceCenters);
        $cityId = LocationController::getCityID();
        return view('pages.home.index', ['serviceCenters' => $serviceCenters, 'title' => $title, 'cityID' => $cityId]);
    }

    public function serviceCenterList(Request $request): View
    {
        $serviceCenters = ServiceCenter::filter()->get();
        // $serviceCenters = $this->getServiceCenterListPaginated($request->input('page', 1));
        return view('layouts.service-center-list-items', ['serviceCenters' => $serviceCenters]);
    }

    private function getServiceCenterListPaginated(int $page)
    {
        return ServiceCenter::filter()->paginate($this->perPage, ['*'], 'page', $page);
    }

    public function aside(string $id): View
    {
        $serviceCenter = ServiceCenter::getByID((int) $id)->get()->first();
        if (!$serviceCenter) abort(404);

        $web = json_decode($serviceCenter->web);
        $web = is_array($web)
            ? array_values(array_filter(
                $web,
                fn ($value) =>
                    is_scalar($value)
                    && trim((string) $value) !== ''
            ))
            : [];

        $additionalPhones = json_decode($serviceCenter->additional_phones);
        $additionalPhones = is_array($additionalPhones)
            ? array_values(array_filter(
                $additionalPhones,
                fn ($value) =>
                    is_scalar($value)
                    && trim((string) $value) !== ''
            ))
            : [];

        $photos = json_decode($serviceCenter->photos);
        $photos = is_array($photos)
            ? array_values(array_filter(
                $photos,
                fn ($photo) =>
                    is_object($photo)
                    && is_scalar($photo->name ?? null)
                    && trim((string) $photo->name) !== ''
            ))
            : [];

        $emails = json_decode($serviceCenter->emails);
        $emails = is_array($emails)
            ? array_values(array_filter(
                $emails,
                fn ($value) =>
                    is_scalar($value)
                    && trim((string) $value) !== ''
            ))
            : [];

        return view(
            'pages.home.layouts.service-center-details',
            [
                'serviceCenter' => $serviceCenter,
                'photos' => $photos,
                'additionalPhones' => $additionalPhones,
                'web' => $web,
                'emails' => $emails,
            ]
        );
    }

    public function show(string $id): View|RedirectResponse
    {
        $serviceCenter = ServiceCenter::getByID((int)$id)->get()->first();
        if (!$serviceCenter) return redirect()->route('undefined');
        CookieController::setCookie(CookieConstants::LOCATION, $serviceCenter->city_id, CookieController::getYears(1));

        $web = json_decode($serviceCenter->web);
        $web = is_array($web) ? array_values(array_filter($web, fn ($value) => is_scalar($value) && trim((string) $value) !== '')) : [];
        $additionalPhones = json_decode($serviceCenter->additional_phones);
        $additionalPhones = is_array($additionalPhones)
            ? array_values(array_filter($additionalPhones, fn ($value) => is_scalar($value) && trim((string) $value) !== ''))
            : [];
        $photos = json_decode($serviceCenter->photos);
        $photos = is_array($photos)
            ? array_values(array_filter($photos, fn ($photo) => is_object($photo) && is_scalar($photo->name ?? null) && trim((string) $photo->name) !== ''))
            : [];
        $coord = json_decode($serviceCenter->coord, true);
        $coord = is_array($coord) && is_numeric($coord['lat'] ?? null) && is_numeric($coord['long'] ?? null)
            ? ['lat' => (float) $coord['lat'], 'long' => (float) $coord['long']]
            : null;

        return view('pages.service-center.index', [
            'serviceCenter' => $serviceCenter,
            'web' => $web,
            'additionalPhones' => $additionalPhones,
            'photos' => $photos,
            'coord' => $coord,
            'workingHours' => $serviceCenter->workingHours->keyBy('day_of_week'),
            'equipmentTypes' => $serviceCenter->equipmentTypes->map(function ($equipmentType) use ($serviceCenter) {
                return $equipmentType->setRelation('brands', $serviceCenter->brands->filter(function ($brand) use ($equipmentType) {
                    return $brand->equipment_type_id === $equipmentType->id;
                })->keyBy('id'));
            }),
            'buybackPrices' => $serviceCenter->buybackPrices->groupBy('equipment_type_id')->map(function ($equipmentTypeGroup) {
                return [
                    'max' => $equipmentTypeGroup->max('price'),
                    'items' => $equipmentTypeGroup->keyBy('brand_id')
                ];
            }),
            'similars' => $this->getServiceCenterSimilars($serviceCenter),
        ]);
    }

    public function getServiceCenterSimilars(ServiceCenter $serviceCenter): Collection
    {
        $brandIds = $serviceCenter->brands->pluck('id')->toArray();
        return ServiceCenter::similarFilter(+$serviceCenter->city_id, +$serviceCenter->id, $brandIds)->get();
    }
}
