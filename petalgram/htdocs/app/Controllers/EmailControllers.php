<?php
namespace App\Controllers;

class EmailControllers extends BaseController
{
    // Send welcome email to customers
    public function sendCustomerWelcomeEmail($userEmail, $userName)
    {
        $email = \Config\Services::email();

        $email->setFrom('delamenkimshin@gmail.com', 'Coffee Lang');
        $email->setTo($userEmail);
        $email->setSubject('Welcome to the Coffee Lang Family!');

        $message = "Dear {$userName},

Welcome to Coffee Lang! We are truly grateful you've chosen to join our coffee community.

Your account has been successfully created:
- Name: {$userName}
- Email: {$userEmail}
- Member Since: " . date('F j, Y') . "
- Role: Customer

We're honored to be part of your daily coffee moments and look forward to serving you the perfect cup that feels like a warm hug.

To get started, please login to your account here: " . base_url('/login') . "

Thank you for trusting us with your coffee experience. We can't wait to create beautiful memories together.

With warm regards,
The Coffee Lang Team

Brewing happiness, one cup at a time";

        $email->setMessage($message);

        if ($email->send()) {
            log_message('info', "Customer welcome email sent to: {$userEmail}");
            return true;
        } else {
            log_message('error', "Failed to send customer email to: {$userEmail}. Error: " . $email->printDebugger(['headers']));
            return false;
        }
    }

    // Send welcome email to admins
    public function sendAdminWelcomeEmail($userEmail, $userName)
    {
        $email = \Config\Services::email();

        $email->setFrom('delamenkimshin@gmail.com', 'Coffee Lang');
        $email->setTo($userEmail);
        $email->setSubject('Coffee Lang - Administrator Account Activated');

        $message = "Dear {$userName},

Welcome to the Coffee Lang Administrator Team!

Your administrator account has been successfully created and activated:
- Name: {$userName}
- Email: {$userEmail}
- Role: Administrator
- Account Created: " . date('F j, Y') . "

As an administrator, you now have access to the Coffee Lang management dashboard where you can:
- Manage user accounts
- View system analytics
- Monitor orders and operations
- Update platform settings

Access your admin dashboard here: " . base_url('/admin/dashboard') . "

Please ensure you maintain the security of your administrator credentials and follow our security protocols.

We trust you to help us maintain the excellence that Coffee Lang stands for.

Best regards,
Coffee Lang System Administration";
        
        $email->setMessage($message);
        
        if ($email->send()) {
            log_message('info', "Admin welcome email sent to: {$userEmail}");
            return true;
        } else {
            log_message('error', "Failed to send admin email to: {$userEmail}. Error: " . $email->printDebugger(['headers']));
            return false;
        }
    }
}