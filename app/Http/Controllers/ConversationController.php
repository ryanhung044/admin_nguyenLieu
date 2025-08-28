<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Http\Requests\StoreConversationRequest;
use App\Http\Requests\UpdateConversationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ConversationController extends Controller
{
    public function zalo(Request $request)
    {
        Log::info('Zalo Webhook', $request->all());

        $event = $request->input('event_name');
        $senderId = $request->input('sender.id');
        $text = $request->input('message.text');

        if ($event === 'user_send_text' && $text) {
            $conversation = Conversation::firstOrCreate(
                ['platform' => 'zalo', 'external_id' => $senderId],
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

}
