<?php
/**
 * Memorra Tours Contact Form Handler
 * Processes contact form submissions and sends emails to admin
 */

// Enable error reporting for debugging (disable in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Set content type to JSON
header('Content-Type: application/json');

// Check if form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Get form data and sanitize
    $name = strip_tags(trim($_POST["name"]));
    $name = str_replace(array("\r","\n"),array(" "," "),$name);
    $email = filter_var(trim($_POST["email"]), FILTER_SANITIZE_EMAIL);
    $subject = strip_tags(trim($_POST["subject"]));
    $message = trim($_POST["message"]);
    
    // Validation
    $errors = [];
    
    // Check required fields
    if (empty($name)) {
        $errors[] = "Name is required";
    }
    
    if (empty($email)) {
        $errors[] = "Email is required";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format";
    }
    
    if (empty($subject)) {
        $errors[] = "Subject is required";
    }
    
    if (empty($message)) {
        $errors[] = "Message is required";
    } elseif (strlen($message) < 10) {
        $errors[] = "Message should be at least 10 characters long";
    }
    
    // If no errors, process the form
    if (empty($errors)) {
        
        // Admin email (replace with your actual email)
        $recipient = "info@memorratours.com";
        $website_name = "Memorra Tours";
        
        // Email subject
        $email_subject = "New Contact Form: $subject";
        
        // Email content
        $email_content = "
        <!DOCTYPE html>
        <html>
        <head>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: linear-gradient(135deg, #1e5c6e 0%, #7cc5e6 100%); color: white; padding: 20px; text-align: center; border-radius: 8px 8px 0 0; }
                .content { background: #f9f9f9; padding: 20px; border-radius: 0 0 8px 8px; }
                .field { margin-bottom: 15px; }
                .field-label { font-weight: bold; color: #1e5c6e; }
                .footer { margin-top: 20px; padding-top: 20px; border-top: 1px solid #ddd; font-size: 12px; color: #666; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h2>New Contact Form Submission</h2>
                    <p>From $website_name Website</p>
                </div>
                <div class='content'>
                    <div class='field'>
                        <div class='field-label'>Name:</div>
                        <div>$name</div>
                    </div>
                    <div class='field'>
                        <div class='field-label'>Email:</div>
                        <div>$email</div>
                    </div>
                    <div class='field'>
                        <div class='field-label'>Subject:</div>
                        <div>$subject</div>
                    </div>
                    <div class='field'>
                        <div class='field-label'>Message:</div>
                        <div>" . nl2br(htmlspecialchars($message)) . "</div>
                    </div>
                    <div class='field'>
                        <div class='field-label'>Submitted On:</div>
                        <div>" . date('F j, Y \a\t g:i A') . "</div>
                    </div>
                    <div class='field'>
                        <div class='field-label'>IP Address:</div>
                        <div>" . $_SERVER['REMOTE_ADDR'] . "</div>
                    </div>
                </div>
                <div class='footer'>
                    <p>This email was sent from the contact form on $website_name website.</p>
                    <p>© " . date('Y') . " $website_name. All rights reserved.</p>
                </div>
            </div>
        </body>
        </html>
        ";
        
        // Email headers
        $headers = "From: $website_name <noreply@memorratours.com>\r\n";
        $headers .= "Reply-To: $name <$email>\r\n";
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
        $headers .= "X-Mailer: PHP/" . phpversion();
        
        // Send email
        if (mail($recipient, $email_subject, $email_content, $headers)) {
            
            // Auto-reply to the user
            $user_subject = "Thank you for contacting Memorra Tours";
            $user_message = "
            <!DOCTYPE html>
            <html>
            <head>
                <style>
                    body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                    .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                    .header { background: linear-gradient(135deg, #1e5c6e 0%, #7cc5e6 100%); color: white; padding: 20px; text-align: center; border-radius: 8px 8px 0 0; }
                    .content { background: #f9f9f9; padding: 20px; border-radius: 0 0 8px 8px; }
                    .footer { margin-top: 20px; padding-top: 20px; border-top: 1px solid #ddd; font-size: 12px; color: #666; }
                </style>
            </head>
            <body>
                <div class='container'>
                    <div class='header'>
                        <h2>Thank You for Contacting Memorra Tours!</h2>
                    </div>
                    <div class='content'>
                        <p>Dear $name,</p>
                        
                        <p>Thank you for getting in touch with Memorra Tours! We have received your message and our team will get back to you within 24 hours.</p>
                        
                        <p><strong>Here's a summary of your inquiry:</strong></p>
                        <p><strong>Subject:</strong> $subject</p>
                        <p><strong>Message:</strong><br>" . nl2br(htmlspecialchars($message)) . "</p>
                        
                        <p>In the meantime, you might find these resources helpful:</p>
                        <ul>
                            <li><a href='https://memorratours.com/tours.html'>Browse Our Tour Packages</a></li>
                            <li><a href='https://memorratours.com/destinations.html'>Explore Sri Lanka Destinations</a></li>
                            <li><a href='https://wa.me/94777402140'>Chat with us on WhatsApp</a></li>
                        </ul>
                        
                        <p>For urgent inquiries, feel free to call us at <strong>+94 77 740 2140</strong>.</p>
                        
                        <p>Best regards,<br>
                        <strong>The Memorra Tours Team</strong></p>
                    </div>
                    <div class='footer'>
                        <p>Memorra Tours | Unawatuna, Galle, Sri Lanka<br>
                        Phone: +94 77 740 2140 | Email: info@memorratours.com</p>
                        <p>© " . date('Y') . " Memorra Tours. All rights reserved.</p>
                    </div>
                </div>
            </body>
            </html>
            ";
            
            $user_headers = "From: Memorra Tours <info@memorratours.com>\r\n";
            $user_headers .= "Reply-To: info@memorratours.com\r\n";
            $user_headers .= "MIME-Version: 1.0\r\n";
            $user_headers .= "Content-Type: text/html; charset=UTF-8\r\n";
            
            // Send auto-reply
            mail($email, $user_subject, $user_message, $user_headers);
            
            // Return success response
            echo json_encode([
                'status' => 'success',
                'message' => '✅ Thank you! Your message has been sent successfully. We will get back to you within 24 hours.'
            ]);
            
        } else {
            // Email sending failed
            echo json_encode([
                'status' => 'error',
                'message' => '❌ Sorry, there was a problem sending your message. Please try again or contact us directly at info@memorratours.com'
            ]);
        }
        
    } else {
        // Validation errors
        echo json_encode([
            'status' => 'error',
            'message' => '❌ Please fix the following errors: ' . implode(', ', $errors)
        ]);
    }
    
} else {
    // Not a POST request
    echo json_encode([
        'status' => 'error',
        'message' => '❌ Invalid request method. Please use the contact form.'
    ]);
}

// Log the submission (optional)
function logContactSubmission($name, $email, $subject, $status) {
    $log_file = __DIR__ . '/contact_log.txt';
    $timestamp = date('Y-m-d H:i:s');
    $log_entry = "[$timestamp] Name: $name | Email: $email | Subject: $subject | Status: $status\n";
    file_put_contents($log_file, $log_entry, FILE_APPEND | LOCK_EX);
}

?>