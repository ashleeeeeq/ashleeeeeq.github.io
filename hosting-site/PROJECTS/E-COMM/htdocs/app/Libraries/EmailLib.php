<?php

namespace App\Libraries;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

class EmailLib
{
    protected $mail;
    protected $isConfigured = false;
    
    public function __construct()
    {
        // Check if email configuration exists
        $fromEmail = getenv('email.from_email');
        $smtpUser = getenv('email.smtp_user');
        $smtpPass = getenv('email.smtp_pass');
        
        // If no email configuration, disable email functionality
        if (empty($fromEmail) || empty($smtpUser) || empty($smtpPass)) {
            log_message('warning', 'Email configuration is missing. Email features disabled.');
            $this->isConfigured = false;
            return;
        }
        
        $this->mail = new PHPMailer(true);
    
        // TEMPORARY HARDCODED VALUES - REPLACE WITH YOUR ACTUAL VALUES
        $this->mail->isSMTP();
        $this->mail->Host       = 'smtp.gmail.com';
        $this->mail->SMTPAuth   = true;
        $this->mail->Username   = 'delamenkimshin@gmail.com';  // Your email
        $this->mail->Password   = 'ptfntyipqvztuilj';
        $this->mail->Port       = 587;
        
        // Sender info
        $this->mail->setFrom('delamenkimshin@gmail.com', 'CREATRIX COIR');
        
        // Default settings
        $this->mail->isHTML(true);
        $this->mail->CharSet = 'UTF-8';
    }
    
    /**
     * Send email to a single recipient
     */
    public function sendEmail($to, $subject, $body, $altBody = '')
    {
        // If email is not configured, return success (so app doesn't break)
        if (!$this->isConfigured) {
            log_message('debug', "Email would be sent (but email not configured): To: $to, Subject: $subject");
            return [
                'success' => true,
                'message' => 'Email skipped (configuration missing)',
                'skipped' => true
            ];
        }
        
        try {
            // Clear previous recipients and attachments
            $this->mail->clearAddresses();
            $this->mail->clearAttachments();
            $this->mail->clearReplyTos();
            $this->mail->clearCCs();
            $this->mail->clearBCCs();
            
            // Add recipient
            $this->mail->addAddress($to);
            
            // Email content
            $this->mail->Subject = $subject;
            $this->mail->Body    = $body;
            $this->mail->AltBody = $altBody ?: strip_tags($body);
            
            // Send email
            $this->mail->send();
            
            return [
                'success' => true,
                'message' => 'Email sent successfully to ' . $to
            ];
            
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to send email: ' . $this->mail->ErrorInfo
            ];
        }
    }
    
    /**
     * Send welcome email to new user
     */
    public function sendWelcomeEmail($to, $name)
    {
        if (!$this->isConfigured) {
            return ['success' => true, 'message' => 'Email skipped', 'skipped' => true];
        }
        
        $subject = 'Welcome to CREATRIX Coir!';
        
        $body = "
        <html>
        <head>
            <style>
                body { font-family: 'Poppins', Arial, sans-serif; line-height: 1.6; color: #2C3B31; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: #1F4529; color: white; padding: 20px; text-align: center; border-radius: 10px 10px 0 0; }
                .content { padding: 30px; background: #F0F4F1; border-radius: 0 0 10px 10px; }
                .button { display: inline-block; padding: 12px 24px; background: #2A5C37; color: white; text-decoration: none; border-radius: 5px; margin: 20px 0; }
                .footer { text-align: center; margin-top: 30px; font-size: 12px; color: #647368; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h2>Welcome to CREATRIX Coir!</h2>
                </div>
                <div class='content'>
                    <p>Dear {$name},</p>
                    <p>Thank you for joining CREATRIX Coir! We're excited to have you as part of our community.</p>
                    <p>At CREATRIX, we believe in sustainable living through coconut coir innovation.</p>
                    <a href='" . base_url('/storefront') . "' class='button'>Start Shopping</a>
                    <p>Best regards,<br>The CREATRIX Coir Team</p>
                </div>
                <div class='footer'>
                    <p>&copy; 2026 CREATRIX Coir. All rights reserved.</p>
                </div>
            </div>
        </body>
        </html>
        ";
        
        return $this->sendEmail($to, $subject, $body);
    }
    
    /**
     * Send verification email
     */
    public function sendVerificationEmail($to, $name, $verificationLink)
    {
        if (!$this->isConfigured) {
            return ['success' => true, 'message' => 'Email skipped', 'skipped' => true];
        }
        
        $subject = 'Verify Your Email - CREATRIX Coir';
        
        $body = "
        <html>
        <head>
            <style>
                body { font-family: 'Poppins', Arial, sans-serif; line-height: 1.6; color: #2C3B31; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: #1F4529; color: white; padding: 20px; text-align: center; border-radius: 10px 10px 0 0; }
                .content { padding: 30px; background: #F0F4F1; border-radius: 0 0 10px 10px; }
                .button { display: inline-block; padding: 12px 24px; background: #2A5C37; color: white; text-decoration: none; border-radius: 5px; margin: 20px 0; }
                .footer { text-align: center; margin-top: 30px; font-size: 12px; color: #647368; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h2>Verify Your Email Address</h2>
                </div>
                <div class='content'>
                    <p>Dear {$name},</p>
                    <p>Please click the button below to verify your email address:</p>
                    <center>
                        <a href='{$verificationLink}' class='button'>Verify Email</a>
                    </center>
                    <p>If you didn't create an account, please ignore this email.</p>
                    <p>Best regards,<br>The CREATRIX Coir Team</p>
                </div>
                <div class='footer'>
                    <p>&copy; 2026 CREATRIX Coir. All rights reserved.</p>
                </div>
            </div>
        </body>
        </html>
        ";
        
        return $this->sendEmail($to, $subject, $body);
    }
    
    /**
     * Send order confirmation email
     */
    public function sendOrderConfirmation($to, $orderData)
    {
        if (!$this->isConfigured) {
            return ['success' => true, 'message' => 'Email skipped', 'skipped' => true];
        }
        
        $subject = 'Order Confirmation #' . $orderData['order_id'];
        
        $body = "
        <html>
        <head>
            <style>
                body { font-family: 'Poppins', Arial, sans-serif; line-height: 1.6; color: #2C3B31; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: #1F4529; color: white; padding: 20px; text-align: center; border-radius: 10px 10px 0 0; }
                .content { padding: 30px; background: #F0F4F1; border-radius: 0 0 10px 10px; }
                .button { display: inline-block; padding: 12px 24px; background: #2A5C37; color: white; text-decoration: none; border-radius: 5px; margin: 20px 0; }
                .footer { text-align: center; margin-top: 30px; font-size: 12px; color: #647368; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h2>Order Confirmation</h2>
                </div>
                <div class='content'>
                    <p>Thank you for your order!</p>
                    <p><strong>Order Number:</strong> #{$orderData['order_id']}<br>
                    <strong>Date:</strong> {$orderData['date']}<br>
                    <strong>Total:</strong> ₱" . number_format($orderData['total'], 2) . "</p>
                    
                    <a href='" . base_url('/transactions') . "' class='button'>View My Orders</a>
                    
                    <p>Thank you for choosing CREATRIX Coir!</p>
                </div>
                <div class='footer'>
                    <p>&copy; 2026 CREATRIX Coir. All rights reserved.</p>
                </div>
            </div>
        </body>
        </html>
        ";
        
        return $this->sendEmail($to, $subject, $body);
    }
}