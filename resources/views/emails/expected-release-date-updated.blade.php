<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NU Lipa - Updated Expected Release Date</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #1a202c;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }

        .email-container {
            max-width: 680px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.15);
            position: relative;
        }

        .header {
            background: linear-gradient(135deg, #2c3192 0%, #1e2570 100%);
            padding: 50px 40px;
            text-align: center;
            color: white;
            position: relative;
            overflow: hidden;
        }

        .header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="25" cy="25" r="1" fill="white" opacity="0.08"/><circle cx="75" cy="75" r="1" fill="white" opacity="0.08"/><circle cx="50" cy="10" r="0.5" fill="white" opacity="0.04"/><circle cx="10" cy="50" r="0.5" fill="white" opacity="0.04"/><circle cx="90" cy="30" r="0.5" fill="white" opacity="0.04"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
            opacity: 0.4;
        }

        .header-content {
            position: relative;
            z-index: 2;
        }

        .header h1 {
            font-size: 32px;
            font-weight: 800;
            margin-bottom: 12px;
            letter-spacing: -0.5px;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
        }

        .header p {
            font-size: 18px;
            opacity: 0.95;
            font-weight: 400;
            margin-bottom: 20px;
        }

        .header-content {
            position: relative;
            z-index: 2;
        }

        .logo {
            position: absolute;
            top: 25px;
            left: 30px;
            width: 70px;
            height: 70px;
            z-index: 3;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.15);
            padding: 8px;
            backdrop-filter: blur(10px);
        }

        .content {
            padding: 50px 40px;
        }

        .greeting {
            font-size: 24px;
            font-weight: 700;
            color: #2c3192;
            margin-bottom: 25px;
            letter-spacing: -0.3px;
        }

        .message {
            font-size: 16px;
            line-height: 1.8;
            margin-bottom: 35px;
            color: #4a5568;
        }

        .request-details {
            background: linear-gradient(135deg, #f8fafc 0%, #edf2f7 100%);
            border-radius: 16px;
            padding: 30px;
            margin: 30px 0;
            border: 1px solid #e2e8f0;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            position: relative;
        }

        .request-details::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 4px;
            height: 100%;
            background: linear-gradient(135deg, #2c3192 0%, #1e2570 100%);
            border-radius: 16px 0 0 16px;
        }

        .details-title {
            font-size: 18px;
            font-weight: 700;
            color: #2c3192;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .detail-row {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 16px;
            padding: 12px 0;
            border-bottom: 1px solid #e2e8f0;
        }

        .detail-row:last-child {
            border-bottom: none;
            margin-bottom: 0;
        }

        .detail-label {
            font-weight: 600;
            color: #4a5568;
            min-width: 140px;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .detail-value {
            color: #2d3748;
            flex: 1;
            text-align: right;
            font-weight: 500;
        }

        .highlight-section {
            background: linear-gradient(135deg, #fef5e7 0%, #fed7aa 100%);
            border: 2px solid #f59e0b;
            border-radius: 16px;
            padding: 30px;
            margin: 35px 0;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .highlight-section::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(245, 158, 11, 0.1) 0%, transparent 70%);
            border-radius: 50%;
        }

        .highlight-content {
            position: relative;
            z-index: 2;
        }

        .highlight-content {
            position: relative;
            z-index: 2;
        }

        .highlight-title {
            font-size: 20px;
            font-weight: 800;
            color: #92400e;
            margin-bottom: 15px;
            letter-spacing: -0.3px;
        }

        .highlight-date {
            font-size: 28px;
            font-weight: 900;
            color: #92400e;
            margin-bottom: 8px;
            text-shadow: 0 1px 2px rgba(146, 64, 14, 0.1);
        }

        .highlight-time {
            font-size: 16px;
            color: #92400e;
            opacity: 0.9;
            font-weight: 600;
        }

        .important-notice {
            background: linear-gradient(135deg, #ecfdf5 0%, #d1fae5 100%);
            border: 2px solid #10b981;
            border-radius: 16px;
            padding: 25px;
            margin: 35px 0;
            position: relative;
        }

        .important-notice::before {
            position: absolute;
            top: -15px;
            left: 25px;
            background: white;
            padding: 8px;
            border-radius: 50%;
            border: 3px solid #10b981;
            font-size: 20px;
        }

        .notice-content {
            margin-left: 10px;
        }

        .notice-title {
            font-size: 18px;
            font-weight: 700;
            color: #065f46;
            margin-bottom: 12px;
        }

        .notice-text {
            font-size: 15px;
            color: #065f46;
            line-height: 1.7;
            margin-bottom: 8px;
        }

        .contact-info {
            background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
            border: 1px solid #0ea5e9;
            border-radius: 12px;
            padding: 20px;
            margin: 30px 0;
            text-align: center;
        }

        .contact-title {
            font-size: 16px;
            font-weight: 700;
            color: #0c4a6e;
            margin-bottom: 8px;
        }

        .contact-text {
            font-size: 14px;
            color: #0c4a6e;
            opacity: 0.9;
        }

        .footer {
            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
            padding: 40px;
            text-align: center;
            border-top: 2px solid #e2e8f0;
            position: relative;
        }

        .footer::before {
            content: '';
            position: absolute;
            top: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 60px;
            height: 4px;
            background: linear-gradient(135deg, #2c3192 0%, #1e2570 100%);
            border-radius: 2px;
        }

        .footer-content {
            position: relative;
            z-index: 2;
        }

        .footer p {
            color: #64748b;
            font-size: 15px;
            margin-bottom: 12px;
            font-weight: 500;
        }

        .footer .signature {
            font-weight: 700;
            color: #2c3192;
            font-size: 16px;
            margin-bottom: 20px;
        }

        .footer .university {
            color: #475569;
            font-size: 14px;
            font-weight: 600;
        }

        .ticket-badge {
            background: linear-gradient(135deg, #2c3192 0%, #1e2570 100%);
            color: white;
            padding: 12px 24px;
            border-radius: 25px;
            font-family: 'Courier New', monospace;
            font-size: 14px;
            font-weight: 600;
            display: inline-block;
            margin-top: 20px;
            box-shadow: 0 4px 8px rgba(44, 49, 146, 0.3);
            letter-spacing: 1px;
        }

        .status-badge {
            display: inline-block;
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .status-completed {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
        }

        .status-processing {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            color: white;
        }

        @media (max-width: 600px) {
            .email-container {
                margin: 10px;
                border-radius: 15px;
            }

            .header, .content, .footer {
                padding: 30px 20px;
            }

            .header h1 {
                font-size: 26px;
            }

            .header p {
                font-size: 16px;
            }

            .greeting {
                font-size: 20px;
            }

            .detail-row {
                flex-direction: column;
                align-items: flex-start;
                gap: 4px;
            }

            .detail-label {
                min-width: auto;
                margin-bottom: 4px;
            }

            .detail-value {
                text-align: left;
                margin-top: 4px;
            }

            .highlight-date {
                font-size: 24px;
            }

            .request-details, .highlight-section, .important-notice {
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="header">
            <img src="{{ asset('images/NU_shield.svg.png') }}" alt="NU Logo" class="logo">
            <div class="header-content">
                <h1>Release Date Updated</h1>
                <p>Your {{ $requestType === 'student' ? 'document' : 'on-site' }} request has been updated</p>
            </div>
        </div>

        <div class="content">
            <div class="greeting">
                Hello {{ $requestType === 'student' ? $request->student->user->first_name . ' ' . $request->student->user->last_name : $request->full_name }},
            </div>

            <div class="message">
                <p>We have updated the expected release date for your {{ $requestType === 'student' ? 'document' : 'on-site' }} request. Please find the updated details below:</p>
            </div>

            <div class="request-details">
                <div class="details-title">Request Details</div>

                <div class="detail-row">
                    <span class="detail-label">Ticket Number</span>
                    <span class="detail-value">
                        @if($requestType === 'student')
                            {{ $request->created_at->format('Ymd') }}-{{ $request->id }}
                        @else
                            {{ $request->created_at->format('Ymd') }}-i{{ $request->id }}
                        @endif
                    </span>
                </div>

                <div class="detail-row">
                    <span class="detail-label">Reference Code</span>
                    <span class="detail-value">{{ $requestType === 'student' ? $request->reference_no : ($request->ref_code ?? 'Not available') }}</span>
                </div>

                @if($requestType === 'onsite')
                <div class="detail-row">
                    <span class="detail-label">Course</span>
                    <span class="detail-value">{{ $request->course }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Year Level</span>
                    <span class="detail-value">{{ $request->year_level }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Department</span>
                    <span class="detail-value">{{ $request->department }}</span>
                </div>
                @endif

                <div class="detail-row">
                    <span class="detail-label">Status</span>
                    <span class="detail-value">
                        <span class="status-badge status-{{ $request->status === 'completed' ? 'completed' : 'processing' }}">
                            {{ ucfirst(str_replace('_', ' ', $request->status)) }}
                        </span>
                    </span>
                </div>

                <div class="detail-row">
                    <span class="detail-label">Documents</span>
                    <span class="detail-value">
                        @if($request->requestItems->count() > 0)
                            @foreach($request->requestItems as $item)
                                {{ $item->document->type_document }} (x{{ $item->quantity }})
                                @if(!$loop->last), @endif
                            @endforeach
                        @else
                            Not specified
                        @endif
                    </span>
                </div>
            </div>

            <div class="highlight-section">
                <div class="highlight-content">
                    <div class="highlight-title">New Expected Release Date</div>
                    <div class="highlight-date">{{ \Carbon\Carbon::parse($request->expected_release_date)->format('l, F j, Y') }}</div>
                    <div class="highlight-time">at {{ \Carbon\Carbon::parse($request->expected_release_date)->format('g:i A') }}</div>
                </div>
            </div>

            <div class="important-notice">
                <div class="notice-content">
                    <div class="notice-title">Important Information</div>
                    <div class="notice-text">
                        Please arrive at the registrar's office at the specified date and time to pick up your documents.
                        Bring your ticket number and any required identification.
                    </div>
                    <div class="notice-text">
                        If you have any questions or need to make changes to your request, please contact the registrar's office immediately.
                    </div>
                </div>
            </div>

            <div class="contact-info">
                <div class="contact-title">Need Help?</div>
                <div class="contact-text">
                    Contact the Registrar's Office at piquizon@nu-lipa.edu.ph or visit our office during business hours.
                </div>
            </div>
        </div>

        <div class="footer">
            <div class="footer-content">
                <p>Thank you for using NU Lipa's Document Request System</p>
                <div class="signature">NU Lipa Registrar's Office</div>
                <div class="university">National University - Lipa</div>
                <div class="ticket-badge">
                    @if($requestType === 'student')
                        {{ $request->created_at->format('Ymd') }}-{{ $request->id }}
                    @else
                        {{ $request->created_at->format('Ymd') }}-i{{ $request->id }}
                    @endif
                </div>
            </div>
        </div>
    </div>
</body>
</html>