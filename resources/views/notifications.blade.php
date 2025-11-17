@extends('layouts.admin')

@section('title', 'Notifications | '.Session::get('site_name'))

@section('content')

<div class="main-panel">
    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title">All Notifications</h3>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{url('/home')}}">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Notifications Listing</li>
                </ol>
            </nav>
        </div>

        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Notifications</h4>

                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Message</th>
                                <th>Received</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($notifications as $notification)
                                <tr>
                                    <td>{{ $notification->data['message'] }}</td>
                                    <td>{{ $notification->created_at->diffForHumans() }}</td>
                                    <td>
                                        @if(is_null($notification->read_at))
                                            <form method="POST" action="{{ route('notifications.markAsRead', $notification->id) }}">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="btn btn-sm btn-success">Mark as Read</button>
                                            </form>
                                        @else
                                            <span class="badge badge-secondary">Read</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center text-muted">No new notifications</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="mt-3">
                    {{ $notifications->links() }}
                </div>

            </div>
        </div>

    </div>
</div>

@endsection
