<?php

namespace App\Http\Controllers\Admin;

use App\Company;
use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\TicketReply;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminTicketController extends Controller
{
    public function index(Request $request)
    {
        $query = Ticket::with(['user', 'company', 'assignedTo', 'latestReply'])
            ->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }
        if ($request->filled('company_id')) {
            $query->where('company_id', $request->company_id);
        }

        $tickets = $query->paginate(20)->withQueryString();
        
        $companies = Company::pluck('company_name', 'id');

        return view('admin.tickets.index', compact('tickets', 'companies'));
    }

    public function show(Ticket $ticket)
    {
        $ticket->load('replies.user', 'replies.attachments', 'user', 'company');

        return view('admin.tickets.show', compact('ticket'));
    }

    public function reply(Request $request, Ticket $ticket)
    {
        $request->validate([
            'message' => 'required',
            'attachments.*' => 'file|mimes:jpg,jpeg,png,pdf,doc,docx,xls,xlsx,zip|max:10240'
        ]);

        $reply = $ticket->replies()->create([
            'user_id' => 0,//auth('admin')->id(),
            'message' => $request->message,
        ]);

        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('ticket-attachments', 'public');
                $reply->attachments()->create([
                    'file_name' => $file->getClientOriginalName(),
                    'file_path' => $path,
                    'file_size' => $file->getSize(),
                ]);
            }
        }

        // Update status & assignment if provided
        if ($request->filled('status')) {
            $ticket->update([
                'status' => $request->status,
                'assigned_to' => $request->assigned_to ?? $ticket->assigned_to
            ]);
        }

        return back()->with('success', 'Reply sent successfully!');
    }

    public function updateStatus(Request $request, Ticket $ticket)
    {

        $ticket->update([
            'status' => $request->status,
        ]);

        return back()->with('success', 'Ticket Status updated.');
    }
}
