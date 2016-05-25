<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * @package     local
 * @subpackage  lrsproxy
 * @copyright   2016, Felix J. Garcia <fgarcia@um.es>
 *					  Luis de la Torre Cubillo <ldelatorre@dia.uned.es>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define('AJAX_SCRIPT', true);
define('REQUIRE_CORRECT_ACCESS', true);
define('NO_MOODLE_COOKIES', true);

require_once(dirname(dirname(__FILE__)) . '/../../config.php');
require_once('tokenlib.php');

// Allow CORS requests.
header('Access-Control-Allow-Origin: *');

// Check params
$serviceshortname  = required_param('service',  PARAM_ALPHANUMEXT);
$instanceid   = required_param('instanceid', PARAM_INT);
$userid   = required_param('userid', PARAM_INT);
// $launchid   = required_param('launchid', PARAM_INT);
$courseid  = required_param('course',  PARAM_INT);
$sesskey = required_param('sesskey', PARAM_RAW);

echo $OUTPUT->header();
		
// Check Web Services are enabled
if (!$CFG->enablewebservices) {
    throw new moodle_exception('enablewsdescription', 'webservice');
}

// Check LTI and course
if (! $lti = $DB->get_record('lti', array('id' => $instanceid))) {
    throw new moodle_exception('Wrong LTI params');	
}
if (! $course = $DB->get_record('course', array('id' => $courseid))) {
    throw new moodle_exception('invalidcourseid', 'error');
}
if($lti->course != $courseid) {
	throw new moodle_exception('Wrong course or LTI params.');	
}

// Check userid in course
if (! $lastaccess = $DB->get_record('user_lastaccess', array('userid' => $userid, 'courseid' => $courseid))) {
    throw new moodle_exception('Access not allowed');
}

// Check userid has sesskey
$lastsql = "SELECT * FROM {$CFG->prefix}sessions WHERE userid = {$userid} ORDER BY id DESC LIMIT 1";
if (! $lastsession = $DB->get_record_sql($lastsql)) {	// get user's last session
    throw new moodle_exception('No user sessions in DB');
}
$filename = $CFG->dataroot . "/sessions/sess_" . $lastsession->sid;
$session_raw = file_get_contents($filename);	// read content of the session file
$session_data = sess_unserialize($session_raw);	// unserialize the session file
if ($session_data['USER']->sesskey != $sesskey) {	// check session key
    throw new moodle_exception('Only it is allowed the current session');	
}

// Get user
if (!$user = $DB->get_record('user', array('id' => $userid))) {
    throw new moodle_exception('No user');	
}

// let enrol plugins deal with new enrolments if necessary
enrol_check_plugins($user);

//check if the service exists and is enabled
$service = $DB->get_record('external_services', array('shortname' => $serviceshortname, 'enabled' => 1));
if (empty($service)) {
	// will throw exception if no token found
	throw new moodle_exception('servicenotavailable', 'webservice');
}

//check if there is any required system capability
if ($service->requiredcapability and !has_capability($service->requiredcapability, context_system::instance(), $user)) {
	throw new moodle_exception('missingrequiredcapability', 'webservice', '', $service->requiredcapability);
}

//specific checks related to user restricted service
if ($service->restrictedusers) {
	$authoriseduser = $DB->get_record('external_services_users',
		array('externalserviceid' => $service->id, 'userid' => $user->id));

	if (empty($authoriseduser)) {
		throw new moodle_exception('usernotallowed', 'webservice', '', $serviceshortname);
	}

	if (!empty($authoriseduser->validuntil) and $authoriseduser->validuntil < time()) {
		throw new moodle_exception('invalidtimedtoken', 'webservice');
	}

	if (!empty($authoriseduser->iprestriction) and !address_in_subnet(getremoteaddr(), $authoriseduser->iprestriction)) {
		throw new moodle_exception('invalidiptoken', 'webservice');
	}
}

//Check if a token has already been created for this user and this service
//Note: this could be an admin created or an user created token.
//      It does not really matter we take the first one that is valid.
$tokenssql = "SELECT t.id, t.sid, t.token, t.validuntil, t.iprestriction
		  FROM {external_tokens} t
		 WHERE t.userid = ? AND t.externalserviceid = ? AND t.tokentype = ?
	  ORDER BY t.timecreated ASC";
$tokens = $DB->get_records_sql($tokenssql, array($user->id, $service->id, EXTERNAL_TOKEN_PERMANENT));

//A bit of sanity checks
foreach ($tokens as $key=>$token) {

	/// Checks related to a specific token. (script execution continue)
	$unsettoken = false;
	//if sid is set then there must be a valid associated session no matter the token type
	if (!empty($token->sid)) {
		if (!\core\session\manager::session_exists($token->sid)){
			//this token will never be valid anymore, delete it
			$DB->delete_records('external_tokens', array('sid'=>$token->sid));
			$unsettoken = true;
		}
	}

	//remove token if no valid anymore
	//Also delete this wrong token (similar logic to the web service servers
	//    /webservice/lib.php/webservice_server::authenticate_by_token())
	if (!empty($token->validuntil) and $token->validuntil < time()) {
		$DB->delete_records('external_tokens', array('token'=>$token->token, 'tokentype'=> EXTERNAL_TOKEN_PERMANENT));
		$unsettoken = true;
	}

	// remove token if its ip not in whitelist
	if (isset($token->iprestriction) and !address_in_subnet(getremoteaddr(), $token->iprestriction)) {
		$unsettoken = true;
	}

	if ($unsettoken) {
		unset($tokens[$key]);
	}
}

// if some valid tokens exist then use the most recent
if (count($tokens) > 0) {
	$token = array_pop($tokens);
} else {
	//Note: automatically token generation is not available to admin (they must create a token manually)
	if (!is_siteadmin($user) && has_capability('moodle/webservice:createtoken', context_system::instance())) {
		// if service doesn't exist, dml will throw exception
		$service_record = $DB->get_record('external_services', array('shortname'=>$serviceshortname, 'enabled'=>1), '*', MUST_EXIST);

		// Create a new token.
		$token = new stdClass;
		$token->token = md5(uniqid(rand(), 1));
		$token->userid = $user->id;
		$token->tokentype = EXTERNAL_TOKEN_PERMANENT;
		$token->contextid = context_system::instance()->id;
		$token->creatorid = $user->id;
		$token->timecreated = time();
		$token->externalserviceid = $service_record->id;
		// MDL-43119 Token valid for 3 months (12 weeks).
		$token->validuntil = $token->timecreated + 12 * WEEKSECS;
		$token->id = $DB->insert_record('external_tokens', $token);

		$params = array(
			'objectid' => $token->id,
			'relateduserid' => $user->id,
			'other' => array(
				'auto' => true
			)
		);
		$event = \core\event\webservice_token_created::create($params);
		$event->add_record_snapshot('external_tokens', $token);
		$event->trigger();
	} else {
		throw new moodle_exception('cannotcreatetoken', 'webservice', '', $serviceshortname);
	}
}

// log token access
$DB->set_field('external_tokens', 'lastaccess', time(), array('id'=>$token->id));

$params = array(
	'objectid' => $token->id,
);
$event = \core\event\webservice_token_sent::create($params);
$event->add_record_snapshot('external_tokens', $token);
$event->trigger();

$usertoken = new stdClass;
$usertoken->token = $token->token;
echo json_encode($usertoken);
