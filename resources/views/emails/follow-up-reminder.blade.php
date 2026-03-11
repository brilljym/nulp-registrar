<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NU Lipa - Document Pickup Reminder</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }

        .email-container {
            max-width: 650px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            position: relative;
        }

        .header {
            background: linear-gradient(135deg, #f39c12 0%, #e67e22 100%);
            padding: 40px 30px;
            text-align: center;
            color: white;
            position: relative;
        }

        .header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="25" cy="25" r="1" fill="white" opacity="0.1"/><circle cx="75" cy="75" r="1" fill="white" opacity="0.1"/><circle cx="50" cy="10" r="0.5" fill="white" opacity="0.05"/><circle cx="10" cy="50" r="0.5" fill="white" opacity="0.05"/><circle cx="90" cy="30" r="0.5" fill="white" opacity="0.05"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
            opacity: 0.3;
        }

        .header h1 {
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 8px;
            position: relative;
            z-index: 1;
        }

        .header p {
            font-size: 16px;
            opacity: 0.9;
            position: relative;
            z-index: 1;
        }

        .logo {
            position: absolute;
            top: 20px;
            left: 20px;
            width: 60px;
            height: 60px;
            z-index: 2;
        }

        .content {
            padding: 40px 30px;
        }

        .greeting {
            font-size: 20px;
            font-weight: 600;
            color: #2c2f92;
            margin-bottom: 20px;
        }

        .message {
            font-size: 16px;
            line-height: 1.7;
            margin-bottom: 30px;
            color: #555;
        }

        .request-details {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border-radius: 12px;
            padding: 25px;
            margin: 25px 0;
            border-left: 4px solid #f39c12;
        }

        .detail-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 12px;
            padding: 8px 0;
            border-bottom: 1px solid #dee2e6;
        }

        .detail-row:last-child {
            border-bottom: none;
            margin-bottom: 0;
        }

        .detail-label {
            font-weight: 600;
            color: #2c2f92;
            flex: 1;
        }

        .detail-value {
            flex: 2;
            text-align: right;
            color: #333;
        }

        .highlight-box {
            background: linear-gradient(135deg, #f39c12 0%, #e67e22 100%);
            border-radius: 12px;
            padding: 25px;
            text-align: center;
            margin: 25px 0;
            color: white;
        }

        .highlight-text {
            font-size: 18px;
            font-weight: 700;
            margin-bottom: 5px;
        }

        .highlight-subtext {
            font-size: 14px;
            opacity: 0.9;
        }

        .footer {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            padding: 30px;
            text-align: center;
            border-top: 1px solid #dee2e6;
        }

        .footer p {
            color: #6c757d;
            font-size: 14px;
            margin-bottom: 10px;
        }

        .footer .signature {
            font-weight: 600;
            color: #2c2f92;
        }

        .ticket-number {
            background: #f39c12;
            color: white;
            padding: 8px 16px;
            border-radius: 20px;
            font-family: 'Courier New', monospace;
            font-size: 14px;
            display: inline-block;
            margin-top: 10px;
        }

        .status-badge {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .status-completed {
            background: #d4edda;
            color: #155724;
        }

        .urgent-notice {
            background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
            border-radius: 12px;
            padding: 20px;
            margin: 25px 0;
            color: white;
            text-align: center;
            border-left: 4px solid #bd2130;
        }

        .urgent-text {
            font-size: 16px;
            font-weight: 700;
            margin-bottom: 5px;
        }

        .urgent-subtext {
            font-size: 14px;
            opacity: 0.9;
        }

        @media (max-width: 600px) {
            .detail-row {
                flex-direction: column;
                align-items: flex-start;
            }

            .detail-value {
                text-align: left;
                margin-top: 4px;
            }
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="header">
            <img src="{{ asset('images/NU_shield.svg.png') }}" alt="NU Logo" class="logo">
            <h1>Document Pickup Reminder</h1>
            <p>Your {{ $requestType }} document request is ready for collection</p>
        </div>

        <div class="content">
            <div class="greeting">
                Hello {{ $requestType === 'onsite' ? $request->full_name : $request->student->user->first_name . ' ' . $request->student->user->last_name }},
            </div>

            <div class="message">
                <p>This is a friendly reminder that your {{ $requestType }} document request has been completed and is ready for pickup. It's been a few days since your request was processed, and we want to ensure you don't forget to collect your important documents.</p>
            </div>

            <div class="urgent-notice">
                <div class="urgent-text">
                    ⏰ Please Collect Your Documents Soon
                </div>
                <div class="urgent-subtext">
                    Documents not collected within 30 days may be subject to storage fees or disposal
                </div>
            </div>

            <div class="request-details">
                <div class="detail-row">
                    <span class="detail-label">Reference Code:</span>
                    <span class="detail-value">{{ $requestType === 'onsite' ? $request->ref_code : $request->reference_no }}</span>
                </div>
                @if($requestType === 'onsite')
                <div class="detail-row">
                    <span class="detail-label">Ticket Number:</span>
                    <span class="detail-value">{{ $request->created_at->format('Ymd') }}-i{{ $request->id }}</span>
                </div>
                @endif
                @if($requestType === 'student')
                <div class="detail-row">
                    <span class="detail-label">Student ID:</span>
                    <span class="detail-value">{{ $request->student->student_id }}</span>
                </div>
                @endif
                <div class="detail-row">
                    <span class="detail-label">Course:</span>
                    <span class="detail-value">{{ $request->student ? $request->student->course : $request->course }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Year Level:</span>
                    <span class="detail-value">{{ $request->student ? $request->student->year_level : $request->year_level }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Department:</span>
                    <span class="detail-value">{{ $request->student ? $request->student->department : $request->department }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Status:</span>
                    <span class="detail-value">
                        <span class="status-badge status-completed">{{ ucfirst($request->status) }}</span>
                    </span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Documents:</span>
                    <span class="detail-value">
                        @foreach($request->requestItems as $item)
                            {{ $item->document->type_document }} (x{{ $item->quantity }})
                            @if(!$loop->last), @endif
                        @endforeach
                    </span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Completed Date:</span>
                    <span class="detail-value">{{ $request->updated_at->format('M j, Y g:i A') }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Days Since Completion:</span>
                    <span class="detail-value">{{ $request->updated_at->diffInDays(now()) }} days</span>
                </div>
            </div>

            <div class="highlight-box">
                <div class="highlight-text">
                    📍 Ready for Pickup at Registrar's Office
                </div>
                <div class="highlight-subtext">
                    Please visit the registrar's office during business hours to collect your documents
                </div>
            </div>

            <div class="message">
                <p><strong>Important Information:</strong></p>
                <ul style="margin-left: 20px; margin-bottom: 20px;">
                    <li>Bring your reference code and valid ID when picking up your documents</li>
                    <li>Documents are typically available for pickup within 24-48 hours after completion</li>
                    <li>If you have already collected your documents, please disregard this reminder</li>
                    <li>For any questions or concerns, contact the registrar's office</li>
                </ul>

                <p><strong>Thank you for using NU Lipa's Document Request System!</strong></p>

                <p>We appreciate your patience and cooperation. Your documents are important to us, and we're here to ensure you receive them promptly and securely.</p>
            </div>
        </div>

        <div class="footer">
            <p>Thank you for using NU Lipa's Document Request System</p>
            <p class="signature">NU Lipa Registrar's Office</p>
            @if($requestType === 'onsite')
            <div class="ticket-number">
                Ticket: {{ $request->created_at->format('Ymd') }}-i{{ $request->id }}
            </div>
            @endif
        </div>
    </div>
</body>
</html>