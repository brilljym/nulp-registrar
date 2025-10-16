@php
    $current = $onsiteRequest->current_step;
@endphp

<div class="card shadow-sm" style="max-width:820px; margin:0 auto;">
    <div class="card-body">
        {{-- START --}}
        @if($current === 'start')
            <h5 class="mb-3">Start your request</h5>
            <p>To begin, please provide your details.</p>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#startModal">Begin</button>
        @endif

        {{-- PAYMENT --}}
        @if($current === 'payment')
            <h5 class="mb-2">Payment</h5>
            <p>Please enter your reference code below. Document price: <strong>₱{{ number_format($onsiteRequest->document->price ?? 0,2) }}</strong></p>
            <form method="POST" action="{{ route('onsite.reference.submit') }}">
                @csrf
                <input type="hidden" name="onsite_id" value="{{ $onsiteRequest->id }}">
                <div class="row justify-content-center">
                    <div class="col-md-6 mb-3">
                        <input type="text" name="ref_code" class="form-control" placeholder="Enter reference code" required>
                    </div>
                    <div class="col-md-2 mb-3">
                        <button class="btn btn-success">Submit</button>
                    </div>
                </div>
            </form>
        @endif

        {{-- WINDOW --}}
        @if($current === 'window')
            <h5 class="mb-2">Assigned to Window</h5>
            <p>Window: <strong>{{ $onsiteRequest->window->name ?? 'TBD' }}</strong></p>
            <p>Queue position: <strong>{{ $onsiteRequest->queue_position ?? 'N/A' }}</strong></p>
            <p>Ticket Number: <strong>{{ $ticketNumber }}</strong></p>
        @endif

        {{-- PROCESSING --}}
        @if($current === 'processing')
            <h5 class="mb-2">Processing</h5>
            <p>Assigned Registrar: <strong>{{ $onsiteRequest->registrar->first_name ?? 'Registrar' }} {{ $onsiteRequest->registrar->last_name ?? '' }}</strong></p>
            <p>Ticket Number: <strong>{{ $ticketNumber }}</strong></p>
        @endif

        {{-- RELEASE --}}
        @if($current === 'release')
            <h5 class="mb-2">Ready</h5>
            <p>Your document is ready for pickup at: <strong>{{ $onsiteRequest->window->name ?? 'Window' }}</strong></p>
            <p>Ticket Number: <strong>{{ $ticketNumber }}</strong></p>
        @endif

        {{-- COMPLETED --}}
        @if($current === 'completed')
            <h5 class="mb-2 text-success">Request Completed</h5>
            <p>Your request has been completed successfully.</p>
            
            {{-- Feedback Section --}}
            @if(isset($onsiteRequest) && $onsiteRequest->feedback)
                <div class="alert alert-info mt-3">
                    <i class="bi bi-heart-fill me-2"></i>Thank you for your feedback! 
                    Rating: 
                    @for($i = 1; $i <= $onsiteRequest->feedback->rating; $i++)
                        <span class="text-warning">⭐</span>
                    @endfor
                </div>
            @elseif(isset($onsiteRequest))
                <div class="mt-3">
                    <a href="{{ route('onsite.feedback.show', $onsiteRequest->id) }}" class="btn btn-outline-primary btn-sm">
                        <i class="bi bi-chat-heart me-2"></i>Provide Feedback (Optional)
                    </a>
                </div>
            @endif
            
            <p id="countdown">Redirecting to login in 3...</p>
        @endif
    </div>
</div>

{{-- START Modal --}}
<div class="modal fade" id="startModal" tabindex="-1" aria-labelledby="startModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form method="POST" action="{{ route('onsite.store') }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="startModalLabel">Begin On-site Request</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Full Name</label>
                        <input type="text" name="full_name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Student ID</label>
                        <input type="text" name="student_id" class="form-control" required>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Course</label>
                            <input type="text" name="course" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Year Level</label>
                            <input type="text" name="year_level" class="form-control" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Department</label>
                        <input type="text" name="department" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Start Request</button>
                </div>
            </form>
        </div>
    </div>
</div>
