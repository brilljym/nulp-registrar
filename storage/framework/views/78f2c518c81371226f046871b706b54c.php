<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NU Document Request System - National University Lipa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --nu-blue: #1e3a8a;
            --nu-light-blue: #3b82f6;
            --nu-accent: #6366f1;
            --nu-white: #ffffff;
            --nu-gray-50: #f8fafc;
            --nu-gray-100: #f1f5f9;
            --nu-gray-200: #e2e8f0;
            --nu-gray-300: #cbd5e1;
            --nu-gray-600: #475569;
            --nu-gray-700: #334155;
            --nu-gray-800: #1e293b;
            --nu-gray-900: #0f172a;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html {
            background: var(--nu-blue);
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            line-height: 1.6;
            overflow-x: hidden;
            background: var(--nu-blue);
            margin: 0;
            padding: 0;
        }

        .hero-section {
            min-height: 100vh;
            background: linear-gradient(135deg, var(--nu-blue) 0%, var(--nu-light-blue) 100%);
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            overflow: hidden;
        }

        .hero-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: 
                radial-gradient(circle at 30% 20%, rgba(255, 255, 255, 0.15) 0%, transparent 60%),
                radial-gradient(circle at 70% 80%, rgba(255, 255, 255, 0.1) 0%, transparent 60%),
                linear-gradient(45deg, transparent 30%, rgba(255, 255, 255, 0.05) 50%, transparent 70%);
            opacity: 0;
            animation: backgroundFadeIn 2s ease-out 0.3s forwards;
        }

        .hero-section::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: 
                radial-gradient(circle 200px at 20% 30%, rgba(99, 102, 241, 0.1) 0%, transparent 100%),
                radial-gradient(circle 300px at 80% 70%, rgba(59, 130, 246, 0.1) 0%, transparent 100%);
            animation: floatingOrbs 8s ease-in-out infinite;
        }

        .hero-content {
            position: relative;
            z-index: 2;
            text-align: center;
            max-width: 800px;
            width: 100%;
            margin: 0 auto;
            padding: 2rem;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }

        .logo-container {
            margin-bottom: 2.5rem;
            display: flex;
            flex-direction: column;
            align-items: center;
            width: 100%;
        }

        .nu-logo {
            width: 150px;
            height: 150px;
            margin: 0 auto 1.5rem;
            background: var(--nu-white);
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
            border: 4px solid rgba(255, 255, 255, 0.3);
            animation: logoEntrance 2s cubic-bezier(0.68, -0.55, 0.265, 1.55);
            transform: scale(0);
            overflow: hidden;
            padding: 10px;
        }

        .nu-logo img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }

        .hero-title {
            font-size: 3rem;
            font-weight: 800;
            margin-bottom: 0.5rem;
            color: var(--nu-white);
            line-height: 1.2;
            letter-spacing: -0.02em;
            opacity: 0;
            animation: slideInFromBottom 1.2s cubic-bezier(0.25, 0.46, 0.45, 0.94) 0.5s forwards;
        }

        .hero-subtitle {
            font-size: 1.25rem;
            font-weight: 400;
            margin-bottom: 3rem;
            color: rgba(255, 255, 255, 0.85);
            letter-spacing: 0.01em;
            opacity: 0;
            animation: slideInFromBottom 1.2s cubic-bezier(0.25, 0.46, 0.45, 0.94) 0.8s forwards;
        }

        .hero-description {
            font-size: 1.125rem;
            margin-bottom: 2rem;
            color: rgba(255, 255, 255, 0.9);
            max-width: 520px;
            text-align: center;
            line-height: 1.6;
            opacity: 0;
            animation: fadeInUp 1s cubic-bezier(0.25, 0.46, 0.45, 0.94) 1.2s forwards;
        }



        .nu-logo.loaded {
            animation: pulse 3s ease-in-out infinite 2s;
        }

        @keyframes logoEntrance {
            0% {
                transform: scale(0);
                opacity: 0;
            }
            50% {
                transform: scale(1.1);
                opacity: 0.8;
            }
            100% {
                transform: scale(1);
                opacity: 1;
            }
        }

        @keyframes slideInFromBottom {
            0% {
                opacity: 0;
                transform: translateY(50px);
            }
            100% {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fadeInUp {
            0% {
                opacity: 0;
                transform: translateY(30px);
            }
            100% {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes backgroundFadeIn {
            0% {
                opacity: 0;
            }
            100% {
                opacity: 1;
            }
        }

        @keyframes floatingOrbs {
            0%, 100% {
                transform: translateY(0px) translateX(0px) rotate(0deg);
            }
            25% {
                transform: translateY(-20px) translateX(10px) rotate(90deg);
            }
            50% {
                transform: translateY(-10px) translateX(-10px) rotate(180deg);
            }
            75% {
                transform: translateY(-15px) translateX(5px) rotate(270deg);
            }
        }



        @keyframes pulse {
            0%, 100% {
                box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
            }
            50% {
                box-shadow: 0 25px 50px rgba(0, 0, 0, 0.3), 0 0 30px rgba(255, 255, 255, 0.2);
            }
        }

        @media (max-width: 768px) {
            .hero-content {
                padding: 1.5rem;
                max-width: 100%;
            }
            
            .logo-container {
                margin-bottom: 2rem;
            }
            
            .nu-logo {
                width: 120px;
                height: 120px;
                margin-bottom: 1rem;
                padding: 8px;
            }
            
            .hero-title {
                font-size: 2.25rem;
            }
            
            .hero-subtitle {
                font-size: 1.1rem;
                margin-bottom: 1.5rem;
            }
            
            .hero-description {
                font-size: 1rem;
                margin-bottom: 1.5rem;
                max-width: 100%;
            }
            

        }

        @media (max-width: 480px) {
            .hero-content {
                padding: 1rem;
            }
            
            .hero-title {
                font-size: 2rem;
            }
            
            .hero-subtitle {
                font-size: 1rem;
            }
            
            .hero-description {
                font-size: 0.95rem;
            }
        }
    </style>
</head>
<body>
    <div class="hero-section">
        <div class="hero-content">
            <div class="logo-container">
                <div class="nu-logo">
                    <img src="<?php echo e(asset('images/nuliparegis.jpg')); ?>" alt="National University Lipa" />
                </div>
                <h1 class="hero-title">Document Request System</h1>
                <p class="hero-subtitle">National University Lipa</p>
            </div>
            
            <p class="hero-description">
                Streamline your document requests with our modern, efficient system. 
                Request transcripts, certificates, and other academic documents quickly and securely.
            </p>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Add loaded class to logo for pulse animation
            const logo = document.querySelector('.nu-logo');
            setTimeout(() => {
                logo.classList.add('loaded');
            }, 2000);

            // Redirect to login after a clean delay
            setTimeout(() => {
                // Add smooth exit animation before redirect
                document.body.style.transition = 'opacity 0.8s ease-out';
                document.body.style.opacity = '0';
                
                setTimeout(() => {
                    window.location.href = '/login';
                }, 800);
            }, 4000); // Wait 4 seconds for user to see the page
        });
    </script>
</body>
</html>
<?php /**PATH D:\Nu-Regisv2\resources\views\welcome.blade.php ENDPATH**/ ?>