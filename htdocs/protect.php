<?php 
/**
 * Password-protect the file where this is included
 * 
 * @author Antoine Pouch <ant-1@pouch.name>
 * @package gestionlite
 * @module htdocs
 */

if (!isset($_SERVER['PHP_AUTH_USER'])
    || !isset($_SERVER['PHP_AUTH_PW'])
    || $_SERVER['PHP_AUTH_USER'] != ADMIN_USERNAME
    || $_SERVER['PHP_AUTH_PW'] != ADMIN_PASSWORD)
{
  Header("WWW-Authenticate: Basic realm=\"Identification GestionLite\"");
  Header("HTTP/1.0 401 Unauthorized");

  echo <<<EOB
    <html><body>
    <h1>Forbidden</h1>
    <big>Wrong Username or Password!</big>
    </body></html>
EOB;
  exit;
}
?>