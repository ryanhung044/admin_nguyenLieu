<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Http\Requests\StoreConversationRequest;
use App\Http\Requests\UpdateConversationRequest;
use App\Models\Message;
use Carbon\Carbon;
use Illuminate\Http\Request;
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

        $event = $request->input('event_name');

        // Luôn xác định userId khách (external_id)
        $userId = $request->input('sender.id')
            ?? $request->input('from.id')
            ?? $request->input('user_id_by_app');

        // Nếu không có userId (tức là event từ OA/admin), 
        // thì lấy từ conversation hiện tại (truy ngược theo message_id nếu có)
        if (!$userId) {
            $msgId = $request->input('message.msg_id');
            if ($msgId) {
                $conv = Message::where('platform', 'zalo')
                    ->where('message_id', $msgId)
                    ->first();
                $userId = $conv?->conversation?->external_id;
            }
        }

        // Nếu vẫn không có userId thì bỏ qua
        if (!$userId) {
            Log::warning("Webhook missing userId", $request->all());
            return response()->json(['status' => 'ignored']);
        }

        // external_id = userId khách
        $externalId = $userId;

        // conversation của user này (chỉ tạo mới nếu user nhắn lần đầu)
        $conversation = Conversation::firstOrCreate(
            ['platform' => 'zalo', 'external_id' => $externalId],
            ['user_id' => $externalId, 'last_message' => '', 'last_time' => now()]
        );

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
        $conversations = Conversation::orderByDesc('last_time')->paginate(10);

        return view('admin.conversations.index', compact('conversations'));
    }

    /**
     * Xem chi tiết hội thoại
     */
    public function show($id)
    {
        $conversation = Conversation::findOrFail($id);
        $messages = Message::where('conversation_id', $conversation->id)
            ->orderBy('created_at', 'asc')
            ->get();

        return view('admin.conversations.show', compact('conversation', 'messages'));
    }

    private function getZaloAccessToken()
    {
        $appId = env('APP_ID');
        $appSecret = env('ZALO_SECRET_KEY');
        $refreshToken = env('ZALO_OA_REFRESH_TOKEN'); // cái này bạn tạo 1 lần trên OA dev portal

        $response = Http::asForm()->post('https://oauth.zaloapp.com/v4/oa/access_token', [
            'app_id'        => $appId,
            'app_secret'    => $appSecret,
            'refresh_token' => $refreshToken,
            'grant_type'    => 'refresh_token',
        ]);

        if ($response->failed()) {
            throw new \Exception('Không lấy được Zalo Access Token: ' . $response->body());
        }

        $data = $response->json();
        return $data['access_token'] ?? null;
    }

    public function sendMessage(Request $request, $id)
    {
        $conversation = Conversation::findOrFail($id);

        $request->validate([
            'content' => 'required|string|max:1000',
        ]);

        // 1. Lấy access_token mới nhất
        try {
            $accessToken = $this->getZaloAccessToken();
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }

        // 2. Gửi API đến Zalo OA
        $response = Http::withToken($accessToken)
            ->post('https://openapi.zalo.me/v3.0/oa/message/cs', [
                'recipient' => [
                    'user_id' => $conversation->external_id,
                ],
                'message' => [
                    'text' => $request->content,
                ],
            ]);

        if ($response->failed()) {
            return back()->with('error', 'Gửi tin nhắn OA thất bại: ' . $response->body());
        }

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
