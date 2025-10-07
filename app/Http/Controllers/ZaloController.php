<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
class ZaloController extends Controller
{
    public function chatZalo()
    {
        return view('admin.chat.zalo');
    }

    private $appId;
    private $appSecret;
    private $redirectUri;

    public function __construct()
    {
        $this->appId = env('ZALO_APP_ID');
        $this->appSecret = env('ZALO_SECRET_KEY');
        $this->redirectUri = route('admin.zalo.callback');
    }
    public function redirectToZalo(Request $request)
    {
        // Tạo code verifier
        $codeVerifier = Str::random(43);
        session(['zalo_code_verifier' => $codeVerifier]); // lưu vào session

        // Tạo code challenge
        $codeChallenge = rtrim(strtr(base64_encode(hash('sha256', $codeVerifier, true)), '+/', '-_'), '=');

        // Tạo state chống CSRF
        $state = Str::random(16);
        session(['zalo_state' => $state]);

        $url = "https://oauth.zaloapp.com/v4/permission?" . http_build_query([
            'app_id' => $this->appId,
            'redirect_uri' => $this->redirectUri,
            'code_challenge' => $codeChallenge,
            'state' => $state
        ]);

        // dd($url);

        return redirect($url);
    }

    // Bước 2: Nhận callback từ Zalo
    public function handleCallback(Request $request)
    {
        dd('ok');
        $state = $request->query('state');
        $code = $request->query('code');

        // Kiểm tra state
        if ($state !== session('zalo_state')) {
            return "State không hợp lệ";
        }


        $codeVerifier = session('zalo_code_verifier');

        // Bước 3: Đổi authorization code lấy access token
        $response = Http::asForm()->post('https://oauth.zaloapp.com/v4/access_token', [
            'app_id' => $this->appId,
            'app_secret' => $this->appSecret,
            'code' => $code,
            'code_verifier' => $codeVerifier,
            'redirect_uri' => $this->redirectUri
        ]);

        $data = $response->json();

        if (isset($data['access_token'])) {
            // Lưu access_token vào database hoặc session
            session(['zalo_access_token' => $data['access_token']]);

            return "Đăng nhập thành công! Access token: " . $data['access_token'];
        }

        return "Lỗi khi lấy access token: " . json_encode($data);
    }
}
