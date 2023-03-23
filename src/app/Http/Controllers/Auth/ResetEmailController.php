<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ResetEmailVerifyRequest;
use App\Models\User;
use App\Models\UserVerification;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ResetEmailController extends Controller
{
    /**
     * Reset Email Verification
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function verify(ResetEmailVerifyRequest $request)
    {
        // Get request data
        $requestData = $request->validated();

        // Begin database transaction
        DB::beginTransaction();

        try {
            // Get user to be updated
            /** @var User */
            $user = User::findOrFail($requestData['user']);

            // Get verification record
            /** @var UserVerification */
            $verification = UserVerification::active()
                ->whereToken($requestData['token'])
                ->findOrFail($requestData['verification']);

            // Update user email with verification email
            $user->update(['email' => $verification->email]);

            // Delete verification records with similar email
            UserVerification::deleteUserVerificationByEmail($verification->email);

            // Commit database changes
            DB::commit();
        } catch (ModelNotFoundException $exception) {
            // Display invalid page for non-existing or expired verification
            return view('auth.reset_email.invalid');
        } catch (\Throwable $th) {
            report($th);
            DB::rollBack();
            abort(500);
        }

        return redirect()->route('email.reset.complete')->with('reset-email.verified', true);
    }

    /**
     * Display verification completed page
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function complete(Request $request)
    {
        $previouslyVerified = session('reset-email.verified', false);

        // Check if previously verified
        if (!$previouslyVerified) {
            return redirect()->route('login.get');
        }

        // Invalidate authentication
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return view('auth.reset_email.complete');
    }
}
