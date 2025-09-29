<?php

namespace App\Http\Controllers\Students;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\Payment;
use App\Models\Enrollment;
use App\Models\Checkout;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class SslController extends Controller
{
    private $sslStoreId;
    private $sslStorePassword;
    private $sslApiUrl;
    private $currencyRate;

    public function __construct()
    {
        // Use config values instead of hardcoding
        $this->sslStoreId = config('services.sslcommerz.store_id', 'geniu5e1b00621f81e');
        $this->sslStorePassword = config('services.sslcommerz.store_password', 'geniu5e1b00621f81e@ssl');
        $this->sslApiUrl = config('services.sslcommerz.sandbox') ?
            'https://sandbox.sslcommerz.com/gwprocess/v3/api.php' :
            'https://securepay.sslcommerz.com/gwprocess/v3/api.php';
        $this->currencyRate = config('services.currency.rate', 2.7); // Курс GEL к BDT
    }

    public function store(Request $request)
    {
        // Validate session data
        if (!session()->has('cart_details') || !session()->has('cart')) {
            return redirect()->back()->with('error', 'Cart is empty.');
        }

        $validator = Validator::make($request->all(), [
            // Add any required validation rules
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            $user = Student::findOrFail(currentUserId());
            $txnid = "SSLCZ_TXN_" . uniqid();

            // Convert GEL to BDT for SSLCommerz (они работают с BDT)
            $item_amount_gel = session('cart_details')['total_amount'];
            $item_amount_bdt = $this->convertGelToBdt($item_amount_gel);

            // Save checkout data
            $cart_details = [
                'cart' => session('cart'),
                'cart_details' => session('cart_details'),
                'original_amount_gel' => $item_amount_gel,
                'converted_amount_bdt' => $item_amount_bdt,
                'currency_rate' => $this->currencyRate
            ];

            $check = new Checkout;
            $check->cart_data = base64_encode(json_encode($cart_details));
            $check->student_id = $user->id;
            $check->txnid = $txnid;
            $check->status = 0;
            $check->save();

            // Create payment record (сохраняем в GEL)
            $deposit = new Payment;
            $deposit->student_id = $user->id;
            $deposit->currency = "GEL";
            $deposit->currency_code = "GEL";
            $deposit->amount = $item_amount_gel; // Оригинальная сумма в GEL
            $deposit->converted_amount = $item_amount_bdt; // Конвертированная сумма в BDT
            $deposit->currency_value = $this->currencyRate;
            $deposit->method = 'SSLCommerz';
            $deposit->txnid = $txnid;
            $deposit->status = 0;
            $deposit->save();

            // Prepare SSLCommerz request data (отправляем в BDT)
            $post_data = $this->prepareSslRequestData($user, $txnid, $item_amount_bdt);

            // Send request to SSLCommerz
            $sslResponse = $this->sendSslRequest($post_data);

            if (!$sslResponse['success']) {
                Log::error('SSLCommerz API Error: ' . $sslResponse['message']);
                return redirect()->back()->with('error', 'Payment gateway connection failed.');
            }

            $sslcz = $sslResponse['data'];

            if (isset($sslcz['GatewayPageURL']) && !empty($sslcz['GatewayPageURL'])) {
                return redirect()->away($sslcz['GatewayPageURL']);
            }

            Log::error('SSLCommerz Gateway URL missing', ['response' => $sslcz]);
            return redirect()->back()->with('error', 'Payment gateway error. Please try again.');

        } catch (\Exception $e) {
            Log::error('Payment initiation error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'An error occurred while processing your request.');
        }
    }

    private function convertGelToBdt(float $amountGel): float
    {
        return round($amountGel * $this->currencyRate, 2);
    }

    private function prepareSslRequestData(Student $user, string $txnid, float $amountBdt): array
    {
        return [
            'store_id' => $this->sslStoreId,
            'store_passwd' => $this->sslStorePassword,
            'total_amount' => $amountBdt,
            'currency' => "BDT", // SSLCommerz требует BDT
            'tran_id' => $txnid,
            'success_url' => action([SslController::class, 'notify']),
            'fail_url' => action([SslController::class, 'cancel']),
            'cancel_url' => action([SslController::class, 'cancel']),

            // Customer information
            'cus_name' => $user->name,
            'cus_email' => $user->email,
            'cus_add1' => $user->address ?? 'N/A',
            'cus_city' => $user->city ?? 'N/A',
            'cus_state' => $user->state ?? 'N/A',
            'cus_postcode' => $user->postcode ?? 'N/A',
            'cus_country' => 'Georgia', // Грузия
            'cus_phone' => $user->contact ?? 'N/A',
            'cus_fax' => $user->contact ?? 'N/A',

            // Additional parameters
            'product_category' => 'Education',
            'product_name' => 'Course Enrollment',
            'shipping_method' => 'NO',
            'product_profile' => 'non-physical-goods',

            // Currency information
            'multi_currency' => 'true',
            'currency_type' => 'BDT'
        ];
    }

    private function sendSslRequest(array $post_data): array
    {
        try {
            $handle = curl_init();
            curl_setopt_array($handle, [
                CURLOPT_URL => $this->sslApiUrl,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_CONNECTTIMEOUT => 30,
                CURLOPT_POST => 1,
                CURLOPT_POSTFIELDS => $post_data,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_HTTPHEADER => ['Content-Type: application/x-www-form-urlencoded']
            ]);

            $content = curl_exec($handle);
            $httpCode = curl_getinfo($handle, CURLINFO_HTTP_CODE);
            $error = curl_error($handle);
            curl_close($handle);

            if ($httpCode == 200 && empty($error)) {
                return [
                    'success' => true,
                    'data' => json_decode($content, true)
                ];
            }

            return [
                'success' => false,
                'message' => $error ?: "HTTP Code: $httpCode"
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    public function cancel(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'tran_id' => 'required|string'
            ]);

            if ($validator->fails()) {
                return redirect()->route('studentdashboard')->with('error', 'Invalid request.');
            }

            $deposit = Payment::where('txnid', $request->tran_id)
                ->orderBy('created_at', 'desc')
                ->first();

            if ($deposit) {
                $student = Student::findOrFail($deposit->student_id);
                $this->setSession($student);

                // Можно добавить логирование отмены платежа
                Log::info('Payment cancelled', [
                    'transaction_id' => $request->tran_id,
                    'student_id' => $deposit->student_id,
                    'amount_gel' => $deposit->amount
                ]);
            }

            return redirect()->route('studentdashboard')->with('warning', 'Payment was cancelled.');

        } catch (\Exception $e) {
            Log::error('Payment cancellation error: ' . $e->getMessage());
            return redirect()->route('studentdashboard')->with('error', 'An error occurred.');
        }
    }

    public function notify(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'tran_id' => 'required|string',
                'status' => 'required|string',
                'amount' => 'sometimes|required|numeric',
                'currency' => 'sometimes|required|string'
            ]);

            if ($validator->fails()) {
                Log::error('Invalid SSLCommerz notification', $request->all());
                return redirect()->route('studentdashboard')->with('error', 'Invalid payment notification.');
            }

            if ($request->status == 'VALID') {
                $deposit = Payment::where('txnid', $request->tran_id)
                    ->orderBy('created_at', 'desc')
                    ->first();

                if (!$deposit) {
                    Log::error('Payment not found for transaction: ' . $request->tran_id);
                    return redirect()->route('studentdashboard')->with('error', 'Payment record not found.');
                }

                // Логируем полученные данные от SSLCommerz
                Log::info('SSLCommerz payment successful', [
                    'transaction_id' => $request->tran_id,
                    'ssl_amount' => $request->get('amount'),
                    'ssl_currency' => $request->get('currency'),
                    'our_amount_gel' => $deposit->amount,
                    'converted_amount_bdt' => $deposit->converted_amount
                ]);

                // Update checkout status
                $check = Checkout::where('txnid', $request->tran_id)
                    ->orderBy('created_at', 'desc')
                    ->first();

                if ($check) {
                    $check->status = 1;
                    $check->save();
                }

                // Update payment status
                $deposit->status = 1;
                $deposit->save();

                // Create enrollments
                if ($check && $deposit->status == 1) {
                    $cartData = json_decode(base64_decode($check->cart_data));

                    if (isset($cartData->cart)) {
                        foreach ($cartData->cart as $courseId => $course) {
                            // Проверяем, нет ли уже такой записи
                            $existingEnrollment = Enrollment::where('student_id', $check->student_id)
                                ->where('course_id', $courseId)
                                ->first();

                            if (!$existingEnrollment) {
                                $enrollment = new Enrollment;
                                $enrollment->student_id = $check->student_id;
                                $enrollment->course_id = $courseId;
                                $enrollment->enrollment_date = date('Y-m-d');
                                $enrollment->save();
                            }
                        }
                    }
                }

                $student = Student::findOrFail($deposit->student_id);
                $this->setSession($student);

                // Clear cart session
                session()->forget(['cart', 'cart_details']);

                Log::info('Payment completed successfully', [
                    'student_id' => $student->id,
                    'transaction_id' => $request->tran_id,
                    'amount_gel' => $deposit->amount
                ]);

                return redirect()->route('studentdashboard')->with('success', 'Payment completed successfully!');

            } else {
                Log::warning('Payment failed for transaction: ' . $request->tran_id, [
                    'status' => $request->status,
                    'message' => $request->get('error', 'No error message')
                ]);

                return redirect()->route('studentdashboard')->with('error', 'Payment failed. Please try again.');
            }

        } catch (\Exception $e) {
            Log::error('Payment notification error: ' . $e->getMessage(), $request->all());
            return redirect()->route('studentdashboard')->with('error', 'An error occurred while processing your payment.');
        }
    }

    private function setSession(Student $student): void
    {
        session()->put([
            'userId' => encryptor('encrypt', $student->id),
            'userName' => encryptor('encrypt', $student->name),
            'emailAddress' => encryptor('encrypt', $student->email),
            'studentLogin' => 1,
            'image' => $student->image ?? 'default.png'
        ]);
    }
}
