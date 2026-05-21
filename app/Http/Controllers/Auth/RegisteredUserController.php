<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Region;
use App\Models\Province;
use App\Models\CityMunicipality;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register', [
            'regions' => Region::orderBy('name')->get(),
            'provinces' => Province::orderBy('name')->get(),
            'cities' => CityMunicipality::orderBy('name')->get(),
        ]);
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'middle_initial' => ['nullable', 'string', 'max:3'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'office_type' => ['required', 'string', 'in:Regional,Provincial,City/Municipality'],
            'office_id' => ['required', 'integer'],
        ]);

        $fullName = trim($request->first_name . ' ' . ($request->middle_initial ? $request->middle_initial . '. ' : '') . $request->last_name);

        $officeName = null;
        if ($request->office_type === 'Regional') {
            $officeName = Region::find($request->office_id)?->name;
        } elseif ($request->office_type === 'Provincial') {
            $officeName = Province::find($request->office_id)?->name;
        } elseif ($request->office_type === 'City/Municipality') {
            $officeName = CityMunicipality::find($request->office_id)?->name;
        }

        $user = User::create([
            'name' => $fullName,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'office_type' => $request->office_type,
            'office_name' => $officeName,
            'office_id' => $request->office_id,
        ]);

        event(new Registered($user));

        Auth::login($user);

        $redirectTo = $user->isAdmin() ? route('dashboard', absolute: false) : route('notebooks.index', absolute: false);
        return redirect($redirectTo);
    }
}
