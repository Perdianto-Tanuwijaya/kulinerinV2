<?php

namespace App\Http\Controllers;

use App\Models\Redemption;
use App\Models\Reward;
use App\Models\PointLoyalty;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class RedemptionController extends Controller
{
    public function show(Request $request)
    {
        // dd($request);
        $category = $request->query('category', 'all');

        $userPoints = PointLoyalty::where('user_id', Auth::user()->id)->first();

        $categories = Reward::where('is_active', true)
            ->where('stock', '>', 0)
            ->pluck('category')
            ->unique()
            ->toArray();

        // Tambahkan opsi "All" ke dalam daftar kategori
        array_unshift($categories, 'all');

        $rewards = Reward::query()
            ->where('is_active', true)
            ->where('stock', '>', 0)
            ->when($category !== 'all', function ($query) use ($category) {
                return $query->where('category', $category);
            })
            ->get();

        $user = User::where('id', Auth::id())->first();

        // Cek apakah user sudah login
        if (!$user) {
            return redirect('/login')->with('error', 'You need to login first.');
        }

        // Ambil semua riwayat Redeem berdasarkan user_id, termasuk relasi reward
        $redeems = Redemption::with('reward')
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate(5);

        return view('reward.index', compact('category', 'categories', 'rewards', 'userPoints', 'redeems'));
    }

    public function redeem($id)
    {
        // Start database transaction
        DB::beginTransaction();

        try {
            // Find the reward
            $reward = Reward::findOrFail($id);
            $rewardCategory = $reward->category;

            // Check if reward is active
            if (!$reward->is_active) {
                return response()->json([
                    'success' => false,
                    'message' => 'Reward is not active.'
                ], 400);
                // return response()->json(['message' => 'Reward is not active.'], 400);
                // return redirect()->back()->with('error', 'Reward is not active.');
            }

            // Check if stock is available
            if ($reward->stock <= 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'This reward is out of stock.'
                ], 404);
            }

            // Get current user
            $user = Auth::user();

            // Get user's points
            $pointLoyalty = PointLoyalty::where('user_id', $user->id)->first();

            // Check if user has enough points
            if (!$pointLoyalty || $pointLoyalty->point < $reward->points) {
                return response()->json([
                    'success' => false,
                    'message' => 'You do not have enough points to redeem this reward.'
                ], 201);
            }

            // Generate unique redemption code
            $redemptionCode = $this->generateRedemptionCode($rewardCategory);

            // Create redemption record
            $redemption = new Redemption();
            $redemption->user_id = $user->id;
            $redemption->reward_id = $reward->id;
            $redemption->points_used = $reward->points;
            $redemption->status = 'Success';
            $redemption->redemption_code = $redemptionCode;
            $redemption->save();

            // Deduct points from user
            $pointLoyalty->point -= $reward->points;
            $pointLoyalty->save();

            // Reduce stock
            $reward->stock -= 1;
            $reward->save();

            // Commit transaction
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Reward redeemed successfully! Your redemption code is:',
                'data' => $redemptionCode
            ], 201);

        } catch (\Exception $e) {
            // Rollback transaction on error
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Failed to redeem reward. Please try again later.'
            ], 400);
        }
    }

    private function generateRedemptionCode($rewardCategory)
    {
        // Convert category to uppercase and remove any spaces
        $categoryPrefix = strtoupper(str_replace(' ', '', $rewardCategory));

        // Generate random string for the rest of the code
        $randomPart = strtoupper(Str::random(5));

        // Combine prefix and random part
        $code = $categoryPrefix . $randomPart;

        // Check if code already exists and regenerate if needed
        while (Redemption::where('redemption_code', $code)->exists()) {
            $randomPart = strtoupper(Str::random(5));
            $code = $categoryPrefix . $randomPart;
        }

        return $code;
    }

    // public function history(){
    //     $user = User::where('id', Auth::id())->first();

    //     // Cek apakah user sudah login
    //     if (!$user) {
    //         return redirect('/login')->with('error', 'You need to login first.');
    //     }

    //     // Ambil semua riwayat Redeem berdasarkan user_id, termasuk relasi reward
    //     $redeems = Redemption::with('reward')
    //         ->where('user_id', $user->id)
    //         ->orderBy('created_at', 'desc')
    //         ->get();

    //     return view('reward.index', compact('redeems'));
    // }
}
