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
 * Proxy xAPI protocol
 *
 * @package     local
 * @subpackage  lrsproxy
 * @copyright   2016, Felix J. Garcia <fgarcia@um.es>
 *					  Luis de la Torre Cubillo <ldelatorre@dia.uned.es>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
 
require_once($CFG->libdir . "/externallib.php");

class lrsproxy_external extends external_api {

    public static function echo_text_parameters () {
        return new external_function_parameters(
                array(
					'text' => new external_value(PARAM_TEXT, 'Text to echo', VALUE_DEFAULT, 'Hello!')
                )
        );
    }

    public static function echo_text ($text) {
        global $USER;
		
        // Parameter validation
        $params = self::validate_parameters(self::echo_text_parameters(),
                array('text' => $text));

		// Context validation
        $context = get_context_instance(CONTEXT_USER, $USER->id);
        self::validate_context($context);

        // Capability checking (OPTIONAL but in most web service it should present)
        // if (!has_capability('moodle/user:viewdetails', $context)) {
        //    throw new moodle_exception('cannotviewprofile');
        // }
		
        return $text;
    }

    public static function echo_text_returns () {
        return new external_value(PARAM_TEXT, 'Echo text');
    }


    public static function store_statement_parameters () {
        return new external_function_parameters(
                array(
					'content' => new external_value(PARAM_TEXT, 'Statement to store', VALUE_DEFAULT, '')
                )
        );
    }

    public static function store_statement ($content) {
        global $USER;
		
        // Parameter validation
        $params = self::validate_parameters(self::store_statement_parameters(),
                array('content' => $content));

		// Context validation
        $context = get_context_instance(CONTEXT_USER, $USER->id);
        self::validate_context($context);

		// TODO
		// $response = tincan_store_statement();
		
        return $content;
    }

    public static function store_statement_returns () {
        return new external_value(PARAM_TEXT, 'Statement ID of stored statement');
    }

}