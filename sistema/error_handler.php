<?php
if (function_exists("register_shutdown_function")) {
  register_shutdown_function( "fatal_handler" );
}

function fatal_handler() {
  $errfile = "unknown file";
  $errstr  = "shutdown";
  $errno   = E_CORE_ERROR;
  $errline = 0;
  $error = error_get_last();
  if($error !== NULL) {
    $errno   = $error["type"];
    $errfile = $error["file"];
    $errline = $error["line"];
    $errstr  = $error["message"];
    error_mail(format_error($errno, $errstr, $errfile, $errline));
  }
}

function format_error( $errno, $errstr, $errfile, $errline ) {
  $trace = print_r(debug_backtrace(false),true);
  $content = "
  <table>
  <thead><th>Item</th><th>Description</th></thead>
  <tbody>
  <tr>
    <th>Error</th>
    <td><pre>$errstr</pre></td>
  </tr>
  <tr>
    <th>Errno</th>
    <td><pre>$errno</pre></td>
  </tr>
  <tr>
    <th>File</th>
    <td>$errfile</td>
  </tr>
  <tr>
    <th>Line</th>
    <td>$errline</td>
  </tr>
  <tr>
    <th>Trace</th>
    <td><pre>$trace</pre></td>
  </tr>
  </tbody>
  </table>";
  return $content;
}

function error_mail($body) {
  // Me envio un email con el error
  $headers = "From:info@varcreative.com\r\n";
  $headers.= "MIME-Version: 1.0\r\n";
  $headers.= "Content-Type: text/html; charset=ISO-8859-1\r\n";
  @mail("basile.matias99@gmail.com","ERROR",$body,$headers);
}
?>