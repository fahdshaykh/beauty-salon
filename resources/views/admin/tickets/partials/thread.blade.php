@foreach($ticket->replies()->with('user')->oldest()->get() as $reply)
<div class="media mb-4 {{ !$loop->last ? 'border-bottom pb-4' : '' }}">
    <img src="{{ asset('admin/images/faces/face1.jpg') }}" class="mr-3 rounded-circle" width="50" alt="">
    <div class="media-body">
        <h6 class="mt-0">
            {{ $reply->user->name }}
            <small class="text-muted float-right">{{ $reply->created_at->diffForHumans() }}</small>
        </h6>
        <div class="bg-light p-3 rounded">
            {!! nl2br(e($reply->message)) !!}
        </div>
        @if($reply->attachments->count())
            <div class="mt-2">
                @foreach($reply->attachments as $att)
                    <a href="{{ Storage::url($att->file_path) }}" target="_blank" class="btn btn-sm btn-outline-secondary">
                        {{ $att->file_name }}
                    </a>
                @endforeach
            </div>
        @endif
    </div>
</div>
@endforeach