<?php
/**
 * Simple SMTP Mailer for GoDaddy
 * Uses PHP's built-in mail() with proper SMTP headers
 * Alternative to PHPMailer for better deliverability
 */

class SMTPMailer {
    private $host = 'smtpout.secureserver.net';
    private $port = 465;
    private $username = 'mailservice@trumarx.in';
    private $password = 'Tarkik@2007';
    private $from_email = 'mailservice@trumarx.in';
    private $from_name = 'Trumarx IP Services';
    
    /**
     * Send email using SMTP
     * 
     * @param string $to Recipient email address(es)
     * @param string $subject Email subject
     * @param string $html_body HTML email body
     * @param string $plain_body Plain text email body (optional)
     * @param string $reply_to Reply-to email (optional)
     * @return bool Success status
     */
    public function send($to, $subject, $html_body, $plain_body = '', $reply_to = null) {
        // Create boundary for multipart email
        $boundary = md5(uniqid(time()));
        
        // Build headers
        $headers = "MIME-Version: 1.0\r\n";
        $headers .= "From: " . $this->from_name . " <" . $this->from_email . ">\r\n";
        
        if ($reply_to) {
            // Include reply-to in body instead of header to avoid spam
            $html_body = "<p><strong>Reply to:</strong> <a href='mailto:$reply_to'>$reply_to</a></p>" . $html_body;
        }
        
        $headers .= "Content-Type: multipart/alternative; boundary=\"{$boundary}\"\r\n";
        $headers .= "X-Mailer: PHP/" . phpversion() . "\r\n";
        $headers .= "X-Priority: 3\r\n";
        $headers .= "X-MSMail-Priority: Normal\r\n";
        $headers .= "Importance: Normal\r\n";
        
        // Build email body with both plain text and HTML
        $message = "--{$boundary}\r\n";
        $message .= "Content-Type: text/plain; charset=UTF-8\r\n";
        $message .= "Content-Transfer-Encoding: 7bit\r\n\r\n";
        $message .= $plain_body ?: strip_tags($html_body);
        $message .= "\r\n\r\n";
        
        $message .= "--{$boundary}\r\n";
        $message .= "Content-Type: text/html; charset=UTF-8\r\n";
        $message .= "Content-Transfer-Encoding: 7bit\r\n\r\n";
        $message .= $html_body;
        $message .= "\r\n\r\n";
        $message .= "--{$boundary}--";
        
        // Configure PHP mail to use SMTP
        ini_set('SMTP', $this->host);
        ini_set('smtp_port', $this->port);
        ini_set('sendmail_from', $this->from_email);
        
        // Send email
        return mail($to, $subject, $message, $headers);
    }
    
    /**
     * Create a professional HTML email template
     */
    public function createTemplate($title, $content) {
        return "
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset='UTF-8'>
            <style>
                body { 
                    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Arial, sans-serif;
                    line-height: 1.6; 
                    color: #333;
                    margin: 0;
                    padding: 0;
                    background-color: #f4f4f4;
                }
                .container { 
                    max-width: 600px; 
                    margin: 20px auto; 
                    background: white;
                    border-radius: 8px;
                    overflow: hidden;
                    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
                }
                .header { 
                    background: linear-gradient(135deg, #0a1628 0%, #1a2f4f 100%);
                    color: white; 
                    padding: 30px 20px; 
                    text-align: center;
                }
                .header h2 {
                    margin: 0;
                    font-size: 24px;
                    font-weight: 600;
                }
                .content { 
                    padding: 30px 20px;
                }
                .field { 
                    margin-bottom: 20px;
                    padding-bottom: 15px;
                    border-bottom: 1px solid #eee;
                }
                .field:last-child {
                    border-bottom: none;
                }
                .label { 
                    font-weight: 600;
                    color: #0a1628;
                    display: block;
                    margin-bottom: 5px;
                    font-size: 14px;
                    text-transform: uppercase;
                    letter-spacing: 0.5px;
                }
                .value {
                    color: #555;
                    font-size: 15px;
                }
                .footer { 
                    text-align: center; 
                    padding: 20px; 
                    font-size: 12px; 
                    color: #999;
                    background: #f9f9f9;
                    border-top: 1px solid #eee;
                }
                a {
                    color: #0066cc;
                    text-decoration: none;
                }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h2>{$title}</h2>
                </div>
                <div class='content'>
                    {$content}
                </div>
                <div class='footer'>
                    <p>This email was sent from the Trumarx website contact form.</p>
                    <p>&copy; " . date('Y') . " Trumarx IP Services. All rights reserved.</p>
                </div>
            </div>
        </body>
        </html>";
    }
}
?>
