<?php

namespace App\Http\Controllers\Rio;

use App\Enums\Neo\AcceptBelongType;
use App\Enums\Neo\AcceptConnectionType;
use App\Http\Controllers\Controller;
use App\Http\Requests\Rio\UpdatePrivacyRequest;
use App\Models\User;

class PrivacyController extends Controller
{
    /**
     * Rio Privacy - Edit page
     *
     * @return \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
     */
    public function edit()
    {
        /** @var User */
        $user = auth()->user();

        // Get rio of authenticated user
        $rio = $user->rio()
            ->with('rio_privacy')
            ->firstOrFail();

        // Get rio privacy information
        $rioPrivacy = $rio->rio_privacy;

        // Check if rio privacy exists
        if (!$rioPrivacy) {
            abort(404);
        }

        // Selection dropdowns
        $personalRestrictions = AcceptConnectionType::asSelectArray();
        $participatingRestrictions = AcceptBelongType::asSelectArray();

        return view('rio.privacy.edit', compact(
            'rio',
            'rioPrivacy',
            'personalRestrictions',
            'participatingRestrictions'
        ));
    }

    /**
     * Rio Privacy update information
     *
     * @param \App\Http\Requests\Rio\UpdatePrivacyRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(UpdatePrivacyRequest $request)
    {
        // Request data
        $data = $request->validated();

        /** @var User */
        $user = auth()->user();

        // Get rio of authenticated user
        $rio = $user->rio()
            ->with('rio_privacy')
            ->firstOrFail();

        // Get rio privacy information
        $rioPrivacy = $rio->rio_privacy;

        // Check if rio privacy exists
        if (!$rioPrivacy) {
            abort(404);
        }

        // Update RIO privacy
        $rioPrivacy->update($data);

        return redirect()->back()
            ->withAlertBox('success', __('Updated privacy settings'));
    }
}
