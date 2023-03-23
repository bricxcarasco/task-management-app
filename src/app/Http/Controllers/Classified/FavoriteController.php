<?php

namespace App\Http\Controllers\Classified;

use App\Enums\ServiceSelectionTypes;
use App\Models\ClassifiedFavorite;
use App\Models\User;
use App\Objects\ServiceSelected;
use App\Enums\Classified\SaleProductVisibility;
use App\Http\Controllers\Controller;
use App\Http\Resources\Classified\ClassifiedProductResource;
use App\Models\ClassifiedSale;

class FavoriteController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        /** @var \App\Models\User */
        $user = auth()->user();

        /** @var \App\Models\Rio */
        $rio = $user->rio;

        // Get subject selected session
        $service = ServiceSelected::getSelected();

        return view('classifieds.favorites.index', compact(
            'rio',
            'service',
        ));
    }

    /**
     * Get list of favorite products.
     *
     * @return mixed
     */
    public function getFavoriteProducts()
    {
        // Get subject selected session
        $service = ServiceSelected::getSelected();

        // Get favorite products
        $favoriteProducts = ClassifiedSale::favoriteList($service)
            ->whereIsPublic(SaleProductVisibility::IS_PUBLIC)
            ->paginate(config('bphero.paginate_count'));

        return ClassifiedProductResource::collection($favoriteProducts);
    }

    /**
     * Favorite product
     *
     * @param \App\Models\ClassifiedSale $classifiedSale
     * @return mixed
     */
    public function favoriteProduct(ClassifiedSale $classifiedSale)
    {
        $this->authorize('favorite', [ClassifiedSale::class, $classifiedSale]);

        /** @var User */
        $user = auth()->user();

        // Get subject selected session
        $service = ServiceSelected::getSelected();

        if ($service->type === ServiceSelectionTypes::RIO) {
            $isFavourite = $classifiedSale->favorites()
                ->whereRioId($user->rio->id)
                ->exists();
        } else {
            $isFavourite = $classifiedSale->favorites()
                ->whereNeoId($service->data->id)
                ->exists();
        }

        if (!$isFavourite) {
            $favorite = new ClassifiedFavorite();
            $favorite->classified_sale_id = $classifiedSale->id;
            $favorite->rio_id = $service->type === ServiceSelectionTypes::RIO ? $user->rio->id : null;
            $favorite->neo_id = $service->type === ServiceSelectionTypes::NEO ? $service->data->id : null;
            $favorite->created_rio_id = $user->rio->id;
            $favorite->save();
        }

        return response()->respondSuccess();
    }

    /**
     * Unfavorite product
     *
     * @param \App\Models\ClassifiedSale $classifiedSale
     * @return mixed
     */
    public function unfavoriteProduct(ClassifiedSale $classifiedSale)
    {
        $this->authorize('favorite', [ClassifiedSale::class, $classifiedSale]);

        /** @var User */
        $user = auth()->user();
        $service = ServiceSelected::getSelected();

        if ($service->type === ServiceSelectionTypes::RIO) {
            $classifiedSale->favorites()
                ->whereRioId($user->rio->id)
                ->delete();
        } else {
            $classifiedSale->favorites()
                ->whereNeoId($service->data->id)
                ->delete();
        }

        return response()->respondSuccess();
    }
}
