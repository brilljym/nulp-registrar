<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NU Lipa - Payment Approved</title>
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

        .status-processing {
            background: #fff3cd;
            color: #856404;
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
            <img src="<?php echo e(asset('images/NU_shield.svg.png')); ?>" alt="NU Logo" class="logo">
            <h1>✅ Payment Approved</h1>
            <p>Your <?php echo e($requestType); ?> document request payment has been approved</p>
        </div>

        <div class="content">
            <div class="greeting">
                Hello <?php echo e($requestType === 'onsite' ? $request->full_name : $request->student->user->first_name . ' ' . $request->student->user->last_name); ?>,
            </div>

            <div class="message">
                <p>Great news! Your payment for the <?php echo e($requestType); ?> document request has been approved by our accounting department. Your request is now moving forward in the processing queue.</p>
            </div>

            <div class="request-details">
                <div class="detail-row">
                    <span class="detail-label">Reference Code:</span>
                    <span class="detail-value"><?php echo e($requestType === 'onsite' ? $request->ref_code : $request->reference_no); ?></span>
                </div>
                <?php if($requestType === 'onsite'): ?>
                <div class="detail-row">
                    <span class="detail-label">Ticket Number:</span>
                    <span class="detail-value"><?php echo e($request->created_at->format('Ymd')); ?>-i<?php echo e($request->id); ?></span>
                </div>
                <?php endif; ?>
                <?php if($requestType === 'student'): ?>
                <div class="detail-row">
                    <span class="detail-label">Student ID:</span>
                    <span class="detail-value"><?php echo e($request->student->student_id); ?></span>
                </div>
                <?php endif; ?>
                <div class="detail-row">
                    <span class="detail-label">Course:</span>
                    <span class="detail-value"><?php echo e($request->student ? $request->student->course : $request->course); ?></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Year Level:</span>
                    <span class="detail-value"><?php echo e($request->student ? $request->student->year_level : $request->year_level); ?></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Department:</span>
                    <span class="detail-value"><?php echo e($request->student ? $request->student->department : $request->department); ?></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Status:</span>
                    <span class="detail-value">
                        <span class="status-badge status-processing"><?php echo e(ucfirst($request->status)); ?></span>
                    </span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Documents:</span>
                    <span class="detail-value">
                        <?php $__currentLoopData = $request->requestItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php echo e($item->document->type_document); ?> (x<?php echo e($item->quantity); ?>)
                            <?php if(!$loop->last): ?>, <?php endif; ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Total Amount:</span>
                    <span class="detail-value">₱<?php echo e(number_format($request->requestItems->sum(function($item) { return $item->document->price * $item->quantity; }), 2)); ?></span>
                </div>
            </div>

            <div class="highlight-box">
                <div class="highlight-text">
                    🔄 Next Steps
                </div>
                <div class="highlight-subtext">
                    Your request is now being processed by our registrar team
                </div>
            </div>

            <div class="message">
                <p><strong>What happens next?</strong></p>
                <ul>
                    <li>Your request will be assigned to a registrar for processing</li>
                    <li>You will receive another notification when your documents are ready</li>
                    <li>Please keep your reference code for tracking purposes</li>
                </ul>

                <p>If you have any questions about your request, please contact the registrar's office.</p>
            </div>
        </div>

        <div class="footer">
            <p>Thank you for using NU Lipa's Document Request System</p>
            <p class="signature">NU Lipa Registrar's Office</p>
            <?php if($requestType === 'onsite'): ?>
            <div class="ticket-number">
                Ticket: <?php echo e($request->created_at->format('Ymd')); ?>-i<?php echo e($request->id); ?>

            </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html><?php /**PATH D:\Nu-Regisv2\resources\views\emails\payment-approved.blade.php ENDPATH**/ ?>