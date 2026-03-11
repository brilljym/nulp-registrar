<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Service Feedback - NU Lipa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --nu-blue: #003399;
            --nu-yellow: #FFD700;
            --nu-white: #ffffff;
            --nu-gray: #6c757d;
            --nu-light-gray: #f8f9fa;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, var(--nu-blue) 0%, #0056cc 100%);
            min-height: 100vh;
            color: #333;
        }

        .feedback-container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            padding: 3rem;
            margin: 2rem auto;
            max-width: 600px;
        }

        .rating-stars {
            display: flex;
            justify-content: center;
            gap: 0.5rem;
            margin: 2rem 0;
        }

        .star {
            font-size: 2.5rem;
            color: #ddd;
            cursor: pointer;
            transition: color 0.3s ease;
        }

        .star.active {
            color: #ffc107;
        }

        .star:hover {
            color: #ffc107;
        }

        .btn-primary {
            background: var(--nu-blue);
            border-color: var(--nu-blue);
            padding: 0.75rem 2rem;
            font-weight: 600;
            border-radius: 10px;
        }

        .btn-secondary {
            background: var(--nu-gray);
            border-color: var(--nu-gray);
            padding: 0.75rem 2rem;
            font-weight: 600;
            border-radius: 10px;
        }

        .form-control {
            border-radius: 10px;
            border: 2px solid #e9ecef;
            padding: 0.75rem 1rem;
        }

        .form-control:focus {
            border-color: var(--nu-blue);
            box-shadow: 0 0 0 0.2rem rgba(0, 51, 153, 0.25);
        }

        .ticket-info {
            background: var(--nu-light-gray);
            padding: 1rem;
            border-radius: 10px;
            margin-bottom: 2rem;
            border-left: 4px solid var(--nu-blue);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="feedback-container">
            <div class="text-center mb-4">
                <i class="bi bi-chat-heart text-primary" style="font-size: 3rem;"></i>
                <h2 class="mt-3">Service Feedback</h2>
                <p class="text-muted">Help us improve our services by sharing your experience</p>
            </div>

            {{-- Request Info --}}
            <div class="ticket-info">
                <div class="row">
                    <div class="col-md-6">
                        <strong>Name:</strong> {{ $onsiteRequest->full_name }}<br>
                        <strong>Document:</strong> {{ $onsiteRequest->document->name ?? 'N/A' }}<br>
                    </div>
                    <div class="col-md-6">
                        <strong>Completed:</strong> {{ $onsiteRequest->updated_at->format('M d, Y H:i') }}<br>
                        <strong>Reference:</strong> {{ $onsiteRequest->ref_code ?? 'N/A' }}
                    </div>
                </div>
            </div>

            {{-- Flash Messages --}}
            @if(session('success'))
                <div class="alert alert-success">
                    <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger">
                    <i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}
                </div>
            @endif

            @if(session('info'))
                <div class="alert alert-info">
                    <i class="bi bi-info-circle me-2"></i>{{ session('info') }}
                </div>
            @endif

            {{-- Feedback Form --}}
            <form method="POST" action="{{ route('onsite.feedback.store', $onsiteRequest->id) }}">
                @csrf
                
                <div class="mb-4">
                    <label class="form-label fw-bold">How would you rate your overall experience?</label>
                    <div class="rating-stars">
                        @for($i = 1; $i <= 5; $i++)
                            <span class="star" data-rating="{{ $i }}">⭐</span>
                        @endfor
                    </div>
                    <input type="hidden" name="rating" id="rating" value="">
                    @error('rating')
                        <div class="text-danger small">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="comment" class="form-label fw-bold">Additional Comments <small class="text-muted">(Optional)</small></label>
                    <textarea 
                        name="comment" 
                        id="comment" 
                        class="form-control" 
                        rows="4" 
                        placeholder="Share any specific feedback about our service, staff, or process..."
                        maxlength="1000">{{ old('comment') }}</textarea>
                    <small class="form-text text-muted">Maximum 1000 characters</small>
                    @error('comment')
                        <div class="text-danger small">{{ $message }}</div>
                    @enderror
                </div>

                <div class="d-flex justify-content-between">
                    <a href="{{ route('onsite.timeline', $onsiteRequest->id) }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-left me-2"></i>Back to Timeline
                    </a>
                    <button type="submit" class="btn btn-primary" id="submitBtn" disabled>
                        <i class="bi bi-send me-2"></i>Submit Feedback
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const stars = document.querySelectorAll('.star');
            const ratingInput = document.getElementById('rating');
            const submitBtn = document.getElementById('submitBtn');
            let currentRating = 0;

            stars.forEach((star, index) => {
                star.addEventListener('click', function() {
                    currentRating = index + 1;
                    ratingInput.value = currentRating;
                    updateStars();
                    submitBtn.disabled = false;
                });

                star.addEventListener('mouseenter', function() {
                    highlightStars(index + 1);
                });
            });

            document.querySelector('.rating-stars').addEventListener('mouseleave', function() {
                updateStars();
            });

            function updateStars() {
                stars.forEach((star, index) => {
                    if (index < currentRating) {
                        star.classList.add('active');
                    } else {
                        star.classList.remove('active');
                    }
                });
            }

            function highlightStars(rating) {
                stars.forEach((star, index) => {
                    if (index < rating) {
                        star.classList.add('active');
                    } else {
                        star.classList.remove('active');
                    }
                });
            }
        });
    </script>
</body>
</html>