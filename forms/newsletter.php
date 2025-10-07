<?php
/**
 * Memorra Tours Newsletter Subscription Handler
 */

// Enable error reporting for debugging (disable in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Set content type to JSON
header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Get and validate email
    $email = filter_var(trim($_POST["email"]), FILTER_SANITIZE_EMAIL);
    
    if (empty($email)) {
        echo json_encode([
            'status' => 'error',
            'message' => '❌ Email address is required.'
        ]);
        exit;
    }
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode([
            'status' => 'error',
            'message' => '❌ Please enter a valid email address.'
        ]);
        exit;
    }
    
    // Admin email
    $admin_email = "info@memorratours.com";
    $website_name = "Memorra Tours";
    
    // Email content for admin notification
    $admin_subject = "New Newsletter Subscription";
    $admin_message = "
    <!DOCTYPE html>
    <html>
    <head>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
            .container { max-width: 600px; margin: 0 auto; padding: 20px; }
            .header { background: linear-gradient(135deg, #1e5c6e 0%, #7cc5e6 100%); color: white; padding: 20px; text-align: center; border-radius: 8px 8px 0 0; }
            .content { background: #f9f9f9; padding: 20px; border-radius: 0 0 8px 8px; }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h2>New Newsletter Subscription</h2>
            </div>
            <div class='content'>
                <p><strong>New subscriber:</strong> $email</p>
                <p><strong>Subscription Date:</strong> " . date('F j, Y \a\t g:i A') . "</p>
                <p><strong>IP Address:</strong> " . $_SERVER['REMOTE_ADDR'] . "</p>
            </div>
        </div>
    </body>
    </html>
    ";
    
    $admin_headers = "From: $website_name <noreply@memorratours.com>\r\n";
    $admin_headers .= "Reply-To: $email\r\n";
    $admin_headers .= "MIME-Version: 1.0\r\n";
    $admin_headers .= "Content-Type: text/html; charset=UTF-8\r\n";
    
    // Welcome email to subscriber
    $welcome_subject = "Welcome to Memorra Tours Travel Community!";
    $welcome_message = "
    <!DOCTYPE html>
    <html>
    <head>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
            .container { max-width: 600px; margin: 0 auto; padding: 20px; }
            .header { background: linear-gradient(135deg, #1e5c6e 0%, #7cc5e6 100%); color: white; padding: 20px; text-align: center; border-radius: 8px 8px 0 0; }
            .content { background: #f9f9f9; padding: 20px; border-radius: 0 0 8px 8px; }
            .cta-button { display: inline-block; background: linear-gradient(135deg, #1e5c6e 0%, #7cc5e6 100%); color: white; padding: 12px 24px; text-decoration: none; border-radius: 5px; margin: 10px 5px; }
            .social-links { margin: 20px 0; }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h2>Welcome to Memorra Tours!</h2>
                <p>Your Sri Lanka Travel Adventure Starts Here</p>
            </div>
            <div class='content'>
                <p>Hello Travel Enthusiast,</p>
                
                <p>🎉 Thank you for joining the Memorra Tours travel community! We're excited to have you on board.</p>
                
                <p><strong>What you'll receive:</strong></p>
                <ul>
                    <li>📧 Exclusive Sri Lanka travel deals and packages</li>
                    <li>🏝️ Insider tips on hidden gems and must-visit spots</li>
                    <li>📅 Seasonal travel guides and best times to visit</li>
                    <li>🎁 Special subscriber-only discounts and offers</li>
                    <li>📸 Stunning travel inspiration from Sri Lanka</li>
                </ul>
                
                <p><strong>Ready to start planning?</strong></p>
                <div style='text-align: center;'>
                    <a href='https://memorratours.com/tours.html' class='cta-button'>Explore Our Tours</a>
                    <a href='https://memorratours.com/destinations.html' class='cta-button'>Discover Destinations</a>
                </div>
                
                <div class='social-links'>
                    <p><strong>Follow us for daily inspiration:</strong></p>
                    <p>
                        <a href='https://www.facebook.com/memorratours'>Facebook</a> | 
                        <a href='https://www.instagram.com/memorratours.sl'>Instagram</a> | 
                        <a href='https://www.tiktok.com/@memorra_tours'>TikTok</a> |
                        <a href='https://wa.me/94777402140'>WhatsApp</a>
                    </p>
                </div>
                
                <p>Need help planning your Sri Lanka adventure?<br>
                Call us: <strong>+94 77 740 2140</strong></p>
                
                <p>Happy travels!<br>
                <strong>The Memorra Tours Team</strong></p>
            </div>
        </div>
    </body>
    </html>
    ";
    
    $welcome_headers = "From: Memorra Tours <info@memorratours.com>\r\n";
    $welcome_headers .= "Reply-To: info@memorratours.com\r\n";
    $welcome_headers .= "MIME-Version: 1.0\r\n";
    $welcome_headers .= "Content-Type: text/html; charset=UTF-8\r\n";
    
    // Send emails
    $admin_sent = mail($admin_email, $admin_subject, $admin_message, $admin_headers);
    $welcome_sent = mail($email, $welcome_subject, $welcome_message, $welcome_headers);
    
    if ($admin_sent || $welcome_sent) {
        echo json_encode([
            'status' => 'success',
            'message' => '🎉 Thank you! You\'ve successfully joined the Memorra Tours travel community. Check your email for a welcome message!'
        ]);
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => '❌ There was a problem with your subscription. Please try again.'
        ]);
    }
    
} else {
    echo json_encode([
        'status' => 'error',
        'message' => '❌ Invalid request method.'
    ]);
}

// Log subscription (optional)
function logNewsletterSubscription($email, $status) {
    $log_file = __DIR__ . '/newsletter_log.txt';
    $timestamp = date('Y-m-d H:i:s');
    $log_entry = "[$timestamp] Email: $email | Status: $status\n";
    file_put_contents($log_file, $log_entry, FILE_APPEND | LOCK_EX);
}

?>