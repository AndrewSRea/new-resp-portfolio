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
     */
}
