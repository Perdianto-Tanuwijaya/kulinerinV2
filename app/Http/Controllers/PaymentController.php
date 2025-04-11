<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Payment;
use App\Models\Reservation;
use App\Models\Restaurant;
use App\Models\RestaurantBalance;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use stdClass;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    public function generateSymmetricSignature(Request $request)
    {
        $clientSecret = "YZwFtTCi7YwOWbwQUOcAstJI82CRHmlRkHkdGja7Tn6apvyXKeI5Fncitn1QfUqUlu3E717QkSdV5DiCzlO06w9mw4OUNWmLRw4kht1JfIUVKNlOen0Qs1S8mp5T9EAI";
        $httpMethod = "POST";
        $endpointUrl = $request->url;
        $accessToken = $request->accessToken;
        $timestamp = $request->timestamp == null ? Carbon::now('Asia/Jakarta')->toIso8601String() : $request->timestamp;
        $requestBody = json_encode($request->requestBody);
        $hashedBody = hash("sha256", $requestBody);
        $stringSign = $httpMethod . ":" . $endpointUrl . ":" . $accessToken . ":" . $hashedBody . ":" . $timestamp;
        // dd($stringSign);
        $hmacBin = hash_hmac("sha512", $stringSign, $clientSecret, true);
        $result = base64_encode($hmacBin);
        return $result;
    }

    public function generateAsymmetricSignature(Request $request)
    {
        if ($request->type == 'token') {
            $clientId = "AXQUpjhI-G374379766-SNAP";
            $stringSign = $clientId . "|" . ($request->timestamp == null ? Carbon::now('Asia/Jakarta')->toIso8601String() : $request->timestamp);
            // $hashedBody = hash("sha256", $stringSign, true);
            $privateKey = File::get(storage_path("app/private/private.key"));
            $signature = "";
            if (openssl_sign($stringSign, $signature, $privateKey, OPENSSL_ALGO_SHA256)) {
                return base64_encode($signature);
            } else {
                return "Error";
            }
        }
    }

    public function getB2BToken(Request $request)
    {
        $signature = PaymentController::generateAsymmetricSignature($request);
        $clientId = "AXQUpjhI-G374379766-SNAP";
        $contentType = "application/json";
        $response = Http::withHeaders([
            'X-SIGNATURE' => $signature,
            'X-TIMESTAMP' => $request->timestamp == null ? Carbon::now('Asia/Jakarta')->toIso8601String() : $request->timestamp,
            'X-CLIENT-KEY' => $clientId,
            'Content-Type' => $contentType
        ])->post('https://merchants.sbx.midtrans.com/v1.0/access-token/b2b', [
            'grantType' => 'client_credentials',
        ]);
        $data = $response->json();
        return $data;
    }
    public function generateQris(Request $request)
    {
        $amountValue = $request->input('amount');
        // dd($amountValue);
        $request->timestamp = Carbon::now('Asia/Jakarta')->toIso8601String();
        $request->type = "token";
        $accessToken = PaymentController::getB2BToken($request)["accessToken"];
        $clientId = "AXQUpjhI-G374379766-SNAP";
        $contentType = "application/json";
        $externalId = Carbon::now()->format('YmdHisv');


        $partnerReferenceNo = Carbon::now()->format('YmdHisv');
        $amount = new stdClass();
        // $amount->value = $request->amount . ".00";
        $amount->value = $amountValue  . ".00";
        $amount->currency = "IDR";

        // $feeAmount = new stdClass();
        // $feeAmount->value = "500" . ".00";
        // $feeAmount->currency = "IDR";

        $merchantId = "G374379766";
        $terminalId = "374379766";
        $validityPeriod = Carbon::now('Asia/Jakarta')->addMinutes(5)->toIso8601String();

        $additionalInfo = new stdClass();
        $additionalInfo->acquirer = "GOPAY"; //GOPAY or AIRPAY SHOPEE
        // $additionalInfo->postalCode = 15810;
        // $additionalInfo->feeType = 1;

        $requestBody = [
            'partnerReferenceNo' => $partnerReferenceNo,
            'amount' => $amount,
            // 'feeAmount' => $feeAmount,
            'merchantId' => $merchantId,
            'terminalId' => $terminalId,
            'validityPeriod' => $validityPeriod,
            'additionalInfo' => $additionalInfo,
        ];
        $request->type = "trans";
        $request->url = "/v1.0/qr/qr-mpm-generate";
        $request->accessToken = $accessToken;
        $request->requestBody = $requestBody;
        $signature = PaymentController::generateSymmetricSignature($request);
        $response = Http::withHeaders([
            'X-SIGNATURE' => $signature,
            'X-TIMESTAMP' => $request->timestamp,
            'X-PARTNER-ID' => $clientId,
            'X-EXTERNAL-ID' => $externalId,
            'Authorization' => 'Bearer ' . $accessToken,
            'Content-Type' => $contentType,
            'CHANNEL-ID' => '12345'
        ])->post('https://merchants.sbx.midtrans.com/v1.0/qr/qr-mpm-generate', $requestBody);
        $data = $response->json();
        $data['validityPeriod'] = $validityPeriod;
        return $data;
    }

    public function checkStatus(Request $request)
    {
        $request->timestamp = Carbon::now('Asia/Jakarta')->toIso8601String();
        $request->type = "token";
        $contentType = "application/json";
        $accessToken = PaymentController::getB2BToken($request)["accessToken"];
        $clientId = "AXQUpjhI-G374379766-SNAP";
        $originalExternalId = $request->externalId;
        $channelId = '12345';
        $originalPartnerReferenceNo = $request->partnerReferenceNo;
        $externalId = Carbon::now()->format('YmdHisv');
        $merchantId = "G374379766";
        $serviceCode = "47";

        $requestBody = [
            'originalPartnerReferenceNo' => $originalPartnerReferenceNo,
            'originalExternalId' => $originalExternalId,
            'merchantId' => $merchantId,
            'serviceCode' => $serviceCode,
        ];

        $request->type = "trans";
        $request->accessToken = $accessToken;
        $request->requestBody = $requestBody;
        $request->url = '/v1.0/qr/qr-mpm-query';
        $signature = PaymentController::generateSymmetricSignature($request);

        $response = Http::withHeaders([
            'X-SIGNATURE' => $signature,
            'X-TIMESTAMP' => $request->timestamp,
            'X-PARTNER-ID' => $clientId,
            'X-EXTERNAL-ID' => $externalId,
            'Authorization' => 'Bearer ' . $accessToken,
            'Content-Type' => $contentType,
            'CHANNEL-ID' => $channelId
        ])->post('https://merchants.sbx.midtrans.com/v1.0/qr/qr-mpm-query', $requestBody);
        $data = $response->json();
        // if ($data->latestTransactionStatus != "03" && $data->latestTransactionStatus != "07") {
        //     //save to db
        // }
        return $data;

    }
    public function index()
    {
        $restaurant = Restaurant::where('user_id', Auth::user()->id)->first();

        // saldo
        $balance = RestaurantBalance::where('restaurant_id', $restaurant->id)->value('restaurantBalance');

        //list incoming
        $payments = Reservation::where('restaurant_id', $restaurant->id)
            ->whereNotNull('priceTotal')
            ->orderBy('updated_at', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        //list wd
        $withdraws = Payment::where('restaurant_id', $restaurant->id)
            ->orderBy('updated_at', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(10);
            // dd($withdraw);

        return view('restaurant.payment.index', compact('balance', 'payments', 'withdraws'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:10000',
            'bankName' => 'required|string',
            'bankAccount' => 'required|string',
        ]);
        // Log::info("Masuk: " . $request->bankName);

        $amount = str_replace(',', '', $request->amount);
        $amount = (float) $amount;

        // Ambil restoran berdasarkan user yang login
        $restaurant = Restaurant::where('user_id', Auth::id())->first();

        if (!$restaurant) {
            return back()->with('error', 'Restaurant not found.');
        }

        // Ambil saldo saat ini
        $currentBalance = RestaurantBalance::where('restaurant_id', $restaurant->id)->sum('restaurantBalance');

        if ($request->amount > $currentBalance) {
            return back()->with('error', 'Insufficient balance.');
        }

        DB::beginTransaction();
        try {
            // Simpan data pembayaran (withdraw)
            $payment = new Payment();
            $payment->restaurant_id = $restaurant->id;
            $payment->amount = $amount;
            $payment->bankName = $request->bankName;
            $payment->bankAccount = $request->bankAccount;
            $payment->withdrawDate = Carbon::now()->toDateString();
            $payment->withdrawTime = Carbon::now()->toTimeString();
            $payment->status = 'Pending';
            $payment->save();

            // Kurangi saldo restaurantBalance
            RestaurantBalance::where('restaurant_id', $restaurant->id)
                ->decrement('restaurantBalance', $request->amount);

            DB::commit();

            return back()->with('success', 'Withdrawal request has been submitted.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    // Admin
    public function show(Request $request){

        $withdraws = Payment::query();

        if ($request->has('search') && !empty($request->search)) {
            $withdraws->whereHas('restaurant', function ($query) use ($request) {
                $query->where('restaurantName', 'LIKE', '%' . $request->search . '%');
            });
        }

        $withdraws = $withdraws->latest()->paginate(10);

        return view ('admin.payment.index', compact('withdraws'));
    }

    public function updateStatus(Request $request, $id)
    {
        $payment = Payment::findOrFail($id);
        $restaurant = Restaurant::findOrFail($payment->restaurant_id);

        if ($request->status === 'Rejected') {
            // Ambil saldo saat ini
            $balance = RestaurantBalance::where('restaurant_id', $restaurant->id)->first();

            if ($balance) {
                // Tambahkan kembali jumlah yang ditarik ke saldo restoran
                $balance->restaurantBalance += $payment->amount;
                $balance->save();
            } else {
                // Jika saldo belum ada, buat record baru
                RestaurantBalance::create([
                    'restaurant_id' => $restaurant->id,
                    'restaurantBalance' => $payment->amount
                ]);
            }
        }

        // Update status pembayaran
        $payment->status = $request->status;
        $payment->save();

        return response()->json(['message' => 'Payment status updated successfully.']);
    }



}
