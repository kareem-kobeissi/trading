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
    private $caFile = '';
    private $verifyTls = true;

    public function __construct($email, $password, $debug = false, $host = 'smtp.gmail.com', $port = 587, $caFile = '', $verifyTls = true)
    {
        $this->username = $email;
        $this->password = $password;
        $this->debug = $debug;
        $this->host = $host;
        $this->port = (int) $port;
        $this->caFile = trim((string) $caFile);
        $this->verifyTls = (bool) $verifyTls;
    }

    public function send($to, $subject, $message, $headers = '')
    {
        try {
            // Connect to Gmail SMTP
            $sslOptions = [
                'verify_peer' => $this->verifyTls,
                'verify_peer_name' => $this->verifyTls,
                'peer_name' => $this->host,
                'SNI_enabled' => true,
                'allow_self_signed' => !$this->verifyTls,
            ];

            if ($this->caFile !== '' && is_file($this->caFile)) {
                $sslOptions['cafile'] = $this->caFile;
            }

            $context = stream_context_create(['ssl' => $sslOptions]);
            $this->socket = stream_socket_client(
                'tcp://' . $this->host . ':' . $this->port,
                $errno,
                $errstr,
                15,
                STREAM_CLIENT_CONNECT,
                $context
            );

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
            $this->expectResponse([334], 'SMTP authentication initialization failed');

            $this->sendCommand(base64_encode($this->username));
            $this->expectResponse([334], 'SMTP username was rejected');

            $this->sendCommand(base64_encode($this->password));
            $this->expectResponse([235], 'SMTP authentication failed. Check the mailbox username and password');

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
            $isMessage = str_contains($cmd, "\r\n") || str_contains($cmd, "\n");
            $sensitive = preg_match('/^[A-Za-z0-9+\/=]{8,}$/', trim($cmd));
            error_log(">> " . (($sensitive || $isMessage) ? '[sensitive content hidden]' : substr($cmd, 0, 100)));
        }
        fwrite($this->socket, $cmd . "\r\n");
    }

    private function expectResponse(array $allowedCodes, $errorMessage)
    {
        $response = $this->readResponse();
        $code = (int) substr($response, 0, 3);

        if (!in_array($code, $allowedCodes, true)) {
            throw new Exception($errorMessage . " (SMTP " . $code . ")");
        }

        return $response;
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
