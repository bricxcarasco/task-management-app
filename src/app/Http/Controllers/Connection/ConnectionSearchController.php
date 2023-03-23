<?php

namespace App\Http\Controllers\Connection;

use App\Enums\ServiceSelectionTypes;
use App\Enums\EntityType;
use App\Enums\YearsOfExperiences;
use App\Http\Controllers\Controller;
use App\Http\Requests\Connection\SearchRequest;
use App\Http\Resources\Connection\Search\SearchListResource;
use App\Models\BusinessCategory;
use App\Models\Neo;
use App\Models\Rio;
use App\Models\User;
use Illuminate\Http\Request;
use Session;

class ConnectionSearchController extends Controller
{
    /**
     * Connection page - Search tab
     *
     * @return \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
     */
    public function search()
    {
        /** @var User */
        $user = auth()->user();

        if (empty(json_decode(Session::get('ServiceSelected')))) {
            Session::put('ServiceSelected', json_encode([
                "data" => $user->rio,
                "type" => ServiceSelectionTypes::RIO
            ]));
        }

        $service = json_decode(Session::get('ServiceSelected'));

        $businessCategories = BusinessCategory::pluck('business_category_name', 'id');
        $yearsOfExperiences = YearsOfExperiences::getValues();

        return view('connection.search.connection_search', compact(
            'service',
            'yearsOfExperiences',
            'businessCategories'
        ));
    }

    /**
     * Connection page - Search tab - Results display
     *
     * @param \App\Http\Requests\Connection\SearchRequest $request
     * @return \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
     */
    public function result(SearchRequest $request)
    {
        // Get request data
        $requestData = $request->validated();

        /** @var User */
        $user = auth()->user();

        if (empty(json_decode(Session::get('ServiceSelected')))) {
            Session::put('ServiceSelected', json_encode([
                "data" => $user->rio,
                "type" => ServiceSelectionTypes::RIO
            ]));
        }

        $service = json_decode(Session::get('ServiceSelected'));
        $search = Rio::search($requestData, $service);

        if ((int) $requestData['search_target'] === EntityType::NEO) {
            $search = Neo::search($requestData, $service);
        }

        if (count($search->groupBy('id')->get()) < 1) {
            $requestData['search_expert'] = true;
            $search = Rio::search($requestData, $service);

            if ((int) $requestData['search_target'] === EntityType::NEO) {
                $search = Neo::search($requestData, $service);
            }
        }

        $searchCount = count($search->groupBy('id')->get());

        return view('connection.search.connection_search_result', compact(
            'service',
            'searchCount',
            'requestData'
        ));
    }

    /**
     * Store search filters to session when back button is clicked
     *
     * @param \App\Http\Requests\Connection\SearchRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function storeSearchFiltersToSession(SearchRequest $request)
    {
        // Get request data
        $requestData = $request->validated();

        // Store search filters to session
        session(['connections.search' => $requestData]);

        return response()->json('success');
    }

    /**
     * Search results list
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function searchListItem(Request $request)
    {
        // Get request data
        $requestData = $request->all();

        /** @var User */
        $user = auth()->user();

        if (empty(json_decode(Session::get('ServiceSelected')))) {
            Session::put('ServiceSelected', json_encode([
                "data" => $user->rio,
                "type" => ServiceSelectionTypes::RIO
            ]));
        }

        $service = json_decode(Session::get('ServiceSelected'));
        $search = Rio::search($requestData, $service);

        if ((int) $requestData['search_target'] === EntityType::NEO) {
            $search = Neo::search($requestData, $service);
        }

        $searchResult = $search->groupBy('id')
            ->paginate(config('bphero.paginate_count'));

        return SearchListResource::collection($searchResult)
            ->additional(['meta' => [
                'requests' => $requestData,
            ]]);
    }
}
