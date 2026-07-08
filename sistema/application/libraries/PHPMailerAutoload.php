<?php
/**
 * PHPMailer SPL autoloader.
 * PHP Version 5
 * @package PHPMailer
 * @link https://github.com/PHPMailer/PHPMailer/ The PHPMailer GitHub project
 * @author Marcus Bointon (Synchro/coolbru) <phpmailer@synchromedia.co.uk>
 * @author Jim Jagielski (jimjag) <jimjag@gmail.com>
 * @author Andy Prevost (codeworxtech) <codeworxtech@users.sourceforge.net>
 * @author Brent R. Matzelle (original founder)
 * @copyright 2012 - 2014 Marcus Bointon
 * @copyright 2010 - 2012 Jim Jagielski
 * @copyright 2004 - 2009 Andy Prevost
 * @license http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License
 * @note This program is distributed in the hope that it will be useful - WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or
 * FITNESS FOR A PARTICULAR PURPOSE.
 */

/**
 * PHPMailer SPL autoloader.
 * @param string $classname The name of the class to load
 */
function PHPMailerAutoload($classname)
{
    //Can't use __DIR__ as it's only in PHP 5.3+
    $filename = dirname(__FILE__).DIRECTORY_SEPARATOR.'class.'.strtolower($classname).'.php';
    if (is_readable($filename)) {
        require $filename;
    }
}

if (version_compare(PHP_VERSION, '5.1.2', '>=')) {
    //SPL autoloading was introduced in PHP 5.1.2
    if (version_compare(PHP_VERSION, '5.3.0', '>=')) {
        spl_autoload_register('PHPMailerAutoload', true, true);
    } else {
        spl_autoload_register('PHPMailerAutoload');
    }
} else {
    /**
     * Fall back to traditional autoload for old PHP versions
     * @param string $classname The name of the class to load
     */
    function __autoload($classname)
    {
        PHPMailerAutoload($classname);
    }
}

function phpmailer_send($conf = array()) {

  $subject = isset($conf["subject"]) ? $conf["subject"] : "";
  $body = isset($conf["body"]) ? $conf["body"] : "";
  $from = isset($conf["from"]) ? $conf["from"] : "no-reply@varcreative.com";
  $from_name = isset($conf["from_name"]) ? $conf["from_name"] : "Varcreative";

  // Pueden ser un string o un array
  $to = isset($conf["to"]) ? $conf["to"] : "";
  $cc = isset($conf["cc"]) ? $conf["cc"] : "";
  $bcc = isset($conf["bcc"]) ? $conf["bcc"] : "";

  $to_name = isset($conf["to_name"]) ? $conf["to_name"] : "";
  $reply_to = isset($conf["reply_to"]) ? $conf["reply_to"] : "";
  $reply_to_name = isset($conf["reply_to_name"]) ? $conf["reply_to_name"] : "";

  if (empty($to)) return FALSE;

  $mail = new PHPMailer();
  $mail->IsSMTP();
  $mail->CharSet = 'UTF-8';
  $mail->Host       = "smtp.mandrillapp.com";
  $mail->SMTPAuth   = true;
  $mail->Port       = 25;
  $mail->Username   = "Varcreative";
  $mail->Password   = "XkF5_TxdFAmZcuImcRastQ";

  // De
  $mail->setFrom($from, $from_name);

  // Para
  if (is_string($to)) {
    $mail->addAddress($to, $to_name);  
  } else if (is_array($to)) {
    foreach($to as $t) $mail->addAddress($t);
  }

  // Con copia a
  if (is_string($cc)) {
    $mail->addAddress($cc);
  } else if (is_array($cc)) {
    foreach($cc as $t) $mail->addAddress($t);
  }

  // Con copia oculta
  if (is_string($bcc)) {
    $mail->addAddress($bcc);
  } else if (is_array($bcc)) {
    foreach($bcc as $t) $mail->addAddress($t);
  }
  
  if (!empty($reply_to)) $mail->addReplyTo($reply_to, $reply_to_name);

  $mail->isHTML(true);  
  $mail->Subject = $subject;
  $mail->Body    = $body;
  $res = $mail->send();
  return $res;
}