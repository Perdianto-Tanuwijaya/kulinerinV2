<?php

use App\Http\Controllers\PaymentController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post("/generateSymmetricSignature", [PaymentController::class, 'generateSymmetricSignature']);
Route::post("/generateAsymmetricSignature", [PaymentController::class, 'generateAsymmetricSignature']);
Route::post("/getB2BToken", [PaymentController::class, 'getB2BToken']);
Route::post("/generateQris", [PaymentController::class, 'generateQris']);
Route::post("/checkStatus", [PaymentController::class, 'checkStatus']);

Route::get('/operational-hours/{day}/{restaurant_id}', function ($day, $restaurant_id) {
    $operationalHours = App\Models\OperationalHour::where('restaurant_id', $restaurant_id)
        ->where('day', $day)
        ->first();

    return response()->json([
        'operational_hours' => $operationalHours
    ]);
});
