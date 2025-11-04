<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Queue Status Display - NU Regis</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --nu-blue: #003399;
            --nu-yellow: #FFD700;
            --nu-white: #ffffff;
            --nu-gray: #6c757d;
            --nu-light-gray: #f8f9fa;
            --nu-dark-overlay: rgba(0, 0, 0, 0.4);
            
            --primary-color: #2c3e50;
            --secondary-color: #3498db;
            --success-color: #27ae60;
            --warning-color: #f39c12;
            --danger-color: #e74c3c;
            --info-color: #17a2b8;
            --dark-color: #2c3e50;
            --light-color: #ecf0f1;
            
            /* Enhanced professional color palette */
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
            
            /* Spacing and sizing */
            --border-radius-sm: 0.375rem;
            --border-radius-md: 0.5rem;
            --border-radius-lg: 0.75rem;
            --border-radius-xl: 1rem;
            --shadow-sm: 0 1px 2px 0 rgb(0 0 0 / 0.05);
            --shadow-md: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
            --shadow-lg: 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1);
            --shadow-xl: 0 20px 25px -5px rgb(0 0 0 / 0.1), 0 8px 10px -6px rgb(0 0 0 / 0.1);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, rgba(0, 51, 153, 0.85), rgba(0, 51, 153, 0.6), rgba(255, 215, 0, 0.1)), 
                        url('{{ asset("images/NU-header.jpg") }}');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            background-repeat: no-repeat;
            min-height: 100vh;
            height: 100vh;
            display: flex;
            flex-direction: column;
            position: relative;
            overflow: hidden;
            margin: 0;
            padding: 0;
        }

        /* Add subtle overlay for better text readability */
        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, 
                rgba(0, 51, 153, 0.2) 0%, 
                rgba(0, 51, 153, 0.4) 30%,
                rgba(0, 51, 153, 0.2) 70%,
                rgba(255, 215, 0, 0.05) 100%);
            backdrop-filter: blur(0.5px);
            z-index: -1;
        }

        /* Background overlay with blur */
        .bg-overlay {
            display: none;
        }

        /* Main content wrapper */
        .content-wrapper {
            position: relative;
            z-index: 2;
            display: flex;
            flex-direction: column;
            height: 100vh;
            overflow: hidden;
        }

        /* Header */
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
            z-index: 10;
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

        .nu-welcome {
            font-size: 0.95rem;
            font-weight: 400;
        }

        /* Footer */
        .nu-footer {
            display: none;
        }

        .footer-left {
            display: none;
        }

        .footer-right {
            display: none;
        }

        .main-container {
            max-width: 1600px;
            margin: 0 auto;
            padding: 1rem;
            min-height: calc(100vh - 6rem);
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }

        .header-section {
            text-align: center;
            margin-bottom: 1rem;
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            padding: 1rem;
            border-radius: var(--border-radius-lg);
            box-shadow: 0 8px 32px rgba(0, 51, 153, 0.15);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .header-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(90deg, var(--primary-blue), var(--accent-color));
            border-radius: var(--border-radius-lg) var(--border-radius-lg) 0 0;
        }

        .header-section h1 {
            color: var(--neutral-800);
            font-weight: 600;
            margin-bottom: 0.25rem;
            font-size: 1.5rem;
            letter-spacing: -0.025em;
            line-height: 1.2;
        }

        .header-section p {
            color: var(--neutral-500);
            font-size: 0.875rem;
            margin-bottom: 0;
            font-weight: 400;
        }

        .unified-queue-container {
            background: rgba(255, 255, 255, 0.50);
            backdrop-filter: blur(10px);
            border-radius: var(--border-radius-xl);
            padding: 2rem;
            box-shadow: var(--shadow-xl);
            border: 1px solid rgba(255, 255, 255, 0.2);
            margin-bottom: 1.5rem;
            margin-top: 2rem;
            position: relative;
            overflow: hidden;
            animation: fadeInUp 0.8s cubic-bezier(0.4, 0, 0.2, 1), float 6s ease-in-out infinite;
        }

        @keyframes float {
            0%, 100% {
                transform: translateY(0px);
            }
            50% {
                transform: translateY(-5px);
            }
        }

        .unified-queue-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--nu-blue), var(--accent-color), var(--nu-yellow));
            border-radius: var(--border-radius-xl) var(--border-radius-xl) 0 0;
        }

        .queue-sections-grid {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 2rem;
            margin-top: 1rem;
        }

        .queue-section {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(5px);
            border-radius: var(--border-radius-lg);
            padding: 1.5rem;
            border: 1px solid rgba(255, 255, 255, 0.3);
            height: auto;
            min-height: 300px;
            transition: all 0.3s ease;
            box-shadow: 0 8px 32px rgba(0, 51, 153, 0.1);
        }

        .queue-section:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
            border-color: var(--primary-blue);
        }

        .queue-section h2 {
            font-weight: 600;
            margin-bottom: 1.5rem;
            font-size: 1.2rem;
            text-align: center;
            color: var(--neutral-800);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 2px solid var(--primary-blue);
            padding-bottom: 0.75rem;
            position: relative;
        }

        .queue-section h2::before {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 50%;
            transform: translateX(-50%);
            width: 40px;
            height: 2px;
            background: var(--accent-color);
        }

        .unified-queue-title {
            text-align: center;
            margin-bottom: 2rem;
            color: var(--neutral-800);
            font-weight: 700;
            font-size: 1.8rem;
            letter-spacing: -0.025em;
        }

        .section-stats {
            background: transparent;
            padding: 0;
            border: none;
            min-height: auto;
        }

        /* In Queue and Ready for Pickup Grid Layout */
        .in-queue .section-stats,
        .ready-pickup .section-stats {
            min-height: 200px;
        }

        .queue-numbers-list {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 1rem;
            padding: 0;
            margin: 0;
            min-height: 200px;
            list-style: none;
        }

        .queue-number-display {
            padding: 1.5rem 1rem;
            font-size: 1.2rem;
            font-weight: 600;
            color: var(--neutral-800);
            border: 2px solid var(--neutral-200);
            border-radius: var(--border-radius-md);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            background: var(--nu-white);
            min-height: 120px;
            transition: all 0.3s ease;
        }

        .queue-number-display:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
            border-color: var(--primary-blue);
        }

        .queue-number-display.empty-window-slot {
            border-style: dashed;
            background: var(--neutral-100);
            opacity: 0.6;
        }

        .queue-number {
            font-weight: 700;
            color: var(--neutral-800);
            display: block;
            font-size: 1.6rem;
            margin-top: 0.5rem;
        }

        .window-assignment {
            font-size: 1rem;
            color: var(--primary-blue);
            font-weight: 700;
            display: block;
            margin-bottom: 0.5rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .kiosk-indicator {
            font-size: 0.9rem;
            color: var(--neutral-600);
            display: flex;
            align-items: center;
            gap: 0.25rem;
            margin-top: 0.5rem;
        }

        .no-queue-message {
            color: var(--neutral-400);
            font-style: italic;
            font-size: 1rem;
            text-align: center;
            padding: 2rem;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 200px;
        }

        .no-queue-message i {
            font-size: 2rem;
            margin-bottom: 1rem;
            opacity: 0.5;
        }

        /* Waiting Queue Grid Layout Styles */
        .waiting .section-stats {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 0.75rem;
            grid-template-rows: auto;
        }

        .waiting .queue-count-indicator {
            grid-column: 1 / -1;
            margin-bottom: 0.75rem;
        }

        .waiting-queue-number {
            padding: 0.75rem;
            font-size: 1rem;
            font-weight: 600;
            color: var(--neutral-800);
            display: flex;
            flex-direction: row;
            align-items: center;
            gap: 0.75rem;
            background: var(--neutral-50);
            border-radius: var(--border-radius-md);
            border: 1px solid var(--neutral-200);
            transition: all 0.2s ease;
            min-height: 60px;
        }

        .waiting-queue-number:hover {
            background: var(--neutral-100);
            transform: translateY(-1px);
            box-shadow: var(--shadow-sm);
        }

        .waiting-queue-number .queue-number {
            font-weight: 700;
            color: var(--neutral-800);
            display: block;
            font-size: 1.1rem;
            flex-grow: 1;
        }

        .position-number {
            background: var(--primary-blue);
            color: var(--nu-white);
            width: 1.8rem;
            height: 1.8rem;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 0.85rem;
            flex-shrink: 0;
        }

        /* Show queue count indicator */
        .queue-count-indicator {
            background: var(--accent-color);
            color: var(--nu-white);
            padding: 0.25rem 0.75rem;
            border-radius: 15px;
            font-size: 0.8rem;
            font-weight: 600;
            margin-bottom: 0.75rem;
            text-align: center;
            display: inline-block;
        }

        @media (max-width: 1200px) {
            .queue-sections-grid {
                grid-template-columns: 1fr 1fr;
                gap: 1.5rem;
            }
            
            .unified-queue-container {
                padding: 1.5rem;
            }
        }

        @media (max-width: 768px) {
            .nu-header {
                padding: 0.75rem 1rem;
            }

            .nu-logo-container {
                gap: 0.5rem;
            }

            .nu-shield {
                height: 1.75rem;
            }

            .nu-title {
                font-size: 1rem;
            }

            .nu-welcome {
                font-size: 0.8rem;
            }

            .main-container {
                padding: 4rem 0.75rem 4rem;
                min-height: calc(100vh - 8rem);
            }

            .queue-sections-grid {
                grid-template-columns: 1fr;
                gap: 1rem;
            }

            .unified-queue-container {
                padding: 1rem;
            }

            .unified-queue-title {
                font-size: 1.4rem;
                margin-bottom: 1.5rem;
            }
            
            .windows-grid {
                grid-template-columns: 1fr;
            }
            
            .header-section h1 {
                font-size: 1.25rem;
            }
            
            .header-section {
                padding: 0.75rem;
                margin-bottom: 1rem;
            }

            .nu-footer {
                padding: 0.5rem 1rem;
                font-size: 0.7rem;
            }

            .footer-left {
                font-size: 0.65rem;
            }

            .footer-right {
                font-size: 0.6rem;
                margin-top: 0.25rem;
            }

            .timestamp {
                top: 70px;
                right: 10px;
                padding: 0.5rem 0.75rem;
                font-size: 0.75rem;
            }

            .refresh-indicator {
                bottom: 70px;
                right: 10px;
                padding: 0.75rem;
            }
        }

        /* Enhanced animations and interactions */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .header-section {
            animation: fadeInUp 0.6s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .unified-queue-container {
            animation: fadeInUp 0.8s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .queue-section {
            animation: slideIn 0.6s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .queue-section:nth-child(1) {
            animation-delay: 0.1s;
        }

        .queue-section:nth-child(2) {
            animation-delay: 0.2s;
        }

        .queue-section:nth-child(3) {
            animation-delay: 0.3s;
        }

        /* Video advertisement styles */
        .video-ad-container {
            position: fixed;
            bottom: 20px;
            right: 20px;
            width: 320px;
            height: 180px;
            z-index: 1000;
            border-radius: var(--border-radius-lg);
            overflow: hidden;
            box-shadow: 0 8px 32px rgba(0, 51, 153, 0.3);
            border: 2px solid rgba(255, 255, 255, 0.3);
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            opacity: 0;
            transform: scale(0.8);
            transition: all 0.3s ease;
        }

        .video-ad-container.visible {
            opacity: 1;
            transform: scale(1);
        }

        .video-ad-container video {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: calc(var(--border-radius-lg) - 3px);
        }

        .video-ad-close {
            position: absolute;
            top: 5px;
            right: 5px;
            background: rgba(0, 0, 0, 0.7);
            color: var(--nu-white);
            border: none;
            border-radius: 50%;
            width: 25px;
            height: 25px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            font-size: 12px;
            z-index: 1001;
            transition: background 0.2s ease;
        }

        .video-ad-close:hover {
            background: rgba(0, 0, 0, 0.9);
        }

        /* Image Slideshow Styles */
        .image-slideshow-container {
            position: fixed;
            bottom: 20px;
            left: 20px;
            width: 400px;
            height: 225px;
            z-index: 1000;
            border-radius: var(--border-radius-lg);
            overflow: hidden;
            box-shadow: 0 8px 32px rgba(0, 51, 153, 0.3);
            border: 2px solid rgba(255, 255, 255, 0.3);
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            opacity: 1;
            transform: scale(1);
            transition: all 0.3s ease;
        }

        .image-slideshow {
            position: relative;
            width: 100%;
            height: 100%;
        }

        .slideshow-slide {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            opacity: 0;
            transition: opacity 1.5s ease-in-out;
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            border-radius: calc(var(--border-radius-lg) - 3px);
        }

        .slideshow-slide.active {
            opacity: 1;
        }

        .slideshow-indicators {
            position: absolute;
            bottom: 15px;
            left: 50%;
            transform: translateX(-50%);
            display: flex;
            gap: 8px;
            z-index: 1002;
        }

        .slideshow-indicator {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.5);
            border: 2px solid rgba(255, 255, 255, 0.8);
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .slideshow-indicator.active {
            background: var(--nu-yellow);
            border-color: var(--nu-white);
            transform: scale(1.2);
        }

        .slideshow-indicator:hover {
            background: rgba(255, 255, 255, 0.8);
        }

        .slideshow-close {
            position: absolute;
            top: 10px;
            right: 10px;
            background: rgba(0, 0, 0, 0.7);
            color: var(--nu-white);
            border: none;
            border-radius: 50%;
            width: 30px;
            height: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            font-size: 14px;
            z-index: 1002;
            transition: all 0.2s ease;
        }

        .slideshow-close:hover {
            background: rgba(0, 0, 0, 0.9);
            transform: scale(1.1);
        }

        @media (max-width: 768px) {
            .video-ad-container {
                width: 280px;
                height: 157px;
                bottom: 15px;
                right: 15px;
            }

            .image-slideshow-container {
                width: 280px;
                height: 157px;
                bottom: 15px;
                left: 15px;
            }
        }
    </style>
</head>
<body>
    
    <!-- Main content wrapper -->
    <div class="content-wrapper">
        <!-- Header -->
        <header class="nu-header">
            <div class="nu-logo-container">
                <img src="{{ asset('images/NU_shield.svg.png') }}" alt="NU Shield" class="nu-shield">
                <span class="nu-title">NU LIPA</span>
            </div>
            <span class="nu-welcome">Welcome to NU Lipa</span>
        </header>

        <!-- Main content area -->
        <main class="main-container">
            <!-- Unified Queue Container -->
            <div class="unified-queue-container">
                <h1 class="unified-queue-title">
                    <i class="fas fa-users-cog"></i> Queue Management System
                </h1>
                
                <div class="queue-sections-grid">
                    <!-- In Queue Section -->
                    <div class="queue-section in-queue">
                        <h2>
                            <i class="fas fa-clock"></i> In Queue
                        </h2>
                        <div class="section-stats">
                            <div class="queue-numbers-list">
                                @if($inQueueRequests->count() > 0)
                                    @foreach($inQueueRequests as $request)
                                        <div class="queue-number-display">
                                            <span class="window-assignment">
                                                <i class="fas fa-desktop"></i> {{ $request['window_assignment'] ?? 'Unassigned' }}
                                            </span>
                                            <span class="queue-number">{{ $request['queue_number'] }}</span>
                                        </div>
                                    @endforeach
                                @else
                                    <div class="no-queue-message">
                                        <i class="fas fa-users"></i>
                                        No one in queue
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Ready for Pickup Section -->
                    <div class="queue-section ready-pickup">
                        <h2>
                            <i class="fas fa-check-circle"></i> Ready for Pickup
                        </h2>
                        <div class="section-stats">
                            <div class="queue-numbers-list">
                                @if($readyForPickupRequests->count() > 0)
                                    @foreach($readyForPickupRequests as $request)
                                        <div class="queue-number-display">
                                            <span class="window-assignment">
                                                <i class="fas fa-desktop"></i> {{ $request['window_assignment'] ?? 'Unassigned' }}
                                            </span>
                                            <span class="queue-number">{{ $request['queue_number'] }}</span>
                                        </div>
                                    @endforeach
                                @else
                                    <div class="no-queue-message">
                                        <i class="fas fa-users"></i>
                                        No requests ready for pickup
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Waiting Section -->
                    <div class="queue-section waiting">
                        <h2>
                            <i class="fas fa-hourglass-half"></i> Waiting Queue
                        </h2>
                        <div class="section-stats">
                            @if($waitingRequests->count() > 0)
                                @foreach($waitingRequests->take(10) as $index => $request)
                                    <div class="waiting-queue-number">
                                        <div class="position-number">{{ $request['position'] ?? ($index + 1) }}</div>
                                        <div>
                                            <span class="queue-number">{{ $request['queue_number'] }}</span>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <div class="no-queue-message">
                                    <i class="fas fa-users"></i>
                                    No one waiting in queue
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </main>

        <!-- Video Advertisement Container -->
        <div class="video-ad-container" id="videoAdContainer">
            <button class="video-ad-close" id="videoAdClose" title="Close video">&times;</button>
            <video id="nuAdvertisementVideo" muted playsinline>
                <source src="{{ asset('videos/nu-advertisment.mp4') }}" type="video/mp4">
            </video>
        </div>
        
        <!-- Image Slideshow Container -->
        <div class="image-slideshow-container" id="imageSlideshowContainer">
            <button class="slideshow-close" id="slideshowClose" title="Close slideshow">&times;</button>
            <div class="image-slideshow" id="imageSlideshow">
                <div class="slideshow-slide active" style="background-image: url('{{ asset('images/NU-adv1.jpg') }}');"></div>
                <div class="slideshow-slide" style="background-image: url('{{ asset('images/NU-adv2.jpg') }}');"></div>
            </div>
            <div class="slideshow-indicators" id="slideshowIndicators">
                <div class="slideshow-indicator active" data-slide="0"></div>
                <div class="slideshow-indicator" data-slide="1"></div>
            </div>
        </div>
        
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Pusher JS for Real-time Updates -->
    <script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
    <script>
        // Initialize Pusher for real-time queue updates
        const pusher = new Pusher('{{ config('broadcasting.connections.pusher.key') }}', {
            cluster: '{{ config('broadcasting.connections.pusher.options.cluster') }}',
            encrypted: true
        });
        
        // Debug Pusher connection
        pusher.connection.bind('connected', function() {
            console.log('✅ Queue Display: Pusher connected successfully');
        });
        
        pusher.connection.bind('error', function(err) {
            console.error('❌ Queue Display: Pusher connection error:', err);
        });

        pusher.connection.bind('disconnected', function() {
            console.log('⚠️ Queue Display: Pusher disconnected');
        });

        // Subscribe to queue update channels
        const queueUpdatesChannel = pusher.subscribe('queue-updates');
        const registrarChannel = pusher.subscribe('registrar-notifications');
        const newOnsiteRequestsChannel = pusher.subscribe('new-onsite-requests');
        const onsiteRequestUpdatesChannel = pusher.subscribe('onsite-request-updates');
        const newStudentRequestsChannel = pusher.subscribe('new-student-requests');
        
        // Listen for queue updates
        queueUpdatesChannel.bind('realtime.notification', function(data) {
            console.log('🔄 Queue Display: Received queue update:', data);
            refreshQueueDisplay();
        });

        // Listen for registrar notifications that affect queue
        registrarChannel.bind('realtime.notification', function(data) {
            console.log('🔄 Queue Display: Received registrar notification:', data);
            
            // Refresh if it's a status update that affects the queue
            if (data.data && data.data.status_update) {
                refreshQueueDisplay();
            }
        });

        // Listen for new onsite requests
        newOnsiteRequestsChannel.bind('realtime.notification', function(data) {
            console.log('🔄 Queue Display: Received new onsite request:', data);
            refreshQueueDisplay();
        });

        // Listen for onsite request updates
        onsiteRequestUpdatesChannel.bind('realtime.notification', function(data) {
            console.log('🔄 Queue Display: Received onsite request update:', data);
            refreshQueueDisplay();
        });

        // Listen for new student requests
        newStudentRequestsChannel.bind('realtime.notification', function(data) {
            console.log('🔄 Queue Display: Received new student request:', data);
            refreshQueueDisplay();
        });

        // Function to refresh the queue display
        function refreshQueueDisplay() {
            console.log('🔄 Refreshing queue display...');
            
            // Wait a short moment to allow database updates to complete
            setTimeout(() => {
                window.location.reload();
            }, 1000);
        }

        // Function to show queue update notification
        function showQueueNotification(message, type = 'info') {
            const notification = document.createElement('div');
            notification.className = 'queue-notification';
            notification.style.cssText = `
                position: fixed;
                top: 80px;
                right: 20px;
                background: var(--nu-blue);
                color: var(--nu-white);
                padding: 1rem 1.5rem;
                border-radius: var(--border-radius-md);
                box-shadow: var(--shadow-lg);
                z-index: 9998;
                font-weight: 600;
                font-size: 0.9rem;
                max-width: 300px;
                transform: translateX(350px);
                transition: transform 0.3s ease;
                border-left: 4px solid var(--nu-yellow);
            `;
            
            notification.innerHTML = `
                <div style="display: flex; align-items: center; gap: 0.5rem;">
                    <i class="fas fa-bell"></i>
                    <span>${message}</span>
                </div>
            `;
            
            document.body.appendChild(notification);
            
            // Animate in
            setTimeout(() => {
                notification.style.transform = 'translateX(0)';
            }, 100);
            
            // Auto-remove after 3 seconds
            setTimeout(() => {
                notification.style.transform = 'translateX(350px)';
                setTimeout(() => {
                    if (notification.parentNode) {
                        notification.parentNode.removeChild(notification);
                    }
                }, 300);
            }, 3000);
        }

        // Clean up on page unload
        window.addEventListener('beforeunload', function() {
            pusher.disconnect();
        });

        // Function to announce queue numbers using TTS
        function announceQueueNumbers() {
            console.log('🎤 Starting TTS announcement...');
            
            // Function to get voices (handles async loading)
            function getVoices() {
                let voices = speechSynthesis.getVoices();
                if (voices.length === 0) {
                    // Voices not loaded yet, try again after a short delay
                    setTimeout(() => {
                        voices = speechSynthesis.getVoices();
                        console.log('🎭 Available voices (delayed):', voices.map(voice => ({
                            name: voice.name,
                            lang: voice.lang,
                            default: voice.default
                        })));
                        proceedWithAnnouncement(voices);
                    }, 100);
                } else {
                    console.log('🎭 Available voices:', voices.map(voice => ({
                        name: voice.name,
                        lang: voice.lang,
                        default: voice.default
                    })));
                    proceedWithAnnouncement(voices);
                }
            }
            
            function proceedWithAnnouncement(voices) {
                if ('speechSynthesis' in window) {
                    // Announce In Queue numbers
                    const inQueueDisplays = document.querySelectorAll('.in-queue .queue-number-display');
                    console.log('📋 Found', inQueueDisplays.length, 'in-queue displays to announce');
                    inQueueDisplays.forEach((display, index) => {
                        const queueNumberElement = display.querySelector('.queue-number');
                        const windowAssignmentElement = display.querySelector('.window-assignment');
                        
                        if (queueNumberElement && windowAssignmentElement) {
                            const queueNumber = queueNumberElement.textContent.trim();
                            const windowAssignment = windowAssignmentElement.textContent.trim();
                            
                            if (queueNumber && windowAssignment) {
                                const announcement = `In queue serving ${queueNumber} at ${windowAssignment}`;
                                console.log('🔊 Announcing:', announcement);
                                const utterance = new SpeechSynthesisUtterance(announcement);
                                utterance.rate = 0.8; // Adjust speed if needed
                                utterance.pitch = 1; // Adjust pitch if needed
                                
                                // Set voice to sound like Siri (female voice)
                                // Try to find a female voice that sounds like Siri
                                let siriLikeVoice = voices.find(voice => 
                                    voice.name.toLowerCase().includes('samantha') || // macOS
                                    voice.name.toLowerCase().includes('susan') ||    // macOS
                                    voice.name.toLowerCase().includes('zira') ||     // Windows
                                    voice.name.toLowerCase().includes('female') ||
                                    (voice.name.toLowerCase().includes('english') && voice.name.toLowerCase().includes('us'))
                                );
                                
                                // If no Siri-like voice found, use the first female voice or default
                                if (!siriLikeVoice) {
                                    siriLikeVoice = voices.find(voice => voice.lang.includes('en') && !voice.name.toLowerCase().includes('male'));
                                }
                                
                                if (siriLikeVoice) {
                                    utterance.voice = siriLikeVoice;
                                    console.log('🎭 Using voice:', siriLikeVoice.name);
                                }
                                
                                speechSynthesis.speak(utterance);
                            }
                        }
                    });

                    // Announce Ready for Pickup numbers
                    const readyPickupDisplays = document.querySelectorAll('.ready-pickup .queue-number-display');
                    console.log('📋 Found', readyPickupDisplays.length, 'ready-pickup displays to announce');
                    readyPickupDisplays.forEach((display, index) => {
                        const queueNumberElement = display.querySelector('.queue-number');
                        const windowAssignmentElement = display.querySelector('.window-assignment');
                        
                        if (queueNumberElement && windowAssignmentElement) {
                            const queueNumber = queueNumberElement.textContent.trim();
                            const windowAssignment = windowAssignmentElement.textContent.trim();
                            
                            if (queueNumber && windowAssignment) {
                                const announcement = `Ready for pickup ${queueNumber} at ${windowAssignment}`;
                                console.log('🔊 Announcing:', announcement);
                                const utterance = new SpeechSynthesisUtterance(announcement);
                                utterance.rate = 0.8; // Adjust speed if needed
                                utterance.pitch = 1; // Adjust pitch if needed
                                
                                // Set voice to sound like Siri (female voice)
                                // Try to find a female voice that sounds like Siri
                                let siriLikeVoice = voices.find(voice => 
                                    voice.name.toLowerCase().includes('samantha') || // macOS
                                    voice.name.toLowerCase().includes('susan') ||    // macOS
                                    voice.name.toLowerCase().includes('zira') ||     // Windows
                                    voice.name.toLowerCase().includes('female') ||
                                    (voice.name.toLowerCase().includes('english') && voice.name.toLowerCase().includes('us'))
                                );
                                
                                // If no Siri-like voice found, use the first female voice or default
                                if (!siriLikeVoice) {
                                    siriLikeVoice = voices.find(voice => voice.lang.includes('en') && !voice.name.toLowerCase().includes('male'));
                                }
                                
                                if (siriLikeVoice) {
                                    utterance.voice = siriLikeVoice;
                                    console.log('🎭 Using voice:', siriLikeVoice.name);
                                }
                                
                                speechSynthesis.speak(utterance);
                            }
                        }
                    });
                } else {
                    console.warn('Text-to-speech not supported in this browser.');
                }
            }
            
            getVoices();
        }

        // Add smooth animations when page loads
        document.addEventListener('DOMContentLoaded', function() {
            const cards = document.querySelectorAll('.window-card');
            cards.forEach((card, index) => {
                card.style.opacity = '0';
                card.style.transform = 'translateY(20px)';
                setTimeout(() => {
                    card.style.transition = 'all 0.5s ease';
                    card.style.opacity = '1';
                    card.style.transform = 'translateY(0)';
                }, index * 100);
            });

            // Announce queue numbers on page load
            announceQueueNumbers();

            // Initialize video advertisement
            initializeVideoAd();

            // Initialize image slideshow
            initializeImageSlideshow();
        });

        // Function to initialize video advertisement
        function initializeVideoAd() {
            const videoContainer = document.getElementById('videoAdContainer');
            const video = document.getElementById('nuAdvertisementVideo');
            const closeButton = document.getElementById('videoAdClose');

            let videoInterval;
            let isVideoPlaying = false;

            // Function to show video
            function showVideo() {
                if (isVideoPlaying) return; // Prevent multiple instances

                console.log('🎬 Showing NU advertisement video');
                videoContainer.classList.add('visible');
                isVideoPlaying = true;

                // Start playing the video
                video.currentTime = 0;
                video.play().then(() => {
                    console.log('▶️ Video started playing');
                }).catch(error => {
                    console.error('❌ Error playing video:', error);
                });
            }

            // Function to hide video
            function hideVideo() {
                console.log('⏹️ Hiding NU advertisement video');
                videoContainer.classList.remove('visible');
                video.pause();
                video.currentTime = 0;
                isVideoPlaying = false;
            }

            // Close button functionality
            closeButton.addEventListener('click', function() {
                hideVideo();
                // Reset the interval to start counting from now
                clearInterval(videoInterval);
                startVideoInterval();
            });

            // Video ended event
            video.addEventListener('ended', function() {
                console.log('🏁 Video ended');
                hideVideo();
                // Start new interval for next video
                startVideoInterval();
            });

            // Video error handling
            video.addEventListener('error', function(e) {
                console.error('❌ Video error:', e);
                hideVideo();
                // Try again in 5 seconds
                setTimeout(startVideoInterval, 5000);
            });

            // Function to start the video interval
            function startVideoInterval() {
                videoInterval = setInterval(() => {
                    showVideo();
                }, 5000); // Show every 5 seconds
            }

            // Start the video cycle
            console.log('🎬 Initializing NU advertisement video system');
            startVideoInterval();

            // Cleanup on page unload
            window.addEventListener('beforeunload', function() {
                clearInterval(videoInterval);
                hideVideo();
            });
        }

        // Function to initialize image slideshow
        function initializeImageSlideshow() {
            const slideshowContainer = document.getElementById('imageSlideshowContainer');
            const slideshow = document.getElementById('imageSlideshow');
            const slides = document.querySelectorAll('.slideshow-slide');
            const indicators = document.querySelectorAll('.slideshow-indicator');
            const closeButton = document.getElementById('slideshowClose');

            let currentSlide = 0;
            let slideshowInterval;
            const slideDuration = 4000; // 4 seconds per slide

            // Function to show a specific slide
            function showSlide(index) {
                // Hide all slides
                slides.forEach(slide => slide.classList.remove('active'));
                indicators.forEach(indicator => indicator.classList.remove('active'));

                // Show the current slide
                slides[index].classList.add('active');
                indicators[index].classList.add('active');

                currentSlide = index;
                console.log(`🖼️ Showing slideshow slide ${index + 1}`);
            }

            // Function to show next slide
            function nextSlide() {
                currentSlide = (currentSlide + 1) % slides.length;
                showSlide(currentSlide);
            }

            // Function to start slideshow
            function startSlideshow() {
                slideshowInterval = setInterval(nextSlide, slideDuration);
                console.log('🎠 Starting image slideshow');
            }

            // Function to stop slideshow
            function stopSlideshow() {
                clearInterval(slideshowInterval);
                console.log('⏸️ Stopping image slideshow');
            }

            // Function to hide slideshow
            function hideSlideshow() {
                slideshowContainer.style.opacity = '0';
                slideshowContainer.style.transform = 'scale(0.8)';
                stopSlideshow();
                console.log('🙈 Hiding image slideshow');
            }

            // Function to show slideshow
            function showSlideshow() {
                slideshowContainer.style.opacity = '1';
                slideshowContainer.style.transform = 'scale(1)';
                startSlideshow();
                console.log('👁️ Showing image slideshow');
            }

            // Indicator click handlers
            indicators.forEach((indicator, index) => {
                indicator.addEventListener('click', function() {
                    stopSlideshow();
                    showSlide(index);
                    startSlideshow(); // Restart with new timing
                });
            });

            // Close button functionality
            closeButton.addEventListener('click', function() {
                hideSlideshow();
            });

            // Pause on hover
            slideshowContainer.addEventListener('mouseenter', stopSlideshow);
            slideshowContainer.addEventListener('mouseleave', startSlideshow);

            // Start the slideshow
            showSlideshow();

            // Cleanup on page unload
            window.addEventListener('beforeunload', function() {
                stopSlideshow();
            });
        }
    </script>
</body>
</html>