<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\TicketReply;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TicketController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Ticket::class, 'ticket');
    }

    public function index(Request $request)
    {
        $user = Auth::user();

        $query = Ticket::with(['user', 'assignedTo', 'latestReply'])
            ->where('company_id', $user->company_id);

        if (!$user->hasRole('admin')) {
            $query->where('user_id', $user->id);
        }

        // get rows per page if passed with request
        if (request('rows')) {
            $rows = 500 < intval(request('rows')) ? 500 : intval(request('rows'));
            $rows = 5 > $rows ? 5 : $rows;
            Auth::user()->setSettings(['tickets_rows_per_page' => $rows]);
        }
        // otherwise retrieve from user's settings
        $rows = Auth::user()->getSetting('tickets_rows_per_page', auth()->user()->default_rows);

        $tickets = $query->latest()->paginate($rows);

        return view('tickets.index', compact('tickets', 'rows'));
    }

    public function create()
    {
        return view('tickets.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'subject' => 'required|max:255',
            'description' => 'required',
            'priority' => 'required|in:low,medium,high,urgent',
        ]);

        Ticket::create([
            'user_id' => Auth::id(),
            'company_id' => Auth::user()->company_id,
            'subject' => $request->subject,
            'description' => $request->description,
            'priority' => $request->priority,
            'status' => 'open',
        ]);

        return redirect()->route('tickets.index')
            ->with('success', 'Ticket created successfully!');
    }

    public function show(Ticket $ticket)
    {
        $ticket->load('replies.user', 'replies.attachments');
        return view('tickets.show', compact('ticket'));
    }

    public function reply(Request $request, Ticket $ticket)
    {
        $request->validate([
            'message' => 'required',
            'attachments.*' => 'file|mimes:jpg,png,pdf,doc,docx,xls,xlsx,zip|max:10240'
        ]);

        $reply = TicketReply::create([
            'ticket_id' => $ticket->id,
            'user_id' => Auth::id(),
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

        // Optional: update status
        if (Auth::user()->hasRole('admin') && $request->status) {
            $ticket->update(['status' => $request->status]);
        }

        return back()->with('success', 'Reply added!');
    }

    public function updateStatus(Request $request, Ticket $ticket)
    {
        $this->authorize('update', $ticket);

        $ticket->update([
            'status' => $request->status,
            'assigned_to' => $request->assigned_to ?? $ticket->assigned_to
        ]);

        return back()->with('success', 'Ticket updated.');
    }
}
