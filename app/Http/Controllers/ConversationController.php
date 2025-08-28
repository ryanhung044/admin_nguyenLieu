<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Http\Requests\StoreConversationRequest;
use App\Http\Requests\UpdateConversationRequest;
use App\Models\Message;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ConversationController extends Controller
{
    public function zalo(Request $request)
    {
        Log::info('Zalo Webhook', $request->all());

        $event = $request->input('event_name');
        $senderId = $request->input('sender.id');

        // Lấy hoặc tạo Conversation
        $conversation = Conversation::firstOrCreate(
            ['platform' => 'zalo', 'external_id' => $senderId],
            ['last_message' => '', 'last_time' => now()]
        );

        switch ($event) {
            case 'user_send_text':
                $text = $request->input('message.text');
                if ($text) {
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
                break;

            case 'user_send_image':
                $images = $request->input('message.attachments', []);
                foreach ($images as $img) {
                    $conversation->messages()->create([
                        'sender_type' => 'user',
                        'message_type' => 'image',
                        'message_text' => $img['payload']['url'] ?? null,
                        'sent_at' => now(),
                    ]);
                }
                $conversation->update([
                    'last_message' => '[Hình ảnh]',
                    'last_time' => now(),
                ]);
                break;

            case 'user_send_sticker':
                $sticker = $request->input('message.sticker_id');
                $conversation->messages()->create([
                    'sender_type' => 'user',
                    'message_type' => 'sticker',
                    'message_text' => $sticker,
                    'sent_at' => now(),
                ]);
                $conversation->update([
                    'last_message' => '[Sticker]',
                    'last_time' => now(),
                ]);
                break;

            case 'follow':
                // Người dùng vừa quan tâm OA
                $conversation->messages()->create([
                    'sender_type' => 'system',
                    'message_type' => 'event',
                    'message_text' => 'Người dùng đã quan tâm OA',
                    'sent_at' => now(),
                ]);
                $conversation->update([
                    'last_message' => 'Người dùng đã quan tâm OA',
                    'last_time' => now(),
                ]);
                break;

            case 'unfollow':
                $conversation->messages()->create([
                    'sender_type' => 'system',
                    'message_type' => 'event',
                    'message_text' => 'Người dùng bỏ quan tâm OA',
                    'sent_at' => now(),
                ]);
                $conversation->update([
                    'last_message' => 'Người dùng bỏ quan tâm OA',
                    'last_time' => now(),
                ]);
                break;

            default:
                Log::warning("Unhandled Zalo event: $event", $request->all());
                break;
        }

        return response()->json(['status' => 'ok']);
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

    /**
     * Gửi tin nhắn trong hội thoại
     */
    public function sendMessage(Request $request, $id)
    {
        $conversation = Conversation::findOrFail($id);

        $request->validate([
            'content' => 'required|string|max:1000',
        ]);

        // Lưu tin nhắn
        $message = new Message();
        $message->conversation_id = $conversation->id;
        $message->sender = 'admin'; // hoặc auth()->user()->id
        $message->content = $request->content;
        $message->save();

        // Cập nhật hội thoại
        $conversation->last_message = $request->content;
        $conversation->last_time = Carbon::now();
        $conversation->save();

        return redirect()->route('admin.conversations.show', $conversation->id)
            ->with('success', 'Tin nhắn đã được gửi.');
    }
}
