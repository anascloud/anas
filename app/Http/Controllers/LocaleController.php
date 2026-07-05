<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class LocaleController extends Controller
{
    public function update(Request $request): RedirectResponse
    {
        $request->validate([
            'locale' => ['required', 'in:en,he'],
        ]);

        return back()->withCookie(
            cookie('locale', $request->input('locale'), 60 * 24 * 365)
        );
    }
}
