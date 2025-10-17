<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = auth()->user()->notifications()->latest()->paginate(10);
        return view('notifications', compact('notifications'));
    }

    // Mark a notification as read
    public function markAsRead($id)
    {
        $notification = auth()->user()->notifications()->find($id);
        if ($notification) {
            $notification->markAsRead();
        }
        return redirect()->route('notifications.all')->with('success', 'Notification marked as read.');
    }
}
