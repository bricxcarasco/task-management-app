<?php

namespace App\Http\Controllers\Notifications;

use App\Enums\BasicSettings;
use App\Http\Controllers\Controller;
use App\Http\Requests\Notification\UpdateGeneralSettingsRequest;
use App\Http\Requests\Notification\UpsertMailTemplatesRequest;
use App\Models\NotificationRejectSetting;

class BasicSettingController extends Controller
{
    /**
     * Basic settings page.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function index()
    {
        /** @var \App\Models\User */
        $user = auth()->user();

        /** @var \App\Models\Rio */
        $rio = $user->rio;

        // Get mail templates
        $mailTemplates = BasicSettings::asSelectArray();

        return view('notifications.basic-settings', compact(
            'user',
            'rio',
            'mailTemplates'
        ));
    }

    /**
     * Get authenticated user selected mail settings.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getMailTemplates()
    {
        /** @var \App\Models\User */
        $user = auth()->user();

        // Guard clause if non-existing rio
        if (empty($user->rio)) {
            return response()->respondNotFound();
        }

        // Get notification setting of authenticated RIO
        $notifSetting = NotificationRejectSetting::whereRioId($user->rio_id)->first();

        // Set default notification setting to null as ALL
        $selectedNotifSettings = BasicSettings::getSelectableTemplateValues();

        // If there are rejected settings, set selected settings
        if (!empty($notifSetting)) {
            $rejectedTemplates = explode(',', $notifSetting->mail_template_id);
            $selectedNotifSettings = array_diff($selectedNotifSettings, $rejectedTemplates);
        }

        return response()->respondSuccess($selectedNotifSettings);
    }

    /**
     * Update RIO mail template settings.
     *
     * @param \App\Http\Requests\Notification\UpsertMailTemplatesRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateMailTemplates(UpsertMailTemplatesRequest $request)
    {
        /** @var \App\Models\User */
        $user = auth()->user();

        // Guard clause if non-existing rio
        if (empty($user->rio)) {
            return response()->respondNotFound();
        }

        // Get validated data
        $requestData = $request->validated();

        // Get notification setting
        $notifSetting = NotificationRejectSetting::whereRioId($requestData['rio_id']);

        // Return error for invalid template IDs
        $allowedSelectableOptions = BasicSettings::getSelectableTemplateValues();
        $unselectedTemplateIds = $requestData['mail_template_id'] ?? [];

        foreach ($unselectedTemplateIds as $templateId) {
            if (!in_array((int) $templateId, $allowedSelectableOptions)) {
                return response()->respondBadRequest();
            }
        }

        // Set mail_template_id as string or null
        $unselectedTemplateStr = implode(',', $unselectedTemplateIds);
        $requestData['mail_template_id'] = $unselectedTemplateStr !== ''
            ? $unselectedTemplateStr
            : null;

        // Update data if exists, create if new
        if ($notifSetting->exists()) {
            $notifSetting->update($requestData);
        } else {
            $notifSetting = new NotificationRejectSetting();
            $notifSetting->fill($requestData);
            $notifSetting->save();
        }

        return response()->respondSuccess($requestData);
    }

    /**
     * Update RIO mail template settings.
     *
     * @param \App\Http\Requests\Notification\UpdateGeneralSettingsRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateGeneralSettings(UpdateGeneralSettingsRequest $request)
    {
        /** @var \App\Models\User */
        $user = auth()->user();

        // Get validated data
        $requestData = $request->validated();

        // Update user information
        $user->update($requestData);

        return response()->respondSuccess();
    }
}
