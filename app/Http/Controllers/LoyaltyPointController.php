<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PointLoyalty;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class LoyaltyPointController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = User::where('role', '1')->with('pointLoyalties')->orderBy('id', 'asc');

        if ($request->has('search') && !empty($request->search)) {
            $query->where('username', 'LIKE', '%' . $request->search . '%');
        }

        $pointUser = $query->get();
        // dd($pointUser);
        return view('admin.point.index', compact('pointUser'));
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
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $user = User::findOrFail($id); // Pastikan user ada di tabel users

        // Cari data loyalty point berdasarkan user_id
        $point = PointLoyalty::where('user_id', $id)->first();

        // Log::info("User ID yang diedit " . $user);
        // Jika belum ada, buat data baru dengan point = 0
        if (!$point) {
            $point = PointLoyalty::create([
                'user_id' => $id,
                'point' => 0
            ]);
        }

        return response()->json([
            'id' => $point->id,
            'user_id' => $point->user_id,
            'username' => $user->username,
            'point' => $point->point
        ]);

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
        $request->validate([
            'point' => 'required|integer|min:1',
        ]);

        // Cari entri point loyalty berdasarkan user_id
        $pointLoyalty = PointLoyalty::where('user_id', $id)->first();
        // Log::info("User ID yang diupdate " . $pointLoyalty);

        if ($pointLoyalty) {
            // Jika sudah ada, tambahkan point baru ke point lama
            $pointLoyalty->point += $request->point;
        } else {
            // Jika belum ada, buat entri baru
            $pointLoyalty = new PointLoyalty();
            $pointLoyalty->user_id = $id;
            $pointLoyalty->point = $request->point;
        }

        // Simpan perubahan
        $pointLoyalty->save();

        return response()->json([
            'success' => true,
            'message' => 'Points updated successfully!',
            'updated_point' => $pointLoyalty->point
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
