<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class Email extends BaseConfig
{
    public string $fromEmail  = 'delamenkimshin@gmail.com';
    public string $fromName   = 'CREATRIX COIR';
    public string $protocol   = 'smtp';
    public string $SMTPHost   = 'smtp.gmail.com';
    public string $SMTPUser   = 'delamenkimshin@gmail.com';
    public string $SMTPPass   = 'ptfntyipqvztuilj'; // Your 16-character App Password
    public int    $SMTPPort   = 587;                   // 465 for SSL, 587 for TLS
    public string $SMTPCrypto = 'tls';                 // 'ssl' or 'tls'
    public string $mailType   = 'html';
    public string $charset    = 'utf-8';
    public bool   $wordWrap   = true;
}