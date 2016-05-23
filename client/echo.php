<?php
// This client for local_wstemplate is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//

/**
 * XMLRPC client for Moodle
 *
 * This script does not depend of any Moodle code,
 * and it can be called from a browser.
 *
 */

/// MOODLE ADMINISTRATION SETUP STEPS
// 1- Install the plugin
// 2- Enable web service advance feature (Admin > Advanced features)
// 3- Enable XMLRPC protocol (Admin > Plugins > Web services > Manage protocols)
// 4- Create a token for a specific user and for the service 'LRS Proxy' (Admin > Plugins > Web services > Manage tokens)
// 5- Run this script directly from your browser: you should see 'Hello, FIRSTNAME'

/// SETUP - NEED TO BE CHANGED
$token = '8e1b12fedfd98796c04e199407acfd62';
$domainname = 'http://localhost/moodle';

/// FUNCTION NAME
$functionname = 'lrsproxy_echo_text';

/// PARAMETERS
$text = 'Hello';

///// XML-RPC CALL
header('Content-Type: text/plain');
$serverurl = $domainname . '/webservice/xmlrpc/server.php'. '?wstoken=' . $token;
require_once('./curl.php');
$curl = new curl;
$post = xmlrpc_encode_request($functionname, array($text));
$resp = xmlrpc_decode($curl->post($serverurl, $post));
print_r($resp);