<?php

namespace App\Http\Controllers;

use App\Events\ChatMessageSent;
use App\Models\ChatRoom;
use App\Models\ChatMessage;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    public function index(Request $request)
    {
        $room = ChatRoom::firstOrCreate(
            ['slug' => 'global'],
            ['name' => 'Sala Global']
        );

        $messages = $room->messages()
            ->with('user')
            ->latest()
            ->take(30)
            ->get()
            ->reverse()
            ->values();

        return view('game.chat', [
            'room' => $room,
            'messages' => $messages,
        ]);
    }

    public function store(Request $request, ChatRoom $room)
    {
        $validated = $request->validate([
            'message' => ['required', 'string', 'max:500'],
        ]);

        $message = $room->messages()->create([
            'user_id' => $request->user()->id,
            'message' => $validated['message'],
        ]);

        $message->load('user');

        event(new ChatMessageSent($message));

        return response()->json($this->formatMessage($message));
    }

    private function formatMessage(ChatMessage $message): array
    {
        return [
            'id' => $message->id,
            'room_id' => $message->chat_room_id,
            'user' => [
                'id' => $message->user?->id,
                'name' => $message->user?->name ?? 'Usuario',
            ],
            'message' => $message->message,
            'created_at' => $message->created_at?->format('H:i') ?? now()->format('H:i'),
        ];
    }
}
