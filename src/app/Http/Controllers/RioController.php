<?php

namespace App\Http\Controllers;

use App\Enums\AttributeCodes;
use App\Enums\LogTypes;
use App\Enums\PrefectureTypes;
use App\Enums\YearsOfExperiences;
use App\Enums\Rio\GenderType;
use App\Enums\ServiceSelectionTypes;
use App\Http\Requests\Rio\UpdateAffiliateRequest;
use App\Http\Requests\Rio\UpdateBirthdateRequest;
use App\Http\Requests\Rio\UpdateGenderRequest;
use App\Http\Requests\Rio\UpdateHomeAddressRequest;
use App\Http\Requests\Rio\UpdateNameRequest;
use App\Http\Requests\Rio\UpdatePresentAddressRequest;
use App\Http\Requests\Rio\UpdateSelfIntroductionRequest;
use App\Http\Requests\Rio\UpdateTelephoneRequest;
use App\Http\Requests\Rio\UpsertAwardHistoryRequest;
use App\Http\Requests\Rio\UpsertEducationalBackgroundRequest;
use App\Http\Requests\Rio\UpsertIndustryRequest;
use App\Http\Requests\Rio\UpsertProductRequest;
use App\Http\Requests\Rio\UpsertProfessionRequest;
use App\Http\Requests\Rio\UpsertQualificationRequest;
use App\Http\Requests\Rio\UpsertSkillRequest;
use App\Http\Resources\Rio\AffiliateResource;
use App\Http\Resources\Rio\AwardResource;
use App\Http\Resources\Rio\EducationalBackgroundResource;
use App\Http\Resources\Rio\IndustryResource;
use App\Http\Resources\Rio\ProductResource;
use App\Http\Resources\Rio\ProfessionResource;
use App\Http\Resources\Rio\QualificationResource;
use App\Http\Resources\Rio\SkillResource;
use App\Models\BusinessCategory;
use App\Models\Neo;
use App\Models\Rio;
use App\Models\RioConnection;
use App\Models\RioExpert;
use App\Models\RioLog;
use App\Models\RioProfile;
use App\Models\User;
use App\Models\NeoBelong;
use App\Models\NeoConnection;
use Illuminate\Http\Request;
use Carbon\Carbon;
use DB;
use Session;
use App\Enums\ConnectionStatuses as EnumsConnectionStatuses;
use App\Enums\NeoBelongStatuses;
use App\Http\Requests\Rio\UpdateProfileImageRequest;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use App\Objects\ServiceSelected;

class RioController extends Controller
{
    /**
     * Rio Profile page - Introduction tab
     *
     * @param string|null $id
     * @return \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
     */
    public function introduction($id = null)
    {
        /** @var bool $isAccessInProfile bool to determine if the access to the page is from the profile page */
        $isAccessInProfile = (!is_null(request()->input('is_access_in_profile'))) ? true : false ;

        /** @var User */
        $user = auth()->user();

        $rioId = !empty($id)
             ? $id
             : $user->rio_id
             ?? null;

        $rio = Rio::with('products')->findOrFail($rioId);

        $invitationCount = NeoConnection::allInvitesForNeoBelong()
            ->count();

        // Get service selected
        $service = ServiceSelected::getSelected();

        if (!$isAccessInProfile && !empty($rio) && ($user->id != $id)) {
            $this->createRIOviewlog(
                LogTypes::VIEWED,
                $rio->id,
                json_encode([
                    'accessed_by' => $service->type,
                    'id' => $service->data->id,
                ]),
            );
        }

        $neoPrivacyStatus = null;

        $status = RioConnection::connectionStatus($rio);
        $status['is_applicant'] = false;
        $status['is_rio_requested_to_neo'] = false;

        if ($service->type === ServiceSelectionTypes::NEO) {
            $neo = Neo::findOrFail($service->data->id);
            $neoPrivacyStatus = RioConnection::neoConnectionStatus($rio, $neo);

            /** @var Neo $neo->id */
            $status['is_rio_requested_to_neo'] = NeoConnection::whereNeoId($neo->id)
                ->whereConnectionRioId($rio->id)
                ->whereStatus(EnumsConnectionStatuses::PENDING)
                ->exists();
        }

        $service = json_decode(Session::get('ServiceSelected'));
        $neoStatus = NeoConnection::whereNeoId($service->data->id)
            ->whereConnectionRioId($id)
            ->whereStatus(EnumsConnectionStatuses::APPLYING_BY_NEO)
            ->first()->status ?? 0;

        $isProfileOwner = ((int) $rioId === $user->id);
        $connectionApplicant = RioConnection::where('id', $status['connection_id'])->pluck('created_rio_id')->toArray();

        if (!empty($connectionApplicant)) {
            $status['is_applicant'] = $connectionApplicant[0] === $user->id;
        }

        return view('rio.profile.introduction', compact(
            'rio',
            'status',
            'service',
            'neoStatus',
            'neoPrivacyStatus',
            'invitationCount',
            'isProfileOwner'
        ));
    }

    /**
     * RIO Profile page - Information tab
     *
     * @param string|null $id
     * @return \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
     */
    public function information($id = null)
    {
        /** @var bool $isAccessInProfile bool to determine if the access to the page is from the profile page */
        $isAccessInProfile = (!is_null(request()->input('is_access_in_profile'))) ? true : false ;

        /** @var User */
        $user = auth()->user();

        // Initialize RIO and NEO information
        /** @var Rio */
        $rio = !empty($id)
            ? Rio::findOrFail($id)
            : $user->rio;

        $rioProfile = $rio->rio_profile()
            ->firstOrFail();

        $neos = $rio->neos()
            ->wherePivot('is_display', true)
            ->get();

        // Get service selected
        $service = ServiceSelected::getSelected();

        if (!$isAccessInProfile && !empty($rio) && ($user->id != $id)) {
            $this->createRIOviewlog(
                LogTypes::VIEWED,
                $rio->id,
                json_encode([
                    'accessed_by' => $service->type,
                    'id' => $service->data->id,
                ]),
            );
        }

        // Initialize RIO expert resources
        $professions = $rio->professions;
        $industries = $rio->industries;
        $awards = $rio->awards;
        $skills = $rio->skills;
        $qualifications = $rio->qualifications;
        $educationalBackgrounds = $rio->educational_backgrounds;

        // Initialize data lists
        $genders = GenderType::asSelectArray();
        $prefectures = PrefectureTypes::asSelectArray();

        /** @var string */
        $overseas = __('Overseas');

        // Construct present prefecture text
        if ($rioProfile->present_address_prefecture == PrefectureTypes::OTHER) {
            $presentNationality = $rioProfile->present_address_nationality ?? null;
            $rioProfile['present_address_prefecture_formatted'] = $presentNationality
                ? "$overseas ($presentNationality)" : null;
        } else {
            $rioProfile['present_address_prefecture_formatted'] =
                $prefectures[$rioProfile->present_address_prefecture] ?? null;
        }

        // Construct home prefecture text
        if ($rioProfile->home_address_prefecture == PrefectureTypes::OTHER) {
            $homeNationality = $rioProfile->home_address_nationality ?? null;
            $rioProfile['home_address_prefecture_formatted'] = $homeNationality
                ? "$overseas ($homeNationality)" : null;
        } else {
            $rioProfile['home_address_prefecture_formatted'] =
                $prefectures[$rioProfile->home_address_prefecture] ?? null;
        }

        $status = RioConnection::connectionStatus($rio);
        $status['is_applicant'] = false;
        $status['is_rio_requested_to_neo'] = false;

        $neoStatus = NeoConnection::whereNeoId($service->data->id)
            ->whereConnectionRioId($id)
            ->whereStatus(EnumsConnectionStatuses::APPLYING_BY_NEO)
            ->first()->status ?? 0;

        $neoPrivacyStatus = null;

        if ($service->type === ServiceSelectionTypes::NEO) {
            $neo = Neo::findOrFail($service->data->id);
            $neoPrivacyStatus = RioConnection::neoConnectionStatus($rio, $neo);

            $status['is_rio_requested_to_neo'] = NeoConnection::whereNeoId($neo->id)
                ->whereConnectionRioId($rio->id)
                ->whereStatus(EnumsConnectionStatuses::PENDING)
                ->exists();
        }

        $invitationCount = NeoConnection::allInvitesForNeoBelong()
            ->count();

        $isProfileOwner = ($rio->id === $user->id);
        $connectionApplicant = RioConnection::where('id', $status['connection_id'])->pluck('created_rio_id')->toArray();

        if (!empty($connectionApplicant)) {
            $status['is_applicant'] = $connectionApplicant[0] === $user->id;
        }

        return view('rio.profile.information', compact(
            'user',
            'neos',
            'rio',
            'rioProfile',
            'professions',
            'industries',
            'awards',
            'skills',
            'qualifications',
            'educationalBackgrounds',
            'genders',
            'id',
            'status',
            'neoStatus',
            'service',
            'neoPrivacyStatus',
            'invitationCount',
            'isProfileOwner'
        ));
    }

    /**
     * Rio profile edit page
     *
     * @return \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
     */
    public function edit()
    {
        /** @var User */
        $user = auth()->user();

        // Initialize data
        $rio = $user->rio;
        $rioProfile = $user->rio->rio_profile;
        $rioExpert = $user->rio->rio_expert;
        $prefectures = PrefectureTypes::asSelectArray();

        // Industry dropdown data
        $yearsOfExperiences = YearsOfExperiences::asSelectArray();
        $businessCategories = BusinessCategory::pluck('business_category_name', 'id');

        return view('rio.profile.edit', compact(
            'user',
            'rio',
            'rioProfile',
            'rioExpert',
            'prefectures',
            'yearsOfExperiences',
            'businessCategories',
        ));
    }

    /**
     * Rio Profile - Update Profile Image
     *
     * @param \App\Http\Requests\Rio\UpdateProfileImageRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateImage(UpdateProfileImageRequest $request)
    {
        /** @var User */
        $user = auth()->user();
        $rio = $user->rio;

        $this->authorize('updateDelete', [Rio::class, $rio]);

        /** @var RioProfile */
        $rioProfile = $rio->rio_profile;
        $rioId = $rio->id;

        $rioProfileImagePath = config('bphero.rio_profile_image');
        $profileImageSessionKey = "rio.profile_image.${rioId}";

        // Guard clause for non-existing rio
        if (empty($rio) || !$request->session()->has($profileImageSessionKey)) {
            return response()->respondNotFound();
        }

        $fileName = $request->session()->get($profileImageSessionKey);

        $tempFilePath = "${rioProfileImagePath}${rioId}/tmp/${fileName}";
        $filePath = "${rioProfileImagePath}${rioId}";

        // browse all files .jp, .png on folder and remove
        $files = Storage::disk('public')->files($filePath. "/");
        if ($files) {
            foreach ($files as $file) {
                Storage::disk('public')->delete($file);
            }
        }

        if (Storage::disk('public')->move($tempFilePath, "${filePath}/${fileName}")) {
            $rioProfile->profile_photo = $fileName;
            if ($rioProfile->save()) {
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
     * Rio Profile - Delete Profile Image
     *
     * @param Rio $rio
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteImage(Rio $rio)
    {
        /** @var User */
        $user = auth()->user();

        $this->authorize('updateDelete', [Rio::class, $rio]);

        /** @var RioProfile */
        $rioProfile = $rio->rio_profile;
        $rioId = $rio->id;

        $rioProfileImagePath = config('bphero.rio_profile_image');

        // Guard clause for non-existing rio
        if (empty($rio) && is_null($rioProfile->profile_photo)) {
            return response()->respondNotFound();
        }

        if (!is_null($rioProfile->profile_photo)) {
            $filePath = "${rioProfileImagePath}${rioId}/" . $rioProfile->profile_photo;
            if (Storage::disk('public')->exists($filePath)) {
                Storage::disk('public')->delete($filePath);

                $rioProfile->profile_photo = null;

                if ($rioProfile->save()) {
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
     * Rio Profile - Update Birthdate Information
     *
     * @param \App\Http\Requests\Rio\UpdateBirthdateRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateBirthdate(UpdateBirthdateRequest $request)
    {
        /** @var User */
        $user = auth()->user();
        $rio = $user->rio;

        // Guard clause for non-existing rio
        if (empty($rio)) {
            return response()->respondNotFound();
        }

        $requestData = $request->validated();

        $rio->update($requestData);

        return response()->respondSuccess($requestData);
    }

    /**
     * Update self-introduction for authenticated rio
     *
     * @param \App\Http\Requests\Rio\UpdateSelfIntroductionRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateSelfIntroduction(UpdateSelfIntroductionRequest $request)
    {
        /** @var User */
        $user = auth()->user();

        // Request data
        $data = $request->validated();

        try {
            DB::beginTransaction();

            // Update RIO profile self-introduction
            RioProfile::whereRioId($user->rio->id)->update($data);

            DB::commit();

            return response()->respondSuccess($data);
        } catch (\Exception $exception) {
            DB::rollBack();

            throw $exception;
        }
    }

    /**
     * Update RIO Profile Present address information.
     *
     * @param \App\Http\Requests\Rio\UpdatePresentAddressRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updatePresentAddress(UpdatePresentAddressRequest $request)
    {
        /** @var User */
        $user = auth()->user();

        /** @var RioProfile */
        $rioProfile = $user->rio->rio_profile;

        // Request data
        $data = $request->validated();

        // If prefecture is outside Japan, set nationality to nulll
        if ((int) $data['present_address_prefecture'] !== PrefectureTypes::OTHER) {
            $data['present_address_nationality'] = null;
        }

        // Update RIO profile present address
        $rioProfile->update($data);

        return response()->respondSuccess($data);
    }

    /**
     * Update RIO Profile Gender information.
     *
     * @param \App\Http\Requests\Rio\UpdateGenderRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateGender(UpdateGenderRequest $request)
    {
        /** @var User */
        $user = auth()->user();
        $rio = $user->rio;

        // Guard clause for non-existing rio
        if (empty($rio)) {
            return response()->respondNotFound();
        }

        $requestData = $request->validated();

        $rio->update($requestData);

        return response()->respondSuccess($requestData);
    }

    /**
     * Update RIO Profile Telephone information.
     *
     * @param \App\Http\Requests\Rio\UpdateTelephoneRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateTelephone(UpdateTelephoneRequest $request)
    {
        /** @var User */
        $user = auth()->user();
        $rio = $user->rio;

        $requestData = $request->validated();

        $rio->update($requestData);

        return response()->respondSuccess($requestData);
    }

    /**
     * Update RIO Profile Name information.
     *
     * @param \App\Http\Requests\Rio\UpdateNameRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateName(UpdateNameRequest $request)
    {
        /** @var User */
        $user = auth()->user();
        $rio = $user->rio;

        $requestData = $request->validated();

        $rio->update($requestData);

        return response()->respondSuccess($requestData);
    }

    /**
     * Fetch profession for authenticated rio
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getProfessions(Request $request)
    {
        // Get rio of authenticated user
        /** @var User */
        $user = auth()->user();
        $rio = $user->rio ?? null;

        // Guard clause for non-existing rio
        if (empty($rio)) {
            return response()->respondNotFound();
        }

        // Get professions as json resource
        $results = ProfessionResource::collection($rio->professions);

        return response()->respondSuccess($results);
    }

    /**
     * Create profession for authenticated rio
     *
     * @param \App\Http\Requests\Rio\UpsertProfessionRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function createProfession(UpsertProfessionRequest $request)
    {
        // Get request data
        $requestData = $request->validated();

        // Get rio of authenticated user
        /** @var User */
        $user = auth()->user();
        $rio = $user->rio ?? null;

        // Guard clause for non-existing rio
        if (empty($rio)) {
            return response()->respondNotFound();
        }

        // Check if maximum professions are reached
        $expertCount = $rio->professions()->count();
        if ($expertCount >= 10) {
            return response()->respondForbidden();
        }

        // Create new profession for rio user
        $rioExpert = new RioExpert();
        $rioExpert->fill($requestData);
        $rioExpert->rio_id = $user->rio_id;
        $rioExpert->attribute_code = AttributeCodes::PROFESSION;
        $rioExpert->sort = 0;
        $rioExpert->save();

        return response()->respondSuccess();
    }

    /**
     * Update profession for authenticated rio
     *
     * @param \App\Http\Requests\Rio\UpsertProfessionRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateProfession(UpsertProfessionRequest $request, RioExpert $profession)
    {
        $this->authorize('update', [RioExpert::class, $profession]);

        // Get request data
        $requestData = $request->validated();

        // Update profession
        $profession->update($requestData);

        return response()->respondSuccess();
    }

    /**
     * Delete profession of authenticated rio
     *
     * @param \App\Models\RioExpert $profession
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteProfession(RioExpert $profession)
    {
        $this->authorize('delete', [RioExpert::class, $profession]);

        // Delete rio expert profession
        $profession->delete();

        return response()->respondSuccess();
    }

    /**
     * Fetch industries for authenticated rio
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getIndustries(Request $request)
    {
        // Get rio of authenticated user
        /** @var User */
        $user = auth()->user();
        $rio = $user->rio;

        // Guard clause for non-existing rio
        if (empty($rio)) {
            return response()->respondNotFound();
        }

        // Get industries as json resource
        $results = IndustryResource::collection($rio->industries);

        return response()->respondSuccess($results);
    }

    /**
     * Create industry for authenticated rio
     *
     * @param \App\Http\Requests\Rio\UpsertIndustryRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function createIndustry(UpsertIndustryRequest $request)
    {
        // Get request data
        $requestData = $request->validated();

        // Get rio of authenticated user
        /** @var User */
        $user = auth()->user();
        $rio = $user->rio ?? null;

        // Guard clause for non-existing rio
        if (empty($rio)) {
            return response()->respondNotFound();
        }

        // Check if maximum industries are reached
        $expertCount = $rio->industries()->count();
        if ($expertCount >= 10) {
            return response()->respondForbidden();
        }

        // Get business category based on input
        /** @var BusinessCategory */
        $businessCategory = BusinessCategory::findOrFail($requestData['business_category_id']);

        // Create new industry for rio user
        $rioExpert = new RioExpert();
        $rioExpert->fill($requestData);
        $rioExpert->rio_id = $user->rio_id;
        $rioExpert->attribute_code = AttributeCodes::INDUSTRY;
        $rioExpert->content = $businessCategory->business_category_name;
        $rioExpert->sort = 0;
        $rioExpert->save();

        return response()->respondSuccess();
    }

    /**
     * Update industry for authenticated rio
     *
     * @param \App\Http\Requests\Rio\UpsertIndustryRequest $request
     * @param \App\Models\RioExpert $industry
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateIndustry(UpsertIndustryRequest $request, RioExpert $industry)
    {
        $this->authorize('update', [RioExpert::class, $industry]);

        // Get request data
        $requestData = $request->validated();

        // Get rio of authenticated user
        /** @var User */
        $user = auth()->user();
        $rio = $user->rio ?? null;

        // Guard clause for non-existing rio
        if (empty($rio)) {
            return response()->respondNotFound();
        }

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
     * Delete industry of authenticated rio
     *
     * @param \App\Models\RioExpert $industry
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteIndustry(RioExpert $industry)
    {
        $this->authorize('delete', [RioExpert::class, $industry]);

        // Delete rio expert industry
        $industry->delete();

        return response()->respondSuccess();
    }

    /**
     * Fetch qualification for authenticated rio
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getQualifications(Request $request)
    {
        // Get rio of authenticated user
        /** @var User */
        $user = auth()->user();
        $rio = $user->rio ?? null;

        // Guard clause for non-existing rio
        if (empty($rio)) {
            return response()->respondNotFound();
        }

        // Get professions as json resource
        $results = QualificationResource::collection($rio->qualifications);

        return response()->respondSuccess($results);
    }

    /**
     * Create qualification for authenticated rio
     *
     * @param \App\Http\Requests\Rio\UpsertQualificationRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function createQualification(UpsertQualificationRequest $request)
    {
        // Get request data
        $requestData = $request->validated();

        // Get rio of authenticated user
        /** @var User */
        $user = auth()->user();
        $rio = $user->rio ?? null;

        // Guard clause for non-existing rio
        if (empty($rio)) {
            return response()->respondNotFound();
        }

        // Check if maximum qualifications are reached
        $expertCount = $rio->qualifications()->count();
        if ($expertCount >= 10) {
            return response()->respondForbidden();
        }

        // Create new qualification for rio user
        $rioExpert = new RioExpert();
        $rioExpert->fill($requestData);
        $rioExpert->rio_id = $user->rio_id;
        $rioExpert->attribute_code = AttributeCodes::QUALIFICATIONS;
        $rioExpert->sort = 0;
        $rioExpert->save();

        return response()->respondSuccess();
    }

    /**
     * Update qualification for authenticated rio
     *
     * @param \App\Http\Requests\Rio\UpsertQualificationRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateQualification(UpsertQualificationRequest $request, RioExpert $qualification)
    {
        $this->authorize('update', [RioExpert::class, $qualification]);

        // Get request data
        $requestData = $request->validated();

        // Update qualification
        $qualification->update($requestData);

        return response()->respondSuccess();
    }

    /**
     * Delete qualification of authenticated rio
     *
     * @param \App\Models\RioExpert $qualification
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteQualification(RioExpert $qualification)
    {
        $this->authorize('delete', [RioExpert::class, $qualification]);

        // Delete rio expert qualification
        $qualification->delete();

        return response()->respondSuccess();
    }

    /**
     * Fetch skill for authenticated rio
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getSkills(Request $request)
    {
        // Get rio of authenticated user
        /** @var User */
        $user = auth()->user();
        $rio = $user->rio ?? null;

        // Guard clause for non-existing rio
        if (empty($rio)) {
            return response()->respondNotFound();
        }

        // Get professions as json resource
        $results = SkillResource::collection($rio->skills);

        return response()->respondSuccess($results);
    }

    /**
     * Create skill for authenticated rio
     *
     * @param \App\Http\Requests\Rio\UpsertSkillRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function createSkill(UpsertSkillRequest $request)
    {
        // Get request data
        $requestData = $request->validated();

        // Get rio of authenticated user
        /** @var User */
        $user = auth()->user();
        $rio = $user->rio ?? null;

        // Guard clause for non-existing rio
        if (empty($rio)) {
            return response()->respondNotFound();
        }

        // Check if maximum skills are reached
        $expertCount = $rio->skills()->count();
        if ($expertCount >= 10) {
            return response()->respondForbidden();
        }

        // Create new skill for rio user
        $rioExpert = new RioExpert();
        $rioExpert->fill($requestData);
        $rioExpert->rio_id = $user->rio_id;
        $rioExpert->attribute_code = AttributeCodes::SKILLS;
        $rioExpert->sort = 0;
        $rioExpert->save();

        return response()->respondSuccess();
    }

    /**
     * Update skill for authenticated rio
     *
     * @param \App\Http\Requests\Rio\UpsertSkillRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateSkill(UpsertSkillRequest $request, RioExpert $skill)
    {
        $this->authorize('update', [RioExpert::class, $skill]);

        // Get request data
        $requestData = $request->validated();

        // Update skill
        $skill->update($requestData);

        return response()->respondSuccess();
    }

    /**
     * Delete skill of authenticated rio
     *
     * @param \App\Models\RioExpert $skill
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteSkill(RioExpert $skill)
    {
        $this->authorize('delete', [RioExpert::class, $skill]);

        // Delete rio expert skill
        $skill->delete();

        return response()->respondSuccess();
    }

    /**
     * Fetch educational backgrounds for authenticated rio
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getEducationalBackgrounds(Request $request)
    {
        // Get rio of authenticated user
        /** @var User */
        $user = auth()->user();
        $rio = $user->rio;

        // Guard clause for non-existing rio
        if (empty($rio)) {
            return response()->respondNotFound();
        }

        // Get educational backgrounds as json resource
        $results = EducationalBackgroundResource::collection($rio->educational_backgrounds);

        return response()->respondSuccess($results);
    }

    /**
     * Create educational backgrounds for authenticated rio
     *
     * @param \App\Http\Requests\Rio\UpsertEducationalBackgroundRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function createEducationalBackground(UpsertEducationalBackgroundRequest $request)
    {
        // Get request data
        $requestData = $request->validated();

        // Get rio of authenticated user
        /** @var User */
        $user = auth()->user();
        $rio = $user->rio ?? null;

        // Guard clause for non-existing rio
        if (empty($rio)) {
            return response()->respondNotFound();
        }

        // Check if maximum educational backgrounds are reached
        $expertCount = $rio->educational_backgrounds()->count();
        if ($expertCount >= 10) {
            return response()->respondForbidden();
        }

        // Create new educational backgrounds for rio user
        $rioExpert = new RioExpert();
        $rioExpert->rio_id = $user->rio_id;
        $rioExpert->content = $requestData['school_name'];
        $rioExpert->additional = $requestData['graduation_date'];
        $rioExpert->attribute_code = AttributeCodes::EDUCATIONAL_BACKGROUND;
        $rioExpert->sort = 0;
        $rioExpert->save();

        return response()->respondSuccess();
    }

    /**
     * Update educational background for authenticated rio
     *
     * @param \App\Http\Requests\Rio\UpsertEducationalBackgroundRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateEducationalBackground(UpsertEducationalBackgroundRequest $request, RioExpert $educationalBackground)
    {
        $this->authorize('update', [RioExpert::class, $educationalBackground]);

        // Get request data
        $requestData = $request->validated();

        // Update educational background
        $educationalBackground->update([
            'content' => $requestData['school_name'],
            'additional' => $requestData['graduation_date'],
        ]);

        return response()->respondSuccess();
    }

    /**
     * Delete educational background of authenticated rio
     *
     * @param \App\Models\RioExpert $educationalBackground
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteEducationalBackground(RioExpert $educationalBackground)
    {
        $this->authorize('delete', [RioExpert::class, $educationalBackground]);

        // Delete rio expert educational background
        $educationalBackground->delete();

        return response()->respondSuccess();
    }

    /**
     * Fetch awards for authenticated rio
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAwards(Request $request)
    {
        // Get rio of authenticated user
        /** @var User */
        $user = auth()->user();
        $rio = $user->rio ?? null;

        // Guard clause for non-existing rio
        if (empty($rio)) {
            return response()->respondNotFound();
        }

        // Get professions as json resource
        $results = AwardResource::collection($rio->awards);

        return response()->respondSuccess($results);
    }

    /**
     * Register new RIO profile award history.
     *
     * @param \App\Http\Requests\Rio\UpsertAwardHistoryRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function registerAward(UpsertAwardHistoryRequest $request)
    {
        // Get validated data
        $requestData = $request->validated();

        // Get rio of authenticated user
        /** @var User */
        $user = auth()->user();
        $rio = $user->rio ?? null;

        // Guard clause for non-existing rio
        if (empty($rio)) {
            return response()->respondNotFound();
        }

        // Check if maximum awards are reached
        $expertCount = $rio->awards()->count();
        if ($expertCount >= 10) {
            return response()->respondForbidden();
        }

        // Save award histories
        $rioExpert = new RioExpert();
        $rioExpert->rio_id = $user->rio_id;
        $rioExpert->content = $requestData['content'];
        $rioExpert->additional = $requestData['award_year'];
        $rioExpert->attribute_code = AttributeCodes::AWARDS;
        $rioExpert->sort = 0;
        $rioExpert->save();

        return response()->respondSuccess();
    }

    /**
     * Update award history for authenticated rio
     *
     * @param \App\Http\Requests\Rio\UpsertAwardHistoryRequest $request
     * @param \App\Models\RioExpert $award
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateAward(UpsertAwardHistoryRequest $request, RioExpert $award)
    {
        $this->authorize('update', [RioExpert::class, $award]);

        // Get request data
        $requestData = $request->validated();

        // Get rio of authenticated user
        /** @var User */
        $user = auth()->user();
        $rio = $user->rio ?? null;

        // Guard clause for non-existing rio
        if (empty($rio)) {
            return response()->respondNotFound();
        }

        // Update award history
        $award->update([
            'content' => $requestData['content'],
            'additional' => $requestData['award_year'],
        ]);

        return response()->respondSuccess();
    }

    /**
     * Delete a RIO profile award history.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\RioExpert $award
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteAward(Request $request, RioExpert $award)
    {
        $this->authorize('delete', [RioExpert::class, $award]);

        // Delete rio expert award
        $award->delete();

        return response()->respondSuccess();
    }

    /**
     * Update RIO Profile Home Address information.
     *
     * @param \App\Http\Requests\Rio\UpdateHomeAddressRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateHomeAddress(UpdateHomeAddressRequest $request)
    {
        /** @var User */
        $user = auth()->user();

        /** @var RioProfile */
        $rioProfile = $user->rio->rio_profile;

        // Request data
        $data = $request->validated();

        // If prefecture is outside Japan, set nationality to nulll
        if ((int) $data['home_address_prefecture'] !== PrefectureTypes::OTHER) {
            $data['home_address_nationality'] = null;
        }

        // Update RIO profile present address
        $rioProfile->update($data);

        return response()->respondSuccess($data);
    }

    /**
     * Fetch products for authenticated rio
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getProducts(Request $request)
    {
        // Get rio of authenticated user
        /** @var User */
        $user = auth()->user();
        $rio = $user->rio ?? null;

        // Guard clause for non-existing rio
        if (empty($rio)) {
            return response()->respondNotFound();
        }

        // Get products as json resource
        $results = ProductResource::collection($rio->products);

        return response()->respondSuccess($results);
    }

    /**
     * Create product for authenticated rio
     *
     * @param \App\Http\Requests\Rio\UpsertProductRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function createProduct(UpsertProductRequest $request)
    {
        // Get request data
        $requestData = $request->validated();

        // Get rio of authenticated user
        /** @var User */
        $user = auth()->user();
        $rio = $user->rio ?? null;

        // Guard clause for non-existing rio
        if (empty($rio)) {
            return response()->respondNotFound();
        }

        // Check if maximum products are reached
        $expertCount = $rio->products()->count();
        if ($expertCount >= config('bphero.product_limit')) {
            return response()->respondForbidden();
        }

        // Encode image_link and reference_url to information
        $information['image_link'] = null;
        $information['reference_url'] = !empty($requestData['reference_url']) ? $requestData['reference_url'] : null;
        $requestData['information'] = json_encode($information, JSON_UNESCAPED_SLASHES);

        // Create new product for rio user
        $rioExpert = new RioExpert();
        $rioExpert->fill($requestData);
        $rioExpert->rio_id = $user->rio_id;
        $rioExpert->attribute_code = AttributeCodes::PRODUCT_SERVICE_INFORMATION;
        $rioExpert->sort = 0;
        $rioExpert->save();

        return response()->respondSuccess();
    }

    /**
     * Update product for authenticated rio
     *
     * @param \App\Http\Requests\Rio\UpsertProductRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateProduct(UpsertProductRequest $request, RioExpert $product)
    {
        $this->authorize('update', [RioExpert::class, $product]);

        // Get request data
        $requestData = $request->validated();

        // Encode image_link and reference_url to information
        $information['image_link'] = null;
        $information['reference_url'] = !empty($requestData['reference_url']) ? $requestData['reference_url'] : null;
        $requestData['information'] = json_encode($information, JSON_UNESCAPED_SLASHES);

        // Update profession
        $product->update($requestData);

        return response()->respondSuccess();
    }

    /**
     * Delete product of authenticated rio
     *
     * @param \App\Models\RioExpert $product
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteProduct(RioExpert $product)
    {
        $this->authorize('delete', [RioExpert::class, $product]);

        // Delete rio expert product
        $product->delete();

        return response()->respondSuccess();
    }

    /**
     * Fetch affiliates for authenticated rio
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAffiliates(Request $request)
    {
        // Get rio of authenticated user
        /** @var User */
        $user = auth()->user();
        $rio = $user->rio ?? null;

        // Guard clause for non-existing rio
        if (empty($rio)) {
            return response()->respondNotFound();
        }

        $affiliates = NeoBelong::affiliateList()->get();
        $results = AffiliateResource::collection($affiliates);

        return response()->respondSuccess($results);
    }

    /**
     * Update affiliates for authenticated rio
     *
     * @param \App\Http\Requests\Rio\UpdateAffiliateRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateAffiliate(UpdateAffiliateRequest $request, NeoBelong $affiliate)
    {
        $this->authorize('update', [NeoBelong::class, $affiliate]);

        // Get request data
        $requestData = $request->validated();

        // Update affiliate
        $affiliate->update($requestData);

        return response()->respondSuccess();
    }

    /**
     * Create session for selected RIO service
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function createRIOServiceSession(Request $request)
    {
        /** @var User */
        $user = auth()->user();

        //Get request id
        $id = $request->get('id');

        //Get rio of authenticated user
        $rio =  $user->rio;

        if ($rio->id != $id) {
            return response()->respondNotFound();
        }

        $data = ([
            'name' => $rio->family_name. ' ' .$rio->first_name,
            'link' => route('rio.profile.introduction'),
            'profile_image' => $rio->rio_profile->profile_image ?? asset('images/profile/user_default.png'),
            'profile_edit_link' => route('rio.profile.edit'),
            'privacy_edit_link' => route('rio.privacy.edit'),
        ]);

        // Set service selected
        ServiceSelected::setSelected($rio);

        return response()->respondSuccess($data);
    }

    /**
     * Create log for accessed RIO Page
     *
     * @param int $logType type of log in rio logs
     * @param int|null $rio_id rio profile id
     * @param string|false $logDetail log detail string
     * @return void|null
     */
    private function createRIOviewlog($logType, $rio_id = null, $logDetail = '')
    {
        $rioLog = new RioLog();
        /** @var int $rio_id */
        $rioLog->rio_id = $rio_id;
        $rioLog->logged_at = Carbon::now();
        $rioLog->log_type = $logType;
        /** @var string $logDetail */
        $rioLog->log_detail = $logDetail;
        $rioLog->save();
    }

    /**
    * Rio profile manage invitation page
    *
    * @return \Illuminate\View\View
    */
    public function invitationList()
    {
        $invitationLists = NeoConnection::allInvitesForNeoBelong()
            ->paginate(config('bphero.paginate_count'));

        return view('rio.profile.manage-pending-invitation', compact('invitationLists'));
    }

    /** Accept pending invitation
    *
    * @param \App\Models\NeoBelong $neoBelong
    * @return \Illuminate\Http\JsonResponse
    *
    */
    public function acceptInvitation(NeoBelong $neoBelong)
    {
        $neoBelong->update([
            'status' => NeoBelongStatuses::AFFILIATED
        ]);

        return response()->respondSuccess();
    }

    /** Decline pending invitation
    *
    * @param \App\Models\NeoBelong $neoBelong
    * @return \Illuminate\Http\JsonResponse
    *
    */
    public function declineInvitation(NeoBelong $neoBelong)
    {
        //Update status
        $neoBelong->delete();

        return response()->respondSuccess();
    }

    /** Update pending invitation lists
    *
    * @param \Illuminate\Http\Request $request
    * @param \App\Models\Neo $neo
    * @return array|string
    */
    public function updatePendingInvitation(Request $request, Neo $neo)
    {
        $pageNo = $request['pageNo'];

        $invitationLists = NeoConnection::allInvitesForNeoBelong()
            ->paginate(config('bphero.paginate_count'), ['*'], 'page', $pageNo);

        return response()->respondSuccess($invitationLists);
    }

    /** Get pending invitation list
    *
    * @return array|string
    */
    public function getInviteLists(Request $request)
    {
        $firstPage = 1;
        $pageNo = $request['page'] ?? $firstPage;

        //Get Last page
        $getLastPage = NeoConnection::allInvitesForNeoBelong()
           ->paginate(config('bphero.paginate_count'))->lastPage();

        if ($pageNo !== $firstPage) {
            //Go to prev page if its the last RIO
            $invitationLists= NeoConnection::allInvitesForNeoBelong()
                ->paginate(config('bphero.paginate_count'), ['*'], 'page', $getLastPage);
        } else {
            //Go to first page by default
            $invitationLists= NeoConnection::allInvitesForNeoBelong()
                ->paginate(config('bphero.paginate_count'));
        }

        return response()->respondSuccess($invitationLists);
    }
}
