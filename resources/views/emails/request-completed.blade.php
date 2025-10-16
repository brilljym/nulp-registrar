<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NU Lipa - Request Completed</title>
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
            background: linear-gradient(135deg, #2c2f92 0%, #1e2461 100%);
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
            border-left: 4px solid #2c2f92;
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
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
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
            background: #2c2f92;
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
            <h1>🎉 Request Completed</h1>
            <p>Your {{ $requestType }} document request has been successfully completed</p>
        </div>

        <div class="content">
            <div class="greeting">
                Hello {{ $requestType === 'onsite' ? $request->full_name : $request->student->user->first_name . ' ' . $request->student->user->last_name }},
            </div>

            <div class="message">
                <p>Congratulations! Your {{ $requestType }} document request has been successfully completed. Please check your email for further updates about your requested documents. We will notify you once your documents are ready for pickup.</p>
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
            </div>

            <div class="highlight-box">
                <div class="highlight-text">
                    ✅ All Documents Released
                </div>
                <div class="highlight-subtext">
                    Your request has been fully processed and completed
                </div>
            </div>

            <div class="message">
                <p><strong>Thank you for using NU Lipa's Document Request System!</strong></p>

                <p>Your documents have been successfully processed and released. If you need to request additional documents in the future, you can easily do so through our online system.</p>

                <p>If you have any feedback about your experience or questions about your completed request, please don't hesitate to contact the registrar's office.</p>

                <p>We hope to serve you again soon!</p>
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