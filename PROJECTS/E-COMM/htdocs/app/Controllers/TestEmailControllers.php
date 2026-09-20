<?php

namespace App\Controllers;

use App\Libraries\EmailLib;

class TestEmailControllers extends BaseController
{
    public function index()
    {
        echo "<h2>Testing Email Configuration</h2>";
        
        // Check email configuration
        $smtpUser = getenv('email.smtp_user');
        $smtpHost = getenv('email.smtp_host');
        $smtpPort = getenv('email.smtp_port');
        $fromEmail = getenv('email.from_email');
        
        echo "<h3>Current Configuration:</h3>";
        echo "SMTP Host: " . ($smtpHost ?: '<span style="color:red">NOT SET</span>') . "<br>";
        echo "SMTP User: " . ($smtpUser ?: '<span style="color:red">NOT SET</span>') . "<br>";
        echo "SMTP Port: " . ($smtpPort ?: '<span style="color:red">NOT SET</span>') . "<br>";
        echo "From Email: " . ($fromEmail ?: '<span style="color:red">NOT SET</span>') . "<br>";
        
        if (empty($smtpUser) || $smtpUser == 'your-email@gmail.com') {
            echo "<p style='color:orange; margin-top:20px;'>⚠ Please configure your email settings in .env file</p>";
            echo "<p>Add these lines to your .env file:</p>";
            echo "<pre style='background:#f4f4f4; padding:10px; border-radius:5px;'>
email.smtp_host = smtp.gmail.com
email.smtp_port = 587
email.smtp_user = your-email@gmail.com
email.smtp_pass = your-app-password
email.from_email = your-email@gmail.com
email.from_name = CREATRIX Coir
</pre>";
            return;
        }
        
        // Test email sending
        echo "<h3>Sending test email...</h3>";
        
        $emailLib = new EmailLib();
        $result = $emailLib->sendEmail(
            $smtpUser,
            'Test Email from CREATRIX',
            '<h1>Test Successful!</h1><p>Your email configuration is working correctly!</p><p>This is a test email sent from your CREATRIX application.</p>'
        );
        
        if ($result['success']) {
            echo "<p style='color:green; font-weight:bold;'>✓ Test email sent successfully to {$smtpUser}!</p>";
            echo "<p>Check your inbox (and spam folder).</p>";
        } else {
            echo "<p style='color:red; font-weight:bold;'>✗ Email failed: " . $result['message'] . "</p>";
        }
    }
    
    public function testWelcome()
    {
        $emailLib = new EmailLib();
        $testEmail = getenv('email.smtp_user') ?: 'test@example.com';
        
        echo "<h2>Testing Welcome Email</h2>";
        echo "Sending to: {$testEmail}<br>";
        
        $result = $emailLib->sendWelcomeEmail($testEmail, 'Test User');
        
        if ($result['success']) {
            echo "<p style='color:green; font-weight:bold;'>✓ Welcome email sent successfully!</p>";
        } else {
            echo "<p style='color:red; font-weight:bold;'>✗ Failed: " . $result['message'] . "</p>";
        }
    }
    
    public function checkConfig()
    {
        echo "<h2>Email Configuration Check</h2>";
        
        $config = [
            'email.smtp_host' => getenv('email.smtp_host'),
            'email.smtp_port' => getenv('email.smtp_port'),
            'email.smtp_user' => getenv('email.smtp_user'),
            'email.smtp_pass' => getenv('email.smtp_pass') ? '********' : 'NOT SET',
            'email.from_email' => getenv('email.from_email'),
            'email.from_name' => getenv('email.from_name'),
        ];
        
        echo "<table border='1' cellpadding='10' cellspacing='0' style='border-collapse: collapse;'>";
        foreach ($config as $key => $value) {
            $status = ($value && $value != 'NOT SET') ? '✓' : '✗';
            $color = ($value && $value != 'NOT SET') ? 'green' : 'red';
            echo "<tr>";
            echo "<td style='padding: 8px;'><strong>{$key}</strong></td>";
            echo "<td style='padding: 8px; color: {$color};'>{$status} {$value}</td>";
            echo "</tr>";
        }
        echo "</table>";
        
        if (!$config['email.smtp_user'] || $config['email.smtp_user'] == 'your-email@gmail.com') {
            echo "<p style='color:red; margin-top:20px;'>⚠ Email is not configured! Please add email settings to your .env file.</p>";
        } else {
            echo "<p style='color:green; margin-top:20px;'>✓ Email configuration looks good!</p>";
            echo "<p><a href='" . base_url('/test-email') . "' style='background:#2A5C37; color:white; padding:10px 20px; text-decoration:none; border-radius:5px;'>Send Test Email</a></p>";
        }
    }
    
    public function checkEnv()
    {
        echo "<h2>.env File Diagnostic</h2>";
        
        // Show ROOTPATH
        echo "ROOTPATH: " . ROOTPATH . "<br>";
        echo "Current directory: " . getcwd() . "<br><br>";
        
        // Check if .env exists
        $envFile = ROOTPATH . '.env';
        echo "Checking for .env at: " . $envFile . "<br>";
        
        if (file_exists($envFile)) {
            echo "✓ .env file FOUND!<br><br>";
            
            // Read and display .env content
            echo "<h3>.env file content (email lines):</h3>";
            $content = file_get_contents($envFile);
            $lines = explode("\n", $content);
            foreach ($lines as $line) {
                $line = trim($line);
                if (strpos($line, 'email.') === 0 && !empty($line)) {
                    // Hide password for security
                    if (strpos($line, 'email.smtp_pass') === 0) {
                        echo "email.smtp_pass=********<br>";
                    } else {
                        echo htmlspecialchars($line) . "<br>";
                    }
                }
            }
            
            // Try to parse .env manually
            echo "<h3>Manual parse_ini_file results:</h3>";
            $parsed = parse_ini_file($envFile);
            if ($parsed) {
                echo "email.smtp_host: " . ($parsed['email.smtp_host'] ?? 'NOT FOUND') . "<br>";
                echo "email.smtp_user: " . ($parsed['email.smtp_user'] ?? 'NOT FOUND') . "<br>";
                echo "email.smtp_port: " . ($parsed['email.smtp_port'] ?? 'NOT FOUND') . "<br>";
                echo "email.from_email: " . ($parsed['email.from_email'] ?? 'NOT FOUND') . "<br>";
            } else {
                echo "Failed to parse .env file<br>";
                echo "Check that your .env file has proper formatting (no spaces around =)<br>";
            }
            
            // Check getenv results
            echo "<h3>getenv() results:</h3>";
            echo "email.smtp_host: " . (getenv('email.smtp_host') ?: 'NOT SET') . "<br>";
            echo "email.smtp_user: " . (getenv('email.smtp_user') ?: 'NOT SET') . "<br>";
            
        } else {
            echo "✗ .env file NOT FOUND!<br>";
            echo "Please create the .env file at: " . $envFile . "<br><br>";
            
            // Show what files are in ROOTPATH
            echo "<h3>Files in ROOTPATH:</h3>";
            $files = scandir(ROOTPATH);
            foreach ($files as $file) {
                if ($file != '.' && $file != '..') {
                    echo "- " . $file . "<br>";
                }
            }
            
            // Create .env file automatically
            echo "<h3>Creating .env file...</h3>";
            $sampleEnv = "# Email Configuration
email.smtp_host=smtp.gmail.com
email.smtp_port=587
email.smtp_user=delamenkimshin@gmail.com
email.smtp_pass=ptfntyipqvztuilj
email.from_email=delamenkimshin@gmail.com
email.from_name=CREATRIX COIR
";
            if (file_put_contents($envFile, $sampleEnv)) {
                echo "✓ .env file created! Please edit it with your actual email credentials.<br>";
                echo "File location: " . $envFile . "<br>";
            } else {
                echo "✗ Failed to create .env file. Please create it manually.<br>";
            }
        }
    }
}