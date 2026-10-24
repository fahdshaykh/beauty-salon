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

        // Base query with relationships
        $query = Ticket::with(['user', 'assignedTo', 'latestReply'])
                    ->where('company_id', $user->company_id);

        // Non-admin users can only see their own tickets
        if (!$user->hasRole('admin')) {
            $query->where('user_id', $user->id);
        }

        // Filter by Priority
        if ($request->filled('priority') && in_array($request->priority, ['low', 'medium', 'high', 'urgent'])) {
            $query->where('priority', $request->priority);
        }

        // Filter by Date Range (using created_at — most common for tickets)
        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        // Rows per page
        if ($request->filled('rows')) {
            $rows = max(5, min(500, (int)$request->rows));
            $user->setSettings(['tickets_rows_per_page' => $rows]);
        }
        $rows = $user->getSetting('tickets_rows_per_page', $user->default_rows ?? 15);

        // Execute query with pagination + preserve all filters
        $tickets = $query->latest('created_at')
                        ->paginate($rows)
                        ->withQueryString();

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

        return back()->with('success', 'Ticket created successfully!');
    }

    public function show(Ticket $ticket)
    {
        $ticket->load('replies.user', 'replies.attachments');
        return view('tickets.show', compact('ticket'));
    }

    public function reply(Request $request, Ticket $ticket)
    {
        $this->authorize('update', $ticket);

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
