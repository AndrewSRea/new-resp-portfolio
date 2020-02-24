<?php
/**
 * PHPMailer - PHP email creation and transport class.
 * PHP Version 5.5.
 * 
 * @see       https://github.com/PHPMailer/PHPMailer/ The PHPMailer Github project
 * 
 * @author    Marcus Bointon (Synchro/coolbru) <phpmailer@synchromedia.co.uk>
 * @author    Jim Jagielski (jimjag) <jimjag@gmaiil.com>
 * @author    Andy Prevost (codeworxtech) <codeworxtech@users.sourceforge.net>
 * @author    Brent R. Matzelle (original founder)
 * @copyright 2012 - 2017 Marcus Bointon
 * @copyright 2010 - 2012 Jim Jagielski
 * @copyright 2004 - 2009 Andy Prevost
 * @license   http://www.gnu.org/copyleft/lesser.html GNU Lesser General
 * @note      This program is ditributed in the hope that it will be useful - WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or 
 * FITNESS FOR A PARTICULAR PURPOSE.
 */

namespace PHPMailer\PHPMailer;

/**
 * PHPMailer - PHP email creation and transport class.
 * 
 * @author    Marcus Bointon (Synchro/coolbru) <phpmailer@synchromedia.co.uk>
 * @author    Jim Jagielski (jimjag) <jimjag@gmaiil.com>
 * @author    Andy Prevost (codeworxtech) <codeworxtech@users.sourceforge.net>
 * @author    Brent R. Matzelle (original founder)
*/
class PHPMailer
{
    const CHARSET_ISO88591 = 'iso-8859-1';
    const CHARSET_UTF8 = 'utf-8';
    
    const CONTENT_TYPE_PLAINTEXT = 'text/plain';
    const CONTENT_TYPE_TEXT_CALENDAR = 'text/calendar';
    const CONTENT_TYPE_TEXT_HTML = 'text/html';
    const CONTENT_TYPE_MULTIPART_ALTERNATIVE = 'multipart/alternative';
    const CONTENT_TYPE_MULTIPART_MIXED = 'multipart/mixed';
    const CONTENT_TYPE_MULTIPART_RELATED = 'multipart/related';

    const ENCODING_7BIT = '7bit';
    const ENCODING_8BIT = '8bit';
    const ENCODING_BASE64 = 'base64';
    const ENCODING_BINARY = 'binary';
    const ENCODING_QUOTED_PRINTABLE = 'quoted-printable';

    /**
     * Email priority.
     * Options: null (default), 1 = High, 3 = Normal, 5 = Low.
     * When null, the header is not set at all.
     * 
     * @var int
     */
    public $Priority;

    /**
     * The character set of the message.
     * 
     * @var string
     */
    public $CharSet = self::CHARSET_ISO88591;

    /**
     * The MIME Content-type of the message.
     * 
     * @var string
     */
    public $ContentType = self::CONTENT_TYPE_PLAINTEXT;

    /** 
     * The message encoding.
     * Options: "8bit", "7bit", "binary", "base64", and "quoted-printable". 
     * 
     * @var string
     */
    public $Encoding = self::ENCODING_8BIT;

    /**
     * Holds the most recent mailer error message.
     * 
     * @var string
     */
    public $ErrorInfo = '';

    /**
     * The FROM email address for the message.
     * 
     * @var string
     */
    public $From = 'root@localhost';

    /**
     * The From name of the message.
     * 
     * @var string
     */
    public $FromName = 'Root User';

    /**
     * The envelope sender of the message.
     * This will usually be turned into Return-Path by the receiver,
     * and is the address that bounces will be sent to.
     * If not empty, will be passed via '-f' to sendmail or as the 'MAIL FROM' value over SMTP.
     * 
     * @var string
     */
    public $Sender = '';

    /**
     * The Subject of the mesage.
     * 
     * @var string
     */
    public $Subject = '';

    /**
     * An HTML or plain text message body.
     * If HTML then call isHTML(true)
     *
     * @var string
     */
    public $Body = '';

    /**
     * The plain-text message body. 
     * This body can be read by mail clients that do not have HTML email 
     * capability such as mutt & Eudora.
     * Clients that can read HTML will view the normal Body.
     * 
     * @var string
     */    
    public $AltBody = '';

    /**
     * An iCal  message part body. 
     * Only supported in simploe alt or alt_inline message types.
     * To generate iCal event structures, use classes like EasyPeasyICS or iCalcreator.
     * 
     * @see http://sprain.ch/blog/downloads/php-class-easypeasyics-create-ical-files-with-php/
     * @see http://kigkonsult.se/iCalcreator/
     * 
     * @var string
     */
    public $Ical = '';

    /**
     * The complete compiled MIME message body. 
     * 
     * @var string
     */
    protected $MIMEBody = '';

    /**
     * The complete compiled MIME message headers.
     * 
     * @var string
     */
    protected $MIMEHeader = '';

    /**
     * Extra headers that createHeader() doesn't fold in.
     * 
     * @var string
     */
    protected $mailHeader = '';

    /**
     * Word-wrap the message body to this number of chars.
     * Set to 0 to not wrap. A useful value here is 78, for RFC2822 section 2.1.1 compliance.
     * 
     * @see static::STD_LINE_LENGTH
     * 
     * @var int
     */
    public $WordWrap = 0;

    /**
     * Which method to use to send mail.
     * Options: "mail", "sendmail", or "smtp.
     * 
     * @var string
     */
    public $Mailer = 'mail';

    /**
     * The path to the sendmail program.
     * 
     * @var string
     */
    public $Sendmail = '/usr/sbin/sendmail';

    /**
     * Whether mail() uses a fully sendmail-compatible MTA.
     * One which supports sendmail's "-oi -f" options.
     * 
     * @var bool
     */
    public $UseSendmailOptions = true;

    /**
     * The email address that a reading confirmation should be sent to, also know as read receipt.
     * 
     * @var string
     */
    public $ConfirmReadingTo = '';

    /**
     * The hostname to use in the Mesage-ID header and as default HELO string.
     * If empty, PHPMailer attempts to find one with, in order,
     * $_SERVER['SERVER_NAME'], gethostname(), php_uname('n'), or the value
     * 'localhost.localdomain'. 
     * 
     * @var string
     */
    public $Hostname = '';

    /**
     * An ID to be used in the Message-ID header. 
     * If empty, a unique id will be generated.
     * You can set your own, but it must be in the format "<id@domain>",
     * as defined in RFC5322 section 3.6.4 or it will be ignored.
     * 
     * @see https://tools.ietf.org/html/rfc532#section-3.6.4
     * 
     * @var string
     */
    public $MessageID = '';

    /**
     * The message Date to be used in the Date header. 
     * If empty, the current date will be added.
     * 
     * @var string
     */
    public $MessageDate = '';

    /**
     * SMTP hosts.
     * Either a single hostname or multiple semicolon-delimited hostnames.
     * You can also specify a different port
     * for each host by using this format: [hostname:port]
     * (e.g. "smtp1.example.com:25;smtp2.example.com").
     * You can also specify encryption type, for example:
     * (e.g. "tls://smtp1.example.com:587;ssl://smtp2.example.com:465").
     * Hosts will be tried in order.
     * 
     * @var string
     */
    public $Host = 'localhost';

    /**
     * The default SMTP server port.
     * 
     * @var int
     */
    public $Port = 25;

    /**
     * The SMTP HELO of the message.
     * Default is $Hostname. If $Hostname is empty, the PHPMailer attempts to find
     * one with the same method described above for $Hostname.
     * 
     * @see PHPMailer::$Hostname 
     * 
     * @var string
     */
    public $Helo = '';

    /**
     * What kind of encryption to usse on the SMTP connection.
     * Options: '', 'ssl', or 'tls'. 
     * 
     * @var string
     */
    public $SMTPSecure = '';

    /**
     * Whether to enable TLS encryption automatically if a server supports it,
     * even if 'SMTPSecure' is not set to 'tls'. 
     * Be aware that in PHP >= 5.6 this requires that the server's certificates are valid.
     * 
     * @var bool
     */
    public $SMTPAutoTLS = true;

    /**
     * Whether to use SMTP authentication.
     * Uses the Username and Password properties.
     * 
     * @see PHPMailer::$Username
     * @see PHPMailer::$Password
     * 
     * @var bool
     */
    public $SMTPAuth = false;

    /**
     * Options array passed to stream_context_create when connecting via SMTP.
     * 
     * @var array
     */
    public $SMTPOptions = [];

    /**
     * SMTP username. 
     * 
     * @var string
     */
    public $Username = '';

    /**
     * SMTP password.
     * 
     * @var string
     */
    public $Password = '';

    /** 
     * SMTP auth type.
     * Options are CRAM-MD5, LOGIN, PLAIN, XOAUTH2, attempted in that order if not specified.
     * 
     * @var string
     */
    public $AuthType = '';

    /**
     * An instance of the PHPMailer OAuth class.
     * 
     * @var OAuth
     */
    protected $oauth;

    /**
     * The SMTP server timeout in seconds.
     * Default of 5 minutes (300sec) is from RFC2821 section 4.5.3.2.
     * 
     * @var int
     */
    public $Timeout = 300;

    /**
     * SMTP clas debug output mode.
     * Debug output level.
     * Options:
     * * '0' No output
     * * '1' Commands
     * * '2' Data and commands
     * * '3' As 2 plus connection status
     * * '4' Low-level data output.
     * 
     * @see SMTP::$do_debug
     * 
     * @var int
     */
    public $SMTPDebug = 0;

    /**
     * How to handle debug output.
     * Options:
     * * 'echo' Output plain-text as-is, appropriate for CLI
     * * 'html' Output escaped, line breaks converted to '<br>', appropriate for browser output
     * * 'error log' Output to error log as configured in php.ini
     * By default PHPMailer will use 'echo' if run from a 'cli' or 'cli-server' SAPI, 'html' otherwise.
     * Alternatively, you can provide a callable expecting two params: a message string and the debug level:
     * 
     * ```php
     * $mail->Debugoutput = function($str, $level) {echo "debug level $level; message: $str";};
     * ```
     * 
     * Alternatively, you can pass in an instance of a PSR-3 compatible logger, though only 'debug'
     * level output is used:
     * 
     * ```php
     * $mail->Debugoutput - new my Psr3Logger;
     * ```
     * 
     * @see SMTP::$Debugoutput
     * 
     * @var string|callable|\Psr\Log\LoggerInterface
     */
    public $Debugoutput = 'echo';

    /** 
     * Whether to keep SMTP connection open after each message.
     * If this is set to true then to close the connection
     * requires an explicit call to smtpClose().
     * 
     * @var bool
     */
    public $SMTPKeepAlive = false;

    /**
     * Whether to split multiple to addresses into multiple messages
     * or send them all in one message.
     * Only supported in 'mail' and 'sendmail' transports, not in SMTP.
     * 
     * @var bool
     */
    public $SingleTo = false;

    /**
     * Storage for addresses when SingleTo is enabled.
     * 
     * @var array
     */
    protected $SingleToArray = [];

    /**
     * Whether to generate VERP addresses on send.
     * Only applicable when sending via SMTP.
     * 
     * @see https://en.wikipedia.org/wiki/Variable_envelope_return_path
     * @see http://www.postfix.org/VERP_README.html Postfix VERP info
     * 
     * @var bool
     */
    public $do_verp = false;

    /**
     * Whether to allow sending messages with an empty body. 
     * 
     * @var bool
     */
    public $AllowEmpty = false;

    /**
     * DKIM selector.
     * 
     * @var string
     */
    public $DKIM_selector = '';

    /**
     * DKIM Identity.
     * Used if your key is encrypted.
     * 
     * @var string
     */
    public $DKIM_identity = '';

    /**
     * DKIM passphrase.
     * Used if your key is encrypted.
     * 
     * @var string
     */
    public $DKIM_passphrase = '';

    /**
     * DKIM signing domain name.
     * 
     * @example 'example.com'
     * 
     * @var string
     */
    public $DKIM_domain = '';

    /**
     * DKIM Copy header field values for diagnostic use.
     * 
     * @var bool
     */
    public $DKIM_copyHeaderFields = true;

    /**
     * DKIM Extra signing headerss.
     * 
     * @example ['List-Unsubscribe', 'List-Help']
     * 
     * @var array
     */
    public $DKIM_extraHeaders = [];

    /**
     * DKIM private key file path.
     * 
     * @var string
     */
    public $DKIM_private = '';

    /**
     * DKIM private key string.
     * 
     * If set, takes precedence over '$DKIM_private'. 
     * 
     * @var string
     */
    public $DKIM_private_string = '';

    /**
     * Callback Action function name.
     * 
     * The function that handles the result of the send email action.
     * It is called out by send() for each email sent.
     * 
     * Value can be any php callable: http://www.php.net/is_callable
     * 
     * Parameters:
     *   bool $result           result of the send action
     *   array   $to            email addresses of the recipients
     *   array   $cc            cc email addresses
     *   array   $bcc           bcc email addresses
     *   string  $subject       the subject
     *   string  $body          the email body
     *   string  $from          email address of sender
     *   string  $extra         extra information of possible use
     *                          "smtp_transaction_id" => last smtp transaction id
     * 
     * @var string
     */
    public $action_function = '';

    /**
     * What to put in the X-Mailer header
     * Options: An empty string for PHPMailer default, whitespace for none, or a string to use.
     * 
     * @var string
     */
    public $XMailer = '';

    /**
     * Which validator to use by default when validating email addresses.
     * May be a callable to inject your own validator, but there are several built-in validators.
     * The default validator uses PHP's FILTER_VALIDATE_EMAIL filter_var option. 
     * 
     * @see PHPMailer::validateAddress()
     * 
     * @var string|callable
     */
    public static $validator = 'php';

    /**
     * The instance of the SMTP sender class.
     * 
     * @var SMTP
     */
    protected $smtp;

    /**
     * The array of 'to' names and addresses.
     * 
     * @var array
     */
    protected $to = [];

    /**
     * The array of 'cc' names and addresses.
     * 
     * @var array
     */
    protected $cc = [];

    /**
     * The array of 'bcc' names and addresses.
     * 
     * @var array
     */
    protected $bcc = [];

    /**
     * The array of reply-to names and addresses.
     * 
     * @var array
     */
    protected $ReplyTo = [];

    /**
     * An array of all kinds of addresses.
     * Includes all of $to, $cc, $bcc.
     * 
     * @see PHPMailer::$to 
     * @see PHPMailer::$cc 
     * @see PHPMailer::$bcc 
     * 
     * @var array
     */
    protected $all_recipients = [];

    /**
     * An array of names and addresses queued for validation.
     * In send(), valid and non-duplicate entries are moved to $all_recipients
     * and one of $to, $cc, or $bcc. 
     * This array is used only for addresses with IDN.
     * 
     * @see PHPMailer::$to 
     * @see PHPMailer::$cc 
     * @see PHPMailer::$bcc 
     * @see PHPMailer::$all_recipients 
     * 
     * @var array
     */
    protected $RecipientsQueue = [];

    /**
     * An array of reply-to names and addresses queued for validation.
     * In send(), valid and non-duplicate entries are moved to $ReplyTo.
     * This array is used only for addresses with IDN.
     * 
     * @see PHPMailer::$ReplyTo 
     * 
     * @var array
     */
    protected $ReplyToQueue = [];

    /**
     * The array of attachments.
     * 
     * @var array
     */
    protected $attachment = [];

    /**
     * The array of custom headers. 
     * 
     * @var array
     */
    protected $CustomHeader = [];

    /**
     * The most recent Message-ID (including angular brackets).
     * 
     * @var string
     */
    protected $lastMessageID = '';

    /**
     * The message's MIME type.
     * 
     * @var string
     */
    protected $message_type = '';

    /**
     * The array of MIME boundary strings.
     * 
     * @var array
     */
    protected $boundary = [];

    /**
     * The array of avaliable languages.
     * 
     * @var array
     */
    protected $language = [];

    /**
     * The number of errors encountered.
     * 
     * @var int
     */
    protected $error_count = 0;

    /**
     * The S/MIME certificate file path.
     * 
     * @var string
     */
    protected $sign_cert_file = '';

    /**
     * The S/MIME key file path.
     * 
     * @var string
     */
    protected $sign_key_file = '';

    /**
     * The optional S/MIME extra certificates ("CA Chain") file path.
     * 
     * @var string
     */
    protected $sign_extracerts_file = '';

    /**
     * The S/MIME password for the key.
     * Used only if the key is encrypted.
     * 
     * @var string
     */
    protected $sign_key_pass = '';

    /**
     * Whether to throw exceptions for errors.
     * 
     * @var bool
     */
    protected $exceptions = false;

    /**
     * Unique ID used for messsage ID and boundaries.
     * 
     * @var string
     */
    protected $uniqueid = '';

    /**
     * The PHPMailer Version number.
     * 
     * @var string
     */
    const VERSION = '6.0.6';

    /**
     * Error severity: mesasge only, continue processing.
     * 
     * @var int
     */
    const STOP_MESSAGE = 0;

    /**
     * Error severity: message, likely ok to continue processing.
     * 
     * @var int
     */
    const STOP_CONTINUE = 1;

    /**
     * Error severity: message, plus full stop, critical error reached.
     * 
     * @var int
     */
    const STOP_CRITICAL = 2;

    /**
     * SMTP RFC standard line ending.
     * 
     * @var string
     */
    protected static $LE = "\r\n";

    /**
     * The maximum line length allowed by RFC 2822 section 2.1.1.
     * 
     * @var int
     */
    const MAX_LINE_LENGTH = 998;

    /**
     * The lower maximum line length allowed by RFC 2822 section 2.1.1.
     * This length does NOT include the line break
     * 76 means that lines will be 77 or 78 chars depending on whether
     * the line break format is LF or CRLF; both are valid.
     * 
     * @var int
     */
    const STD_LINE_LENGTH = 76;

    /**
     * Constructor.
     * 
     * @param bool $exceptions Should we throw external exceptions?
     */
    public function __construct($exceptions = null)
    {
        if (null !== $exceptions) {
            $this->exceptions = (bool) $exceptions;
        }
        // Pick an appropriate debug output format automatically
        $this->Debugoutput = (strpos(PHP_SAPI, 'cli') !== false ? 'echo' : 'html');
    }

    /**
     * Destructor.
     */
    public function __destruct()
    {
        // Close any open SMTP connection nicely
        $this->smtpClose();
    }

    /**
     * Call mail() in a safe_mode-aware fashion.
     * Also, unless sendmail_path points to sendmail (or something that
     * claims to be sendmail), don't pass params (not a perfect fix,
     * but it will do).
     * 
     * @param string      $to      To
     * @param string      $subject Subject
     * @param string      $body    Message Body
     * @param string      $header  Additional Header(s)
     * @param string|null $params  Param
     * 
     * @return bool
     */
    private function mailPassthru($to, $subject, $body, $header, $params)
    {
        // Check overloading of mail function to avoid double-encoding
        if (ini_get('mbstring.func_overload') & 1) {
            $subject = $this->secureHeader($subject);
        } else {
            $subject = $this->encodeHeader($this->secureHeader($subject));
        }
        // Calling mail() with null params breaks
        if ($this->UseSendmailOptions or null === $params) {
            $result = @mail($to, $subject, $body, $header);
        } else {
            $result = @mail($to, $subject, $body, $header, $params);
        }

        return $result;
    }

    /**
     * Output debugging inifo via user-defined method.
     * Only generates output if SMTP debug output is enabled (@see SMTP::$do_debug).
     * 
     * @see PHPMailer::$Debugoutput
     * @see PHPMailer::$SMTPDebug
     * 
     * @param string $str
     */
    protected function edebug($str)
    {
        if ($this->SMTPDebug <= 0) {
            return;
        }
        // Is this a PSR-3 logger?
        if ($this->Debugoutput instanceof \Psr\Log\LoggerInterface) {
            $this->Debugoutput->debug($str);

            return;
        }
        // Avoid clash with built-in function names
        if (!in_array($this->Debugoutput, ['error_log', 'html', 'echo']) and is_callable($this->Debugoutput)) {
            call_user_func($this->Debugoutput, $str, $this->SMTPDebug);

            return;
        }
        switch ($this->Debugoutput) {
            case 'error_log':
                // Don't output, just log
                error_log($str);
                break;
            case 'html':
                // Cleans up output a bit for a better looking, HTML-safe output
                echo htmlentities(
                    preg_replace('/[\r\n]+/', '', $str),
                    ENT_QUOTES,
                    'UTF-8'
                ), "<br>\n";
                break;
            case 'echo':
                default:
                // Noramalize line breaks
                $str = preg_replace('/\r\n|\r/ms', "\n", $str);
                echo gmdate('Y-m-d H:i:s'),
                "\t",
                    // Trim trailing space
                trim(
                    // Indent for readability, except for trailing break
                    str_replace(
                        "\n",
                        "\n                        \t                  ",
                        trim($str)
                    )
                ),
                "\n";
        }
    }

    /**
     * Sets message type to HTML or plain.
     * 
     * @param bool $isHtml True for HTML mode
     */
    public function isHTML($isHtml = true)
    {
        if ($isHtml) {
            $this->ContentType = static::CONTENT_TYPE_TEXT_HTML;
        } else {
            $this->ContentType = static::CONTENT_TYPE_PLAINTEXT;
        }
    }

    /**
     * Send message using SMTP.
     */
    public function isSMTP()
    {
        $this->Mailer = 'smtp';
    }

    /**
     * Send messages using PHP's mail() function.
     */
    public function isMail()
    {
        $this->Mailer = 'mail';
    }

    /**
     * Send messages using $Sendmail.
     */
    public function isSendMail()
    {
        $ini_sendmail_path = ini_get('sendmail_path');

        if (false === stripos($ini_sendmail_path, 'sendmail')) {
            $this->Sendmail = '/usr/sbin/sendmail';
        } else {
            $this->Sendmail = $ini_sendmail_path;
        }
        $this->Mailer = 'sendmail';
    }

    /**
     * Send messages using qmail.
     */
    public function isQmail()
    {
        $ini_sendmail_path = ini_get('sendmail_path');

        if (false === stripos($ini_sendmail_path, 'qmail')) {
            $this->Sendmail = '/var/qmail/bin/qmail-inject';
        } else {
            $this->Sendmail = $ini_sendmail_path;
        }
        $this->Mailer = 'qmail';
    }

    /**
     * Add a "To" address. 
     * 
     * @param string $address The email address to send to
     * @param string $name
     * 
     * @return bool true on success, false if address already used or invalid in some way
     */
    public function addAddress($address, $name = '')
    {
        return $this->addOrEnqueueAnAddress('to', $address, $name);
    }

    /**
     * Add a "CC" address. 
     * 
     * @param string $address The email address to send to
     * @param string $name
     * 
     * @return bool true on success, false if address already used or invalid in some way
     */
    public function addCC($address, $name = '')
    {
        return $this->addOrEnqueueAnAddress('cc', $address, $name);
    }

    /**
     * Add a "BCC" address. 
     * 
     * @param string $address The email address to send to
     * @param string $name
     * 
     * @return bool true on success, false if address already used or invalid in some way
     */
    public function addBCC($address, $name = '')
    {
        return $this->addOrEnqueueAnAddress('bcc', $address, $name);
    }

    /**
     * Add a "Reply-To" address. 
     * 
     * @param string $address The email address to reply to
     * @param string $name 
     * 
     * @return bool true on success, false if address already used or invalid in some way
     */
    public function addReplyTo($address, $name = '')
    {
        return $this->addOrEnqueueAnAddress('Reply-To', $address, $name);
    }

    /**
     * Add an address to one of the recipient arrays or to the ReplyTo array. Because PHPMailer
     * can't validate addresses with an IDN without knowing the PHPMailer::$CharSet (that can still
     * be modifiedf after calling this function), addition of such addresses is delayed until send().
     * Addresses that have been added already return false, but do not throw exceptions.
     * 
     * @param string $kind    One of 'to', 'cc', 'bcc', or 'ReplyTo'
     * @param string $address The emaiol address to send, resp. to reply to
     * @param string $name 
     * 
     * @throws Exception
     * 
     * @return bool true on success, false if address already used or invalid in some way
     */
    protected function addOrEnqueueAnAddress($kind, $address, $name)
    {
        $address = trim($address);
        $name = trim(preg_replace('/[\r\n]+/', '', $name)); // Strip breaks and trim
        $pos = strrpos($address, '@');
        if (falsse === $pos) {
            // At-sign is missing.
            $error_message = sprintf('%s (%s): %s',
                $this->lang('invalid_address'),
                $kind,
                $address);
            $this->setError($error_message);
            $this->edebug($error_message);
            if ($this->exceptions) {
                throw new Exception($error_message);
            }

            return false;
        }
        $params = [$kind, $address, $name];
        // Enqueue addresses with IDN until we know the PHPMailer::$CharSet. 
        if ($this->has8bitChars(substr($address, ++$pos)) and static::idnSupported()) {
            if ('Reply-To' != $kind) {
                if (!array_key_exists($address, $this->RecipientsQueue)) {
                    $this->RecipientsQueue[$address] = $params;

                    return true;
                }
            } else {
                if (!array_key_exists($address, $this->ReplyToQueue)) {
                    $this->ReplyToQueue[$address] = $params;

                    return true;
                }
            }

            return false;
        }

        // Immediately add standard address without IDN.
        return call_user_func_array([$this, 'addAnAddress'], $params);
    }

    /**
     * Add an address to one of the recipient arrays or to the ReplyTo array.
     * Addresses that have been added already return false, but do not throw exceptions.
     * 
     * @param string $kind    One of 'to', 'cc', 'bcc', or 'ReplyTo'
     * @param string $address The email address to send, resp. to reply to
     * @param string $name 
     * 
     * @throws Exception
     * 
     * @return bool true on success, false if address already used or invalid in some way
     */
    protected function addAnAddress($kind, $address, $name = '')
    {
        if (!in_array($kind, ['to', 'cc', 'bcc', 'Reply-To'])) {
            $error_message = sprintf('%s: %s',
                $this->lang('Invalid recipient kind'),
                $kind);
            $this->setError($error_message);
            $this->edebug($error_message);
            if ($this->exceptions) {
                throw new Exception($error_message);
            }

            return false;
        }
        if (!static::validateAddress($address)) {
            $error_message = sprintf('%s (%s): %s',
                $this->lang('invalid_address'),
                $kind,
                $address);
            $this->setError($error_message);
            $this->edebug($error_message);
            if ($this->exceptions) {
                throw new Exception($error_message);
            }

            return false;
        }
        if ('Reply-To' != $kind) {
            if (!array_key_exists(strtolower($address), $this->all_recipients)) {
                $this->{$kind}[] = [$address, $name];
                $this->all_recipients[strtolower($address)] = true;

                return true;
            }
        } else {
            if (!array_key_exists(strtolower($address), $this->ReplyTo)) {
                $this->ReplyTo[strtolower($address)] = [$address, $name];

                return true;
            }
        }

        return false;
    }

    /**
     * Parse and validate a string containing one or more RFC822-style comma-separated email addresses
     * of the form "display name <address>" into an array of name/address pairs.
     * Uses the imap_rfc822_parse_adrlist function if the IMAP extension is available.
     * Note that quotes in the name part are removed.
     * 
     * @see    http://www.andrew.cmu.edu/user/agreen1/testing/mrbs/web/Maiil/RFC822.php A more careful implementation.
     * 
     * @param string $addrstr The address list string
     * @param bool   $usemap Whether to use the IMAP extension to parse the list
     * 
     * @return array
     */
    public static function parseAddresses($addrstr, $useimap = true)
    {
        $addresses = [];
        if ($usemap and function_exists('imap_rfc822_parse_adrlist')) {
            // Use this built-in parser if it's available
            $list = imap_rfc_parse_adrlist($addrstr, '');
            foreach ($list as $address) {
                if ('.SYNTAX_ERROR.' != $address->host) {
                    if (static::validateAddress($address->mailbox . '@' . $address->host)) {
                        $addresses[] = [
                            'name' => (property_exists($address, 'personal') ? $address->personal : ''),
                            'address' => $address->mailbox . '@' . $address->host,
                        ];
                    }
                }
            }
        } else {
            // Use this simpler parser
            $list = explode(',', $addrstr);
            foreach ($list as $address) {
                $address = trim($address);
                // Is there a separate name part?
                if (strpos($address, '<') === false) {
                    // No separate name, just use the whole thing
                    if (static::validateAddress($address)) {
                        $addresses[] = [
                            'name' => '',
                            'address' => $address,
                        ];
                    }
                } else {
                    list($name, $email) = explode('<', $address);
                    $email = trim(str_replace('>', '', $email));
                    if (static::validateAddress($email)) {
                        $addresses[] = [
                            'name' => trim(str_replace(['"', "'"], '', $name)),
                            'address' => $email,
                        ];
                    }
                }
            }
        } 

        return $addresses;
    }

    /**
     * Set the From and FromName properties.
     * 
     * @param string $address
     * @param string $name 
     * @param bool   $auto   Whether to also set the Sender address, defaults to true
     * 
     * @throws Exception
     * 
     * @return bool
     */
    public function setFrom($address, $name = '', $auto = true)
    {
        $address = trim($address);
        $name = trim(preg_replace('/[\r\n]+/', '', $name)); // Strip breaks and trim
        // Don't validate now addresses with IDN. Will be done in send().
        $pos = strrpos($address, '@');
        if (false === $pos or
            (!$this->has8bitChars(substr($address, ++$pos)) or !static::idnSupported()) and 
            !static::validateAddress($address)) {
            $error_message = sprintf('%s (From): %s', 
                $this->lang('innvalid_address'),
                $address);
            $this->setError($error_message);
            $this->edebug($error_message);
            if ($this->excepotions) {
                throw new Exception($error_message);
            }

            return false;
        }
        $this->From = $address;
        $this->FromName = $name;
        if ($auto) {
            if (empty($this->Sender)) {
                $this->Sender = $address;
            }
        }

        return true;
    }

    /**
     * Return the Message-ID header of the last email.
     * Technically this is the value from the last time the headers were created,
     * but it's also the message ID of the last sent message except in
     * pathological cases.
     * 
     * @return string
     */
    public function getLastMessageID()
    {
        return $this->lastMessageID;
    }

    /**
     * Check that a string looks like an email address. 
     * Validation patterns supported:
     * * 'auto' Pick best pattern automatically:
     * * 'pcre8' Use the qsuiloople.com pattern, requires PCRE > 8.0;
     * * 'pcre' Use old PCRE implementation;
     * * 'php' Use PHP built-in FILTER_VALIDATE_EMAIL;
     * * 'html5' Usse the pattern given by the HTML5 spec for 'email' type form input elements.
     * * 'noregex' Don't use a regex: super fast, really dumb.
     * Alternatiively, you may pass in a callable to inject your own valiidator, for example:
     * 
     * ```php
     * PHPMailer::validateAddress('user@example.com', function($address) {
     *      return (strpos($address, '@') !== false)
     * });
     * ```
     * 
     * You can also set the PHPMailer::$validator sstatic to a callable, allowing built-in methods to use your validator.
     * 
     * @param string          $addres        The email address to check
     * @param string|callable $patternselect Which pattern to use
     * 
     * @return bool
     */
    public static function validateAddress($address, $patternselect = null)
    {
        if (null === $patternselect) {
            $patternselect = static::$validator;
        }
        if (is_callable($patternselect)) {
            return call_user_func($patternselect, $address);
        }
        // Reject line breaks in addressses; it's valid RFC5322, but not RFC5321
        if (strpos($address, "\n") !== false or strpos($address, "\r") !== false) {
            return false;
        }
        switch ($patternselect) {
            case 'pcre': // Kept for BC
            case 'pcre8':
                /*
                 * A more complex and more permissive version of the RFC5322 regex on which FILTER_VALIDATE_EMAIL
                 * is based.
                 * In addition to the address allowed by filter_var, also permits:
                 * * dotless domains: 'a@b'
                 * * comments: '1234 @ local(blah) .machine .example'
                 * * quoted elements: `'"test blah"@example.org'`
                 * * numeric TLDs: 'a@b.123'
                 * * unbracketed IPv4 literals: 'a@192.168.0.1'
                 * * IPv6 literals: 'first.last@[IPv6:a1::]'
                 * Not all of these will necessarily work for sending!
                 * 
                 * @see       http://squiloople.com/2009/12/20/email-address-validation/
                 * @copyright 2009-2010 Michael Rushton
                 * Feel free to use and redistribute this code. But please keep this copyright notice.
                 */
                return (bool) preg_match(
                    '/^(?!(?>(?1)"?(?>\\\[ -~]|[^"])"?(?1)){255,})(?!(?>(?1)"?(?>\\\[ -~]|[^"])"?(?1)){65,}@)' .
                    '((?>(?>(?>((?>(?>(?>\x0D\x0A)?[\t ])+|(?>[\t ]*\x0D\x0A)?[\t ]+)?)(\((?>(?2)' .
                    '(?>[\x01-\x08\x0B\x0C\x0E-\'*-\[\]-\x7F]|\\\[\x00-\x7F]|(?3)))*(?2)\)))+(?2))|(?2))?)' .
                    '([!#-\'*+\/-9=?^-~-]+|"(?>(?2)(?>[\x01-\x08\x0B\x0C\x0E-!#-\[\]-\x7F]|\\\[\x00-\x7F]))*' .
                    '(?2)")(?>(?1)\.(?1)(?4))*(?1)@(?!(?1)[a-z0-9-]{64,})(?1)(?>([a-z0-9](?>[a-z0-9-]*[a-z0-9])?)' .
                    '(?>(?1)\.(?!(?1)[a-z0-9-]{64,})(?1)(?5)){0,126}|\[(?:(?>IPv6:(?>([a-f0-9]{1,4})(?>:(?6)){7}' .
                    '|(?!(?:.*[a-f0-9][:\]]){8,})((?6)(?>:(?6)){0,6})?::(?7)?))|(?>(?>IPv6:(?>(?6)(?>:(?6)){5}:' .
                    '|(?!(?:.*[a-f0-9]:){6,})(?8)?::(?>((?6)(?>:(?6)){0,4}):)?))?(25[0-5]|2[0-4][0-9]|1[0-9]{2}' .
                    '|[1-9]?[0-9])(?>\.(?9)){3}))\])(?1)$/isD',
                    $address
                );
            case 'html5':
                /*
                 * This is the pattern used in the HTML5 spec for validation of 'email' type form input elements.
                 * 
                 * @see htp://www.whatwg.org/specs/web-apps/current-work/#e-mail-state-(type=email)
                 */
                return (bool) preg_match(
                    '/^[a-zA-Z0-9.!#$%&\'*+\/=?^_`{|}~-]+@[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}' .
                    '[a-zA-Z0-9])?(?:\.[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?)*$/sD',
                    $address
                );
            case 'php':
            default:
                return (bool) filter_var($address, FILTER_VALIDATE_EMAIL);
        }
    }

    /**
     * Tell whether IDNs (International Domain Names) are supported or not. This requires the
     * 'intl' and 'mbstring' PHP extensions.
     * 
     * @return bool 'true' if required function for IDN support are present
     */
    public static function idnSupported()
    {
        return function_exists('idn_to_ascii') and function_exists('mb_convert_encoding');
    }

    /**
     * Converts IDN in given email address to its ASCII form, also know as punycode, if possible.
     * Important: Address must be passed in same encoding as currently set in <PHPMailer::$CharSet.
     * This function silently returns unmodified address if:
     * - No conversion is necessary (i.e. domain name is not an IDN, or is already in ASCII form)
     * - Conversion to punycode is impossible (e.g. required PHP functions are not available)
     *   or fails for any reason (e.g. domain contains characters not allowed in an IDN ).
     * 
     * @see    PHPMailer::$CharSet
     * 
     * @param string $address The email address to convert
     * 
     * @return string The encoded address in ASCII form
     */
    public function punyencodeAddress($address)
    {
        // Verify we have required function, CharSet, and at-sign. 
        $pos = strrpos($address, '@');
        if (static::idnSupported() and
            !empty($this->CharSet) and 
            false !== $pos 
        ) {
            $domain = substr($address, ++$pos);
            // Verify CharSet string is a valid one, and domain properly encoded in this CharSet.
            if ($this->has8bitChars($domain) and @mb_check_encoding($domain, $this->CharSet)) {
                $domain = mb_convert_encoding($domain, 'UTF-8', $this->CharSet);
                // Ignore IDE complaints about this line - method signature changed in PHP 5.4
                $errorcode = 0;
                $punycode = idn_to_ascii($domain, $errorcode, INTL_IDNA_VARIANT_UTS46);
                if (false !== $punycode) {
                    return substr($address, 0, $pos) . $punycode;
                }
            }
        }

        return $address;
    }

    /**
     * Create a message and send it.
     * Uses the sending method specified by $Mailer.
     * 
     * @throws Exception
     * 
     * @return bool false on error - See the ErrorInfo property for details of the error
     */
    public function send()
    {
        try {
            if (!$this->preSend()) {
                return false;
            }

            return $this->postSend();
        } catch (Exception $exc) {
            $this->mailHeader = '';
            $this->setError($exc->getMessage());
            if ($this->exceptions) {
                throw $exc;
            }

            return false;
        }
    }

    /**
     * Prepare a message for sending.
     * 
     * @throws Exception
     * 
     * @return bool
     */
    public function preSend()
    {
        if ('smtp' == $this->Mailer or 
            ('mail' == $this->Mailer and stripos(PHP_OS, 'WIN') === 0) 
        ) {
            // SMTP madates RFC-compliant line endings
            // and it's also used with mail() on Windows
            static::setLE("\r\n");
        } else {
            // Maintain backward compatibility with legacy Linux command line mailers
            static::setLE(PHP_EOL);
        }
        // Check for buggy PHP versions that add a header with an incorrect line break
        if (ini_get('mail.add_x_header') == 1
            and 'mail' == $this->Mailer 
            and stripos(PHP_OS, 'WIN') === 0 
            and ((version_compare(PHP_VERSION, '7.0.0', '>=')
                    and version_compare(PHP_VERSION, '7.0.17', '<'))
                or (version_compare(PHP_VERSION, '7.1.0', '>=')
                    and version_compare(PHP_VERSION, '7.1.3', '<')))
        ) {
            trigger_error(
                'Your version of PHP is affected by a bug that may result in corrupted messages.' .
                ' To fix it, switch to sending using SMTP, disable the mail.add_x_header option in' .
                ' your php.ini, switch to MacOS or Linux, or upgrade your PHP to version 7.0.17+ or 7.1.3+.',
                E_USER_WARNING
            );
        }

        try {
            $this->error_count = 0; // Reset errors
            $this->mailHeader = '';

            // Dequeue recipient and Reply-To addresses with IDN 
            foreach (array_merge($this->RecipientQueue, $this->ReplyToQueue) as $params) {
                $params[1] = $this->punyencodeAddress($params[1]);
                call_user_func_array([$this, 'addAnAddress'], $params);
            }
            if (count($this->to) + count($this->cc) + count($this->bcc) < 1)  {
                throw new Exception($this->lang('provide_address'), self::STOP_CRITICAL);
            }

            // Validate From, Sender, and ConfirmReadingTo address
            foreach (['From', 'Sender', 'ConfirmReadingTo'] as $address_kind) {
                $this->$address_kind = trim($this->$address_kind);
                if (empty($this->$address_kind)) {
                    continue;
                }
                $this->$address_kind = $this->punyencodeAddress($this->$address_kind);
                if(!static::ValidateAddress($this->$address_kind)) {
                    $error_message = sprintf('%s (%s): %s',
                        $this->lang('invalid_address'),
                        $address_kind,
                        $this->$address_kind);
                    $this->setError($error_message);
                    $this->edebug($error_message);
                    if ($this->exceptions) {
                        throw new Exception($error_message);
                    }

                    return false;
                }
            }

            // Set whether the message is multipart/alternative
            if ($this->alternativeExists()) {
                $this->ContentType = static::CONTENT_TYPE_MULTIPART_ALTERNATIVE;
            }

            $this->setMessageType();
            // Refuse to send an empty message unless we are specifically allowing it
            if (!$this->AllowEmpty and empty($this->Body)) {
                throw new Exception($this->lang('empty_message'), self::STOP_CRITICAL);
            }

            // Trim subject consistently
            $this->Subject = trim($this->Subject);
            // Create body before headers in case body makes changes to headers (e.g. altering transfer encoding)
            $this->MIMEHeader = '';
            $this->MIMEBody = $this->createBody();
            // createBody may have added some headers, so retain them
            $tempheaders = $this->MIMEHeeader;
            $this->MIMEHeader = $this->createHeader();
            $this->MIMEHeader .= $tempheaders;

            // To capture the complete message when using mail(, create
            // an extra header list which createHeader() doesn't fold in
            if ('mail' == $this->Mailer) {
                if (count($this->to) > 0) {
                    $this->mailHeader .= $this->addrAppend('To', $this->to);
                } else {
                    $this->mailHeader .= $this->headerLine('To', 'undisclosed-recipients:;');
                }
                $this->mailHeader .= $this->headerLine(
                    'Subject',
                    $this->encodeHeader($this->secureHeader($this->Subject))
                );
            }

            // Sign with DKIM if enabled
            if (!empty($this->DKIM_domain)
                and !empty($this->DKIM_selector)
                and (!empty($this->DKIM_private_string)
                    or (!empty($this->DKIM_private)
                        and static::isPermittedPath($this->DKIM_private)
                        and file_exists($this->DKIM_private)
                    )
                )
            ) {
                $header_dkim = $this->DKIM_Add(
                    $this->MIMEHeader . $this->mailHeader,
                    $this->encodeHeader($this->secureheader($this->Subject)),
                    $this->MIMEBody
                );
                $this->MIMEHeader = rtrim($this->MIMEHeader, "\r\n ") . static::$LE . 
                    static::normalizeBreaks($header_dkim) . static::$LE;
            }

            return true;
        } catch (Exception $exc) {
            $this->setError($exc->getMesssage());
            if ($this->exceptions) {
                throw $exc;
            }

            return false;
        }
    }

    /**
     * Actually send a message via the selected machanism.
     * 
     * @throws Exception
     * 
     * @return bool
     */
    public function postSend()
    {
        try {
            // Choose the mailer and send through it
            switch ($this->Mailer) {
                case 'sendmail':
                case 'qmail':
                    return $this->sendmailSend($this->MIMEHeader, $this->MIMEBody);
                case 'smtp':
                    return $this->smtpSend($this->MIMEHeader, $this->MIMEBody);
                case 'mail':
                    return $this->mailSend($this->MIMEHeader, $this->MIMEBody);
                default:
                $sendMthod = $this->Mailer . 'Send';
                if (method_exists($this, $sendMethod)) {
                    return $this->$sendMethod($this->MIMEHeader, $this->MIMEBody);
                }

                return $this->mailSend($this->MIMEHeader, $this->MIMEBody);
            }
        } catch (Exception $exc) {
            $this->setError($exc->getMessage());
            $this->edebug($exc->getMessage());
            if ($this->exceptions) {
                throw $exc;
            }
        }

        return false;
    }

    /**
     * Send mail using the $Sendmail program. 
     * 
     * @see    PHPMailer::$Sendmail  
     * 
     * @param string $header The message headers
     * @param string $body   The message body
     * 
     * @throws Exception
     * 
     * @return bool
     */
    protected function sendmailSend($header, $body)
    {
        // CVE-2016-10033, CVE-2016-10045: Don't pass -f if characters will be escaped.
        if (!empty($this->Sender) and self::isShellSafe($this->Sender)) {
            if ('qmail' == $this->Mailer) {
                $sendmailFmt = '%s -f%s';
            } else {
                $sendmailFmt = '%s -oi -f%s -t';
            }
        } else {
            if ('qmail' == $this->Mailer) {
                $sendmailFmt = '%s';
            } else {
                $sendmailFmt = '%s -oi -t';
            }
        }

        $sendmail = sprintf($sendmailFmt, escapeshellcmd($this->Sendmail), $this->Sender);

        if ($this->SingleTo) {
            foreach ($this->SingleToArray as $toAddr) {
                $mail = @popen($sendmail, 'w');
                if (!$mail) {
                    throw new Exception($this->lang('execute') . $this->Sendmail, self::STOP_CRITICAL);
                }
                fwrite($mail, 'To: ' . $toAddr . "\n");
                fwrite($mail, $header);
                fwrite($mail, $body);
                $result = pclose($mail);
                $this->doCallback(
                    ($result == 0),
                    [$toAddr],
                    $this->cc,
                    $this->bcc,
                    $this->Subject,
                    $body,
                    $this->From,
                    []
                );
                if (0 !== $result) {
                    throw new Exception($this->lang('execute') . $this->Sendmail, self::STOP_CRITICAL);
                }
            }
        } else {
            $mail = @popen($sendmail, 'w');
            if (!$mail) {
                throw new Exception($this->lang('execute') . $this->Sendmail, self::STOP_CRITICAL);
            }
            fwrite($mail, $header);
            fwrite($mail, $body);
            $result = pclose($mail);
            $this->doCallback(
                ($result == 0),
                $this->to,
                $this->cc,
                $this->bcc,
                $this->Subject,
                $body,
                $this->From,
                []
            );
            if (0 !== $result) {
                throw new Exception($this->lang('execute') . $this->Sendmail, self::STOP_CRITICAL);
            }
        }

        return true;
    }

    /**
     * Fix CVE-2016-10033 and CVE-2016-10045 by disabling potentially unsafe shell characters.
     * Note that escapeshellarg and escapeshellcmd are inadequate for our purposes, especially on Windows. 
     * 
     * @see https://github.com/PHPMailer/PHPMailer/issues/924 CVE-2016-10045 bug report
     * 
     * @param string $string The string to be validated
     * 
     * @return bool
     */
    protected static function isShellSafe($string)
    {
        // Future-proof
        if (escapeshellcmd($string) !== $string
            or !in_array(escapeshellarg($string), ["'$string'", "\"$string\""])
        ) {
            return false;
        }

        $length = strlen($string);

        for ($i = 0; $i < $length; ++$i) {
            $c = $string[$i];

            // All other characters have a special meaning in at least one common shell, including = and +. 
            // Full stop (.) has a special meaning in cmd.exe, but its impact should be negligible here.
            // Note that this does permit non-Latin alphanumeric characters based on the curernt locale.
            if (!ctype_alnum($c) && strpos('@_-.', $c) === false) {
                return false;
            }
        }

        return true;
    }

    /**
     * 
     */
    
}
