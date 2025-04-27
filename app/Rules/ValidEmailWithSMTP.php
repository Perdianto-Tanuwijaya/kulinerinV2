<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Egulias\EmailValidator\EmailValidator;
use Egulias\EmailValidator\Validation\DNSCheckValidation;
use Egulias\EmailValidator\Validation\MultipleValidationWithAnd;
use Egulias\EmailValidator\Validation\RFCValidation;

class ValidEmailWithSMTP implements Rule
{
    protected $message;

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        // Basic format and MX record validation
        $validator = new EmailValidator();
        $multipleValidations = new MultipleValidationWithAnd([
            new RFCValidation(),
            new DNSCheckValidation()
        ]);

        if (!$validator->isValid($value, $multipleValidations)) {
            $this->message = 'Invalid email format or domain does not have a valid MX record.';
            return false;
        }

        // SMTP validation
        return $this->validateSMTP($value);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return $this->message ?? 'The email address is invalid or cannot receive messages.';
    }

    /**
     * Validate email using SMTP check
     *
     * @param string $email
     * @return bool
     */
    protected function validateSMTP($email)
    {
        $domain = substr(strrchr($email, "@"), 1);

        // Get MX records
        $mx_records = [];
        if (!getmxrr($domain, $mx_records)) {
            $this->message = 'The email domain does not have valid MX records.';
            return false;
        }
        \Log::info('Cek Host', [
            'host' => $mx_records[0],
            'port' => 25,
        ]);
        // Connect to the first MX server
        $smtp_conn = @fsockopen($mx_records[0], 25, $errno, $errstr, 5);
        if (!$smtp_conn) {
            $this->message = 'Unable to connect to the email server.';
            return false;
        }

        // Set connection timeout
        stream_set_timeout($smtp_conn, 5);

        // Skip greeting
        fgets($smtp_conn, 515);

        // Say HELO to the server
        $host = $_SERVER['SERVER_NAME'] ?? 'localhost';
        fputs($smtp_conn, "HELO $host\r\n");
        fgets($smtp_conn, 515);

        // Send MAIL FROM command
        $from = 'verification@' . $host;
        fputs($smtp_conn, "MAIL FROM: <$from>\r\n");
        $from_response = fgets($smtp_conn, 515);

        if (strpos($from_response, '250') !== 0) {
            $this->message = 'The email server rejected our sender address.';
            fclose($smtp_conn);
            return false;
        }

        // Send RCPT TO command
        fputs($smtp_conn, "RCPT TO: <$email>\r\n");
        $to_response = fgets($smtp_conn, 515);

        // Close connection
        fputs($smtp_conn, "QUIT\r\n");
        fclose($smtp_conn);

        // Check if the server accepts the email address
        if (strpos($to_response, '250') === 0 || strpos($to_response, '451') === 0 || strpos($to_response, '452') === 0) {
            return true;
        }

        $this->message = 'Invalid email address';
        return false;
    }
}
