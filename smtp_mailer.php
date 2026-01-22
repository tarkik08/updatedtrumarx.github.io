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
    private $timeout = 20;
    private $secure = 'ssl';
    private $lastError = '';

    public function getLastError() {
        return $this->lastError;
    }

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
        $this->lastError = '';

        $encodedSubject = $this->encodeHeader($subject);
        $fromHeader = $this->formatAddress($this->from_email, $this->from_name);

        $body = $plain_body !== '' ? $plain_body : strip_tags((string)$html_body);
        $body = str_replace(["\r\n", "\r"], ["\n", "\n"], (string)$body);
        $body = str_replace("\n", "\r\n", $body);

        $headers = [];
        $headers[] = 'Date: ' . date('r');
        $headers[] = 'Message-ID: <' . bin2hex(random_bytes(16)) . '@' . $this->getDomainFromEmail($this->from_email) . '>';
        $headers[] = 'From: ' . $fromHeader;
        $headers[] = 'To: ' . $to;
        $headers[] = 'Subject: ' . $encodedSubject;
        if ($reply_to) {
            $headers[] = 'Reply-To: ' . $this->formatAddress($reply_to);
        }
        $headers[] = 'MIME-Version: 1.0';
        $headers[] = 'Content-Type: text/plain; charset=UTF-8';

        return $this->sendSmtp($to, implode("\r\n", $headers) . "\r\n\r\n" . $body);
    }

    private function sendSmtp($to, $data) {
        if (!$this->password) {
            $this->lastError = 'SMTP password is missing.';
            return false;
        }

        $transport = ($this->secure === 'ssl') ? 'ssl://' : 'tcp://';
        $socket = @stream_socket_client(
            $transport . $this->host . ':' . $this->port,
            $errno,
            $errstr,
            $this->timeout,
            STREAM_CLIENT_CONNECT
        );

        if (!$socket) {
            $this->lastError = 'Failed to connect to SMTP server: ' . $errstr . ' (' . $errno . ')';
            return false;
        }

        stream_set_timeout($socket, $this->timeout);

        if (!$this->expect($socket, [220])) {
            fclose($socket);
            return false;
        }

        $this->command($socket, 'EHLO ' . $this->getDomainFromEmail($this->from_email));
        if (!$this->expect($socket, [250])) {
            $this->command($socket, 'HELO ' . $this->getDomainFromEmail($this->from_email));
            if (!$this->expect($socket, [250])) {
                fclose($socket);
                return false;
            }
        }

        $this->command($socket, 'AUTH LOGIN');
        if (!$this->expect($socket, [334])) {
            fclose($socket);
            return false;
        }
        $this->command($socket, base64_encode($this->username));
        if (!$this->expect($socket, [334])) {
            fclose($socket);
            return false;
        }
        $this->command($socket, base64_encode($this->password));
        if (!$this->expect($socket, [235])) {
            fclose($socket);
            return false;
        }

        $this->command($socket, 'MAIL FROM:<' . $this->from_email . '>');
        if (!$this->expect($socket, [250])) {
            fclose($socket);
            return false;
        }

        $recipients = $this->parseRecipients($to);
        foreach ($recipients as $rcpt) {
            $this->command($socket, 'RCPT TO:<' . $rcpt . '>');
            if (!$this->expect($socket, [250, 251])) {
                fclose($socket);
                return false;
            }
        }

        $this->command($socket, 'DATA');
        if (!$this->expect($socket, [354])) {
            fclose($socket);
            return false;
        }

        $normalized = str_replace(["\r\n", "\r"], ["\n", "\n"], $data);
        $normalized = str_replace("\n", "\r\n", $normalized);
        $normalized = preg_replace('/\r\n\./', "\r\n..", $normalized);
        fwrite($socket, $normalized . "\r\n.\r\n");

        if (!$this->expect($socket, [250])) {
            fclose($socket);
            return false;
        }

        $this->command($socket, 'QUIT');
        $this->expect($socket, [221]);
        fclose($socket);
        return true;
    }

    private function command($socket, $command) {
        fwrite($socket, $command . "\r\n");
    }

    private function expect($socket, $expectedCodes) {
        $response = '';
        while (!feof($socket)) {
            $line = fgets($socket, 515);
            if ($line === false) {
                break;
            }
            $response .= $line;
            if (preg_match('/^\d{3} /', $line)) {
                break;
            }
        }

        if ($response === '') {
            return false;
        }

        $code = (int)substr(trim($response), 0, 3);
        return in_array($code, $expectedCodes, true);
    }

    private function parseRecipients($to) {
        $parts = preg_split('/\s*,\s*/', trim((string)$to));
        $out = [];
        foreach ($parts as $p) {
            $p = trim($p);
            if ($p === '') {
                continue;
            }
            if (preg_match('/<([^>]+)>/', $p, $m)) {
                $p = trim($m[1]);
            }
            $out[] = $p;
        }
        return $out;
    }

    private function encodeHeader($str) {
        return '=?UTF-8?B?' . base64_encode($str) . '?=';
    }

    private function formatAddress($email, $name = null) {
        $email = trim((string)$email);
        $name = $name !== null ? trim((string)$name) : '';
        if ($name === '') {
            return $email;
        }
        return $this->encodeHeader($name) . ' <' . $email . '>';
    }

    private function getDomainFromEmail($email) {
        $email = (string)$email;
        $pos = strrpos($email, '@');
        if ($pos === false) {
            return 'localhost';
        }
        return substr($email, $pos + 1);
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
