<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\OtpMail;
use App\Models\OTP;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    /**
     * Tentukan ke mana user harus diarahkan setelah login.
     *
     * @return string
     */
    protected function redirectTo()
    {
        return Session::get('from', RouteServiceProvider::HOME);
    }

    /**
     * Buat instance baru dari controller.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function showLoginEmail(){
        return view('auth.email');
    }

    public function showLoginPhone(){
        return view('auth.phone');
    }

    public function inputOTP(){
        $validTypes = ['email', 'phone'];
        if (!request()->filled('type') || !request()->filled('data') || !in_array(request()->input('type'), $validTypes)) {
            abort(404);
        }
        return view('auth.otp');
    }

    public function withEmail(Request $request){
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
        ]);

        if($validator->fails()){
            $errorMessages = $validator->errors()->all();
            $errorMessageText = implode(' ', $errorMessages);
            return redirect()->back()
                ->withErrors($validator)
                ->withInput()
                ->with('warning', $errorMessageText);
        }else{
            $user = User::where('email', $request->get('email'))->first();

            OTP::where('user_id', $user->id)->delete();

            do {
                $otp = mt_rand(100000, 999999);
            } while (OTP::where('otp', $otp)->exists());

            OTP::create([
                'user_id' => $user->id,
                'otp' => $otp,
            ]);

            $data['otp'] = $otp;
            Mail::to($request->get('email'))->send(new OtpMail($data));

            return redirect()->route('otp.input', ['type' => 'email', 'data' => Crypt::encrypt($request->get('email'))])->with('success', 'Berhasil mengirimkan otp ke '.$request->get('email'));
        }  
    }   

    public function withPhone(Request $request){
        $request->merge(['phone' => convertPhone($request->get('phone'))]);
        $validator = Validator::make($request->all(), [
            'phone' => 'required|numeric|exists:users,phone',
        ]);

        if($validator->fails()){
            $errorMessages = $validator->errors()->all();
            $errorMessageText = implode(' ', $errorMessages);
            return redirect()->back()
                ->withErrors($validator)
                ->withInput()
                ->with('warning', $errorMessageText);
        }else{
            $user = User::where('phone', $request->get('phone'))->first();

            // Delete any existing OTP for the user
            OTP::where('user_id', $user->id)->delete();

            do {
                $otp = mt_rand(100000, 999999);
            } while (OTP::where('otp', $otp)->exists());

            OTP::create([
                'user_id' => $user->id,
                'otp' => $otp,
            ]);

            $data['otp'] = $otp;
            $message = "Hallo, {$user->name} (dikirim dari ".env('APP_NAME')."), Silahkan masukkan kode OTP : *$otp* untuk melanjutkan. Segera beritahu admin apabila permintaan ini bukan dari anda!";
            $this->sendMessage($request->get('phone'), $message);

            return redirect()->route('otp.input', ['type' => 'phone', 'data' => Crypt::encrypt($request->get('phone'))])->with('success', 'Berhasil mengirimkan otp ke '.$request->get('phone'));
        }
    }

    public function submitOtp(Request $request, $type){
        $user = null;
        $expired = setting('site.otp_expired') ?? 20;
        $otp = implode('', $request->input('otp', []));


        if($request->has('data') && $type == 'email'){
            $request->merge([
                'otp' => (int) $otp,  
                'email' => $request->input('data'),
            ]);

            $validator = Validator::make($request->all(), [
                'otp' => [
                    'required',
                    'numeric',
                    'digits:6',
                    'exists:otp,otp',
                    function ($attribute, $value, $fail) use ($request, $expired) {
                        $user = User::where('email', $request->email)->first();
                        if (!$user) {
                            $fail('User not found.');
                            return;
                        }
                        $otp = OTP::where('otp', $value)
                            ->where('user_id', $user->id)
                            ->where('created_at', '>=', now()->subMinutes($expired))
                            ->first();
    
                        if (!$otp) {
                            $fail('The OTP is invalid or has expired.');
                        }
                    },
                ],
                'email' => 'required|email|exists:users,email',
            ]);

            if($validator->fails()){
                $errorMessages = $validator->errors()->all();
                $errorMessageText = implode(' ', $errorMessages);
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput()
                    ->with('warning', $errorMessageText);
            }

            $user = User::where('email', $request->email)->first();

        }else if($request->has('data') && $type == 'phone'){
            $request->merge([
                'otp' => (int) $otp,  
                'phone' => convertPhone($request->input('data')),
            ]);
            $validator = Validator::make($request->all(), [
                'otp' => [
                    'required',
                    'numeric',
                    'digits:6',
                    'exists:otp,otp',
                    function ($attribute, $value, $fail) use ($request, $expired) {
                        $user = User::where('phone', $request->phone)->first();
                        if (!$user) {
                            $fail('User not found.');
                            return;
                        }
                        $otp = OTP::where('otp', $value)
                            ->where('user_id', $user->id)
                            ->where('created_at', '>=', now()->subMinutes($expired))
                            ->first();

                        if (!$otp) {
                            $fail('The OTP is invalid or has expired.');
                        }
                    },
                ],
                'phone' => 'required|numeric|exists:users,phone',
            ]);

            if($validator->fails()){
                $errorMessages = $validator->errors()->all();
                $errorMessageText = implode(' ', $errorMessages);
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput()
                    ->with('warning', $errorMessageText);
            }

            $user = User::where('phone', $request->phone)->first();
        }else{
            abort(403);
        }

        if ($user) {
            Auth::login($user);
            config(['session.lifetime' => 10080]);

            // Delete the used OTP
            OTP::where('user_id', $user->id)->delete();

            $prev_url = Session::get('prev_url');
            if ($prev_url) {
                return redirect()->away($prev_url);
            }

            return redirect()->intended('/dashboard');
        }

        return redirect()->back()->with('error', 'An error occurred during authentication.');
    }

    public function sendMessage($phone, $message)
    {
        // Prepare data as JSON
        $data = [
            'phone' => $phone,
            'message' => $message,
        ];

        // Send POST request
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
        ])->post(env('APP_WHATSAPP_SERVER').'/api/send-message', $data);

        // Log or display the response
        if ($response->successful()) {
            return response()->json([
                'status' => true,
                'message' => 'Pesan berhasil dikirim',
                'data' => $response->json(),
            ], 200);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Gagal mengirim pesan',
                'data' => $response->json(),
            ], $response->status());
        }
    }
}
