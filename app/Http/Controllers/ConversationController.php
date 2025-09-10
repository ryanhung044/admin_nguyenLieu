<?php

namespace App\Http\Controllers;

use App\Events\MessageCreated;
use App\Models\Conversation;
use App\Http\Requests\StoreConversationRequest;
use App\Http\Requests\UpdateConversationRequest;
use App\Models\Message;
use App\Models\User;
use App\Models\ZaloToken;
use Carbon\Carbon;
use GuzzleHttp\Client;
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

    // public function zalo(Request $request)
    // {
    //     Log::info('Zalo Webhook', $request->all());

    //     $event = (string)$request->input('event_name');

    //     // 1) Resolve external_id: luôn là ID của USER (không bao giờ là OA)
    //     $externalId = $this->resolveExternalUserId($request, $event);

    //     // Nếu chưa resolve được, bỏ qua để tránh tạo Conversation sai
    //     if (!$externalId) {
    //         Log::warning("Webhook cannot resolve external user id", $request->all());
    //         return response()->json(['status' => 'ignored']);
    //     }

    //     // 2) Dùng (platform, external_id) làm khóa duy nhất cho 1 cuộc chat
    //     //    -> Không bao giờ tạo conversation mới khi external_id trùng
    //     $conversation = Conversation::firstOrCreate(
    //         ['platform' => 'zalo', 'external_id' => $externalId],
    //         ['user_id' => $externalId, 'last_message' => '', 'last_time' => now()]
    //     );

    //     // 2) Kiểm tra trong DB đã có user với zalo_id này chưa
    //     $user = User::where('zalo_id', $externalId)->first();

    //     if (!$user) {
    //         // 🔹 Lấy access_token
    //         $accessToken = $this->getZaloAccessToken();

    //         // 🔹 API v3: lấy thông tin user
    //         $url = "https://openapi.zalo.me/v3.0/oa/user/getprofile";
    //         $response = Http::withHeaders([
    //             'Authorization' => "Bearer {$accessToken}",
    //         ])->post($url, [
    //             'user_id' => $externalId,
    //         ]);

    //         $data = $response->json();
    //         Log::info("Zalo v3 getprofile response", $data);

    //         $name   = $data['data']['display_name'] ?? 'Zalo User';
    //         $avatar = $data['data']['avatar'] ?? null;

    //         // 🔹 Tạo mới user
    //         $user = User::create([
    //             'name'      => $name,
    //             'full_name' => $name,
    //             'avatar'    => $avatar,
    //             'zalo_id'   => $externalId,
    //             'role'      => 'user',
    //         ]);
    //     }

    //     switch ($event) {
    //         /** ----------------
    //          *  USER SEND EVENTS
    //          * ---------------- */
    //         case 'user_send_text':
    //             $text = $request->input('message.text');
    //             $this->storeMessage($conversation, 'user', 'text', $text);
    //             break;

    //         case 'user_send_image':
    //             foreach ($request->input('message.attachments', []) as $img) {
    //                 $this->storeMessage($conversation, 'user', 'image', $img['payload']['url'] ?? '[Ảnh]');
    //             }
    //             break;

    //         case 'user_send_sticker':
    //             $sticker = $request->input('message.sticker_id');
    //             $this->storeMessage($conversation, 'user', 'sticker', "[Sticker:$sticker]");
    //             break;

    //         case 'user_send_gif':
    //             $this->storeMessage($conversation, 'user', 'gif', '[GIF]');
    //             break;

    //         case 'user_send_link':
    //             $link = $request->input('message.link');
    //             $this->storeMessage($conversation, 'user', 'link', $link);
    //             break;

    //         case 'user_send_location':
    //             $loc = $request->input('message.location.address');
    //             $this->storeMessage($conversation, 'user', 'location', $loc);
    //             break;

    //         /** ----------------
    //              *  USER ACTION EVENTS
    //              * ---------------- */
    //         case 'follow':
    //             $this->storeMessage($conversation, 'system', 'event', 'Người dùng đã quan tâm OA');
    //             break;

    //         case 'unfollow':
    //             $this->storeMessage($conversation, 'system', 'event', 'Người dùng bỏ quan tâm OA');
    //             break;

    //         case 'user_click_menu':
    //             $menuId = $request->input('menu.id');
    //             $this->storeMessage($conversation, 'user', 'event', "User click menu: $menuId");
    //             break;

    //         case 'reopen_chat':
    //             $this->storeMessage($conversation, 'system', 'event', "User chat lại sau một thời gian không hoạt động");
    //             break;

    //         /** ----------------
    //              *  OA SEND EVENTS
    //              * ---------------- */
    //         case 'oa_send_msg_result':
    //             $status = $request->input('status');
    //             $this->storeMessage($conversation, 'system', 'oa_send_status', "KQ gửi OA: $status");
    //             break;
    //         case 'oa_send_text':
    //             $text = $request->input('message.text');
    //             $conversation->messages()->create([
    //                 'sender_type' => 'admin',
    //                 'message_type' => 'text',
    //                 'message_text' => $text,
    //                 'sent_at'     => now(),
    //             ]);

    //             $conversation->update([
    //                 'last_message' => $text,
    //                 'last_time'    => now(),
    //             ]);
    //             break;
    //         case 'user_received_message':
    //             $msgId = $request->input('message.msg_id');
    //             $conversation->messages()->create([
    //                 'sender_type'  => 'system',
    //                 'message_type' => 'event',
    //                 'message_text' => "User đã nhận tin nhắn ID: $msgId",
    //                 'sent_at'      => now(),
    //             ]);

    //             $conversation->update([
    //                 'last_message' => '[User đã nhận tin]',
    //                 'last_time'    => now(),
    //             ]);
    //             break;


    //         case 'broadcast_result':
    //             $brId = $request->input('broadcast_id');
    //             $this->storeMessage($conversation, 'system', 'broadcast', "Kết quả broadcast: $brId");
    //             break;

    //         /** ----------------
    //              *  DELIVERY STATUS
    //              * ---------------- */
    //         case 'delivery':
    //             $this->storeMessage($conversation, 'system', 'event', 'Tin nhắn đã giao tới user');
    //             break;

    //         case 'seen':
    //             $this->storeMessage($conversation, 'system', 'event', 'Người dùng đã đọc tin nhắn');
    //             break;

    //         /** ----------------
    //              *  DEFAULT
    //              * ---------------- */
    //         default:
    //             Log::warning("Unhandled Zalo event: $event", $request->all());
    //             $this->storeMessage($conversation, 'system', 'event', "Sự kiện khác: $event");
    //             break;
    //     }

    //     return response()->json(['status' => 'ok']);
    // }
    public function zalo(Request $request)
    {
        // ✅ Luôn ép kiểu array hoặc log bằng JSON để tránh lỗi context null
        // Log::info('Zalo Webhook: ' . json_encode($request->all(), JSON_UNESCAPED_UNICODE));

        $event = (string)$request->input('event_name');

        // 1) Resolve external_id: luôn là ID của USER (không bao giờ là OA)
        $externalId = $this->resolveExternalUserId($request, $event);

        // Nếu chưa resolve được, bỏ qua để tránh tạo Conversation sai
        if (!$externalId) {
            Log::warning(
                "Webhook cannot resolve external user id: " . json_encode($request->all(), JSON_UNESCAPED_UNICODE)
            );
            return response()->json(['status' => 'ignored']);
        }

        // 2) Dùng (platform, external_id) làm khóa duy nhất cho 1 cuộc chat
        $conversation = Conversation::firstOrCreate(
            ['platform' => 'zalo', 'external_id' => $externalId],
            ['user_id' => $externalId, 'last_message' => '', 'last_time' => now()]
        );

        // 3) Kiểm tra trong DB đã có user với zalo_id này chưa
        $user = User::where('zalo_id', $externalId)->first();

        if (!$user) {
            // 🔹 Lấy access_token
            $accessToken = $this->getZaloAccessToken();

            $url = 'https://openapi.zalo.me/v3.0/oa/user/detail';

            $params = [
                'data' => json_encode(['user_id' => $externalId])
            ];

            $response = Http::withHeaders([
                'access_token' => $accessToken,
            ])->get($url, $params);

            // Debug log
            // Log::info("Zalo v3 getprofile raw: " . $response->body());
            // Log::info("Zalo v3 getprofile status: " . $response->status());

            $data = $response->json();
            // Log::info("Zalo v3 getprofile decoded: ", $data ?? []);

            if (($data['error'] ?? 1) === 0 && isset($data['data'])) {
                $profile = $data['data'];
                $name   = $profile['display_name'] ?? 'Zalo User';
                $avatar = $profile['avatar'] ?? null;
            } else {
                Log::warning('Zalo getprofile failed: ' . json_encode($data));
                $name   = 'Zalo User';
                $avatar = null;
            }

            // 🔹 Tạo mới user
            $user = User::updateOrCreate(
                ['zalo_id' => $externalId], // tránh duplicate
                [
                    'name'      => $name,
                    'full_name' => $name,
                    'avatar'    => $avatar,
                    'role'      => 'user',
                ]
            );
            Log::info('$user', $user);
        }

        switch ($event) {
            /** ----------------
             *  USER SEND EVENTS
             * ---------------- */
            case 'user_send_text':
                $text = $request->input('message.text');
                $msg = $this->storeMessage($conversation, 'user', 'text', $text);
                Log::info('$text' . $msg);
                // event(new MessageCreated($msg));
                break;

            case 'user_send_image':
                foreach ($request->input('message.attachments', []) as $img) {
                    $msg = $this->storeMessage(
                        $conversation,
                        'user',
                        'image',
                        $img['payload']['url'] ?? '[Ảnh]'
                    );
                    // event(new MessageCreated($msg));
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
                $this->storeMessage($conversation, 'admin', 'text', $text);
                break;

            case 'user_received_message':
                $msgId = $request->input('message.msg_id');
                $this->storeMessage($conversation, 'system', 'event', "User đã nhận tin nhắn ID: $msgId");
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
                Log::warning(
                    "Unhandled Zalo event: $event | Payload: " . json_encode($request->all(), JSON_UNESCAPED_UNICODE)
                );
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
        $message = $conversation->messages()->create([
            'sender_type'  => $senderType,
            'message_type' => $msgType,
            'message_text' => $msgText,
            'sent_at'      => now(),
            'conversation_id' => $conversation->id
        ]);

        $conversation->update([
            'last_message' => $msgText,
            'last_time'    => now(),
        ]);
        event(new MessageCreated($message));

        // return $message;
    }


    // Facebook webhook
    // public function facebookCallback(Request $request)
    // {
    //     $verifyToken = 'my_fb_wdfasdfasdfasdfebhook_secretdafsdfasasdfasdfasdfasdfsdffsdfuyjsfgt456gdfsg34';

    //     // 🔹 Bước 1: Verify khi cấu hình webhook
    //     if ($request->isMethod('get')) {
    //         if ($request->get('hub_verify_token') === $verifyToken) {
    //             return response($request->get('hub_challenge'), 200);
    //         }
    //         return response('Error, wrong validation token', 403);
    //     }

    //     // 🔹 Bước 2: Nhận event từ FB (POST)
    //     Log::info('Facebook Webhook', $request->all());

    //     if ($request->object === 'page') {
    //         foreach ($request->entry as $entry) {
    //             foreach ($entry['messaging'] ?? [] as $msg) {
    //                 $senderId = $msg['sender']['id'] ?? null;

    //                 // 1. Tin nhắn text
    //                 if (isset($msg['message']['text'])) {
    //                     $this->saveMessage($senderId, 'text', $msg['message']['text']);
    //                 }

    //                 // 2. Tin nhắn có đính kèm (ảnh, video, file...)
    //                 elseif (isset($msg['message']['attachments'])) {
    //                     foreach ($msg['message']['attachments'] as $attachment) {
    //                         $type = $attachment['type'] ?? 'file';
    //                         $url  = $attachment['payload']['url'] ?? json_encode($attachment);
    //                         $this->saveMessage($senderId, $type, $url);
    //                     }
    //                 }

    //                 // 3. Người dùng bấm nút (postback)
    //                 elseif (isset($msg['postback']['payload'])) {
    //                     $payload = $msg['postback']['payload'];
    //                     $this->saveMessage($senderId, 'postback', $payload);
    //                 }

    //                 // 4. Delivery report (xác nhận đã gửi đến user)
    //                 elseif (isset($msg['delivery'])) {
    //                     $delivery = json_encode($msg['delivery']);
    //                     $this->saveMessage($senderId, 'system', "Delivered: $delivery");
    //                 }

    //                 // 5. Read report (user đã đọc)
    //                 elseif (isset($msg['read'])) {
    //                     $read = json_encode($msg['read']);
    //                     $this->saveMessage($senderId, 'system', "Read: $read");
    //                 } else {
    //                     Log::info("⚠️ Event không xử lý", $msg);
    //                 }
    //             }
    //         }
    //     }

    //     return response()->json(['status' => 'ok']);
    // }

    public function facebookCallback(Request $request)
    {
        $verifyToken = 'my_fb_wdfasdfasdfasdfebhook_secretdafsdfasasdfasdfasdfasdfsdffsdfuyjsfgt456gdfsg34';

        // 🔹 Bước 1: Verify webhook
        if ($request->isMethod('get')) {
            if ($request->get('hub_verify_token') === $verifyToken) {
                return response($request->get('hub_challenge'), 200);
            }
            return response('Error, wrong validation token', 403);
        }

        Log::info('Facebook Webhook', $request->all());

        foreach ($request->entry ?? [] as $entry) {

            // 1️⃣ Messenger events
            foreach ($entry['messaging'] ?? [] as $msg) {
                $senderId = $msg['sender']['id'] ?? null;

                if (!$senderId) {
                    Log::warning('Cannot resolve senderId: ' . json_encode($msg));
                    continue;
                }

                // ✅ Conversation
                $conversation = Conversation::firstOrCreate(
                    ['platform' => 'facebook', 'external_id' => $senderId],
                    ['user_id' => $senderId, 'last_message' => '', 'last_time' => now()]
                );

                // ✅ User profile
                $user = User::where('zalo_id', $senderId)->first();
                if (!$user) {
                    $accessToken = config('services.facebook.page_access_token');
                    $url = "https://graph.facebook.com/v23.0/$senderId?fields=name,picture&access_token=$accessToken";
                    $response = Http::get($url)->json();
                    $name = $response['name'] ?? 'FB User';
                    $avatar = $response['picture']['data']['url'] ?? null;

                    $user = User::updateOrCreate(
                        ['zalo_id' => $senderId],
                        ['name' => $name, 'full_name' => $name, 'avatar' => $avatar, 'role' => 'user']
                    );
                }

                // ✅ Xử lý event messaging
                if (isset($msg['message'])) {

                    if (isset($msg['message']['text'])) {
                        $this->storeMessage($conversation, 'user', 'text', $msg['message']['text']);
                    }

                    if (isset($msg['message']['attachments'])) {
                        foreach ($msg['message']['attachments'] as $att) {
                            $type = $att['type'] ?? 'file';
                            $url  = $att['payload']['url'] ?? json_encode($att);
                            $this->storeMessage($conversation, 'user', $type, $url);
                        }
                    }

                    if (isset($msg['message']['is_echo'])) {
                        $this->storeMessage($conversation, 'system', 'echo', json_encode($msg['message']));
                    }

                    if (isset($msg['message']['reactions'])) {
                        $this->storeMessage($conversation, 'user', 'reaction', json_encode($msg['message']['reactions']));
                    }
                }

                if (isset($msg['message_context'])) {
                    $context = $msg['message_context'];

                    foreach ($context['detections'] ?? [] as $det) {
                        $type = $det['type'] ?? 'unknown';
                        $this->storeMessage($conversation, 'system', "message_context_detection_$type", json_encode($det));
                    }

                    foreach ($context['suggestions'] ?? [] as $sug) {
                        $type = $sug['type'] ?? 'unknown';
                        $this->storeMessage($conversation, 'system', "message_context_suggestion_$type", json_encode($sug));
                    }
                }

                if (isset($msg['postback'])) {
                    $payload = $msg['postback']['payload'] ?? '';
                    $this->storeMessage($conversation, 'user', 'postback', $payload);
                }

                if (isset($msg['referral'])) {
                    $this->storeMessage($conversation, 'user', 'referral', json_encode($msg['referral']));
                }

                if (isset($msg['delivery'])) {
                    $this->storeMessage($conversation, 'system', 'delivery', json_encode($msg['delivery']));
                }

                if (isset($msg['read'])) {
                    $this->storeMessage($conversation, 'system', 'read', json_encode($msg['read']));
                }

                if (isset($msg['account_linking'])) {
                    $this->storeMessage($conversation, 'system', 'account_linking', json_encode($msg['account_linking']));
                }
            }

            // 2️⃣ Page-level changes (leadgen, feed, members...)
            foreach ($entry['changes'] ?? [] as $change) {
                $field = $change['field'] ?? 'unknown';
                $value = $change['value'] ?? [];
                $this->storeMessage($conversation ?? null, 'system', $field, json_encode($value));
            }
        }

        return response()->json(['status' => 'ok']);
    }

    public function index()
    {
        $conversations = Conversation::with('user') // load kèm thông tin khách hàng
            ->withCount(['messages as unread_count' => function ($query) {
                $query->whereNull('admin_read_at')->where('sender_type', '!=', 'admin'); // hoặc cột đánh dấu đã đọc

            }])
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
        Message::where('conversation_id', $conversation->id)
            ->where('admin_read', false)
            ->update([
                'admin_read' => true,
                'admin_read_at' => now(),
            ]);

        return view('admin.conversations.show', compact('conversation', 'messages'));
    }


    // public function sendMessage(Request $request, $id)
    // {
    //     $conversation = Conversation::findOrFail($id);

    //     $request->validate([
    //         'type' => 'required|string|in:text,image,file,video,sticker',
    //         'content' => 'required'
    //     ]);
    //     $this->sendMessageToUser($conversation->user_id, $request->type, $request->content);
    //     // $this->sendMessageToUser($conversation->external_id,, $request->content);


    //     // // 3. Lưu DB
    //     // $conversation->messages()->create([
    //     //     'sender_type'   => 'admin',
    //     //     'message_type'  => 'text',
    //     //     'message_text'  => $request->content,
    //     //     'sent_at'       => now(),
    //     // ]);

    //     // $conversation->update([
    //     //     'last_message' => $request->content,
    //     //     'last_time'    => now(),
    //     // ]);

    //     return redirect()
    //         ->route('admin.conversations.show', $conversation->id)
    //         ->with('success', 'Tin nhắn đã được gửi thành công.');
    // }

    public function sendMessage(Request $request, $conversationId)
    {
        $conversation = Conversation::findOrFail($conversationId);
        $type = $request->input('type');
        $content = $request->file('content') ?? $request->input('content');

        if ($request->hasFile('content')) {
            // upload file lên server hoặc lấy URL public
            $path = $request->file('content')->store('uploads', 'public');
            $content = asset('storage/' . $path);
        }

        // gọi hàm gửi message lên Zalo
        $res = $this->sendMessageToUser($conversation->external_id, $type, $content);

        return response()->json(['success' => true, 'data' => $res]);
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
        $token = ZaloToken::orderBy('expired_at', 'desc')->first();

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


    // private function sendMessageToUser($userId, string $message)
    // {
    //     $accessToken = $this->getZaloAccessToken();
    //     $url = "https://openapi.zalo.me/v3.0/oa/message/cs";

    //     $payload = [
    //         "recipient" => [
    //             "user_id" => $userId
    //         ],
    //         "message" => [
    //             "text" => $message
    //         ]
    //     ];

    //     $client = new \GuzzleHttp\Client();

    //     try {
    //         $response = $client->post($url, [
    //             'headers' => [
    //                 'Content-Type' => 'application/json',
    //                 'access_token' => $accessToken, // ✅ Đặt access_token trong header
    //             ],
    //             'json' => $payload,
    //         ]);

    //         $body = json_decode($response->getBody()->getContents(), true);
    //         return $body;
    //     } catch (\Exception $e) {
    //         Log::error("Send message error: " . $e->getMessage());
    //         return false;
    //     }
    // }
    private function uploadFileToZalo($filePath, $type)
    {
        $accessToken = $this->getZaloAccessToken();
        $url = 'https://openapi.zalo.me/v2.0/oa/upload/file';

        $client = new \GuzzleHttp\Client();
        $response = $client->post($url, [
            'headers' => [
                'access_token' => $accessToken
            ],
            'multipart' => [
                [
                    'name' => 'file',
                    'contents' => fopen($filePath, 'r')
                ],
                [
                    'name' => 'type',
                    'contents' => $type // image/file/video
                ]
            ]
        ]);

        $body = json_decode($response->getBody()->getContents(), true);
        if (!empty($body['data']['url'])) {
            return $body['data']['url']; // URL public từ Zalo
        }
        throw new \Exception("Upload to Zalo failed: " . json_encode($body));
    }

    private function sendMessageToUser($userId, string $type, $content)
    {
        $accessToken = $this->getZaloAccessToken();
        $url = "https://openapi.zalo.me/v3.0/oa/message/cs";
        $message = [];

        if (in_array($type, ['image', 'file', 'video']) && $content instanceof \Illuminate\Http\UploadedFile) {
            $filePath = $content->getRealPath();
            $content = $this->uploadFileToZalo($filePath, $type); // lấy URL public từ Zalo
        }

        dd($content);
        switch ($type) {
            case 'text':
                $message['text'] = $content; // string
                break;

            case 'image':
            case 'file':
            case 'video':
                $message['attachment'] = [
                    'type' => $type,
                    'payload' => [
                        'url' => $content // $content là URL public
                    ]
                ];
                break;

            case 'sticker':
                $message['sticker'] = [
                    'sticker_id' => $content // $content là ID sticker
                ];
                break;

            default:
                throw new \Exception("Loại tin nhắn không hợp lệ: $type");
        }

        $payload = [
            "recipient" => [
                "user_id" => $userId
            ],
            "message" => $message
        ];



        $client = new \GuzzleHttp\Client();

        try {
            $response = $client->post($url, [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'access_token' => $accessToken,
                ],
                'json' => $payload,
            ]);

            return json_decode($response->getBody()->getContents(), true);
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


    // --------------------------
    // 1) Đồng bộ danh sách user đang chat
    // --------------------------
    public function syncConversations()
    {
        $accessToken = 'Zjp93so6tqo8syK8ROZQJu_LnGrXaTC2zAh97b2lg3-seV8d2O2a6vJVoIOfuxSpbBcf4WgsmrtbcQf1TyJ0HTJYWr5QfTuHmvYY52U1l0Q_lge2NRQo9kwftJPphBmQwlNcNdFqfsJ7shiZNz-a3UN-s243vAOxk-V7EbZ_hG39qlT0VV6pR9xSqr4dvgvKYRpKK3AkiNMZziyx8gZGBgENXm8oYiKvrPRV1aR5iZV6nguAMztGEkYEg0fStD0rikUT9pJPn66fuQDZKkB3Pjxzedb-eCXRuglUIbQ9c0-qYkyeDgssEfY4t1OggP1NduJL6NZSk2APsCa6AjsD9PA1opyRW9qvZDd61ZN1WnUCwUe90_gA1kVK_WXYqxWuW83d2WE8k3IYb-SW0gtmFEprYm0Chv50QNhMQYvsR9ZKG0';
        // dd($accessToken);
        if (!$accessToken) return response()->json(['error' => 'Cannot get access token'], 500);

        $url = 'https://openapi.zalo.me/v3.0/oa/conversations';

        $resp = Http::get($url, ['access_token' => $accessToken]);
        $data = $resp->json();

        if (($data['error'] ?? 1) !== 0) {
            Log::info('Zalo getConversations response', [
                'status' => $resp->status(),
                'body'   => $resp->body(),
            ]);

            return response()->json(['error' => 'Failed to get conversations']);
        }

        $conversations = $data['data'] ?? [];

        foreach ($conversations as $conv) {
            $externalId = $conv['user_id'];

            Conversation::firstOrCreate(
                ['platform' => 'zalo', 'external_id' => $externalId],
                ['last_message' => '', 'last_time' => now()]
            );

            // Tạo user nếu chưa có
            $user = User::firstOrCreate(
                ['zalo_id' => $externalId],
                ['name' => 'Zalo User', 'role' => 'user']
            );
        }

        return response()->json(['message' => 'Conversations synced', 'count' => count($conversations)]);
    }

    // --------------------------
    // 2) Đồng bộ tin nhắn của user
    // --------------------------
    public function syncMessages($userId)
    {
        $accessToken = $this->getZaloAccessToken();
        if (!$accessToken) return response()->json(['error' => 'Cannot get access token'], 500);

        $conversation = Conversation::where('platform', 'zalo')->where('external_id', $userId)->first();
        if (!$conversation) return response()->json(['error' => 'Conversation not found'], 404);

        $offset = 0;
        do {
            $url = 'https://openapi.zalo.me/v3.0/oa/getmessages';
            $resp = Http::get($url, [
                'access_token' => $accessToken,
                'user_id'      => $userId,
                'offset'       => $offset,
                'count'        => 20,
            ]);

            $data = $resp->json();
            if (($data['error'] ?? 1) !== 0) {
                Log::info('Zalo getMessages failed' . $data);
                Log::info('Zalo getMessages response', [
                    'status' => $resp->status(),
                    'body'   => $resp->body(),
                ]);
                return response()->json(['error' => 'Failed to get messages']);
            }

            $messages = $data['data'] ?? [];

            foreach ($messages as $msg) {
                $conversation->messages()->firstOrCreate(
                    ['message_id' => $msg['message_id']],
                    [
                        'sender_type'  => $msg['sender'], // user/oa
                        'message_type' => 'text',         // có thể detect type khác nếu cần
                        'message_text' => $msg['text'] ?? '[Không có text]',
                        'sent_at'      => isset($msg['timestamp']) ? date('Y-m-d H:i:s', $msg['timestamp']) : now(),
                    ]
                );
            }

            $offset += 20;
        } while (count($messages) == 20);

        return response()->json(['message' => 'Messages synced', 'count' => count($messages)]);
    }
}
