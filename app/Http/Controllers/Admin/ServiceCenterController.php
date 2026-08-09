<?php

namespace App\Http\Controllers\Admin;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Arr;
use \App\Http\Controllers\Controller;
use \App\Models\ServiceCenter;
use \App\Services\ImageSetService;
use \App\Models\EquipmentType;

class ServiceCenterController extends Controller
{
    public const PHOTOS_PATH = 'app/public/uploads/images/shops/';

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return redirect()->route('home');
    }

    // public function getServiceCentersByName(Request $request)
    public function getServiceCentersByName(string $name)
    {
        $serviceCenters = ServiceCenter::getByName($name)->get()->toArray();
        // $serviceCenters = ServiceCenter::getByName($request->input('title'))->get()->toArray();
        if ($serviceCenters) return response()->json(['ok' => true, 'shops' => $serviceCenters]);
        else return response([ 'ok' => false ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, string $id)
    {
        $serviceCenter = ServiceCenter::getById((int)$id)->get()->first();
        if (!$serviceCenter) return redirect()->route('undefined');
        $data = $this->getServiceCenterData($serviceCenter);
        $equipmentTypes = EquipmentType::with('brands')->get()->toArray();
        foreach($equipmentTypes as $ind => $equipmentType) {
            $equipmentTypeKey = array_search((int)$equipmentType['id'], array_column($data['prices'], 'equipment_type_id'));
            if (!$equipmentTypeKey && $equipmentTypeKey !== 0) continue;
            foreach($equipmentType['brands'] as $index => $brand) {
                $brandKey = null;
                if (isset($data['prices'][$equipmentTypeKey]['data'])) {
                    $brandKey = array_search((int)$brand['id'], array_column($data['prices'][$equipmentTypeKey]['data'], 'brand_id'));
                }
                if (!$brandKey && $brandKey !== 0) continue;
                $equipmentTypes[$ind]['brands'][$index]['active'] = true;
                $equipmentTypes[$ind]['brands'][$index]['price'] = $data['prices'][$equipmentTypeKey]['data'][$brandKey]['price'];
            }
        }

        return view('pages.admin.shop.edit.index', [
            'coord' => $data['coord'],
            'shop' => $serviceCenter,
            'photos' => $data['photos'],
            'services' => $data['services'],
            'workingMode' => $data['workingMode'],
            'prices' => $data['prices'],
            'additionalPhones' => $data['additionalPhones'],
            'categories' => $equipmentTypes
        ]);
    }

    private function syncPhotos(Request $request, ImageSetService $imageService, array $oldPhotos, int $serviceCenterId): array
    {
        $photos = $request->file('photos');
        $photosToDelete = $request->input('delete_photos') ?? [];
        $arrPhotos = [
            'uploaded' => [],
            'errors' => []
        ];
        if ($photos) {
            foreach($photos as $photo) {
                $imgData = $imageService::saveToStorage($photo, storage_path(self::PHOTOS_PATH) . $serviceCenterId);
                if ($imgData) {
                    $arrPhotos['uploaded'][$imgData['name']] = $imgData['sizes'];
                } else {
                    $arrPhotos['errors'][] = $photo->getClientOriginalName();
                }
            }
        }
        if (!empty($photosToDelete)) {
          foreach ($photosToDelete as $deletePhoto) {
            unset($oldPhotos[$deletePhoto]);
            $imageService::removeByName($deletePhoto, storage_path(self::PHOTOS_PATH) . $serviceCenterId);
          }
        }
        $photos = array_merge($oldPhotos, $arrPhotos['uploaded']);

        return $photos;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response */
    public function update(Request $request, ImageSetService $imageService, $id)
    {
        $serviceCenter = ServiceCenter::getById((int)$id)->get()->first();
        $data = $request->all();

        //Если в запросе только файл и метод - значит что это предзагрузка фоток
        //Если продолжить выполненин функции, то будет ошибка и в итоге фотки не загрузятся
        if (isset($data['file']) && count($data) < 3) return;

        $serviceCenter->description = $data['description'];
        $serviceCenter->coord = json_encode([
            'lat' => $data['latitude'],
            'long' => $data['longitude']
        ]);
        $serviceCenter->address = $data['address'];
        $serviceCenter->phone = $data['phone'];
        $serviceCenter->additional_phones = json_encode($data['additional_phones']);
        $serviceCenter->telegram = $data['telegram'];
        $serviceCenter->whatsapp = $data['whatsapp'];
        $serviceCenter->web = json_encode($data['web']);
        $this->syncReviewSourcesInfo((array)json_decode($data['services']), (int)$id);
        $this->syncWorkingHours((array)json_decode($data['work_mode']), (int)$id);
        $this->syncBrands((array)$data['sub_categories'], $serviceCenter);

        //return response(['ok' => true]);
        // \App\Helpers::log($data, __DIR__);
        $photos = $this->syncPhotos(
            $request,
            $imageService,
            $serviceCenter->photos ? (array)json_decode($serviceCenter->photos) : [],
            (int)$id
        );
        $serviceCenter->photos = json_encode($photos);
        $serviceCenter->save();

        $content = view('pages.admin.shop.edit.photos-list-items', ['photos' => $photos, 'shop' => $serviceCenter])->render();

        return response(
           [
              'ok' => true,
              'count' => count($photos),
              'items' => $content
            ],
            200,
            [ 'Content-Type' => 'application/json' ]
        );
    }

    public function syncBrands(array $brands, ServiceCenter $serviceCenter): void
    {
        $serviceCenterBrands = $serviceCenter->brands->keyBy('id');
        $brands = collect($brands)->keyBy(function($item) { return $item; });
        $brandsToAttach = $brands->diffKeys($serviceCenterBrands);
        $brandsToDetach = $serviceCenterBrands->diffKeys($brands);

        foreach($brandsToAttach as $brand) {
            $serviceCenter->brands()->attach($brand);
        }

        foreach($brandsToDetach as $brand) {
            $serviceCenter->brands()->detach($brand);
        }
    }

    private function syncWorkingHours(array $modes, int $serviceCenterId): void
    {
        foreach($modes as $day => $mode) {
            $rec = \Illuminate\Support\Facades\DB::table('service_center_working_hours')
                ->where('service_center_id', $serviceCenterId)
                ->where('day_of_week', (int)$day)
            ;
            if ($rec->get()->first()) {
                $rec->update([
                    'is_open' => (int)$mode->is_open,
                    'open_time' => $mode->open,
                    'close_time' => $mode->close,
                ]);
            }
        }
    }


    private function syncReviewSourcesInfo(array $reviewSources, int $serviceCenterId): void
    {
        foreach($reviewSources as $reviewSource) {
            $rec = \Illuminate\Support\Facades\DB::table('service_center_review_source')
                ->where('service_center_id', $serviceCenterId)
                ->where('review_source_id', (int)$reviewSource->id)
            ;
            if ($rec->get()->first()) {
                $rec->update(['rating' => number_format($reviewSource->rating, 2)]);
            }
        }
    }

    public function photosPreload()
    {
        // \App\Helpers::log('asdkfasdf', __DIR__);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
          //
    }
}
