<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Restaurant;
use App\Models\OperationalHour;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Redirect;
use Illuminate\Support\Facades\Log;
use Flasher\Toastr\Prime\ToastrInterface;
use Laravel\Socialite\Facades\Socialite;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\Request;
// use App\Models\Restaurant;

class AuthController extends Controller
{
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }


    public function handleGoogleCallback()
    {
        $googleUser = Socialite::driver('google')->stateless()->user();

        // dd($googleUser);
        if (!$googleUser || !$googleUser->email) {
            return redirect()->route('login')->withErrors('Failed to retrieve Google account information.');
        }

        $user = User::where('email', $googleUser->email)->first();

        if (!$user) {
            // Get first and last name from Google user
            $nameParts = explode(' ', $googleUser->name);
            $firstName = $nameParts[0] ?? '';
            $lastName = isset($nameParts[1]) ? implode(' ', array_slice($nameParts, 1)) : '';
            $username = $firstName . ' ' . $lastName;
            // dd($googleUser->name);
            $user = User::create([
                'email' => $googleUser->email,
                'username' => $googleUser->name,
                'role' => 1,
                'password' => Hash::make(rand(100000, 999999)),
            ]);
            // dd($user);
            $user->save();
            // $user = new User();
            // $user->email = $googleUser->email;
            // $user->username = $googleUser->$username;
            // $user->password = Hash::make(rand(100000, 999999));
            // $user->role = 1;
            // $user->save();
        }

        Auth::login($user);

        return redirect()->route('customerDashboard')->withSuccess('Login Success');
    }

    public function showRegisterForm()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'email' => 'required|email|unique:users',
            'username' => 'unique:users',
            'password' => 'required|alpha_num|min:8|required_with:confirmation_password|same:confirmation_password',
            'confirmation_password' => 'required',
        ]);

        $role = 1;

        if ($request->register_as_restaurant == "1") {
            $role = 2;
        }

        $user = new User();
        $user->email = $request->email;
        $user->username = $request->username;
        $user->password = bcrypt($request->password);
        $user->role = $role;
        $user->save();

        return redirect('/login')->with('success', 'Your account has been created!');
    }

    //Restaurant by VEPEHA

    // public function restaurantCreation(Request $request)
    // {
    //     $request->validate([

    //         'name' => 'required',

    //     ]);
    //     dd($request->name);
    // }

    public function restaurantCreation(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'number' => 'required',
            'city' => 'required',
            'address' => 'required',
            'style' => 'required|string|in:Asian,Western,Fine Dining,Bar',
            'desc' => 'required',
            'image' => 'required|array|min:3', // Ensure at least 3 images
            'image.*' => 'image|mimes:jpeg,png,jpg,gif,webp|max:2048', // Image validation
            'days' => 'required|array|min:1', // Ensure at least one schedule is selected
            'open_time' => 'required|array',
            'close_time' => 'required|array',
        ]);

        $user = auth()->user();

        // Prepare restaurant name for filename (replace spaces with dashes)
        $restaurantNameSlug = str_replace(' ', '-', strtolower($request->name));
        $imagePaths = [];

        foreach ($request->file('image') as $index => $image) {
            $extension = $image->getClientOriginalExtension();
            $filename = "{$user->id}-{$restaurantNameSlug}" . ($index === 0 ? '' : $index) . ".{$extension}";

            try {
                $image->move(public_path('storage/restaurant'), $filename); // Move to public/images/restaurant
                Log::info("Uploaded: " . public_path("storage/restaurant/{$filename}"));
                $imagePaths[] = "restaurant/{$filename}"; // Save relative path
            } catch (\Exception $e) {
                Log::error("Upload failed: " . $e->getMessage());
            }
        }

        // Create restaurant account linked to the user
        $restaurant = Restaurant::create([
            'user_id' => $user->id,
            'restaurantName' => $request->name,
            'restaurantPhoneNumber' => $request->number,
            'restaurantCity' => $request->city,
            'restaurantAddress' => $request->address,
            'restaurantDescription' => $request->desc,
            'restaurantStyle' => $request->style,
            'restaurantImage' => implode(', ', $imagePaths), // Store as comma-separated paths
        ]);

        // ✅ Save Operational Hours
        if ($restaurant) {
            foreach ($request->days as $index => $day) {
                OperationalHour::create([
                    'restaurant_id' => $restaurant->id,
                    'day' => $day,
                    'open_time' => $request->open_time[$index],
                    'close_time' => $request->close_time[$index],
                ]);
            }
        }

        return redirect('/')->with('success', 'Your restaurant account has been created!');
    }


    public function settings()
    {
        // Get the authenticated user
        $user = auth()->user();

        // Fetch the restaurant data linked to the user
        $restaurant = Restaurant::where('user_id', $user->id)->first();

        // Pass it to the view
        return view('dashboard.settings', compact('restaurant'));
    }

    //////////////////////////

    public function showLoginForm()
    {
        return view('auth.loginForm');
    }

    public function login(Request $request)
    {
        // Validate the login credentials
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        // Find the user by email
        $user = User::where('email', $credentials['email'])->first();
        // compact('user');
        // Check if the user exists and if the password matches
        if ($user && Hash::check($credentials['password'], $user->password)) {
            // Log the user in
            Auth::login($user);
            if ($user->role == 1) {
                return redirect()->route('customerDashboard')->withSuccess('Login Success');
                // ->withSuccess('Login Success');
            } else if ($user->role == 3) {
                // toastr()->success('Login Success');
                // return view('dashboard.adminDashboard')->withSuccess('Login Success');
                return redirect()->route('adminDashboard')->withSuccess('Login Success');
            } else if ($user->role == 2) {
                // Check if a restaurant exists for this user
                $restaurant = \App\Models\Restaurant::where('user_id', $user->id)->first();

                if ($restaurant) {
                    return redirect()->route('restaurantDashboard')->withSuccess('Login Success');
                } else {
                    return redirect()->route('noRestaurantPage')->withSuccess('Please register your restaurant first.');
                }
            }
        } else {
            // If authentication fails, redirect back with an error message
            return back()->withErrors([
                'email' => 'Login Failed Credential Not Match.',
            ])->onlyInput('email');
        }
    }
    public function adminDashboard()
    {
        return view('admin.home.index');
    }
    public function restaurantDashboard()
    {
        $restaurant = Restaurant::where('user_id', Auth::user()->id)->first();
        return view('restaurant.home.index', compact('restaurant'));
    }

    public function showRegisterRestaurantForm()
    {
        return view('auth.registerRestaurant');
    }
    public function registerestaurant(Request $request)
    {
        $request->validate([
            'email' => 'required|email|unique:users',
            'restaurantName' => 'required',
            'password' => 'required|alpha_num|min:8|required_with:confirmation_password|same:confirmation_password',
            'confirmation_password' => 'required',
        ]);

        $restaurant = new User();
        $restaurant->email = $request->email;
        // $restaurant->restaurantName = $request->restaurantName;
        $restaurant->password = bcrypt($request->password);
        $restaurant->role = 2;
        $restaurant->save();

        return redirect('/login')->with('success', 'Your account has been created successfully!');
    }



    public function logout(Request $request)
    {
        Auth::logout();  // Log the user out
        $request->session()->invalidate();  // Invalidate the session
        $request->session()->regenerateToken();  // Regenerate CSRF token
        return redirect('/')->with('success', 'Logout Successfull!');
    }
}
