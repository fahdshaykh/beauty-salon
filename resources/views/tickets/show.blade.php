@extends('layouts.admin')
@section('title', $ticket->ticket_id . ' | Support Ticket')

@section('content')
<div class="main-panel">
    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title">
                #{{ $ticket->ticket_id }} — {{ $ticket->subject }}
            </h3>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('tickets.index') }}">Tickets</a></li>
                    <li class="breadcrumb-item active">#{{ $ticket->ticket_id }}</li>
                </ol>
            </nav>
        </div>

        <div class="row">
            <!-- Ticket Info Sidebar -->
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h5>Ticket Information</h5>
                        <hr>

                        <p><strong>Status:</strong>
                            <span class="badge badge-{{ $ticket->status == 'open' ? 'warning' : ($ticket->status == 'resolved' ? 'success' : 'secondary') }}">
                                {{ ucfirst(str_replace('_', ' ', $ticket->status)) }}
                            </span>
                        </p>

                        <p><strong>Priority:</strong>
                            <span class="badge badge-{{ $ticket->priority == 'urgent' ? 'danger' : ($ticket->priority == 'high' ? 'warning' : 'info') }}">
                                {{ ucfirst($ticket->priority) }}
                            </span>
                        </p>

                        <p><strong>Opened By:</strong> {{ $ticket->user->name }}</p>
                        <p><strong>Created:</strong> {{ $ticket->created_at->format('d M Y, h:i A') }}</p>
                        <p><strong>Last Update:</strong> {{ $ticket->updated_at->diffForHumans() }}</p>

                        @if($ticket->assignedTo)
                            <p><strong>Assigned To:</strong> {{ $ticket->assignedTo->name }}</p>
                        @endif

                        <!-- Admin: Quick Status & Assign -->
                        
                        <hr>
                        <form action="{{ route('tickets.updateStatus', $ticket) }}" method="POST">
                            @csrf @method('PATCH')
                            <div class="form-group">
                                <label>Change Status</label>
                                <select name="status" class="form-control" onchange="this.form.submit()">
                                    <option value="open" {{ $ticket->status == 'open' ? 'selected' : '' }}>Open</option>
                                    <option value="in_progress" {{ $ticket->status == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                    <option value="resolved" {{ $ticket->status == 'resolved' ? 'selected' : '' }}>Resolved</option>
                                    <option value="closed" {{ $ticket->status == 'closed' ? 'selected' : '' }}>Closed</option>
                                </select>
                            </div>
                        </form>
                        
                    </div>
                </div>
            </div>

            <!-- Conversation -->
            <div class="col-md-8">
                <div class="card">
                    <div class="card-body">
                        <!-- Original Message -->
                        <div class="media mb-4 pb-4 border-bottom">
                            <img src="{{ $ticket->user->avatar ?? asset('images/faces/face1.jpg') }}" class="mr-3 rounded-circle" width="50" alt="user">
                            <div class="media-body">
                                <h5 class="mt-0">{{ $ticket->user->name }} <small class="text-muted">{{ $ticket->created_at->format('d M Y h:i A') }}</small></h5>
                                <p class="mb-2"><strong>Priority:</strong> {{ ucfirst($ticket->priority) }}</p>
                                <div class="bg-light p-3 rounded">
                                    {!! nl2br(e($ticket->description)) !!}
                                </div>
                            </div>
                        </div>

                        <!-- Replies -->
                        @forelse($ticket->replies as $reply)
                            <div class="media mb-4 {{ $loop->last ? '' : 'pb-4 border-bottom' }}">
                                @if(optional($reply->user)->name)
                                <img src="{{ asset('images/faces/face1.jpg') }}" class="mr-3 rounded-circle" width="50" alt="user">
                                @else
                                <img src="{{ $reply->user->image ?? asset('images/faces/face2.jpg') }}" class="mr-3 rounded-circle" width="50" alt="user">
                                @endif
                                <div class="media-body">
                                    <h6 class="mt-0">
                                        {{ $reply->user->name ?? 'Super Admin' }}
                                        <small class="text-muted float-right">{{ $reply->created_at->diffForHumans() }}</small>
                                    </h6>
                                    <div class="bg-light p-3 rounded">
                                        {!! nl2br(e($reply->message)) !!}
                                    </div>

                                    @if($reply->attachments->count() > 0)
                                        <div class="mt-3">
                                            <strong>Attachments:</strong><br>
                                            @foreach($reply->attachments as $attach)
                                                <a href="{{ Storage::url($attach->file_path) }}" target="_blank" class="btn btn-sm btn-outline-secondary mt-1">
                                                    <i class="fas fa-paperclip"></i> {{ $attach->file_name }}
                                                </a>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <p class="text-center text-muted">No replies yet. Be the first to respond!</p>
                        @endforelse
                    </div>
                </div>

                <!-- Reply Form -->
                <div class="card mt-4">
                    <div class="card-body">
                        <h5>Post a Reply</h5>
                        <form action="{{ route('tickets.reply', $ticket) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="form-group">
                                <textarea name="message" rows="5" class="form-control" placeholder="Type your reply here..." required></textarea>
                            </div>

                            <div class="form-group">
                                <input type="file" name="attachments[]" multiple class="form-control file-upload-info"
                                       accept=".jpg,.jpeg,.png,.pdf,.doc,.docx,.xls,.xlsx,.zip">
                                <small class="text-muted">Attach files (optional)</small>
                            </div>

                            
                            <div class="form-group">
                                <label>Change Status (optional)</label>
                                <select name="status" class="form-control">
                                    <option value="">No change</option>
                                    <option value="in_progress">In Progress</option>
                                    <option value="resolved">Resolved</option>
                                    <option value="closed">Closed</option>
                                </select>
                            </div>

                            <button type="submit" class="btn btn-primary">Send Reply</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection