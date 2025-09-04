<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Http\Requests\StoreConversationRequest;
use App\Http\Requests\UpdateConversationRequest;
use App\Models\Message;
use App\Models\User;
use App\Models\ZaloToken;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ConversationController extends Controller
{
    // public function zalo(Request $request)
    // {
    //     Log::info('Zalo Webhook', $request->all());

    //     $event = $request->input('event_name');
    //     $senderId = $request->input('sender.id');

    //     // Lấy hoặc tạo Conversation
    //     $conversation = Conversation::firstOrCreate(
    //         ['platform' => 'zalo', 'external_id' => $senderId],
    //         ['last_message' => '', 'last_time' => now()]
    //     );

    //     switch ($event) {
    //         case 'user_send_text':
    //             $text = $request->input('message.text');
    //             if ($text) {
    //                 $conversation->messages()->create([
    //                     'sender_type' => 'user',
    //                     'message_type' => 'text',
    //                     'message_text' => $text,
    //                     'sent_at' => now(),
    //                 ]);

    //                 $conversation->update([
    //                     'last_message' => $text,
    //                     'last_time' => now(),
    //                 ]);
    //             }
    //             break;

    //         case 'user_send_image':
    //             $images = $request->input('message.attachments', []);
    //             foreach ($images as $img) {
    //                 $conversation->messages()->create([
    //                     'sender_type' => 'user',
    //                     'message_type' => 'image',
    //                     'message_text' => $img['payload']['url'] ?? null,
    //                     'sent_at' => now(),
    //                 ]);
    //             }
    //             $conversation->update([
    //                 'last_message' => '[Hình ảnh]',
    //                 'last_time' => now(),
    //             ]);
    //             break;

    //         case 'user_send_sticker':
    //             $sticker = $request->input('message.sticker_id');
    //             $conversation->messages()->create([
    //                 'sender_type' => 'user',
    //                 'message_type' => 'sticker',
    //                 'message_text' => $sticker,
    //                 'sent_at' => now(),
    //             ]);
    //             $conversation->update([
    //                 'last_message' => '[Sticker]',
    //                 'last_time' => now(),
    //             ]);
    //             break;

    //         case 'follow':
    //             // Người dùng vừa quan tâm OA
    //             $conversation->messages()->create([
    //                 'sender_type' => 'system',
    //                 'message_type' => 'event',
    //                 'message_text' => 'Người dùng đã quan tâm OA',
    //                 'sent_at' => now(),
    //             ]);
    //             $conversation->update([
    //                 'last_message' => 'Người dùng đã quan tâm OA',
    //                 'last_time' => now(),
    //             ]);
    //             break;

    //         case 'unfollow':
    //             $conversation->messages()->create([
    //                 'sender_type' => 'system',
    //                 'message_type' => 'event',
    //                 'message_text' => 'Người dùng bỏ quan tâm OA',
    //                 'sent_at' => now(),
    //             ]);
    //             $conversation->update([
    //                 'last_message' => 'Người dùng bỏ quan tâm OA',
    //                 'last_time' => now(),
    //             ]);
    //             break;

    //         default:
    //             Log::warning("Unhandled Zalo event: $event", $request->all());
    //             break;
    //     }

    //     return response()->json(['status' => 'ok']);
    // }

    public function zalo(Request $request)
    {
        Log::info('Zalo Webhook', $request->all());

        $event = (string)$request->input('event_name');

        // 1) Resolve external_id: luôn là ID của USER (không bao giờ là OA)
        $externalId = $this->resolveExternalUserId($request, $event);

        // Nếu chưa resolve được, bỏ qua để tránh tạo Conversation sai
        if (!$externalId) {
            Log::warning("Webhook cannot resolve external user id", $request->all());
            return response()->json(['status' => 'ignored']);
        }

        // 2) Dùng (platform, external_id) làm khóa duy nhất cho 1 cuộc chat
        //    -> Không bao giờ tạo conversation mới khi external_id trùng
        $conversation = Conversation::firstOrCreate(
            ['platform' => 'zalo', 'external_id' => $externalId],
            ['user_id' => $externalId, 'last_message' => '', 'last_time' => now()]
        );

        // 2) Kiểm tra trong DB đã có user với zalo_id này chưa
        $user = User::where('zalo_id', $externalId)->first();

        if (!$user) {
            // 🔹 Lấy access_token
            $accessToken = $this->getZaloAccessToken();

            // 🔹 API v3: lấy thông tin user
            $url = "https://openapi.zalo.me/v3.0/oa/user/getprofile";
            $response = Http::withHeaders([
                'Authorization' => "Bearer {$accessToken}",
            ])->post($url, [
                'user_id' => $externalId,
            ]);

            $data = $response->json();
            Log::info("Zalo v3 getprofile response", $data);

            $name   = $data['data']['display_name'] ?? 'Zalo User';
            $avatar = $data['data']['avatar'] ?? null;

            // 🔹 Tạo mới user
            $user = User::create([
                'name'      => $name,
                'full_name' => $name,
                'avatar'    => $avatar,
                'zalo_id'   => $externalId,
                'role'      => 'user',
            ]);
        }

        switch ($event) {
            /** ----------------
             *  USER SEND EVENTS
             * ---------------- */
            case 'user_send_text':
                $text = $request->input('message.text');
                $this->storeMessage($conversation, 'user', 'text', $text);
                break;

            case 'user_send_image':
                foreach ($request->input('message.attachments', []) as $img) {
                    $this->storeMessage($conversation, 'user', 'image', $img['payload']['url'] ?? '[Ảnh]');
                }
                break;

            case 'user_send_sticker':
                $sticker = $request->input('message.sticker_id');
                $this->storeMessage($conversation, 'user', 'sticker', "[Sticker:$sticker]");
                break;

            case 'user_send_gif':
                $this->storeMessage($conversation, 'user', 'gif', '[GIF]');
                break;

            case 'user_send_link':
                $link = $request->input('message.link');
                $this->storeMessage($conversation, 'user', 'link', $link);
                break;

            case 'user_send_location':
                $loc = $request->input('message.location.address');
                $this->storeMessage($conversation, 'user', 'location', $loc);
                break;

            /** ----------------
                 *  USER ACTION EVENTS
                 * ---------------- */
            case 'follow':
                $this->storeMessage($conversation, 'system', 'event', 'Người dùng đã quan tâm OA');
                break;

            case 'unfollow':
                $this->storeMessage($conversation, 'system', 'event', 'Người dùng bỏ quan tâm OA');
                break;

            case 'user_click_menu':
                $menuId = $request->input('menu.id');
                $this->storeMessage($conversation, 'user', 'event', "User click menu: $menuId");
                break;

            case 'reopen_chat':
                $this->storeMessage($conversation, 'system', 'event', "User chat lại sau một thời gian không hoạt động");
                break;

            /** ----------------
                 *  OA SEND EVENTS
                 * ---------------- */
            case 'oa_send_msg_result':
                $status = $request->input('status');
                $this->storeMessage($conversation, 'system', 'oa_send_status', "KQ gửi OA: $status");
                break;
            case 'oa_send_text':
                $text = $request->input('message.text');
                $conversation->messages()->create([
                    'sender_type' => 'admin',
                    'message_type' => 'text',
                    'message_text' => $text,
                    'sent_at'     => now(),
                ]);

                $conversation->update([
                    'last_message' => $text,
                    'last_time'    => now(),
                ]);
                break;
            case 'user_received_message':
                $msgId = $request->input('message.msg_id');
                $conversation->messages()->create([
                    'sender_type'  => 'system',
                    'message_type' => 'event',
                    'message_text' => "User đã nhận tin nhắn ID: $msgId",
                    'sent_at'      => now(),
                ]);

                $conversation->update([
                    'last_message' => '[User đã nhận tin]',
                    'last_time'    => now(),
                ]);
                break;


            case 'broadcast_result':
                $brId = $request->input('broadcast_id');
                $this->storeMessage($conversation, 'system', 'broadcast', "Kết quả broadcast: $brId");
                break;

            /** ----------------
                 *  DELIVERY STATUS
                 * ---------------- */
            case 'delivery':
                $this->storeMessage($conversation, 'system', 'event', 'Tin nhắn đã giao tới user');
                break;

            case 'seen':
                $this->storeMessage($conversation, 'system', 'event', 'Người dùng đã đọc tin nhắn');
                break;

            /** ----------------
                 *  DEFAULT
                 * ---------------- */
            default:
                Log::warning("Unhandled Zalo event: $event", $request->all());
                $this->storeMessage($conversation, 'system', 'event', "Sự kiện khác: $event");
                break;
        }

        return response()->json(['status' => 'ok']);
    }

    private function resolveExternalUserId(Request $request, string $event): ?string
    {
        $oaId = (string)config('services.zalo.oa_id'); // đặt ENV nếu có: ZALO_OA_ID=7380...

        // 1) Event do user khởi phát
        if (str_starts_with($event, 'user_') || in_array($event, ['follow', 'unfollow', 'user_click_menu', 'reopen_chat'])) {
            $uid = $request->input('sender.id')
                ?? $request->input('from.id')
                ?? $request->input('user_id')
                ?? $request->input('user_id_by_app');

            if ($uid && $uid !== $oaId) return (string)$uid;
        }

        // 2) Event do OA khởi phát / trạng thái giao nhận
        //    -> user là "recipient" / "to"
        $uid = $request->input('recipient.id')
            ?? $request->input('to.id')
            ?? $request->input('to_user_id')
            ?? data_get($request->input('message', []), 'to_uid')
            ?? data_get($request->input('message', []), 'user_id')
            ?? null;

        if ($uid && $uid !== $oaId) return (string)$uid;

        // 3) Fallback: truy ngược theo msg_id (nếu bạn có lưu mapping)
        $msgId = $request->input('message.msg_id') ?? $request->input('msg_id');
        if ($msgId) {
            $m = Message::with('conversation')
                ->where('platform', 'zalo')
                ->where(function ($q) use ($msgId) {
                    $q->where('message_id', $msgId)->orWhere('provider_msg_id', $msgId);
                })
                ->first();
            $uid = $m?->conversation?->external_id;
            if ($uid && $uid !== $oaId) return (string)$uid;
        }

        return null;
    }


    private function storeMessage(Conversation $conversation, string $senderType, string $msgType, ?string $msgText)
    {
        $conversation->messages()->create([
            'sender_type'  => $senderType,
            'message_type' => $msgType,
            'message_text' => $msgText,
            'sent_at'      => now(),
        ]);

        $conversation->update([
            'last_message' => $msgText,
            'last_time'    => now(),
        ]);
    }


    // Facebook webhook
    public function facebook(Request $request)
    {
        Log::info('Facebook Webhook', $request->all());

        if ($request->object === 'page') {
            foreach ($request->entry as $entry) {
                foreach ($entry['messaging'] as $msg) {
                    if (isset($msg['message']['text'])) {
                        $senderId = $msg['sender']['id'];
                        $text = $msg['message']['text'];

                        $conversation = Conversation::firstOrCreate(
                            ['platform' => 'facebook', 'external_id' => $senderId],
                            ['last_message' => '', 'last_time' => now()]
                        );

                        $conversation->messages()->create([
                            'sender_type' => 'user',
                            'message_type' => 'text',
                            'message_text' => $text,
                            'sent_at' => now(),
                        ]);

                        $conversation->update([
                            'last_message' => $text,
                            'last_time' => now(),
                        ]);
                    }
                }
            }
        }

        return response()->json(['status' => 'ok']);
    }

    public function index()
    {
        $conversations = Conversation::with('user') // load kèm thông tin khách hàng
            ->orderByDesc('last_time')
            ->paginate(10);
        $conversations->getCollection()->transform(function ($conversation) {
            if ($conversation->customer && $conversation->customer->avatar) {
                $avatar = $conversation->customer->avatar;
                // nếu avatar bắt đầu bằng https thì giữ nguyên, còn không thì thêm storage path
                $conversation->customer->avatar_url = str_starts_with($avatar, 'http')
                    ? $avatar
                    : asset('storage/' . $avatar);
            }
            return $conversation;
        });
        return view('admin.conversations.index', compact('conversations'));
    }


    /**
     * Xem chi tiết hội thoại
     */
    public function show($id)
    {
        $conversation = Conversation::with('user')->findOrFail($id);
        if ($conversation->customer && $conversation->customer->avatar) {
            $avatar = $conversation->customer->avatar;
            $conversation->customer->avatar_url = str_starts_with($avatar, 'http')
                ? $avatar
                : asset('storage/' . $avatar);
        }
        $messages = Message::where('conversation_id', $conversation->id)
            ->with('user') // nếu muốn lấy cả thông tin người gửi
            ->orderBy('created_at', 'asc')
            ->get();

        return view('admin.conversations.show', compact('conversation', 'messages'));
    }


    public function sendMessage(Request $request, $id)
    {
        $conversation = Conversation::findOrFail($id);

        $request->validate([
            'content' => 'required|string|max:1000',
        ]);

        $this->sendMessageToUser($conversation->external_id, $request->content);


        // 3. Lưu DB
        $conversation->messages()->create([
            'sender_type'   => 'admin',
            'message_type'  => 'text',
            'message_text'  => $request->content,
            'sent_at'       => now(),
        ]);

        $conversation->update([
            'last_message' => $request->content,
            'last_time'    => now(),
        ]);

        return redirect()
            ->route('admin.conversations.show', $conversation->id)
            ->with('success', 'Tin nhắn đã được gửi thành công.');
    }

    public function zaloCallback(Request $request)
    {
        $code = $request->input('code');
        if (!$code) {
            return response()->json(['error' => 'Missing code']);
        }

        $resp = Http::asForm()
            ->withHeaders([
                'secret_key'   => env('ZALO_SECRET_KEY'),
                'Content-Type' => 'application/x-www-form-urlencoded',
            ])
            ->post('https://oauth.zaloapp.com/v4/oa/access_token', [
                'app_id'     => env('ZALO_APP_ID'),
                'code'       => $code,
                'grant_type' => 'authorization_code',
            ]);

        $data = $resp->json();

        if (!empty($data['access_token'])) {
            ZaloToken::create([
                'access_token'  => $data['access_token'],
                'refresh_token' => $data['refresh_token'],
                'expired_at'    => now()->addSeconds($data['expires_in']),
            ]);
        }

        return response()->json($data);
    }


    private function getZaloAccessToken(): ?string
    {
        // Lấy token mới nhất trong DB
        $token = ZaloToken::latest()->first();

        // Nếu token còn hạn dùng thì xài luôn
        if ($token && $token->expired_at && now()->lt($token->expired_at)) {
            return $token->access_token;
        }

        $appId     = env('ZALO_APP_ID');
        $appSecret = env('ZALO_SECRET_KEY');
        $refreshToken = $token?->refresh_token ?? env('ZALO_OA_REFRESH_TOKEN');

        if (empty($refreshToken)) {
            Log::error('Missing Zalo OA refresh_token. Please set it from Dashboard.');
            return null;
        }

        $url = "https://oauth.zaloapp.com/v4/oa/access_token";

        try {
            $resp = Http::asForm()
                ->withHeaders([
                    'secret_key'   => $appSecret,
                    'Content-Type' => 'application/x-www-form-urlencoded',
                ])->post($url, [
                    'app_id'        => $appId,
                    'refresh_token' => $refreshToken,
                    'grant_type'    => 'refresh_token',
                ]);

            $data = $resp->json();

            if (!empty($data['access_token'])) {
                // Lưu DB token mới
                ZaloToken::updateOrCreate(
                    ['id' => $token?->id ?? 1],
                    [
                        'access_token'  => $data['access_token'],
                        'refresh_token' => $data['refresh_token'] ?? $refreshToken,
                        'expired_at'    => now()->addSeconds($data['expires_in'] ?? 3600),
                    ]
                );

                return $data['access_token'];
            }

            Log::error('Zalo getAccessToken failed', $data);
            return null;
        } catch (\Throwable $e) {
            Log::error('Zalo getAccessToken exception: ' . $e->getMessage());
            return null;
        }
    }


    private function sendMessageToUser($userId, string $message)
    {
        $accessToken = $this->getZaloAccessToken();
        $url = "https://openapi.zalo.me/v3.0/oa/message/cs";

        $payload = [
            "recipient" => [
                "user_id" => $userId
            ],
            "message" => [
                "text" => $message
            ]
        ];

        $client = new \GuzzleHttp\Client();

        try {
            $response = $client->post($url, [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'access_token' => $accessToken, // ✅ Đặt access_token trong header
                ],
                'json' => $payload,
            ]);

            $body = json_decode($response->getBody()->getContents(), true);
            return $body;
        } catch (\Exception $e) {
            Log::error("Send message error: " . $e->getMessage());
            return false;
        }
    }

    private function fetchRecentChatsFromZalo(int $offset = 0, int $count = 20): array
    {
        $accessToken = 'AGCn7B98zr4l13qRiqRoKYWAFHw_Nu8mSaCxB9P-zM41RtrKn23BPpC_Ucwg9h5CR3WPHumqeLXqOcj4icxPUt132cowKwTSKHuVLvLHjNPsNm1-imd9IL13OdkxUQvEUJKbHAmwYLDc83j6YssCJnL1D7h0K9vN6a0ZGl89ZM0E4GDGwWR8HYym1rp93Bz7AW83PxKJjqmhEGS4uW2n3WvFFq370VPmK0jOEia1zWm0Gbb4tctoRZexGLtdBkjjS1flNRKNnqfW0tHGa0gpPbK256-k6wjwOnPWORmggb9sFWLPcYIyH4mM46IhDDfqJNbIG8rg-qnEP7K3Zt3Y4LbIUpgTHS1qV7PGFub7z153I5eihM7YFKDxR0tKG_0IIsXw78TTiIqUQZXWq7ALB3DXA3l4SEGEUQzx_dYoLlnT'; // sử dụng hàm đã tạo để lấy token

        $response = Http::get('https://openapi.zalo.me/v2.0/oa/listrecentchat', [
            'access_token' => $accessToken,
            'offset' => $offset,
            'count' => $count,
        ]);

        if ($response->failed()) {
            dd('Lỗi khi gọi listrecentchat', ['response' => $response->body()]);
            return [];
        }

        $data = $response->json();
        if (!isset($data['data']['chats'])) {
            dd('Không có dữ liệu chats trả về', ['data' => $data]);
            return [];
        }

        return $data['data']['chats'];
    }

    public function syncRecentChats()
    {
        $chats = $this->fetchRecentChatsFromZalo(0, 50);

        foreach ($chats as $chat) {
            // Mỗi $chat có cấu trúc như: ['user_id' => ..., 'last_message' => ..., 'timestamp' => ...]
            $conversation = Conversation::updateOrCreate(
                ['platform' => 'zalo', 'external_id' => $chat['user_id']],
                [
                    'last_message' => $chat['last_message'],
                    'last_time' => Carbon::createFromTimestampMs($chat['timestamp']),
                ]
            );

            // Nếu muốn, bạn có thể thêm chi tiết tin nhắn cuối vào bảng messages
        }

        return response()->json(['status' => 'success', 'synced' => count($chats)]);
    }
}
