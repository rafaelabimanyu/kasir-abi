<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class MessageController extends Controller
{
    /**
     * Tampilkan halaman chat.
     */
    public function index()
    {
        $users = User::where('id', '!=', (int) Auth::id(), 'and')
            ->select('id', 'name', 'email', 'role', 'last_activity_at')
            ->orderBy('name')
            ->get();

        return view('pages.chat', compact('users'));
    }

    /**
     * Ambil 50 pesan terakhir antara dua user + mark as read.
     * Security: hanya bisa akses percakapan sendiri.
     */
    public function fetchMessages(int $user_id)
    {
        User::findOrFail($user_id);

        $currentUserId = Auth::id();

        $messages = Message::where(function ($q) use ($currentUserId, $user_id) {
                $q->where('sender_id', '=', (int) $currentUserId, 'and')->where('receiver_id', '=', (int) $user_id, 'and');
            })
            ->orWhere(function ($q) use ($currentUserId, $user_id) {
                $q->where('sender_id', '=', (int) $user_id, 'and')->where('receiver_id', '=', (int) $currentUserId, 'and');
            })
            ->latest()
            ->limit(50)
            ->get()
            ->sortBy('created_at')
            ->values();

        // Mark messages from partner as read
        Message::where('sender_id', '=', (int) $user_id, 'and')
            ->where('receiver_id', '=', (int) $currentUserId, 'and')
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return response()->json($messages);
    }

    /**
     * Combined poll endpoint:
     * Returns messages + typing status + unread counts + online statuses.
     * Called every 3 seconds from the active chat.
     */
    public function poll(int $userId)
    {
        User::findOrFail($userId);

        $currentUserId = Auth::id();
        $twoMinutesAgo = now()->subMinutes(2);

        // Messages
        $messages = Message::where(function ($q) use ($currentUserId, $userId) {
                $q->where('sender_id', '=', (int) $currentUserId, 'and')->where('receiver_id', '=', (int) $userId, 'and');
            })
            ->orWhere(function ($q) use ($currentUserId, $userId) {
                $q->where('sender_id', '=', (int) $userId, 'and')->where('receiver_id', '=', (int) $currentUserId, 'and');
            })
            ->latest()
            ->limit(50)
            ->get()
            ->sortBy('created_at')
            ->values();

        // Mark as read
        Message::where('sender_id', '=', (int) $userId, 'and')
            ->where('receiver_id', '=', (int) $currentUserId, 'and')
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        // Typing indicator
        $partnerTyping = (bool) Cache::get("typing_{$userId}_to_{$currentUserId}");

        // Unread counts per sender (for sidebar badges)
        $unreadCounts = Message::where('receiver_id', '=', (int) $currentUserId, 'and')
            ->whereNull('read_at')
            ->groupBy('sender_id')
            ->selectRaw('sender_id, COUNT(*) as count')
            ->pluck('count', 'sender_id');

        // Online statuses for all users
        $onlineStatus = User::where('id', '!=', (int) $currentUserId, 'and')
            ->select('id', 'last_activity_at')
            ->get()
            ->mapWithKeys(fn ($u) => [
                $u->id => $u->last_activity_at && $u->last_activity_at->gt($twoMinutesAgo)
            ]);

        return response()->json([
            'messages'       => $messages,
            'partner_typing' => $partnerTyping,
            'unread_counts'  => $unreadCounts,
            'online_status'  => $onlineStatus,
        ]);
    }

    /**
     * Status endpoint: unread counts + online statuses.
     * Called every 10 seconds when no active conversation.
     */
    public function getStatus()
    {
        $currentUserId = Auth::id();
        $twoMinutesAgo = now()->subMinutes(2);

        $onlineStatus = User::where('id', '!=', (int) $currentUserId, 'and')
            ->select('id', 'last_activity_at')
            ->get()
            ->mapWithKeys(fn ($u) => [
                $u->id => $u->last_activity_at && $u->last_activity_at->gt($twoMinutesAgo)
            ]);

        $unreadCounts = Message::where('receiver_id', '=', (int) $currentUserId, 'and')
            ->whereNull('read_at')
            ->groupBy('sender_id')
            ->selectRaw('sender_id, COUNT(*) as count')
            ->pluck('count', 'sender_id');

        return response()->json([
            'online_status' => $onlineStatus,
            'unread_counts' => $unreadCounts,
            'total_unread'  => $unreadCounts->sum(),
        ]);
    }

    /**
     * Set typing indicator in cache (expires in 4 seconds).
     */
    public function setTyping(Request $request)
    {
        $validated = $request->validate([
            'receiver_id' => 'required|integer|exists:users,id',
        ]);

        Cache::put(
            'typing_' . Auth::id() . '_to_' . $validated['receiver_id'],
            Auth::user()->name,
            now()->addSeconds(4)
        );

        return response()->json(['ok' => true]);
    }

    /**
     * Kirim pesan baru (dengan dukungan lampiran).
     */
    public function sendMessage(Request $request)
    {
        $validated = $request->validate([
            'receiver_id' => 'required|integer|exists:users,id',
            'message'     => 'nullable|string|max:1000',
            'attachment'  => 'nullable|file|max:20480', // 20MB max
        ]);

        if (!$request->filled('message') && !$request->hasFile('attachment')) {
            return response()->json(['message' => 'Pesan atau lampiran harus diisi.'], 422);
        }

        if ((int) $validated['receiver_id'] === Auth::id()) {
            return response()->json(['message' => 'Tidak bisa mengirim pesan ke diri sendiri.'], 422);
        }

        $attachmentPath = null;
        $attachmentType = null;

        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $mime = $file->getMimeType();
            
            if (str_contains($mime, 'image')) {
                $attachmentType = 'image';
            } elseif (str_contains($mime, 'video')) {
                $attachmentType = 'video';
            } else {
                $attachmentType = 'file';
            }

            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('attachments'), $filename);
            $attachmentPath = 'attachments/' . $filename;
        }

        $message = Message::create([
            'sender_id'       => Auth::id(),
            'receiver_id'     => $validated['receiver_id'],
            'message'         => $validated['message'] ?? '',
            'attachment_path' => $attachmentPath,
            'attachment_type' => $attachmentType,
        ]);

        return response()->json($message, 201);
    }
}
