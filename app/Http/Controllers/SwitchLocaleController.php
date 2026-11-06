<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Support\Locale;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class SwitchLocaleController extends Controller
{
    /**
     * Switch the interface language and go back to where the visitor came from.
     *
     * Guests keep the choice in their session; signed-in users also get it saved
     * to their profile so it follows them to other devices.
     */
    public function __invoke(Request $request, string $locale): RedirectResponse
    {
        abort_unless(Locale::isSupported($locale), 404);

        $request->session()->put(Locale::SESSION_KEY, $locale);

        $user = $request->user();

        if ($user instanceof User && $user->locale !== $locale) {
            $user->forceFill(['locale' => $locale])->save();
        }

        return redirect()->to(url()->previous(route('home')));
    }
}
