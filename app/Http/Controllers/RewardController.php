<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;

use Illuminate\Http\Request;
use App\Models\PointLoyalty;
use App\Models\Redemption;
use App\Models\Reward;
// use App\Models\User;
use Illuminate\Support\Facades\Storage;

class RewardController extends Controller
{
    // public function show(Request $request)
    // {
    //     // dd($request);
    //     $category = $request->query('category', 'all');

    //     $userPoints = PointLoyalty::where('user_id', Auth::user()->id)->first();

    //     $categories = Reward::where('is_active', true)
    //         ->where('stock', '>', 0)
    //         ->pluck('category')
    //         ->unique()
    //         ->toArray();

    //     // Tambahkan opsi "All" ke dalam daftar kategori
    //     array_unshift($categories, 'all');

    //     $rewards = Reward::query()
    //         ->where('is_active', true)
    //         ->where('stock', '>', 0)
    //         ->when($category !== 'all', function ($query) use ($category) {
    //             return $query->where('category', $category);
    //         })
    //         ->get();

    //     $user = User::where('id', Auth::id())->first();

    //     // Cek apakah user sudah login
    //     if (!$user) {
    //         return redirect('/login')->with('error', 'You need to login first.');
    //     }

    //     // Ambil semua riwayat Redeem berdasarkan user_id, termasuk relasi reward
    //     $redeems = Redemption::with('reward')
    //         ->where('user_id', $user->id)
    //         ->orderBy('created_at', 'desc')
    //         ->paginate(5);

    //     return view('reward.index', compact('category', 'categories', 'rewards', 'userPoints', 'redeems'));
    // }

    //CRUD DARI SINI
    public function index()
    {
        $rewards = Reward::orderBy('updated_at', 'desc')->get();
        return view('admin.reward.index', compact('rewards'));
    }

    public function edit($id)
    {
        $reward = Reward::findOrFail($id);
        // dd($reward);
        return response()->json($reward);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'category' => 'required|string|max:255',
            'stock' => 'required|integer|min:0',
            'points' => 'required|integer|min:0',
            'is_active' => 'required|boolean',
            'description' => 'nullable|string',
        ],[
            'name.required' => 'Reward Name is Missing',
            'image.required' => 'Image is Missing',
            'category.required' => 'Category is Missing',
            'stock.required' => 'Stock is Missing',
            'points.required' => 'Points is Missing',
            'is_active.required' => 'Is Active is Missing',
            'description.required' => 'Description is Missing',
        ]);

        // Cari reward berdasarkan ID
        $reward = Reward::findOrFail($id);

        // Update data reward
        $reward->name = $request->name;
        $reward->category = $request->category;
        $reward->stock = $request->stock;
        $reward->points = $request->points;
        $reward->is_active = $request->is_active;
        $reward->description = $request->description;
        // Jika ada gambar baru, hapus yang lama dan simpan yang baru
        if ($request->hasFile('image')) {
            // Hapus gambar lama jika ada
            if ($reward->image) {
                Storage::disk('public')->delete($reward->image);
            }

            // Simpan gambar baru
            $imagePath = $request->file('image')->store('post-rewardImage', 'public');
            $reward->image = $imagePath;
        }

        // Simpan perubahan
        $reward->save();

        return response()->json(['message' => 'Reward updated successfully']);
    }

    public function store(Request $request){
        $request->validate([
            'name' => 'required|string|max:255',
            'image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'category' => 'required|string|max:255',
            'stock' => 'required|integer|min:0',
            'points' => 'required|integer|min:0',
            'is_active' => 'required|boolean',
            'description' => 'nullable|string',
        ],[
            'name.required' => 'Reward Name is Missing',
            'image.required' => 'Image is Missing',
            'image.image' => 'The file must be an image',
            'image.mimes' => 'The image must be in JPG, JPEG, or PNG format',
            'image.max' => 'The image size must not exceed 2MB',
            'category.required' => 'Category is Missing',
            'stock.required' => 'Stock is Missing',
            'stock.integer' => 'Stock must be a valid number',
            'stock.min' => 'Stock must be at least 0',
            'points.required' => 'Points are Missing',
            'points.integer' => 'Points must be a valid number',
            'points.min' => 'Points must be at least 0',
            'is_active.required' => 'Is Active field is required',
            'is_active.boolean' => 'Is Active must be either YES (1) or NO (0)',
            'description.required' => 'Description is Missing',
        ]);

        // Simpan data ke database
        $reward = new Reward();
        $reward->name = $request->name;
        $reward->image = $request->image;
        $reward->category = $request->category;
        $reward->stock = $request->stock;
        $reward->points = $request->points;
        $reward->is_active = $request->is_active;
        $reward->description = $request->description;
        if ($request->hasFile('image')) {
            // Hapus gambar lama jika ada
            if ($reward->image) {
                Storage::disk('public')->delete($reward->image);
            }
            // Simpan gambar baru
            $imagePath = $request->file('image')->store('post-rewardImage', 'public');
            $reward->image = $imagePath;
        }
        $reward->save();

        // Kembalikan response JSON
        return response()->json([
            'message' => 'Reward added successfully!',
            'reward' => $reward
        ], 201);

    }
    public function destroy($id)
    {
        $table = Reward::findOrFail($id);
        $table->delete();
        return redirect()->route('reward.index');
    }

}
