@extends('admin.layouts.admin')
@section('title', 'Support Tickets')

@section('content')
<div class="main-panel">
    <div class="content-wrapper">
        <div class="page-header">
            <h3>All Tickets</h3>
        </div>

        <div class="card">
            <div class="card-body">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Ticket ID</th>
                            <th>Company name</th>
                            <th>Subject</th>
                            <th>Status</th>
                            <th>Priority</th>
                            <th>Created</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($tickets as $ticket)
                        <tr>
                            <td><a href="{{ route('admin.tickets.show', $ticket) }}">{{ $ticket->ticket_id }}</a></td>
                            <td>{{ $ticket->company->company_name ?? 'N/A' }}</td>
                            <td>{{ Str::limit($ticket->subject, 40) }}</td>
                            <td><span class="badge badge-{{ $ticket->status === 'open' ? 'warning' : 'success' }}">{{ ucfirst($ticket->status) }}</span></td>
                            <td><span class="badge badge-{{ $ticket->priority === 'urgent' ? 'danger' : 'info' }}">{{ ucfirst($ticket->priority) }}</span></td>
                            <td>{{ $ticket->created_at->format('d M Y') }}</td>
                            <td>
                                <a href="{{ route('admin.tickets.show', $ticket) }}" class="btn btn-sm btn-info">View</a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                {{ $tickets->links() }}
            </div>
        </div>
    </div>
</div>
@endsection