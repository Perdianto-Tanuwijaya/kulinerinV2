<?php

namespace App\Http\Controllers;

use App\Models\MenuRestaurant;
use App\Models\Restaurant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class MenuRestaurantController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $restaurant = Restaurant::where('user_id', Auth::user()->id)->first();
        // $menu = MenuRestaurant::where('restaurant_id', $restaurant->id)->orderByRaw('created_at' || 'updated_at', 'desc')->get();
        $menu = MenuRestaurant::where('restaurant_id', $restaurant->id)->latest('updated_at')->latest('created_at')->get();


        return view('restaurant.menu.index', compact('menu'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $request->merge([
                'menuPrice' => str_replace(',', '', $request->menuPrice)
            ]);
            $restaurant = Restaurant::where('user_id', Auth::user()->id)->first();
            $validatedData = $request->validate([
                'menuName' => 'required|string|max:255',
                'category' => 'required|in:Appetizer,Main Course,Dessert,Beverages',
                'menuPrice' => 'required|numeric',
                'menuImage' => 'required|image|mimes:jpeg,png,jpg|max:5120',
                // 'isAvailable' => 'required|in:YES,NO',
                'description' => 'required|string',
            ],[
                'menuName.required' => 'Menu Name is Missing',
                'category.required' => 'Category is Missing',
                'menuPrice.required' => 'Menu Price is Missing',
                'menuImage.required' => 'Menu Image is Missing',
                'description.required' => 'Description is Missing',
            ]);

            $menu = new MenuRestaurant();
            $menu->restaurant_id = $restaurant->id;
            $menu->menuName = $request->menuName;
            $menu->category = $request->category;
            // $menu->menuPrice = $request->menuPrice;
            $menu->menuPrice = $validatedData['menuPrice'];
            $menu->menuImage = $request->menuImage;
            $menu->description = $request->description;
            // $menu->isAvailable = bcrypt($request->password);
            if ($request->hasFile('menuImage')) {
                if ($menu->menuImage) {
                    Storage::disk('public')->delete($menu->menuImage);
                }
                $imagePath = $request->file('menuImage')->store('post-menuImage', 'public');
                $menu->menuImage = $imagePath;
            }
            $menu->save();

            return response()->json([
                'message' => 'Menu added successfully!',
                'menu' => $menu
            ], 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Validation Error',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Something went wrong',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        $menuItems = MenuRestaurant::where('restaurant_id', $id)
        ->orderBy('category')
        ->get()
        ->groupBy('category');
        $restaurantId = $id;
        $restaurants = Restaurant::find($id);
        $guestInfo = $request->guestInfo;
        $areaInfo = $request->areaInfo;
        $reservationDate = $request->reservationDate;
        $reservationTime = $request->reservationTime;
        $restaurantName = $request->restaurantName;
        $restaurantCity = $request->restaurantCity;
        return view('menu.index', compact('menuItems','restaurants', 'guestInfo', 'areaInfo', 'reservationDate', 'reservationTime', 'restaurantName', 'restaurantCity', 'restaurantId'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $menu = MenuRestaurant::findOrFail($id);
        return response()->json($menu);
        // return view('restaurant.menu.updateMenu', compact('menu'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request->merge([
            'menuPrice' => str_replace(',', '', $request->menuPrice)
        ]);

        $validatedData = $request->validate([
            'menuName' => 'required|string|max:255',
            'category' => 'required|string|max:255',
            'menuPrice' => 'required|numeric',
            'menuImage' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
            'isAVailable' => 'required|in:YES,NO',
            'description' => 'required|string',
        ],[
            'menuName.required' => 'Menu Name is Missing',
            'category.required' => 'Category is Missing',
            'menuPrice.required' => 'Menu Price is Missing',
            'menuImage.required' => 'Menu Image is Missing',
            'description.required' => 'Description is Missing',
        ]);

        // Find the menu item
        $menu = MenuRestaurant::findOrFail($id);
        $restaurant = Restaurant::where('user_id', Auth::user()->id)->first();

        // Update menu item with validated data
        $menu->restaurant_id = $restaurant->id;
        $menu->menuName = $request->menuName;
        // $menu->menuImage = $request->menuImage;
        $menu->category = $request->category;
        $menu->menuPrice = $validatedData['menuPrice'];;
        $menu->isAVailable = $request->isAVailable;
        $menu->description = $request->description;
        if ($request->hasFile('menuImage')) {
            if ($menu->menuImage) {
                Storage::disk('public')->delete($menu->menuImage);
            }
            $imagePath = $request->file('menuImage')->store('post-menuImage', 'public');
            $menu->menuImage = $imagePath;
        }
        $menu->save();

        // Redirect to the menu index with success message
        return response()->json(['success' => 'Menu updated successfully!']);
        // return redirect()->route('menu.index')->with('success', 'Menu updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $menu = MenuRestaurant::findOrFail($id);
        $menu->delete();
        return redirect()->route('menu.index');
        // return redirect()->route('menu.index')->with('success', 'Menu deleted successfully');
    }
}
