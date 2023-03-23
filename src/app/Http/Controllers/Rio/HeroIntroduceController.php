<?php

namespace App\Http\Controllers\Rio;

use App\Http\Controllers\Controller;
use App\Http\Requests\Rio\UpdateMessageTemplateRequest;
use App\Models\User;

class HeroIntroduceController extends Controller
{
    /**
     * Hero Introduce - Introduce page
     *
     * @return \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
     */
    public function index()
    {
        /** @var User */
        $user = auth()->user();
        $rio = $user->rio;

        return view('rio.introduce.index', compact(
            'rio'
        ));
    }

    /**
     * Hero Introduce - Update Message template
     *
     * @param \App\Http\Requests\Rio\UpdateMessageTemplateRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(UpdateMessageTemplateRequest $request)
    {
        /** @var User */
        $user = auth()->user();
        $rio = $user->rio;

        $data = $request->validated();

        $rio->update($data);

        return redirect()->back()
            ->withAlertBox('success', __('Updated Introductory text template'));
    }
}
