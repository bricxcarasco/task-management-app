<?php

namespace App\Http\Controllers;

use App\Enums\AttributeCodes;
use App\Enums\Chat\ChatStatuses;
use App\Enums\Chat\ConnectedChatType;
use App\Enums\Neo\ConnectionStatusType;
use App\Enums\Neo\NeoBelongStatusType;
use App\Enums\Neo\OrganizationAttributeType;
use App\Enums\Neo\RoleType;
use App\Enums\NeoBelongStatuses;
use App\Enums\PrefectureTypes;
use App\Enums\ServiceSelectionTypes;
use App\Enums\YearsOfExperiences;
use App\Http\Requests\Neo\ParticipationRequest;
use App\Http\Requests\Neo\ParticipationInvitationRequest;
use App\Http\Requests\Neo\UpsertIndustryRequest;
use App\Http\Requests\Neo\UpdateIntroductionRequest;
use App\Http\Requests\Neo\UpdateLocationRequest;
use App\Http\Requests\Neo\UpsertBusinessHolidayRequest;
use App\Http\Requests\Neo\UpdateOrganizationNameRequest;
use App\Http\Requests\Neo\UpsertAwardHistoryRequest;
use App\Http\Requests\Neo\UpdateEstablishmentDateRequest;
use App\Http\Requests\Neo\UpdateOverseasSupportRequest;
use App\Http\Requests\Neo\UpdateTelephoneRequest;
use App\Http\Requests\Neo\UpsertHistoryRequest;
use App\Http\Requests\Neo\UpsertProductRequest;
use App\Http\Requests\Neo\UpsertQualificationRequest;
use App\Http\Requests\Neo\UpsertSkillRequest;
use App\Http\Requests\Neo\UpsertUrlRequest;
use App\Http\Requests\Rio\UpdateProfileImageRequest;
use App\Http\Resources\Neo\AwardResource;
use App\Http\Resources\Neo\HistoryResource;
use App\Http\Resources\Neo\IndustryResource;
use App\Http\Resources\Neo\ProductResource;
use App\Http\Resources\Neo\QualificationResource;
use App\Http\Resources\Neo\SkillResource;
use App\Http\Resources\Neo\UrlResource;
use App\Models\BusinessCategory;
use App\Models\Neo;
use App\Models\User;
use App\Models\NeoExpert;
use App\Http\Resources\Neo\EmailResource;
use App\Http\Requests\Neo\UpsertEmailAddressRequest;
use App\Models\Chat;
use App\Models\NeoBelong;
use App\Models\NeoProfile;
use App\Models\NeoConnection;
use App\Models\Notification;
use App\Models\Rio;
use App\Objects\ServiceSelected;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Session;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class NeoController extends Controller
{
    /**
     * Neo profile edit page
     *
     * @param \App\Models\Neo $neo
     * @return \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
     */
    public function edit(Neo $neo)
    {
        $this->authorize('edit', [Neo::class, $neo]);

        /** @var User */
        $user = auth()->user();

        // Initialize data
        $neoProfile = $neo->neo_profile;

        // Attach accessors
        $neo->append('business_duration');
        $neo->append('business_hours');

        // Check if neo profile exists
        if (!$neoProfile) {
            abort(404);
        }

        // Initialize selectable dropdowns
        $prefectures = PrefectureTypes::asSelectArray();
        $yearsOfExperiences = YearsOfExperiences::asSelectArray();
        $organizationTypes = OrganizationAttributeType::asSelectArray();
        $businessCategories = BusinessCategory::pluck('business_category_name', 'id');

        return view('neo.profile.edit', compact(
            'user',
            'neo',
            'neoProfile',
            'prefectures',
            'yearsOfExperiences',
            'organizationTypes',
            'businessCategories'
        ));
    }

    /**
     * Neo Profile - Update Profile Image
     *
     * @param \App\Http\Requests\Rio\UpdateProfileImageRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateImage(UpdateProfileImageRequest $request)
    {
        /** @var User */
        $user = auth()->user();

        // Get service selected
        $service = json_decode(Session::get('ServiceSelected'));

        if ($service->type !== ServiceSelectionTypes::NEO) {
            return response()->respondUnauthorized();
        }

        /** @var Neo */
        $neo = Neo::find($service->data->id);

        /** @var NeoProfile */
        $neoProfile = $neo->neo_profile;
        $neoId = $neo->id;

        $neoProfileImagePath = config('bphero.neo_profile_image');
        $profileImageSessionKey = "neo.profile_image.${neoId}";

        // Guard clause for non-existing neo
        if (empty($neo) || !$request->session()->has($profileImageSessionKey)) {
            return response()->respondNotFound();
        }

        $fileName = $request->session()->get($profileImageSessionKey);

        $tempFilePath = "${neoProfileImagePath}${neoId}/tmp/${fileName}";
        $filePath = "${neoProfileImagePath}${neoId}";

        // browse all files .jp, .png on folder and remove
        $files = Storage::disk('public')->files($filePath. "/");
        if ($files) {
            foreach ($files as $file) {
                Storage::disk('public')->delete($file);
            }
        }

        if (Storage::disk('public')->move($tempFilePath, "${filePath}/${fileName}")) {
            $neoProfile->profile_photo = $fileName;
            if ($neoProfile->save()) {
                $request->session()->forget($profileImageSessionKey);
                $newProfileImage = config('app.url') . "/storage/${filePath}/${fileName}";
                return response()->respondSuccess([
                    'profile_image' => $newProfileImage
                ]);
            }
        }

        return response()->respondInternalServerError();
    }

    /**
     * Neo Profile - Delete Profile Image
     *
     * @param Neo $neo
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteImage(Neo $neo)
    {
        /** @var User */
        $user = auth()->user();

        // Get service selected
        $service = json_decode(Session::get('ServiceSelected'));

        if ($service->type !== ServiceSelectionTypes::NEO) {
            return response()->respondUnauthorized();
        }

        /** @var Neo */
        $neo = Neo::find($service->data->id);

        /** @var NeoProfile */
        $neoProfile = $neo->neo_profile;
        $neoId = $neo->id;

        $neoProfileImagePath = config('bphero.neo_profile_image');

        // Guard clause for non-existing neo
        if (empty($neo) && is_null($neoProfile->profile_photo)) {
            return response()->respondNotFound();
        }

        if (!is_null($neoProfile->profile_photo)) {
            $filePath = "${neoProfileImagePath}${neoId}/" . $neoProfile->profile_photo;
            if (Storage::disk('public')->exists($filePath)) {
                Storage::disk('public')->delete($filePath);

                $neoProfile->profile_photo = null;

                if ($neoProfile->save()) {
                    $defaultProfileImage = config('app.url') . "/" . config('bphero.profile_image_directory') . config('bphero.profile_image_filename');

                    return response()->respondSuccess([
                        'profile_image' => $defaultProfileImage
                    ]);
                }
            }

            return response()->respondNotFound();
        }

        return response()->respondInternalServerError();
    }

    /**
     * Update NEO Profile location information.
     *
     * @param \App\Http\Requests\Neo\UpdateLocationRequest $request
     * @param \App\Models\Neo $neo
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateLocation(UpdateLocationRequest $request, Neo $neo)
    {
        $this->authorize('update', [Neo::class, $neo]);

        // Request data
        $data = $request->validated();
        $neoProfile = $neo->neo_profile;

        // If prefecture is in Japan, set nationality to null
        if ((int) $data['prefecture'] !== PrefectureTypes::OTHER) {
            $data['nationality'] = null;
        }

        // Check if neo profile exists
        if (!$neoProfile) {
            return response()->respondNotFound();
        }

        // Update NEO profile location
        $neoProfile->update($data);

        return response()->respondSuccess($data);
    }

    /**
     * Update organization name and type for specified neo
     *
     * @param \App\Http\Requests\Neo\UpdateOrganizationNameRequest $request
     * @param \App\Models\Neo $neo
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateOrganizationName(UpdateOrganizationNameRequest $request, Neo $neo)
    {
        $this->authorize('update', [Neo::class, $neo]);

        // Get request data
        $requestData = $request->validated();

        // Update neo entity
        $neo->update($requestData);

        return response()->respondSuccess();
    }

    /**
     * Update Neo Profile Telephone information.
     *
     * @param \App\Http\Requests\Neo\UpdateTelephoneRequest $request
     * @param \App\Models\Neo $neo
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateTelephone(UpdateTelephoneRequest $request, Neo $neo)
    {
        $this->authorize('update', [Neo::class, $neo]);

        // Get request data
        $requestData = $request->validated();

        // Update neo entity
        $neo->update($requestData);

        return response()->respondSuccess($requestData);
    }

    /**
     * Update neo introduction for authenticated rio
     *
     * @param \App\Http\Requests\Neo\UpdateIntroductionRequest $request
     * @param \App\Models\Neo $neo
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateIntroduction(UpdateIntroductionRequest $request, Neo $neo)
    {
        $this->authorize('update', [Neo::class, $neo]);

        // Request data
        $data = $request->validated();
        $neoProfile = $neo->neo_profile;

        // Check if neo profile exists
        if (!$neoProfile) {
            return response()->respondNotFound();
        }

        // Update NEO profile location
        $neoProfile->update($data);

        return response()->respondSuccess($data);
    }

    /**
     * Fetch awards for Neo profile
     *
     * @param int $id Neo ID
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAwards(int $id)
    {
        // Get NEO information
        $neo = Neo::whereId($id)->firstOrFail();

        // Guard clause for non-existing
        if (empty($neo)) {
            return response()->respondNotFound();
        }

        // Guard clause for non-accessible neo
        if (!$neo->isUserAccessible()) {
            return response()->respondNotFound();
        }

        // Get awards as json resource
        $results = AwardResource::collection($neo->awards);

        return response()->respondSuccess($results);
    }

    /**
     * Create award for Neo profile
     *
     * @param \App\Http\Requests\Neo\UpsertAwardHistoryRequest $request
     * @param int $id Neo ID
     * @return \Illuminate\Http\JsonResponse
     */
    public function createAward(UpsertAwardHistoryRequest $request, int $id)
    {
        // Get request data
        $requestData = $request->validated();

        // Get NEO information
        $neo = Neo::whereId($id)->firstOrFail();

        // Guard clause for non-existing
        if (empty($neo)) {
            return response()->respondNotFound();
        }

        // Guard clause for non-accessible neo
        if (!$neo->isUserAccessible()) {
            return response()->respondNotFound();
        }

        // Check if maximum awards are reached
        $expertCount = $neo->awards()->count();
        if ($expertCount >= 10) {
            return response()->respondForbidden();
        }

        // Create new award for neo user
        $neoExpert = new NeoExpert();
        $neoExpert->neo_id = $id;
        $neoExpert->content = $requestData['content'];
        $neoExpert->additional = $requestData['award_year'];
        $neoExpert->attribute_code = AttributeCodes::AWARDS;
        $neoExpert->sort = 0;
        $neoExpert->save();

        return response()->respondSuccess();
    }

    /**
     * Update award for Neo profile
     *
     * @param \App\Http\Requests\Neo\UpsertAwardHistoryRequest $request
     * @param \App\Models\NeoExpert $award
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateAward(UpsertAwardHistoryRequest $request, NeoExpert $award)
    {
        $this->authorize('update', [NeoExpert::class, $award]);

        // Get request data
        $requestData = $request->validated();

        // Update award history
        $award->update([
            'content' => $requestData['content'],
            'additional' => $requestData['award_year'],
        ]);

        return response()->respondSuccess();
    }

    /**
     * Delete award for Neo profile
     *
     * @param \App\Models\NeoExpert $award
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteAward(NeoExpert $award)
    {
        $this->authorize('delete', [NeoExpert::class, $award]);

        // Delete neo expert award
        $award->delete();

        return response()->respondSuccess();
    }

    /**
     * Fetch industries for Neo profile
     *
     * @param int $id Neo ID
     * @return \Illuminate\Http\JsonResponse
     */
    public function getIndustries(int $id)
    {
        // Get NEO information
        $neo = Neo::whereId($id)->firstOrFail();

        // Guard clause for non-existing
        if (empty($neo)) {
            return response()->respondNotFound();
        }

        // Guard clause for non-accessible neo
        if (!$neo->isUserAccessible()) {
            return response()->respondNotFound();
        }

        // Get industries as json resource
        $results = IndustryResource::collection($neo->industries);

        return response()->respondSuccess($results);
    }

    /**
     * Create industry for Neo profile
     *
     * @param \App\Http\Requests\Neo\UpsertIndustryRequest $request
     * @param int $id Neo ID
     * @return \Illuminate\Http\JsonResponse
     */
    public function createIndustry(UpsertIndustryRequest $request, int $id)
    {
        // Get request data
        $requestData = $request->validated();

        // Get NEO information
        $neo = Neo::whereId($id)->firstOrFail();

        // Guard clause for non-existing
        if (empty($neo)) {
            return response()->respondNotFound();
        }

        // Guard clause for non-accessible neo
        if (!$neo->isUserAccessible()) {
            return response()->respondNotFound();
        }

        // Check if maximum industries are reached
        $expertCount = $neo->industries()->count();
        if ($expertCount >= 10) {
            return response()->respondForbidden();
        }

        // Get business category based on input
        /** @var BusinessCategory */
        $businessCategory = BusinessCategory::findOrFail($requestData['business_category_id']);

        // Create new industry for neo user
        $neoExpert = new NeoExpert();
        $neoExpert->fill($requestData);
        $neoExpert->neo_id = $id;
        $neoExpert->attribute_code = AttributeCodes::INDUSTRY;
        $neoExpert->content = $businessCategory->business_category_name;
        $neoExpert->sort = 0;
        $neoExpert->save();

        return response()->respondSuccess();
    }

    /**
     * Update industry for Nerofile
     *
     * @param \App\Http\Requests\Neo\UpsertIndustryRequest $request
     * @param \App\Models\NeoExpert $industry
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateIndustry(UpsertIndustryRequest $request, NeoExpert $industry)
    {
        $this->authorize('update', [NeoExpert::class, $industry]);

        // Get request data
        $requestData = $request->validated();

        // Get business category based on input
        /** @var BusinessCategory */
        $businessCategory = BusinessCategory::findOrFail($requestData['business_category_id']);

        // Set content
        $requestData['content'] = $businessCategory->business_category_name;

        // Update industry
        $industry->update($requestData);

        return response()->respondSuccess();
    }

    /**
     * Delete industry for Neo profile
     *
     * @param \App\Models\NeoExpert $industry
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteIndustry(NeoExpert $industry)
    {
        $this->authorize('delete', [NeoExpert::class, $industry]);

        // Delete neo expert industry
        $industry->delete();

        return response()->respondSuccess();
    }

    /**
     * Fetch urls for Neo profile
     *
     * @param int $id Neo ID
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUrls(int $id)
    {
        // Get NEO information
        $neo = Neo::whereId($id)->firstOrFail();

        // Guard clause for non-existing
        if (empty($neo)) {
            return response()->respondNotFound();
        }

        // Guard clause for non-accessible neo
        if (!$neo->isUserAccessible()) {
            return response()->respondNotFound();
        }

        // Get urls as json resource
        $results = UrlResource::collection($neo->urls);

        return response()->respondSuccess($results);
    }

    /**
     * Create url for Neo profile
     *
     * @param \App\Http\Requests\Neo\UpsertUrlRequest $request
     * @param int $id Neo ID
     * @return \Illuminate\Http\JsonResponse
     */
    public function createUrl(UpsertUrlRequest $request, int $id)
    {
        // Get request data
        $requestData = $request->validated();

        // Get NEO information
        $neo = Neo::whereId($id)->firstOrFail();

        // Guard clause for non-existing
        if (empty($neo)) {
            return response()->respondNotFound();
        }

        // Guard clause for non-accessible neo
        if (!$neo->isUserAccessible()) {
            return response()->respondNotFound();
        }

        // Check if maximum urls are reached
        $expertCount = $neo->urls()->count();
        if ($expertCount >= 20) {
            return response()->respondForbidden();
        }

        // Create new url for neo user
        $neoExpert = new NeoExpert();
        $neoExpert->neo_id = $id;
        $neoExpert->content = $requestData['title'];
        $neoExpert->information = $requestData['url'];
        $neoExpert->attribute_code = AttributeCodes::URL;
        $neoExpert->sort = 0;
        $neoExpert->save();

        return response()->respondSuccess();
    }

    /**
     * Update url for Neo profile
     *
     * @param \App\Http\Requests\Neo\UpsertUrlRequest $request
     * @param \App\Models\NeoExpert $url
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateUrl(UpsertUrlRequest $request, NeoExpert $url)
    {
        $this->authorize('update', [NeoExpert::class, $url]);

        // Get request data
        $requestData = $request->validated();

        // Update url information
        $url->update([
            'content' => $requestData['title'],
            'information' => $requestData['url'],
        ]);

        return response()->respondSuccess();
    }

    /**
     * Delete url for Neo profile
     *
     * @param \App\Models\NeoExpert $url
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteUrl(NeoExpert $url)
    {
        $this->authorize('delete', [NeoExpert::class, $url]);

        // Delete neo expert url
        $url->delete();

        return response()->respondSuccess();
    }

    /**
     * Fetch qualifications for Neo profile
     *
     * @param int $id Neo ID
     * @return \Illuminate\Http\JsonResponse
     */
    public function getQualifications(int $id)
    {
        // Get NEO information
        $neo = Neo::whereId($id)->firstOrFail();

        // Guard clause for non-existing
        if (empty($neo)) {
            return response()->respondNotFound();
        }

        // Guard clause for non-accessible neo
        if (!$neo->isUserAccessible()) {
            return response()->respondNotFound();
        }

        // Get qualifications as json resource
        $results = QualificationResource::collection($neo->qualifications);

        return response()->respondSuccess($results);
    }

    /**
     * Create qualification for Neo profile
     *
     * @param \App\Http\Requests\Neo\UpsertQualificationRequest $request
     * @param int $id Neo ID
     * @return \Illuminate\Http\JsonResponse
     */
    public function createQualification(UpsertQualificationRequest $request, int $id)
    {
        // Get request data
        $requestData = $request->validated();

        // Get NEO information
        $neo = Neo::whereId($id)->firstOrFail();

        // Guard clause for non-existing
        if (empty($neo)) {
            return response()->respondNotFound();
        }

        // Guard clause for non-accessible neo
        if (!$neo->isUserAccessible()) {
            return response()->respondNotFound();
        }

        // Check if maximum qualifications are reached
        $expertCount = $neo->qualifications()->count();
        if ($expertCount >= 10) {
            return response()->respondForbidden();
        }

        // Create new qualification for neo user
        $neoExpert = new NeoExpert();
        $neoExpert->fill($requestData);
        $neoExpert->neo_id = $id;
        $neoExpert->attribute_code = AttributeCodes::QUALIFICATIONS;
        $neoExpert->sort = 0;
        $neoExpert->save();

        return response()->respondSuccess();
    }

    /**
     * Update qualification for Neo profile
     *
     * @param \App\Http\Requests\Neo\UpsertQualificationRequest $request
     * @param \App\Models\NeoExpert $qualification
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateQualification(UpsertQualificationRequest $request, NeoExpert $qualification)
    {
        $this->authorize('update', [NeoExpert::class, $qualification]);

        // Get request data
        $requestData = $request->validated();

        // Update qualification
        $qualification->update($requestData);

        return response()->respondSuccess();
    }

    /**
     * Delete qualification for Neo profile
     *
     * @param \App\Models\NeoExpert $qualification
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteQualification(NeoExpert $qualification)
    {
        $this->authorize('delete', [NeoExpert::class, $qualification]);

        // Delete neo expert qualification
        $qualification->delete();

        return response()->respondSuccess();
    }

    /**
     * Fetch skills for Neo profile
     *
     * @param int $id Neo ID
     * @return \Illuminate\Http\JsonResponse
     */
    public function getSkills(int $id)
    {
        // Get NEO information
        $neo = Neo::whereId($id)->firstOrFail();

        // Guard clause for non-existing
        if (empty($neo)) {
            return response()->respondNotFound();
        }

        // Guard clause for non-accessible neo
        if (!$neo->isUserAccessible()) {
            return response()->respondNotFound();
        }

        // Get skills as json resource
        $results = SkillResource::collection($neo->skills);

        return response()->respondSuccess($results);
    }

    /**
     * Create skill for Neo profile
     *
     * @param \App\Http\Requests\Neo\UpsertSkillRequest $request
     * @param int $id Neo ID
     * @return \Illuminate\Http\JsonResponse
     */
    public function createSkill(UpsertSkillRequest $request, int $id)
    {
        // Get request data
        $requestData = $request->validated();

        // Get NEO information
        $neo = Neo::whereId($id)->firstOrFail();

        // Guard clause for non-existing
        if (empty($neo)) {
            return response()->respondNotFound();
        }

        // Guard clause for non-accessible neo
        if (!$neo->isUserAccessible()) {
            return response()->respondNotFound();
        }

        // Check if maximum skills are reached
        $expertCount = $neo->skills()->count();
        if ($expertCount >= 10) {
            return response()->respondForbidden();
        }

        // Create new skill for neo user
        $neoExpert = new NeoExpert();
        $neoExpert->fill($requestData);
        $neoExpert->neo_id = $id;
        $neoExpert->attribute_code = AttributeCodes::SKILLS;
        $neoExpert->sort = 0;
        $neoExpert->save();

        return response()->respondSuccess();
    }

    /**
     * Update skill for Neo profile
     *
     * @param \App\Http\Requests\Neo\UpsertSkillRequest $request
     * @param \App\Models\NeoExpert $skill
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateSkill(UpsertSkillRequest $request, NeoExpert $skill)
    {
        $this->authorize('update', [NeoExpert::class, $skill]);

        // Get request data
        $requestData = $request->validated();

        // Update skill
        $skill->update($requestData);

        return response()->respondSuccess();
    }

    /**
     * Delete skill for Neo profile
     *
     * @param \App\Models\NeoExpert $skill
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteSkill(NeoExpert $skill)
    {
        $this->authorize('delete', [NeoExpert::class, $skill]);

        // Delete neo expert skill
        $skill->delete();

        return response()->respondSuccess();
    }

    /**
     * Update establishment date for specified neo
     *
     * @param \App\Http\Requests\Neo\UpdateEstablishmentDateRequest $request
     * @param \App\Models\Neo $neo
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateEstablishmentDate(UpdateEstablishmentDateRequest $request, Neo $neo)
    {
        $this->authorize('update', [Neo::class, $neo]);

        // Get request data
        $requestData = $request->validated();

        // Update neo entity
        $neo->update($requestData);

        return response()->respondSuccess([
            'business_duration' => $neo->business_duration ?: '-',
        ]);
    }

    /**
     * Fetch histories for specified neo
     *
     * @param \App\Models\Neo $neo
     * @return \Illuminate\Http\JsonResponse
     */
    public function getHistories(Neo $neo)
    {
        $this->authorize('get', [Neo::class, $neo]);

        // Get histories of specified neo
        $histories = $neo->histories()->get();

        // Get professions as json resource
        $results = HistoryResource::collection($histories);

        return response()->respondSuccess($results);
    }

    /**
     * Create history for specified neo
     *
     * @param \App\Http\Requests\Neo\UpsertHistoryRequest $request
     * @param \App\Models\Neo $neo
     * @return \Illuminate\Http\JsonResponse
     */
    public function createHistory(UpsertHistoryRequest $request, Neo $neo)
    {
        $this->authorize('store', [NeoExpert::class, $neo]);

        // Get request data
        $requestData = $request->validated();

        // Check if maximum histories are reached
        $expertCount = $neo->histories()->count();
        if ($expertCount >= 10) {
            return response()->respondForbidden();
        }

        // Create new profession for rio user
        $neoExpert = new NeoExpert();
        $neoExpert->fill($requestData);
        $neoExpert->neo_id = $neo->id;
        $neoExpert->attribute_code = AttributeCodes::HISTORY;
        $neoExpert->sort = 0;
        $neoExpert->save();

        return response()->respondSuccess();
    }

    /**
     * Update history for specified neo
     *
     * @param \App\Http\Requests\Neo\UpsertHistoryRequest $request
     * @param \App\Models\NeoExpert $history
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateHistory(UpsertHistoryRequest $request, NeoExpert $history)
    {
        $this->authorize('update', [NeoExpert::class, $history]);

        // Get request data
        $requestData = $request->validated();

        // Update history
        $history->update($requestData);

        return response()->respondSuccess();
    }

    /**
     * Delete profession of authenticated rio
     *
     * @param \App\Models\NeoExpert $history
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteHistory(NeoExpert $history)
    {
        $this->authorize('delete', [NeoExpert::class, $history]);

        // Delete rio expert history
        $history->delete();

        return response()->respondSuccess();
    }

    /**
     * Fetch products for Neo profile
     *
     * @param int $id Neo ID
     * @return \Illuminate\Http\JsonResponse
     */
    public function getProducts(int $id)
    {
        // Get NEO information
        $neo = Neo::whereId($id)->firstOrFail();

        // Guard clause for non-existing
        if (empty($neo)) {
            return response()->respondNotFound();
        }

        // Guard clause for non-accessible neo
        if (!$neo->isUserAccessible()) {
            return response()->respondNotFound();
        }

        // Get skills as json resource
        $results = ProductResource::collection($neo->products);

        return response()->respondSuccess($results);
    }

    /**
     * Create skill for Neo profile
     *
     * @param \App\Http\Requests\Neo\UpsertProductRequest $request
     * @param int $id Neo ID
     * @return \Illuminate\Http\JsonResponse
     */
    public function createProduct(UpsertProductRequest $request, int $id)
    {
        // Get request data
        $requestData = $request->validated();

        // Get NEO information
        $neo = Neo::whereId($id)->firstOrFail();

        // Guard clause for non-existing
        if (empty($neo)) {
            return response()->respondNotFound();
        }

        // Guard clause for non-accessible neo
        if (!$neo->isUserAccessible()) {
            return response()->respondNotFound();
        }

        // Check if maximum products are reached
        $expertCount = $neo->products()->count();
        if ($expertCount >= config('bphero.product_limit')) {
            return response()->respondForbidden();
        }

        // Encode image_link and reference_url to information
        $information['image_link'] = null;
        $information['reference_url'] = !empty($requestData['reference_url']) ? $requestData['reference_url'] : null;
        $requestData['information'] = json_encode($information, JSON_UNESCAPED_SLASHES);

        // Create new skill for neo user
        $neoExpert = new NeoExpert();
        $neoExpert->fill($requestData);
        $neoExpert->neo_id = $id;
        $neoExpert->attribute_code = AttributeCodes::PRODUCT_SERVICE_INFORMATION;
        $neoExpert->sort = 0;
        $neoExpert->save();

        return response()->respondSuccess();
    }

    /**
     * Update product for Neo profile
     *
     * @param \App\Http\Requests\Neo\UpsertProductRequest $request
     * @param \App\Models\NeoExpert $product
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateProduct(UpsertProductRequest $request, NeoExpert $product)
    {
        $this->authorize('update', [NeoExpert::class, $product]);

        // Get request data
        $requestData = $request->validated();

        // Encode image_link and reference_url to information
        $information['image_link'] = null;
        $information['reference_url'] = !empty($requestData['reference_url']) ? $requestData['reference_url'] : null;
        $requestData['information'] = json_encode($information, JSON_UNESCAPED_SLASHES);

        // Update skill
        $product->update($requestData);

        return response()->respondSuccess();
    }

    /**
     * Delete product for Neo profile
     *
     * @param \App\Models\NeoExpert $product
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteProduct(NeoExpert $product)
    {
        $this->authorize('delete', [NeoExpert::class, $product]);

        // Delete neo expert skill
        $product->delete();

        return response()->respondSuccess();
    }

    /**
     * Fetch emails from neo_experts
     *
     * @param int $id Neo ID
     * @param \App\Models\NeoExpert $email
     * @return \Illuminate\Http\JsonResponse
     */
    public function getEmails(NeoExpert $email, $id)
    {
        $this->authorize('get', [NeoExpert::class, $email]);

        //Get data with the same Neo id
        $emails = NeoExpert::whereNeoId($id)
            ->whereAttributeCode(AttributeCodes::EMAIL)
            ->get();

        // Get emails as json resource
        $results = EmailResource::collection($emails);

        return response()->respondSuccess($results);
    }

    /**
     * Register new Neo expert email address.
     *
     * @param \App\Http\Requests\Neo\UpsertEmailAddressRequest $request
     * @param \App\Models\NeoExpert $email
     * @param int $id Neo ID
     * @return \Illuminate\Http\JsonResponse
     *
     */
    public function createEmail(UpsertEmailAddressRequest $request, NeoExpert $email, $id)
    {
        $this->authorize('create', [NeoExpert::class, $email]);

        // Get validated data
        $requestData = $request->validated();

        $email = NeoExpert::whereNeoId($id)
            ->whereAttributeCode(AttributeCodes::EMAIL)
            ->get();

        // Check if maximum emails are reached
        $expertCount = $email->count();
        if ($expertCount >= 3) {
            return response()->respondForbidden();
        }

        // Save Email Address
        $email = new NeoExpert();
        $email->neo_id = $id;
        $email->content = $requestData['email_address'];
        $email->attribute_code = AttributeCodes::EMAIL;
        $email->sort = 0;
        $email->save();

        return response()->respondSuccess();
    }

    /**
     * Delete a Neo expert email address.
     *
     * @param \App\Models\NeoExpert $email
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteEmail(NeoExpert $email)
    {
        $this->authorize('delete', [NeoExpert::class, $email]);

        // Delete neo expert email
        $email->delete();

        return response()->respondSuccess();
    }

    /**
     * Update a Neo expert email address.
     *
     * @param \App\Http\Requests\Neo\UpsertEmailAddressRequest $request
     * @param \App\Models\NeoExpert $email
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateEmail(UpsertEmailAddressRequest $request, NeoExpert $email)
    {
        $this->authorize('update', [NeoExpert::class, $email]);

        // Get request data
        $requestData = $request->validated();

        // Update neo expert email
        $email->update([
            'content' => $requestData['email_address'],
        ]);

        return response()->respondSuccess();
    }

    /**
     * Register/Update Neo expert business hours and holiday.
     *
     * @param \App\Http\Requests\Neo\UpsertBusinessHolidayRequest $request
     * @param \App\Models\Neo $neo
     * @return \Illuminate\Http\JsonResponse
     *
     */
    public function upsertBusinessHoliday(UpsertBusinessHolidayRequest $request, Neo $neo)
    {
        $this->authorize('update', [Neo::class, $neo]);

        $businessHours = $neo->experts();

        // Get validated data
        $requestData = $request->validated();

        //Create or Update business hours and holiday
        $businessHours->updateOrCreate(
            [
                'neo_id' => $neo->id,
                'sort' => 0,
                'attribute_code' => AttributeCodes::BUSINESS_HOURS
            ],
            $requestData
        );

        return response()->respondSuccess();
    }

    /**
     * Create session for selected NEO service
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function createNEOServiceSession(Request $request)
    {
        /** @var User */
        $user = auth()->user();

        //Get request id
        $id = $request->get('id');

        //Get Neo belongs of authenticated user
        $neoBelong =  $user->rio->neos->where('id', $id)->first();

        if (!$neoBelong) {
            return response()->respondNotFound();
        }

        $data = ([
            'name' => $neoBelong->organization_name,
            'link' => route('neo.profile.introduction', ['neo' => $neoBelong->id]),
            'profile_image' => $neoBelong->neo_profile->profile_photo ?? asset('images/profile/user_default.png'),
            'profile_edit_link' => route('neo.profile.edit', ['neo' => $neoBelong->id]),
            'privacy_edit_link' => route('neo.privacy.edit', ['neo' => $neoBelong->id]),
        ]);

        // Set service selected
        ServiceSelected::setSelected($neoBelong);

        return response()->respondSuccess($data);
    }

    /**
     * Create NEO participation
     *
     * @param int $id Neo ID
     * @return \Illuminate\Http\JsonResponse
     *
     */
    public function createParticipation($id)
    {
        /** @var User */
        $user = auth()->user();

        $neo = Neo::whereId($id)->first();

        if (!$neo) {
            return response()->respondNotFound();
        }

        $neoBelong = NeoBelong::whereNeoId($id)
            ->whereRioId($user->rio->id)
            ->first();

        if ($neoBelong) {
            session()->put('alert', [
                'status' => 'danger',
                'message' => __('Bad Request'),
            ]);

            return response()->respondForbidden();
        }

        $neoBelong = new NeoBelong();
        $neoBelong->neo_id = $id;
        $neoBelong->rio_id = $user->rio->id;
        $neoBelong->role = RoleType::MEMBER;
        $neoBelong->status = NeoBelongStatusType::APPLYING;
        $neoBelong->save();

        return response()->respondSuccess();
    }

    /**
     * Exit Neo for authenticated rio
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function exitNeo(NeoBelong $affiliate)
    {
        $this->authorize('delete', [NeoBelong::class, $affiliate]);

        // Exit Neo
        $affiliate->delete();

        return redirect()
            ->route('neo.profile.introduction', $affiliate->neo_id)
            ->withAlertBox('success', __('Im out of Neo.'));
    }

    /**
     * Approve pending particpants in NEO.
     *
     * @param \App\Http\Requests\Neo\ParticipationRequest $request
     * @return \Illuminate\Http\JsonResponse
     *
     */
    public function approveParticipant(ParticipationRequest $request)
    {
        try {
            $data = $request->validated();

            $currentNeo = Neo::findOrFail(Session::get('neoProfileId'));

            $this->authorize('approveParticipant', [NeoBelong::class, $data, $currentNeo]);

            DB::beginTransaction();

            NeoBelong::whereId($data['id'])
                ->whereNeoId(Session::get('neoProfileId'))
                ->whereRioId($data['rio_id'])
                ->whereStatus(NeoBelongStatusType::APPLYING)
                ->update(['status' => NeoBelongStatuses::AFFILIATED]);

            $status = __('Approved');

            DB::commit();

            return response()->respondSuccess($status);
        } catch (NotFoundHttpException $e) {
            DB::rollback();

            return response()->respondNotFound();
        } catch (\Exception $e) {
            DB::rollback();

            return response()->respondInternalServerError();
        }
    }

    /**
     * Reject pending particpants in NEO.
     *
     * @param \App\Http\Requests\Neo\ParticipationRequest $request
     * @return \Illuminate\Http\JsonResponse
     *
     */
    public function rejectParticipant(ParticipationRequest $request)
    {
        try {
            $data = $request->validated();

            $currentNeo = Neo::findOrFail(Session::get('neoProfileId'));

            $this->authorize('rejectParticipant', [NeoBelong::class, $data, $currentNeo]);

            DB::beginTransaction();

            NeoBelong::whereId($data['id'])
                ->whereNeoId(Session::get('neoProfileId'))
                ->whereRioId($data['rio_id'])
                ->whereStatus(NeoBelongStatusType::APPLYING)
                ->delete();

            $status = __('Rejected');

            DB::commit();

            return response()->respondSuccess($status);
        } catch (NotFoundHttpException $e) {
            DB::rollback();

            return response()->respondNotFound();
        } catch (\Exception $e) {
            DB::rollback();

            return response()->respondInternalServerError();
        }
    }

    /**
     * Cancel NEO Connection
     *
     * @param int $id Neo ID
     * @return \Illuminate\Http\JsonResponse
     *
     */
    public function cancelParticipation($id)
    {
        NeoBelong::findOrFail($id)
            ->delete();

        session()->put('alert', [
            'status' => 'success',
            'message' => __('The participation application has been cancelled.'),
        ]);

        return response()->respondSuccess();
    }

    /**
     * Update displayed pending particpants in NEO.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id Neo ID
     * @return array|string
     *
     */
    public function updatePendingParticipants(Request $request, $id)
    {
        $pageNo = $request->get('pageNo');

        $participants = NeoBelong::applyingList()
            ->whereNeoId($id)
            ->paginate(config('bphero.paginate_count'), ['*'], 'page', $pageNo);

        //Reload pagination and data
        return view('neo.profile.pending-participants', compact('participants'))->render();
    }

    /**
     * Update displayed service selections.
     *
     * @param \Illuminate\Http\Request $request
     * @return array|string
     *
     */
    public function serviceSelections(Request $request)
    {
        /** @var User */
        $user = auth()->user();

        $currentUser = $user->rio;

        $pageNo = $request->get('pageNo');

        $serviceSelected = json_decode(Session::get('ServiceSelected'));

        $neos = $user->rio->neos()
            ->whereStatus(NeoBelongStatuses::AFFILIATED)
            ->paginate(config('bphero.paginate_count'), ['*'], 'page', $pageNo);

        //Reload pagination and data
        return view('components.service-selection', compact('serviceSelected', 'currentUser', 'neos', 'pageNo'))->render();
    }

    /**
     * Create NEO Connection
     *
     * @param int $id Neo ID
     * @param string|null $message
     * @return \Illuminate\Http\JsonResponse
     *
     */
    public function createConnection($id, $message = null)
    {
        /** @var User */
        $user = auth()->user();
        $rio = $user->rio;

        DB::beginTransaction();

        try {
            $serviceSelected = json_decode(Session::get('ServiceSelected'));

            /** @var Neo */
            $neoConnectionReceiver = Neo::whereId($id)->first();

            /** @var NeoBelong */
            $neoConnectionOwner = $neoConnectionReceiver->owner;

            // Set notification data
            $notification = [
                /** @phpstan-ignore-next-line */
                'rio_id' => $neoConnectionOwner->rio->id,
                'receive_neo_id' => $neoConnectionReceiver->id,
                'destination_url' => route('connection.application-list'),
            ];

            $neoConnection = new NeoConnection();
            $neoConnection->neo_id = $id;

            if (!$serviceSelected || $serviceSelected->type === ServiceSelectionTypes::RIO) {
                // Get RIO sender
                /** @var Rio */
                $sender = Rio::whereId($serviceSelected->data->id)->first();

                // For RIO to NEO connection application
                $neoConnection->connection_rio_id = $sender->id ?? $rio->id;
                $notification += [
                    'notification_content' => __('Notification Content - Connection Application', [
                        'sender_name' => $sender->full_name . 'さん'
                    ]),
                ];
            } else {
                // Get NEO sender
                /** @var Neo */
                $sender = Neo::whereId($serviceSelected->data->id)->first();

                // For NEO to NEO connection application
                $neoConnection->connection_neo_id = $sender->id;
                $notification += [
                    'notification_content' => __('Notification Content - Connection Application', [
                        'sender_name' => $sender->organization_name
                    ]),
                ];
            }

            $neoConnection->status = ConnectionStatusType::APPLYING;
            $neoConnection->message = $message;
            $neoConnection->save();

            // Send email notification to NEO connection application
            $neoConnection->sendEmailToConnection($sender, $neoConnectionReceiver);

            // Record new notification
            Notification::createNotification($notification);

            DB::commit();

            return response()->respondSuccess();
        } catch (\Exception $e) {
            DB::rollback();
            Log::debug($e->getMessage());

            return response()->respondInternalServerError();
        }
    }

    /**
     * Cancel NEO Connection
     *
     * @param int $id Neo ID
     * @return \Illuminate\Http\JsonResponse
     *
     */
    public function cancelConnection($id)
    {
        NeoConnection::IsExistingNeoConnection($id)->delete();

        session()->put('alert', [
            'status' => 'success',
            'message' => __('The connection application has been cancelled.'),
        ]);

        return response()->respondSuccess();
    }

    /**
     * Disconnect NEO Connection
     *
     * @param int $id Neo ID
     * @return \Illuminate\Http\JsonResponse
     *
     */
    public function disconnectConnection($id)
    {
        /** @var User */
        $user = auth()->user();

        $service = json_decode(Session::get('ServiceSelected'));

        $neo = Neo::find($id);

        if ($service->type === ServiceSelectionTypes::RIO) {
            $checkChatConnection = Chat::getChatParticipantsPair($neo, ServiceSelectionTypes::NEO, ConnectedChatType::RIO_TO_NEO);
            $chatConnection = $user->rio->chats->where('id', key((array)$checkChatConnection))->first();

            if (empty($chatConnection) && $neo) {
                $chatConnection = $neo->chats->where('id', key((array)$checkChatConnection))->first();
            }

            NeoConnection::IsExistingNeoConnection($id)->delete();
        } else {
            $checkChatConnection = Chat::getChatParticipantsPair($neo, ServiceSelectionTypes::NEO, ConnectedChatType::NEO_TO_NEO);
            /** @var Neo */
            $currentNeo = Neo::find($service->data->id);
            $chatConnection = $currentNeo->chats->where('id', key((array)$checkChatConnection))->first();

            if (empty($chatConnection) && $neo) {
                $chatConnection = $neo->chats->where('id', key((array)$checkChatConnection))->first();
            }

            $idToDelete = NeoConnection::whereConnectionNeoId($service->data->id)
                ->whereNeoId($id)
                ->exists();

            if ($idToDelete) {
                NeoConnection::IsExistingNeoConnection($id)->delete();
            }
        }

        if ($chatConnection) {
            $chatConnection->update([
                'status' => ChatStatuses::ARCHIVE
            ]);
        }

        session()->put('alert', [
            'status' => 'success',
            'message' => __('I disconnected'),
        ]);

        return response()->respondSuccess();
    }

    /**
     * Update displayed pending particpation invites list
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return array|string
     *
     */
    public function inviteLists(Request $request, $id)
    {
        $pageNo = $request->get('pageNo');

        $connectedLists = NeoConnection::connectionWithNeoBelong($id)
            ->where('neo_belongs.status', '!=', NeoBelongStatuses::PENDING)
            ->where('neo_belongs.status', '!=', NeoBelongStatuses::AFFILIATED)
            ->paginate(config('bphero.paginate_count'), ['*'], 'page', $pageNo);

        //Reload pagination and data
        return view('neo.profile.invitation-list', compact('connectedLists'))->render();
    }

    /**
     * Update displayed particpation invitation lists
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return mixed
     *
     */
    public function participationInvitationLists(Request $request, $id)
    {
        $pageNo = $request['pageNo'] ? $request['pageNo'] : 1;
        $text = $request['prevSearch'];

        $connectedLists = NeoConnection::possibleRioInviteesForNeoBelong($id, $text)
            ->paginate(config('bphero.paginate_count'), ['*'], 'page', $pageNo);

        return response()->respondSuccess($connectedLists);
    }

    /**
     * Invite connection
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return mixed
     *
     */
    public function inviteToConnection(Request $request, $id)
    {
        $userId = $request['userId'];

        DB::beginTransaction();

        $isExists = NeoBelong::whereNeoId($id)
            ->whereRioId($userId)
            ->exists();

        if ($isExists) {
            return response()->respondNotFound();
        }

        try {
            // Create NEO invitation
            $invite = new NeoBelong();
            $invite->neo_id = $id;
            $invite->rio_id = $userId;
            $invite->role = RoleType::MEMBER;
            $invite->status = NeoBelongStatusType::INVITING;
            $invite->save();

            // Send email to the invited RIO
            $neo = Neo::whereId($id)->firstOrFail();
            $rio = Rio::whereId($userId)->firstOrFail();
            $invite->sendEmailToInvitedRio($neo, $rio);

            // Record new notification
            Notification::createNotification([
                'rio_id' => $rio->id,
                'destination_url' => route('rio.profile.invitation-list'),
                'notification_content' => __('Notification Content - Neo Invitation', [
                    'neo_name' => $neo->organization_name
                ]),
            ]);

            DB::commit();

            return response()->respondSuccess();
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->respondInternalServerError();
        }
    }

    /**
     * Cancel Invitation
     *
     * @param \App\Http\Requests\Neo\ParticipationInvitationRequest $request
     * @param int $id
     * @return mixed
     *
     */
    public function cancelInvitation(ParticipationInvitationRequest $request, $id)
    {
        try {
            $data = $request->validated();

            $currentNeo = Neo::findOrFail(Session::get('neoProfileId'));

            $this->authorize('cancelInvitation', [NeoBelong::class, $data, $currentNeo]);

            DB::beginTransaction();

            NeoBelong::whereNeoId($id)
                ->whereRioId($data['id'])
                ->delete();

            //To retain current page
            $connectedLists = NeoConnection::connectionWithNeoBelong($id)
                ->where('neo_belongs.status', '!=', NeoBelongStatuses::PENDING)
                ->where('neo_belongs.status', '!=', NeoBelongStatuses::AFFILIATED)
                ->paginate(config('bphero.paginate_count'));

            DB::commit();

            //Reload pagination and data
            return view('neo.profile.invitation-list', compact('connectedLists'))->render();
        } catch (NotFoundHttpException $e) {
            DB::rollback();

            abort(404);
        } catch (\Exception $e) {
            DB::rollback();

            abort(500);
        }
    }

    /**
     * Search Invitation Connection
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return mixed
     *
     */
    public function searchInvitationConnection(Request $request, $id)
    {
        $text = $request['keyword'];

        $connectedLists = NeoConnection::possibleRioInviteesForNeoBelong($id, $text)
            ->paginate(config('bphero.paginate_count'));

        return response()->respondSuccess($connectedLists);
    }

    /**
     * Get Connection lists
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return mixed
     *
     */
    public function getConnectionList(Request $request, $id)
    {
        $connectedLists = NeoConnection::possibleRioInviteesForNeoBelong($id)
            ->paginate(config('bphero.paginate_count'));

        return response()->respondSuccess($connectedLists);
    }

    /**
     * Update Neo Profile Overseas information.
     *
     * @param \App\Http\Requests\Neo\UpdateOverseasSupportRequest $request
     * @param \App\Models\Neo $neo
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateOverseasSupport(UpdateOverseasSupportRequest $request, Neo $neo)
    {
        $this->authorize('update', [Neo::class, $neo]);

        // Get request data
        $requestData = $request->validated();

        // Update neo profile
        $neo->neo_profile()->update($requestData);

        return response()->respondSuccess($requestData);
    }
}
