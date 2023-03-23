<?php

namespace App\Http\Controllers\Rio;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\Rio\UpdatePasswordRequest;
use App\Http\Requests\Rio\AccountInformation\UpdateEmailRequest;
use App\Http\Requests\Rio\AccountInformation\UpdateSecretQuestionRequest;
use App\Enums\Rio\SecretQuestionType;
use App\Models\User;
use App\Models\UserVerification;
use Illuminate\Support\Facades\DB;

class AccountInformationController extends Controller
{
    /**
     * Rio information edit page
     *
     * @return \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
     */
    public function edit()
    {
        /** @var User */
        $user = auth()->user();
        $rio = $user->rio;

        // Guard clause for non-existing rio
        if (empty($rio)) {
            return response()->respondNotFound();
        }

        $secretQuestions = SecretQuestionType::getSecretQuestions();

        return view('rio.information.edit', compact('user', 'rio', 'secretQuestions'));
    }

    /**
     * User password confirmation modal handler.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function confirmPassword(Request $request)
    {
        // Request data
        $data = [];

        return response()->respondSuccess($data);
    }

    /**
     * User password confirmation modal handler.
     *
     * @param UpdateSecretQuestionRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateSecretQuestion(UpdateSecretQuestionRequest $request)
    {
        /** @var User */
        $user = auth()->user();

        // Check user if able to update the secret question
        $this->authorize('update', [User::class, $user]);

        // Request validated get specific data
        /** @phpstan-ignore-next-line */
        $data = $request->safe()->only(['secret_question', 'secret_answer']);

        User::whereId($user->id)->update($data);

        return response()->respondSuccess($data);
    }

    /**
     * Update logged-in user password.
     *
     * @param \App\Http\Requests\Rio\UpdatePasswordRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updatePassword(UpdatePasswordRequest $request)
    {
        /** @var User */
        $user = auth()->user();

        // Request data
        $data = $request->validated();

        // Update authenticated user
        $user->update([
            'password' => Hash::make($data['new_password'])
        ]);

        // Send change password notification
        $user->sendChangePasswordNotification($user);

        return response()->respondSuccess();
    }

    /**
     * Update email address of authenticated user
     *
     * @param \App\Http\Requests\Rio\AccountInformation\UpdateEmailRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateEmail(UpdateEmailRequest $request)
    {
        // Get request data
        $requestData = $request->validated();

        DB::beginTransaction();

        try {
            /** @var User */
            $user = auth()->user();

            // Create verification record
            $verification = UserVerification::createEmailVerify($requestData['email']);

            // Send reset email verification mail
            $user->sendResetEmailVerificationNotification($verification);

            DB::commit();
        } catch (\Throwable $th) {
            report($th);
            DB::rollBack();

            return response()->respondInternalServerError();
        }

        return response()->respondSuccess();
    }
}
