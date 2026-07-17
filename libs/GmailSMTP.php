<?php

/**
 * Simple Gmail SMTP Mailer Class
 * Sends emails through Gmail without requiring PHPMailer
 */

class GmailSMTP
{
    private $host = 'smtp.gmail.com';
    private $port = 587;
    private $username = 'your-email@gmail.com';      // Change this
    private $password = 'your-app-password';          // Change this
    private $socket = null;
    private $debug = false;

    public function __construct($email, $password, $debug = false)
    {
        $this->username = $email;
        $this->password = $password;
        $this->debug = $debug;
    }

    public function send($to, $subject, $message, $headers = '')
    {
        try {
            // Connect to Gmail SMTP
            $this->socket = fsockopen($this->host, $this->port, $errno, $errstr, 15);

            if (!$this->socket) {
                throw new Exception("Connection failed: $errstr ($errno)");
            }

            // Read server response
            $this->readResponse();

            // Send EHLO
            $this->sendCommand("EHLO " . gethostname());
            $this->readResponse();

            // Start TLS
            $this->sendCommand("STARTTLS");
            $this->readResponse();

            // Upgrade to TLS
            if (!stream_socket_enable_crypto($this->socket, true, STREAM_CRYPTO_METHOD_TLSv1_2_CLIENT)) {
                throw new Exception("Failed to enable TLS encryption");
            }

            // Send EHLO again after TLS
            $this->sendCommand("EHLO " . gethostname());
            $this->readResponse();

            // Authenticate
            $this->sendCommand("AUTH LOGIN");
            $this->readResponse();

            $this->sendCommand(base64_encode($this->username));
            $this->readResponse();

            $this->sendCommand(base64_encode($this->password));
            $this->readResponse();

            // Set From
            $this->sendCommand("MAIL FROM:<" . $this->username . ">");
            $this->readResponse();

            // Set To
            $this->sendCommand("RCPT TO:<" . $to . ">");
            $this->readResponse();

            // Send message
            $this->sendCommand("DATA");
            $this->readResponse();

            // Build email
            $email = "From: " . $this->username . "\r\n";
            $email .= "To: " . $to . "\r\n";
            $email .= "Subject: " . $subject . "\r\n";
            $email .= "MIME-Version: 1.0\r\n";
            $email .= "Content-Type: text/html; charset=UTF-8\r\n";
            $email .= $headers;
            $email .= "\r\n" . $message . "\r\n";

            $this->sendCommand($email . "\r\n.");
            $response = $this->readResponse();

            // Quit
            $this->sendCommand("QUIT");
            $this->readResponse();

            fclose($this->socket);

            return strpos($response, '250') === 0;
        } catch (Exception $e) {
            if ($this->debug) {
                error_log("Gmail SMTP Error: " . $e->getMessage());
            }
            if ($this->socket) {
                @fclose($this->socket);
            }
            throw $e;
        }
    }

    /**
     * Alias for send() method for convenience
     */
    public function sendEmail($to, $subject, $message, $headers = '')
    {
        return $this->send($to, $subject, $message, $headers);
    }

    private function sendCommand($cmd)
    {
        if ($this->debug) {
            error_log(">> " . substr($cmd, 0, 50));
        }
        fwrite($this->socket, $cmd . "\r\n");
    }

    private function readResponse()
    {
        $response = '';
        while (!feof($this->socket)) {
            $line = fgets($this->socket, 1024);
            $response .= $line;

            if ($this->debug) {
                error_log("<< " . trim($line));
            }

            if (substr($line, 3, 1) == ' ') {
                break;
            }
        }
        return $response;
    }
}
