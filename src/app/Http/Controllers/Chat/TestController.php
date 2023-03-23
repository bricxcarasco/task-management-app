<?php

namespace App\Http\Controllers\Chat;

use App\Http\Controllers\Controller;

class TestController extends Controller
{
    /**
     * Test Message Room
     *
     * @return \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
     */
    public function index()
    {
        return view('chat.test.index');
    }
}
