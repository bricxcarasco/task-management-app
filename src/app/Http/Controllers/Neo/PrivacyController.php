<?php

namespace App\Http\Controllers\Neo;

use App\Enums\Neo\AcceptParticipationType;
use App\Enums\Neo\RestrictConnectionType;
use App\Http\Controllers\Controller;
use App\Http\Requests\Neo\UpdatePrivacyRequest;
use App\Models\Neo;
use App\Models\User;

class PrivacyController extends Controller
{
    /**
     * NEO profile privacy page
     *
     * @return \Illuminate\View\View
     */
    public function edit(Neo $neo)
    {
        $this->authorize('privacy', [Neo::class, $neo]);

        /** @var User */
        $user = auth()->user();

        // Get neo of authenticated user
        $neo = $user->rio->neos()
            ->with('neo_privacy')
            ->whereNeoId($neo->id)
            ->firstOrFail();

        // Get neo privacy information
        $neoPrivacy = $neo->neo_privacy;

        // Check if neo privacy exists
        if (!$neoPrivacy) {
            abort(404);
        }

        $restrictConnections = RestrictConnectionType::asSelectArray();
        $acceptParticipations = AcceptParticipationType::asSelectArray();

        return view('neo.privacy.edit', compact(
            'neo',
            'neoPrivacy',
            'restrictConnections',
            'acceptParticipations'
        ));
    }

    /**
     * Neo Privacy update information
     *
     * @param \App\Http\Requests\Neo\UpdatePrivacyRequest $request
     * @param int $id Neo ID
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(UpdatePrivacyRequest $request, $id)
    {
        // Request data
        $data = $request->validated();

        /** @var User */
        $user = auth()->user();

        // Get neo of authenticated user
        $neo = $user->rio->neos()
            ->with('neo_privacy')
            ->whereNeoId($id)
            ->firstOrFail();

        // Get neo privacy information
        $neoPrivacy = $neo->neo_privacy;

        // Check if neo privacy exists
        if (!$neoPrivacy) {
            abort(404);
        }

        // Update neo privacy
        $neoPrivacy->update($data);

        return redirect()->back()
            ->withAlertBox('success', __('Updated privacy settings'));
    }
}
