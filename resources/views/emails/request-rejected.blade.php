<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NU Lipa - Request Rejected</title>
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
            background: linear-gradient(135deg, #dc3545 0%, #b02a37 100%);
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
            color: #dc3545;
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
            border-left: 4px solid #dc3545;
        }

        .detail-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 12px;
            padding-bottom: 8px;
            border-bottom: 1px solid #dee2e6;
        }

        .detail-row:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }

        .detail-label {
            font-weight: 600;
            color: #dc3545;
            min-width: 140px;
        }

        .detail-value {
            color: #333;
            text-align: right;
            flex: 1;
        }

        .remarks-box {
            background: linear-gradient(135deg, #fff3cd 0%, #ffeaa7 100%);
            border: 2px solid #ffc107;
            border-radius: 12px;
            padding: 20px;
            margin: 25px 0;
        }

        .remarks-title {
            font-size: 18px;
            font-weight: 700;
            color: #856404;
            margin-bottom: 12px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .remarks-content {
            font-size: 14px;
            color: #856404;
            background: white;
            padding: 15px;
            border-radius: 8px;
            border: 1px solid #ffeaa7;
            font-style: italic;
        }

        .action-box {
            background: linear-gradient(135deg, #d1ecf1 0%, #bee5eb 100%);
            border: 2px solid #17a2b8;
            border-radius: 12px;
            padding: 20px;
            margin: 25px 0;
            text-align: center;
        }

        .action-text {
            font-size: 16px;
            font-weight: 600;
            color: #0c5460;
            margin-bottom: 8px;
        }

        .action-subtext {
            font-size: 14px;
            color: #0c5460;
            opacity: 0.8;
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
            color: #dc3545;
        }

        .ticket-number {
            background: #dc3545;
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

        .status-rejected {
            background: #f8d7da;
            color: #721c24;
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
            <h1>Request Rejected</h1>
            <p>Your {{ $requestType === 'student' ? 'document' : 'on-site' }} request has been rejected</p>
        </div>

        <div class="content">
            <div class="greeting">
                Hello {{ $requestType === 'student' ? $request->student->user->first_name . ' ' . $request->student->user->last_name : $request->full_name }},
            </div>

            <div class="message">
                <p>We regret to inform you that your {{ $requestType === 'student' ? 'document' : 'on-site' }} request has been rejected by the registrar. Please review the details below and the remarks from the registrar.</p>
            </div>

            <div class="remarks-box">
                <div class="remarks-title">
                    <span>💬</span> Registrar Remarks
                </div>
                <div class="remarks-content">
                    {{ $remarks }}
                </div>
            </div>

            <div class="request-details">
                <div class="detail-row">
                    <span class="detail-label">Ticket Number:</span>
                    <span class="detail-value">
                        @if($requestType === 'student')
                            {{ $request->created_at->format('Ymd') }}-{{ $request->id }}
                        @else
                            {{ $request->created_at->format('Ymd') }}-i{{ $request->id }}
                        @endif
                    </span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Reference Code:</span>
                    <span class="detail-value">{{ $requestType === 'student' ? $request->reference_no : ($request->ref_code ?? 'Not available') }}</span>
                </div>
                @if($requestType === 'onsite')
                <div class="detail-row">
                    <span class="detail-label">Course:</span>
                    <span class="detail-value">{{ $request->course }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Year Level:</span>
                    <span class="detail-value">{{ $request->year_level }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Department:</span>
                    <span class="detail-value">{{ $request->department }}</span>
                </div>
                @endif
                <div class="detail-row">
                    <span class="detail-label">Status:</span>
                    <span class="detail-value">
                        <span class="status-badge status-rejected">{{ ucfirst($request->status) }}</span>
                    </span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Documents:</span>
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
                <div class="detail-row">
                    <span class="detail-label">Request Date:</span>
                    <span class="detail-value">{{ $request->created_at->format('M d, Y') }}</span>
                </div>
            </div>

            <div class="action-box">
                <div class="action-text">
                    🔄 Next Steps
                </div>
                <div class="action-subtext">
                    Please review the registrar's remarks and re-submit your request with the necessary corrections. You can access your request timeline to make changes and re-approve.
                </div>
            </div>

            <div class="message">
                <p>If you have any questions about this rejection or need assistance with re-submitting your request, please contact the registrar's office.</p>
            </div>
        </div>

        <div class="footer">
            <p>Thank you for using NU Lipa's Document Request System</p>
            <p class="signature">NU Lipa Registrar's Office</p>
            <div class="ticket-number">
                Ticket:
                @if($requestType === 'student')
                    {{ $request->created_at->format('Ymd') }}-{{ $request->id }}
                @else
                    {{ $request->created_at->format('Ymd') }}-i{{ $request->id }}
                @endif
            </div>
        </div>
    </div>
</body>
</html>