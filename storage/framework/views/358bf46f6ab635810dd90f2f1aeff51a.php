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
            color: #2c2f92;
            min-width: 140px;
        }

        .detail-value {
            color: #333;
            text-align: right;
            flex: 1;
        }

        .highlight-box {
            background: linear-gradient(135deg, #fff3cd 0%, #ffeaa7 100%);
            border: 2px solid #ffc107;
            border-radius: 12px;
            padding: 20px;
            margin: 25px 0;
            text-align: center;
        }

        .highlight-text {
            font-size: 18px;
            font-weight: 700;
            color: #856404;
            margin-bottom: 8px;
        }

        .highlight-subtext {
            font-size: 14px;
            color: #856404;
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
            <img src="<?php echo e(asset('images/NU_shield.svg.png')); ?>" alt="NU Logo" class="logo">
            <h1>📅 Release Date Updated</h1>
            <p>Your on-site document request has been updated</p>
        </div>

        <div class="content">
            <div class="greeting">
                Hello <?php echo e($onsiteRequest->full_name); ?>,
            </div>

            <div class="message">
                <p>We have updated the expected release date for your on-site document request. Please find the updated details below:</p>
            </div>

            <div class="request-details">
                <div class="detail-row">
                    <span class="detail-label">Ticket Number:</span>
                    <span class="detail-value"><?php echo e($onsiteRequest->created_at->format('Ymd')); ?>-i<?php echo e($onsiteRequest->id); ?></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Reference Code:</span>
                    <span class="detail-value"><?php echo e($onsiteRequest->ref_code ?? 'Not available'); ?></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Course:</span>
                    <span class="detail-value"><?php echo e($onsiteRequest->course); ?></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Year Level:</span>
                    <span class="detail-value"><?php echo e($onsiteRequest->year_level); ?></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Department:</span>
                    <span class="detail-value"><?php echo e($onsiteRequest->department); ?></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Status:</span>
                    <span class="detail-value">
                        <span class="status-badge status-completed"><?php echo e(ucfirst($onsiteRequest->status)); ?></span>
                    </span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Documents:</span>
                    <span class="detail-value">
                        <?php if($onsiteRequest->requestItems->count() > 0): ?>
                            <?php $__currentLoopData = $onsiteRequest->requestItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php echo e($item->document->type_document); ?> (x<?php echo e($item->quantity); ?>)
                                <?php if(!$loop->last): ?>, <?php endif; ?>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <?php else: ?>
                            Not specified
                        <?php endif; ?>
                    </span>
                </div>
            </div>

            <div class="highlight-box">
                <div class="highlight-text">
                    📅 New Expected Release Date
                </div>
                <div class="highlight-text">
                    <?php echo e(\Carbon\Carbon::parse($onsiteRequest->expected_release_date)->format('l, F j, Y')); ?>

                </div>
                <div class="highlight-subtext">
                    at <?php echo e(\Carbon\Carbon::parse($onsiteRequest->expected_release_date)->format('g:i A')); ?>

                </div>
            </div>

            <div class="message">
                <p><strong>Important:</strong> Please arrive at the registrar's office at the specified date and time to pick up your documents. Bring your ticket number and any required identification.</p>

                <p>If you have any questions or need to make changes to your request, please contact the registrar's office immediately.</p>
            </div>
        </div>

        <div class="footer">
            <p>Thank you for using NU Lipa's Document Request System</p>
            <p class="signature">NU Lipa Registrar's Office</p>
            <div class="ticket-number">
                Ticket: <?php echo e($onsiteRequest->created_at->format('Ymd')); ?>-i<?php echo e($onsiteRequest->id); ?>

            </div>
        </div>
    </div>
</body>
</html><?php /**PATH D:\Nu-Regisv2\resources\views\emails\expected-release-date-updated.blade.php ENDPATH**/ ?>