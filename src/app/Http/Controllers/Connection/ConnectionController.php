<?php

namespace App\Http\Controllers\Connection;

use App\Enums\Connection\ListFilters;
use App\Enums\ServiceSelectionTypes;
use App\Enums\Neo\RoleType;
use App\Http\Controllers\Controller;
use App\Http\Requests\Connection\FetchConnectionListRequest;
use App\Http\Resources\Connection\Lists\ApplicationListResource;
use App\Models\NeoBelong;
use App\Models\RioConnection;
use App\Models\User;
use Illuminate\Http\Request;
use Session;

class ConnectionController extends Controller
{
    /**
     * Connected list.
     *
     * @param \App\Http\Requests\Connection\FetchConnectionListRequest $request
     * @return \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
     */
    public function connectionList(FetchConnectionListRequest $request)
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
        $result = RioConnection::connectedList($service, $requestData)
            ->paginate(config('bphero.paginate_count'))
            ->withQueryString();

        return view('connection.lists.connection-list', compact(
            'service',
            'result',
            'requestData'
        ));
    }

    /** Update displayed connections.
    *
    * @param \App\Http\Requests\Connection\FetchConnectionListRequest $request
    * @return array|string
    *
    */
    public function connectionListItem(FetchConnectionListRequest $request)
    {
        // Get request data
        $requestData = $request->validated();
        $pageNo = $request->get('page_no');

        /** @var User */
        $user = auth()->user();

        if (empty(json_decode(Session::get('ServiceSelected')))) {
            Session::put('ServiceSelected', json_encode([
                "data" => $user->rio,
                "type" => ServiceSelectionTypes::RIO
            ]));
        }

        $service = json_decode(Session::get('ServiceSelected'));
        $result = RioConnection::connectedList($service, $requestData)
            ->paginate(config('bphero.paginate_count'), ['*'], 'page', $pageNo);

        return view('connection.lists.connection-list-items', compact(
            'service',
            'result',
            'requestData'
        ))
        ->render();
    }

    /**
     * Connection application list.
     *
     * @param \App\Http\Requests\Connection\FetchConnectionListRequest $request
     * @return \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
     */
    public function applicationList(FetchConnectionListRequest $request)
    {
        // Get request data
        $requestData = $request->validated();
        $mode = $requestData['mode'] ?? ListFilters::SHOW_ALL;
        $status = true;

        /** @var User */
        $user = auth()->user();

        if (empty(json_decode(Session::get('ServiceSelected')))) {
            Session::put('ServiceSelected', json_encode([
                "data" => $user->rio,
                "type" => ServiceSelectionTypes::RIO
            ]));
        }

        $service = json_decode(Session::get('ServiceSelected'));
        $result = RioConnection::connectionApplicationList($service, $requestData)
            ->paginate(config('bphero.paginate_count'))
            ->withQueryString();

        if ($service->type === 'NEO') {
            $status = NeoBelong::where('neo_id', $service->data->id)
                ->where('rio_id', $user->rio->id)
                ->where('role', '<>', RoleType::MEMBER)
                ->exists();
        }

        return view('connection.lists.connection-requests-list', compact(
            'service',
            'result',
            'status',
            'mode'
        ));
    }

    /** Update applications list.
    *
    * @param \Illuminate\Http\Request $request
    * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
    *
    */
    public function applicationListItem(Request $request)
    {
        // Get request data
        $requestData = $request->all();
        $mode = $requestData['mode'] ?? ListFilters::SHOW_ALL;
        $status = true;

        /** @var User */
        $user = auth()->user();

        if (empty(json_decode(Session::get('ServiceSelected')))) {
            Session::put('ServiceSelected', json_encode([
                "data" => $user->rio,
                "type" => ServiceSelectionTypes::RIO
            ]));
        }

        $service = json_decode(Session::get('ServiceSelected'));
        $result = RioConnection::connectionApplicationList($service, $requestData)
            ->paginate(config('bphero.paginate_count'));

        if ($service->type === 'NEO') {
            $status = NeoBelong::where('neo_id', $service->data->id)
                ->where('rio_id', $user->rio->id)
                ->where('role', '<>', RoleType::MEMBER)
                ->exists();
        }

        return ApplicationListResource::collection($result)
            ->additional(['meta' => [
                'requests' => $mode,
            ]]);
    }

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

        return view('connection.search.connection_search', compact(
            'service'
        ));
    }
}
