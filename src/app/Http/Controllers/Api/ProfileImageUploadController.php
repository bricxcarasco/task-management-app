<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Controller;
use App\Http\Requests\Rio\ProfileImageRequest;
use App\Enums\ServiceSelectionTypes;
use Session;

class ProfileImageUploadController extends Controller
{
    /**
     * Process Upload API Endpoint
     *
     * @param \App\Http\Requests\Rio\ProfileImageRequest $request
     * @return \Illuminate\Http\Response|\Illuminate\Contracts\Routing\ResponseFactory
     */
    public function processUpload(ProfileImageRequest $request)
    {
        /** @var \App\Models\User */
        $user = auth()->user();

        $entityId = $user->rio_id;

        $profileImagePath = config('bphero.rio_profile_image');
        $sessionKey = "rio.profile_image.";

        // Get service selected
        $service = json_decode(Session::get('ServiceSelected'));

        if ($service->type === ServiceSelectionTypes::NEO) {
            $entityId = $service->data->id;
            $profileImagePath = config('bphero.neo_profile_image');
            $sessionKey = "neo.profile_image.";
        }

        if ($request->hasFile('profile_image')) {
            $file = $request->file('profile_image');

            /** @phpstan-ignore-next-line */
            $fileName = $entityId . '.' . $file->extension();
            $folderName = "${profileImagePath}${entityId}/tmp";

            /** @phpstan-ignore-next-line */
            $result = Storage::disk('public')->put("${folderName}/${fileName}", file_get_contents($file));

            // Return failed save
            if ($result === false) {
                return response()->respondInternalServerError(['Could not save file.']);
            }

            $profileImageSessionKey = "${sessionKey}${entityId}";

            $request->session()->put($profileImageSessionKey, $fileName);

            return response()->respondSuccess([
                "folder_name" => $folderName
            ]);
        }

        // Return failed temporary upload
        return response()->respondInternalServerError(['Could not save file.']);
    }

    /**
     * Revert uploaded file
     *
     * @param Request $request
     * @return \Illuminate\Http\Response|\Illuminate\Contracts\Routing\ResponseFactory
     */
    public function revertUpload(Request $request)
    {
        /** @var \App\Models\User */
        $user = auth()->user();

        $entityId = $user->rio_id;

        $profileImagePath = config('bphero.rio_profile_image');

        // Get service selected
        $service = json_decode(Session::get('ServiceSelected'));

        if ($service->type === ServiceSelectionTypes::NEO) {
            $entityId = $service->data->id;
            $profileImagePath = config('bphero.neo_profile_image');
        }

        $folderPath = "${profileImagePath}${entityId}/tmp";

        if (Storage::disk('public')->deleteDirectory($folderPath)) {
            return response()->respondSuccess();
        }

        // Return failed revert
        return response()->respondInternalServerError();
    }
}
