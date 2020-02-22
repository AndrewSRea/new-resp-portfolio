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
}
