<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Track Your Request - {{ $studentRequest->reference_no }}</title>
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
            --nu-dark-overlay: rgba(0, 0, 0, 0.4);
            
            --primary-blue: #2563eb;
            --primary-blue-hover: #1d4ed8;
            --neutral-50: #f9fafb;
            --neutral-100: #f3f4f6;
            --neutral-200: #e5e7eb;
            --neutral-300: #d1d5db;
            --neutral-400: #9ca3af;
            --neutral-500: #6b7280;
            --neutral-600: #4b5563;
            --neutral-700: #374151;
            --neutral-800: #1f2937;
            --neutral-900: #111827;
            --accent-color: #10b981;
            --error-color: #ef4444;
            --warning-color: #f59e0b;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: url('{{ asset('images/login-bg.jpg') }}') no-repeat center center fixed;
            background-size: cover;
            min-height: 100vh;
            position: relative;
            overflow-x: hidden;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }

        .bg-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: var(--nu-dark-overlay);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            z-index: 1;
        }

        .site-content {
            position: relative;
            z-index: 10;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* Enhanced Header */
        .nu-header {
            background: var(--nu-blue);
            color: var(--nu-white);
            padding: 0.5rem 1.5rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1050;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .nu-logo-container {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .nu-shield {
            height: 2rem;
            width: auto;
        }

        .nu-title {
            font-size: 1.25rem;
            font-weight: 700;
            letter-spacing: 0.5px;
        }

        .back-button {
            background: var(--nu-yellow);
            color: var(--nu-blue);
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s ease;
            font-size: 0.85rem;
        }

        .back-button:hover {
            background: #e6b800;
            color: var(--nu-blue);
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(255, 215, 0, 0.4);
        }

        /* Main Content */
        .main-content {
            flex: 1;
            padding: 5rem 1rem 3rem;
            min-height: calc(100vh - 7rem);
            position: relative;
            width: 100%;
        }

        /* Footer */
        .nu-footer {
            background: var(--nu-blue);
            color: var(--nu-white);
            padding: 0.75rem 1.5rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 0.8rem;
            position: fixed;
            left: 0;
            right: 0;
            bottom: 0;
            z-index: 1050;
        }

        .footer-left {
            font-weight: 600;
        }

        .footer-right {
            text-align: right;
            font-weight: 400;
        }

        /* Timeline Styles */
        .timeline-container {
            background: var(--nu-white);
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            padding: 2rem;
            margin: 2rem auto;
            max-width: 1200px;
        }

        .timeline-header {
            background: linear-gradient(135deg, var(--nu-blue) 0%, #001f5f 100%);
            color: var(--nu-white);
            padding: 1.5rem;
            text-align: center;
            border-radius: 15px;
            margin-bottom: 2rem;
        }

        .timeline-header h3 {
            font-size: 1.5rem;
            font-weight: 700;
            margin: 0;
        }

        /* Request Details Card */
        .request-details-card {
            background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
            border: 1px solid #e9ecef;
            border-radius: 15px;
            padding: 1.5rem;
            margin-bottom: 2rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        }

        /* Payment Receipt Section */
        .payment-section {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 10px;
            padding: 1rem;
            margin: 1rem 0;
        }

        .receipt-preview {
            max-width: 200px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            cursor: pointer;
            transition: transform 0.2s ease;
        }

        .receipt-preview:hover {
            transform: scale(1.05);
        }

        /* Status badges */
        .status-badge {
            font-size: 0.875rem;
            font-weight: 600;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .status-pending {
            background: #fff3cd;
            color: #856404;
            border: 1px solid #ffeaa7;
        }

        .status-processing {
            background: #cff4fc;
            color: #055160;
            border: 1px solid #a6e9f7;
        }

        .status-ready {
            background: #cfe2ff;
            color: #084298;
            border: 1px solid #9ec5fe;
        }

        .status-completed {
            background: #d1e7dd;
            color: #0f5132;
            border: 1px solid #a3cfbb;
        }

        /* Timeline steps responsive */
        .timeline-steps-container {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            scrollbar-width: none;
            -ms-overflow-style: none;
        }

        .timeline-steps-container::-webkit-scrollbar {
            display: none;
        }

        /* Enhanced Responsive Design */
        @media (max-width: 768px) {
            .nu-header {
                padding: 0.75rem 1rem;
                flex-direction: row;
                justify-content: space-between;
            }

            .nu-logo-container {
                gap: 0.5rem;
            }

            .nu-shield {
                height: 1.5rem;
            }

            .nu-title {
                font-size: 0.95rem;
            }

            .back-button {
                font-size: 0.75rem;
                padding: 0.4rem 0.8rem;
            }

            .main-content {
                padding: 4rem 0.5rem 4rem;
                min-height: calc(100vh - 8rem);
            }

            .timeline-container {
                max-width: min(92vw, 360px);
                margin: 0 auto;
                border-radius: 12px;
                padding: 1rem;
            }

            .timeline-header {
                padding: 1rem;
                border-radius: 10px;
            }

            .timeline-header h3 {
                font-size: 1.1rem;
                margin-bottom: 0.1rem;
            }

            .request-details-card {
                padding: 1rem;
                margin-bottom: 1rem;
            }

            /* Timeline steps for mobile */
            .timeline-steps-container {
                padding: 0 15px;
                margin: 0 -15px;
            }

            .timeline-step {
                min-width: 70px;
                text-align: center;
            }

            .timeline-step .rounded-circle {
                width: 45px !important;
                height: 45px !important;
                font-size: 20px;
            }

            .timeline-step .fw-semibold {
                font-size: 0.7rem;
                margin-top: 0.5rem;
            }

            .nu-footer {
                padding: 0.5rem 1rem;
                flex-direction: column;
                align-items: flex-start;
                gap: 0.25rem;
                font-size: 0.7rem;
            }

            .footer-right {
                text-align: left;
            }
        }

        @media (max-width: 480px) {
            .timeline-container {
                max-width: min(95vw, 320px);
                padding: 0.75rem;
            }

            .timeline-header {
                padding: 0.75rem;
            }

            .timeline-header h3 {
                font-size: 1rem;
            }

            .request-details-card {
                padding: 0.75rem;
            }

            .timeline-step .rounded-circle {
                width: 40px !important;
                height: 40px !important;
                font-size: 18px;
            }

            .timeline-step .fw-semibold {
                font-size: 0.65rem;
            }
        }

        /* Button enhancements */
        .btn {
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.2s ease;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
        }

        /* Card hover effects */
        .card {
            transition: all 0.3s ease;
        }

        .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body>
    <div class="bg-overlay"></div>
    
    <div class="site-content">
        <!-- Enhanced Header -->
        <header class="nu-header">
            <div class="nu-logo-container">
                <img src="{{ asset('images/NU_shield.svg.png') }}" alt="NU Shield" class="nu-shield">
                <span class="nu-title">NU LIPA</span>
            </div>
            <a href="{{ route('student.my-requests') }}" class="back-button">
                <i class="bi bi-arrow-left me-1"></i>My Requests
            </a>
        </header>

        <!-- Main Content -->
        <main class="main-content">
            <div class="timeline-container">
                <div class="timeline-header">
                    <h3><i class="bi bi-search me-2"></i>Track Your Document Request</h3>
                    <p style="margin: 0; font-size: 0.9rem; opacity: 0.9;">
                        Reference: <strong>{{ $studentRequest->reference_no }}</strong>
                    </p>
                </div>

                {{-- Request Details Summary --}}
                <div class="request-details-card">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="d-flex align-items-center mb-3">
                                <div class="text-primary me-3">
                                    <i class="bi bi-person-circle" style="font-size: 1.5rem;"></i>
                                </div>
                                <div>
                                    <small class="text-muted text-uppercase fw-medium">Student</small>
                                    <div class="fw-semibold">{{ $studentRequest->student->user->first_name }} {{ $studentRequest->student->user->last_name }}</div>
                                    <small class="text-muted">{{ $studentRequest->student->student_id }}</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex align-items-center mb-3">
                                <div class="text-success me-3">
                                    <i class="bi bi-calendar-check" style="font-size: 1.5rem;"></i>
                                </div>
                                <div>
                                    <small class="text-muted text-uppercase fw-medium">Submitted</small>
                                    <div class="fw-semibold">{{ $studentRequest->created_at->format('M d, Y') }}</div>
                                    <small class="text-muted">{{ $studentRequest->created_at->format('h:i A') }}</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex align-items-center mb-3">
                                <div class="text-info me-3">
                                    <i class="bi bi-file-earmark-text" style="font-size: 1.5rem;"></i>
                                </div>
                                <div>
                                    <small class="text-muted text-uppercase fw-medium">Documents</small>
                                    <div class="fw-semibold">{{ $studentRequest->requestItems->count() }} item(s)</div>
                                    <small class="text-muted">₱{{ number_format($studentRequest->total_cost, 2) }}</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex align-items-center mb-3">
                                <div class="text-warning me-3">
                                    <i class="bi bi-clock-history" style="font-size: 1.5rem;"></i>
                                </div>
                                <div>
                                    <small class="text-muted text-uppercase fw-medium">Status</small>
                                    <div>
                                        <span class="status-badge status-{{ $studentRequest->status }}">
                                            {{ ucfirst(str_replace('_', ' ', $studentRequest->status)) }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @if($studentRequest->reason)
                        <div class="col-12">
                            <div class="d-flex align-items-start">
                                <div class="text-secondary me-3">
                                    <i class="bi bi-clipboard-check" style="font-size: 1.5rem;"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <small class="text-muted text-uppercase fw-medium">Reason for Request</small>
                                    <div class="fw-medium">{{ $studentRequest->reason }}</div>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>

                    {{-- Documents List --}}
                    <div class="border-top pt-3 mt-3">
                        <h6 class="mb-2 fw-bold">Requested Documents</h6>
                        <div class="row g-2">
                            @foreach($studentRequest->requestItems as $item)
                                <div class="col-md-6">
                                    <div class="d-flex align-items-center p-2 border rounded">
                                        <div class="text-primary me-2">
                                            <i class="bi bi-file-earmark-text"></i>
                                        </div>
                                        <div class="flex-grow-1">
                                            <div class="fw-medium">{{ $item->document->type_document }}</div>
                                            <small class="text-muted">
                                                Qty: {{ $item->quantity }} | 
                                                @if($item->price > 0)
                                                    ₱{{ number_format($item->price * $item->quantity, 2) }}
                                                @else
                                                    Free
                                                @endif
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- Payment Receipt Section --}}
                    @if($studentRequest->total_cost > 0)
                        <div class="border-top pt-3 mt-3">
                            <h6 class="mb-2 fw-bold">Payment Information</h6>
                            @if($studentRequest->payment_receipt_path)
                                <div class="payment-section">
                                    <div class="d-flex align-items-center">
                                        <div class="me-3">
                                            <img src="{{ route('accounting.receipt.student', $studentRequest) }}" 
                                                 alt="Payment Receipt" 
                                                 class="receipt-preview" 
                                                 data-bs-toggle="modal" 
                                                 data-bs-target="#receiptModal">
                                        </div>
                                        <div class="flex-grow-1">
                                            <div class="d-flex align-items-center mb-2">
                                                @if($studentRequest->payment_approved)
                                                    <i class="bi bi-check-circle-fill text-success me-2"></i>
                                                    <span class="text-success fw-semibold">Payment Approved</span>
                                                @else
                                                    <i class="bi bi-clock-fill text-warning me-2"></i>
                                                    <span class="text-warning fw-semibold">Payment Under Review</span>
                                                @endif
                                            </div>
                                            <small class="text-muted">
                                                Receipt uploaded. Click image to view full size.
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <div class="alert alert-warning">
                                    <i class="bi bi-exclamation-triangle me-2"></i>
                                    <strong>Payment Required:</strong> Please upload your payment receipt to continue processing.
                                </div>
                            @endif
                        </div>
                    @endif
                </div>

                {{-- Timeline Steps --}}
                <div class="d-flex justify-content-center mb-4">
                    <div class="d-flex align-items-center justify-content-between w-100 timeline-steps-container" style="max-width: 900px; padding: 0 30px;">
                        @foreach ($steps as $index => $step)
                            @if ($index > 0)
                                <div style="height: 4px; flex-grow: 1; background-color: {{ $index <= $currentStepIndex ? '#28a745' : '#ced4da' }}; margin: 0 -10px; z-index: 0;"></div>
                            @endif
                            <div class="timeline-step text-center position-relative" style="width: 90px; z-index: 1;">
                                <div class="mb-2 d-flex justify-content-center">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center shadow-sm"
                                         style="width: 55px; height: 55px;
                                                background-color: {{ $index < $currentStepIndex ? '#28a745' : ($index === $currentStepIndex ? '#0d6efd' : '#dee2e6') }};
                                                color: #fff; font-size: 24px;">
                                        {{ $step['icon'] }}
                                    </div>
                                </div>
                                <div class="fw-semibold small {{ $index < $currentStepIndex ? 'text-success' : ($index === $currentStepIndex ? 'text-primary' : 'text-muted') }}">
                                    {{ $step['label'] }}
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Status-specific Information --}}
                <div class="text-center mt-4">
                    @if($studentRequest->status === 'pending')
                        @if($studentRequest->total_cost > 0 && !$studentRequest->payment_receipt_path)
                            <div class="alert alert-warning">
                                <h5 class="alert-heading"><i class="bi bi-credit-card me-2"></i>Payment Required</h5>
                                <p>Please upload your payment receipt to proceed with your document request.</p>
                                <hr>
                                <p class="mb-0">Total Amount: <strong>₱{{ number_format($studentRequest->total_cost, 2) }}</strong></p>
                            </div>
                        @elseif($studentRequest->payment_receipt_path && !$studentRequest->payment_approved)
                            <div class="alert alert-info">
                                <h5 class="alert-heading"><i class="bi bi-clock me-2"></i>Payment Under Review</h5>
                                <p>Your payment receipt has been uploaded and is currently being reviewed by our accounting team.</p>
                                <hr>
                                <p class="mb-0">We'll update you once the payment is verified.</p>
                            </div>
                        @else
                            <div class="alert alert-success">
                                <h5 class="alert-heading"><i class="bi bi-check-circle me-2"></i>Request Submitted Successfully</h5>
                                <p>Your document request has been submitted and is ready for processing.</p>
                                @if($studentRequest->expected_release_date)
                                    <hr>
                                    <p class="mb-0">Expected Release Date: <strong>{{ $studentRequest->expected_release_date->format('M d, Y') }}</strong></p>
                                @endif
                            </div>
                        @endif

                    @elseif($studentRequest->status === 'processing')
                        <div class="alert alert-info">
                            <h5 class="alert-heading"><i class="bi bi-gear me-2"></i>Document Being Processed</h5>
                            <p>Your document request is currently being processed by our registrar team.</p>
                            @if($studentRequest->assignedRegistrar)
                                <hr>
                                <p class="mb-0">Assigned to: <strong>{{ $studentRequest->assignedRegistrar->first_name }} {{ $studentRequest->assignedRegistrar->last_name }}</strong></p>
                            @endif
                        </div>

                    @elseif($studentRequest->status === 'ready_for_release')
                        <div class="alert alert-primary">
                            <h5 class="alert-heading"><i class="bi bi-box-seam me-2"></i>Ready</h5>
                            <p>Your document is ready for pickup or download.</p>
                            @if($studentRequest->expected_release_date)
                                <hr>
                                <p class="mb-0">Available for pickup since: <strong>{{ $studentRequest->expected_release_date->format('M d, Y') }}</strong></p>
                            @endif
                        </div>

                    @elseif($studentRequest->status === 'completed')
                        <div class="alert alert-success">
                            <h5 class="alert-heading"><i class="bi bi-check-circle-fill me-2"></i>Request Completed</h5>
                            <p>Your document request has been completed successfully!</p>
                            <hr>
                            <p class="mb-0">Thank you for using NU Lipa Document Services.</p>
                        </div>
                    @endif
                </div>

                {{-- Status-specific Information --}}
            </div>
        </main>

        <!-- Footer -->
        <footer class="nu-footer">
            <div class="footer-left">
                <div class="fw-bold">NU ONLINE SERVICES</div>
                <div>All Rights Reserved. National University</div>
            </div>
            <div class="footer-right">
                CONTACT US<br>
                <span class="fw-normal">NU Bldg, SM City Lipa, JP Laurel Highway, Lipa City, Batangas</span>
            </div>
        </footer>
    </div>

    {{-- Receipt Modal --}}
    @if($studentRequest->payment_receipt_path)
    <div class="modal fade" id="receiptModal" tabindex="-1" aria-labelledby="receiptModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="receiptModalLabel">Payment Receipt</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <img src="{{ route('accounting.receipt.student', $studentRequest) }}" 
                         alt="Payment Receipt" 
                         class="img-fluid"
                         style="max-height: 70vh;">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Pusher JS for Real-time Updates -->
    <script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
    <script>
        // Initialize Pusher for real-time updates
        const pusher = new Pusher('{{ config('broadcasting.connections.pusher.key') }}', {
            cluster: '{{ config('broadcasting.connections.pusher.options.cluster') }}',
            encrypted: true
        });

        // Subscribe to request-specific channel
        const channelName = 'request-{{ $studentRequest->reference_no }}';
        const requestChannel = pusher.subscribe(channelName);
        
        // Listen for status updates
        requestChannel.bind('realtime.notification', function(data) {
            console.log('🔄 Received real-time update:', data);
            
            // Show notification
            showNotification(data.message || 'Status updated', data.type || 'info');
            
            // Refresh the page after a delay
            setTimeout(() => {
                window.location.reload();
            }, 2000);
        });

        // Function to show notifications
        function showNotification(message, type = 'info') {
            const alertClass = type === 'error' ? 'danger' : type;
            const notification = document.createElement('div');
            notification.className = `alert alert-${alertClass} alert-dismissible fade show position-fixed`;
            notification.style.cssText = 'top: 20px; right: 20px; z-index: 9999; max-width: 400px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);';
            notification.innerHTML = `
                <i class="bi bi-info-circle me-2"></i>
                <strong>Update:</strong> ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            
            document.body.appendChild(notification);
            
            setTimeout(() => {
                if (notification.parentNode) {
                    notification.remove();
                }
            }, 5000);
        }

        // Clean up on page unload
        window.addEventListener('beforeunload', function() {
            pusher.disconnect();
        });
    </script>
</body>
</html>