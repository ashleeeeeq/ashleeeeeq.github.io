<?php
// test_email.php
require_once 'vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

echo "<h2>Testing Email Configuration</h2>";

// Check if .env exists
if (file_exists('.env')) {
    echo "✓ .env file found<br>";
    
    // Load .env manually for testing
    $lines = file('.env');
    $emailConfig = [];
    foreach ($lines as $line) {
        if (strpos($line, 'email.') === 0) {
            echo "✓ " . trim($line) . "<br>";
            $parts = explode('=', trim($line), 2);
            if (count($parts) == 2) {
                $emailConfig[$parts[0]] = $parts[1];
            }
        }
    }
    
    // Test email sending
    if (!empty($emailConfig['email.smtp_user']) && $emailConfig['email.smtp_user'] != 'your-email@gmail.com') {
        echo "<h3>Sending test email...</h3>";
        
        $mail = new PHPMailer(true);
        
        try {
            // Server settings
            $mail->isSMTP();
            $mail->Host       = $emailConfig['email.smtp_host'] ?? 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = $emailConfig['email.smtp_user'];
            $mail->Password   = $emailConfig['email.smtp_pass'];
            $mail->SMTPSecure = ($emailConfig['email.smtp_port'] ?? 587) == 465 ? PHPMailer::ENCRYPTION_SMTPS : PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = $emailConfig['email.smtp_port'] ?? 587;
            
            // Recipients
            $mail->setFrom($emailConfig['email.from_email'] ?? $emailConfig['email.smtp_user'], 'CREATRIX Test');
            $mail->addAddress($emailConfig['email.smtp_user']); // Send to yourself
            
            // Content
            $mail->isHTML(true);
            $mail->Subject = 'Test Email from CREATRIX';
            $mail->Body    = '<h1>Test Successful!</h1><p>Your email configuration is working correctly!</p>';
            
            $mail->send();
            echo "<p style='color:green; font-weight:bold;'>✓ Test email sent successfully! Check your inbox.</p>";
            
        } catch (Exception $e) {
            echo "<p style='color:red; font-weight:bold;'>✗ Email failed: " . $mail->ErrorInfo . "</p>";
        }
    } else {
        echo "<p style='color:orange;'>⚠ Please configure your email settings in .env file</p>";
    }
} else {
    echo "✗ .env file not found!<br>";
}
?>