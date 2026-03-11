<?php

require 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "Mail Configuration:\n";
echo "Default mailer: " . config('mail.default') . "\n";
echo "SMTP host: " . config('mail.mailers.smtp.host') . "\n";
echo "SMTP port: " . config('mail.mailers.smtp.port') . "\n";
echo "SMTP username: " . config('mail.mailers.smtp.username') . "\n";
echo "SMTP encryption: " . config('mail.mailers.smtp.encryption') . "\n";

// Test RequestRejectedMail
try {
    echo "\nTesting RequestRejectedMail...\n";

    // Get a student request to test with
    $studentRequest = App\Models\StudentRequest::first();
    if (!$studentRequest) {
        echo "No student requests found to test with.\n";
        exit;
    }

    echo "Using student request ID: {$studentRequest->id}\n";
    echo "Has student relationship: " . ($studentRequest->student ? 'Yes' : 'No') . "\n";
    echo "Has user relationship: " . ($studentRequest->student && $studentRequest->student->user ? 'Yes' : 'No') . "\n";

    if ($studentRequest->student && $studentRequest->student->user) {
        $email = $studentRequest->student->user->personal_email ?? $studentRequest->student->user->school_email;
        echo "Personal email: " . ($studentRequest->student->user->personal_email ?? 'null') . "\n";
        echo "School email: " . ($studentRequest->student->user->school_email ?? 'null') . "\n";
        echo "Selected email: " . ($email ?? 'null') . "\n";

        if ($email) {
            echo "Will send email to: {$email}\n";
        } else {
            echo "No email found - email will NOT be sent\n";
        }
    } else {
        echo "Missing student or user relationship - email will NOT be sent\n";
    }

    $mail = new App\Mail\RequestRejectedMail($studentRequest, 'student', 'Test rejection remarks');
    echo "RequestRejectedMail created successfully\n";

    // Try to send the email
    \Illuminate\Support\Facades\Mail::to('test@example.com')->send($mail);
    echo "RequestRejectedMail sent successfully to test@example.com\n";

} catch (Exception $e) {
    echo "RequestRejectedMail test failed: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}