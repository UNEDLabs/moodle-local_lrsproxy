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
require_once($CFG->dirroot. "/local/lrsproxy/locallib.php");

class lrsproxy_external extends external_api {
	
    public static function echo_text_parameters () {
        return new external_function_parameters(
                array(
					'text' => new external_value(PARAM_TEXT, 'Text to echo', VALUE_DEFAULT, '')
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

	// proxy for https://github.com/adlnet/xAPI-Spec/blob/master/xAPI.md#722-poststatements
    public static function store_statement_parameters () {
        return new external_function_parameters(
                array(
					'statement' => new external_value(PARAM_TEXT, 'Statement to store', VALUE_DEFAULT, '')
                )
        );
    }

    public static function store_statement ($statement) {
        global $USER;
		
        // Parameter validation
        $params = self::validate_parameters(self::store_statement_parameters(),
					array('statement' => $statement));

		// Context validation
        $context = get_context_instance(CONTEXT_USER, $USER->id);
        self::validate_context($context);

		// Store statement
		$response = tincan_store_statement($statement);
		
		return $response;
    }

    public static function store_statement_returns () {
        return new external_value(PARAM_TEXT, 'Statement ID of stored statement');
    }

	// proxy for https://github.com/adlnet/xAPI-Spec/blob/master/xAPI.md#722-poststatements
    public static function store_statements_parameters () {
        return new external_function_parameters(
                array(
					'statements' => new external_multiple_structure(
						new external_single_structure(
							array(
								'statement' => new external_value(PARAM_TEXT, 'Statement to store', VALUE_DEFAULT, '')
							)
						)
					)
                )
        );
    }

    public static function store_statements (array $statements) {
        global $USER;
		
        // Parameter validation
        $params = self::validate_parameters(self::store_statements_parameters(),
					array('statements' => $statements));

		// Context validation
        $context = get_context_instance(CONTEXT_USER, $USER->id);
        self::validate_context($context);

		// Store statements
		$response = array();		
		foreach ($params['statements'] as $element) {
			array_push($response, tincan_store_statement(array_values($element)[0]));
		}
		
		return $response;
    }

    public static function store_statements_returns () {
        return new external_multiple_structure(
			new external_value(PARAM_TEXT, 'Statement ID of stored statement')
		);
    }

	// proxy for https://github.com/adlnet/xAPI-Spec/blob/master/xAPI.md#723-getstatements
    public static function fetch_statement_parameters () {
        return new external_function_parameters(		
                array(
                    'registration' => new external_value(PARAM_TEXT, 'Registration ID to fetch', VALUE_DEFAULT, null),
					'statementId' => new external_value(PARAM_TEXT, 'Statement ID to fetch', VALUE_DEFAULT, null),
                    'agent' => new external_value(PARAM_TEXT, 'Agent to fetch', VALUE_DEFAULT, null),
                    'verb' => new external_value(PARAM_TEXT, 'Verb to fetch', VALUE_DEFAULT, null),
                    'activity' => new external_value(PARAM_TEXT, 'Activity to fetch', VALUE_DEFAULT, null),
                    'since' => new external_value(PARAM_TEXT, 'Only Statements stored since the specified timestamp (exclusive) are returned.', VALUE_DEFAULT, null),
                    'until' => new external_value(PARAM_TEXT, 'Only Statements stored at or before the specified timestamp are returned.', VALUE_DEFAULT, null),
                )
        );
    }
	
    public static function fetch_statement ($registration, $statementid, $agent, $verb, $activity, $since, $until) {
        global $USER;
		
        // Parameter validation
        $params = self::validate_parameters(self::fetch_statement_parameters(),
                array('statementId' => $statementid));

		// Context validation
        $context = get_context_instance(CONTEXT_USER, $USER->id);
        self::validate_context($context);

		// TODO
		// $response = tincan_fetch_statements();

		return $registration;
    }
	
    public static function fetch_statement_returns () {
        return new external_value(PARAM_TEXT, 'Statements requested if exists');
    }

	// proxy for https://github.com/adlnet/xAPI-Spec/blob/master/xAPI.md#74-state-api
    public static function store_activity_state_parameters () {
        return new external_function_parameters(
                array(
                    'content' => new external_value(PARAM_TEXT, 'State document to store', VALUE_DEFAULT, ''),
                    'activityId' => new external_value(PARAM_TEXT, 'Activity ID associated with this state'),
                    'agent' => new external_value(PARAM_RAW, 'Agent associated with this state'),
                    'registration' => new external_value(PARAM_TEXT, 'Registration ID associated with this state', VALUE_DEFAULT, null),
                    'stateId' => new external_value(PARAM_TEXT, 'ID for the state, within the given context'),
                )
        );
    }
	
    public static function store_activity_state($content, $activityId, $agent, $registration, $stateId) {
        global $USER;
		
        // Parameter validation
        $params = self::validate_parameters(self::store_activity_state_parameters(),
                array('content' => $content));

		// Context validation
        $context = get_context_instance(CONTEXT_USER, $USER->id);
        self::validate_context($context);

		// TODO
		// $response = tincan_store_activity_state();

		return $content;
	}
	
    public static function store_activity_state_returns() {
        return new external_value(PARAM_TEXT, 'Success or Failure');
    }
	
	// proxy for https://github.com/adlnet/xAPI-Spec/blob/master/xAPI.md#74-state-api
    public static function fetch_activity_state_parameters() {
        return new external_function_parameters(
                array(
                    'activityId' => new external_value(PARAM_TEXT, 'Activity ID associated with state(s)'),
                    'agent' => new external_value(PARAM_RAW, 'Agent associated with state(s)'),
                    'registration' => new external_value(PARAM_TEXT, 'Registration ID associated with state(s)', VALUE_DEFAULT, null),
                    'stateId' => new external_value(PARAM_TEXT, 'ID for the state, within the given context', VALUE_DEFAULT, null),
                    'since' => new external_value(PARAM_TEXT, 'Only states stored since the specified timestamp (exclusive) are returned.', VALUE_DEFAULT, null),
                    'until' => new external_value(PARAM_TEXT, 'Only states stored at or before the specified timestamp are returned.', VALUE_DEFAULT, null),
                )
        );
    }
	
    public static function fetch_activity_state($activityid, $agent, $registration, $stateid, $since, $until) {
        global $USER;
		
        // Parameter validation
        $params = self::validate_parameters(self::fetch_activity_state_parameters(),
                array('activityId' => $activityid));

		// Context validation
        $context = get_context_instance(CONTEXT_USER, $USER->id);
        self::validate_context($context);

		// TODO
		// $response = tincan_fetch_activity_state();

		return $activityId;
    }

    public static function fetch_activity_state_returns() {
        return new external_value(PARAM_TEXT, 'Activity state value');
    }

	// proxy for https://github.com/adlnet/xAPI-Spec/blob/master/xAPI.md#74-state-api
    public static function delete_activity_state_parameters() {
        return new external_function_parameters(
                array(
                    'activityId' => new external_value(PARAM_TEXT, 'Activity ID associated with state(s)'),
                    'agent' => new external_value(PARAM_RAW, 'Agent associated with state(s)'),
                    'registration' => new external_value(PARAM_TEXT, 'Registration ID associated with state(s)', VALUE_DEFAULT, null),
                )
        );
    }
    public static function delete_activity_state($activityid, $agent, $registration) {
        global $USER;
		
        // Parameter validation
        $params = self::validate_parameters(self::delete_activity_state_parameters(),
                array('activityId' => $activityid));

		// Context validation
        $context = get_context_instance(CONTEXT_USER, $USER->id);
        self::validate_context($context);

		// TODO
		// $response = tincan_fetch_activity_state();

		return $activityId;
    }

    public static function delete_activity_state_returns() {
        return new external_value(PARAM_TEXT, 'Empty string');
    }

}