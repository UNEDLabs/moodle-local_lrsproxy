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

function sess_unserialize($session_data) {
	$method = ini_get("session.serialize_handler");
	switch ($method) {
		case "php":
			return sess_unserialize_php($session_data);
			break;
		case "php_binary":
			return sess_unserialize_phpbinary($session_data);
			break;
		default:
			throw new moodle_exception("Unsupported session.serialize_handler: " . $method . ". Supported: php, php_binary");
	}
}

function sess_unserialize_php($session_data) {
	$return_data = array();
	$offset = 0;
	while ($offset < strlen($session_data)) {
		if (!strstr(substr($session_data, $offset), "|")) {
			throw new moodle_exception("Session unserializing, invalid data, remaining: " . substr($session_data, $offset));
		}
		$pos = strpos($session_data, "|", $offset);
		$num = $pos - $offset;
		$varname = substr($session_data, $offset, $num);
		$offset += $num + 1;
		$data = unserialize(substr($session_data, $offset));
		$return_data[$varname] = $data;
		$offset += strlen(serialize($data));
	}
	return $return_data;
}

function sess_unserialize_phpbinary($session_data) {
	$return_data = array();
	$offset = 0;
	while ($offset < strlen($session_data)) {
		$num = ord($session_data[$offset]);
		$offset += 1;
		$varname = substr($session_data, $offset, $num);
		$offset += $num;
		$data = unserialize(substr($session_data, $offset));
		$return_data[$varname] = $data;
		$offset += strlen(serialize($data));
	}
	return $return_data;
}
?>
