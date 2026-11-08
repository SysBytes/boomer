<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (!auth()->user()->hasAnyRole(['admin', 'developer'])) {
                abort(403, 'No autorizado.');
            }
            return $next($request);
        });
    }

    private function getRoles()
    {
        $query = Role::query();
        if (auth()->check() && !auth()->user()->hasRole('developer')) {
            $query->where('slug', '!=', 'developer');
        }
        return $query->get();
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (auth()->check() && auth()->user()->hasRole('developer')) {
            $users = User::with('roles')->get();
        } else {
            $users = User::whereDoesntHave('roles', function ($q) {
                $q->where('slug', 'developer');
            })->with('roles')->get();
        }
        $roles = $this->getRoles();
        return view('backoffice.user.index', compact('users', 'roles'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $roles = $this->getRoles();
        $countries = \App\Models\Country::where('active', 1)->get();
        return view('backoffice.user.create', compact('roles', 'countries'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email|max:255',
            'phone' => 'nullable|string|max:20',
            'password' => 'required|string|min:8|confirmed',
            'roles' => 'required|array',
            'roles.*' => 'exists:roles,id',
            'countries' => 'required|array',
            'countries.*' => 'exists:countries,id',
        ]);

        $data = $request->all();
        $data['password'] = Hash::make($data['password']);
        $data['active'] = $request->has('active') ? 1 : 0;
        $data['name'] = ucwords(strtolower($data['name']));

        // Phone formatting logic
        if (!empty($data['phone'])) {
            $cleanPhone = preg_replace('/[^0-9]/', '', $data['phone']);
            
            // Get user's primary country code
            $countryCode = '57'; // Default Colombia
            if (!empty($request->countries)) {
                $firstCountry = \App\Models\Country::find($request->countries[0]);
                if ($firstCountry) {
                    $countryCode = preg_replace('/[^0-9]/', '', $firstCountry->phone_code);
                }
            }

            // Strip the country code if the user accidentally included it (only if length > 10)
            if (str_starts_with($cleanPhone, $countryCode) && strlen($cleanPhone) > 10) {
                $possibleLocal = substr($cleanPhone, strlen($countryCode));
                if (strlen($possibleLocal) >= 7) {
                    $cleanPhone = $possibleLocal;
                }
            }
            
            $data['phone'] = ltrim($cleanPhone, '0');
        }

        // Legacy country string mapping
        if (count($request->countries) > 1) {
            $data['country'] = 'Global';
        } else {
            $data['country'] = $request->countries[0] == 1 ? 'Colombia' : 'Argentina';
        }

        $user = User::create($data);
        $user->roles()->sync($request->roles);
        $user->countries()->sync($request->countries);

        return redirect()->route('user.index')->with('success', 'Usuario creado correctamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        $roles = $this->getRoles();
        $countries = \App\Models\Country::where('active', 1)->get();
        return view('backoffice.user.edit', compact('user', 'roles', 'countries'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'phone' => 'nullable|string|max:20',
            'password' => 'nullable|string|min:8|confirmed',
            'roles' => 'required|array',
            'roles.*' => 'exists:roles,id',
            'countries' => 'required|array',
            'countries.*' => 'exists:countries,id',
        ]);

        $data = $request->all();

        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $data['active'] = $request->has('active') ? 1 : 0;
        $data['name'] = ucwords(strtolower($data['name']));

        // Phone formatting logic
        if (!empty($data['phone'])) {
            $cleanPhone = preg_replace('/[^0-9]/', '', $data['phone']);
            
            // Get user's primary country code
            $countryCode = '57'; // Default Colombia
            if (!empty($request->countries)) {
                $firstCountry = \App\Models\Country::find($request->countries[0]);
                if ($firstCountry) {
                    $countryCode = preg_replace('/[^0-9]/', '', $firstCountry->phone_code);
                }
            }

            // Strip the country code if the user accidentally included it (only if length > 10)
            if (str_starts_with($cleanPhone, $countryCode) && strlen($cleanPhone) > 10) {
                $possibleLocal = substr($cleanPhone, strlen($countryCode));
                if (strlen($possibleLocal) >= 7) {
                    $cleanPhone = $possibleLocal;
                }
            }
            
            $data['phone'] = ltrim($cleanPhone, '0');
        }

        // Legacy country string mapping
        if (count($request->countries) > 1) {
            $data['country'] = 'Global';
        } else {
            $data['country'] = $request->countries[0] == 1 ? 'Colombia' : 'Argentina';
        }

        $user->update($data);
        $user->roles()->sync($request->roles);
        $user->countries()->sync($request->countries);

        return redirect()->route('user.index')->with('success', 'Usuario actualizado correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('user.index')->with('success', 'Usuario eliminado correctamente.');
    }
}
