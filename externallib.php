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
        $context = context_user::instance($USER->id);
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
					'statement' => new external_value(PARAM_RAW, 'Statement to store')
                )
        );
    }

    public static function store_statement ($statement) {
        global $USER;
		
        // Parameter validation
        $params = self::validate_parameters(self::store_statement_parameters(),
					array('statement' => $statement));

		// Context validation
        $context = context_user::instance($USER->id);
        self::validate_context($context);

		// Store statement
		$response = tincan_store_statement($statement);
		
		return $response;
    }

    public static function store_statement_returns () {
        return new external_value(PARAM_RAW, 'Statement ID of stored statement');
    }

	// proxy for https://github.com/adlnet/xAPI-Spec/blob/master/xAPI.md#722-poststatements
    public static function store_statements_parameters () {
        return new external_function_parameters(
                array(
					'statements' => new external_multiple_structure(
						new external_single_structure(
							array(
								'statement' => new external_value(PARAM_RAW, 'Statement to store')
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
        $context = context_user::instance($USER->id);
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
			new external_value(PARAM_RAW, 'Statement ID of stored statement')
		);
    }

	// proxy for https://github.com/adlnet/xAPI-Spec/blob/master/xAPI.md#723-getstatements
    public static function retrieve_statement_parameters () {
        return new external_function_parameters(		
                array(
					'statementId' => new external_value(PARAM_RAW, 'Statement ID to retrieve'),
                )
        );
    }
	
    public static function retrieve_statement ($statementid) {
        global $USER;
		
        // Parameter validation
        $params = self::validate_parameters(self::retrieve_statement_parameters(),
                array('statementId' => $statementid));

		// Context validation
        $context = context_user::instance($USER->id);
        self::validate_context($context);

		// Query statements
		$response = tincan_retrieve_statement($statementid);

		return $response;
    }
	
    public static function retrieve_statement_returns () {
        return new external_value(PARAM_RAW, 'Statement requested if exists');
    }

	// proxy for https://github.com/adlnet/xAPI-Spec/blob/master/xAPI.md#723-getstatements
    public static function fetch_statements_parameters () {
        return new external_function_parameters(		
                array(
                    'registration' => new external_value(PARAM_RAW, 'Registration ID to fetch', VALUE_DEFAULT, null),
                    'agent' => new external_value(PARAM_RAW, 'Agent to fetch', VALUE_DEFAULT, null),
                    'verb' => new external_value(PARAM_RAW, 'Verb to fetch', VALUE_DEFAULT, null),
                    'activity' => new external_value(PARAM_RAW, 'Activity to fetch', VALUE_DEFAULT, null),
                    'since' => new external_value(PARAM_RAW, 'Only Statements stored since the specified timestamp (exclusive) are returned.', VALUE_DEFAULT, null),
                    'until' => new external_value(PARAM_RAW, 'Only Statements stored at or before the specified timestamp are returned.', VALUE_DEFAULT, null),
                )
        );
    }
	
    public static function fetch_statements ($registration, $agent, $verb, $activity, $since, $until) {
        global $USER;
		
        // Parameter validation
        $params = self::validate_parameters(self::fetch_statements_parameters(), array(
					'registration' => $registration, 
					'agent' => $agent, 
					'verb' => $verb, 
					'activity' => $activity, 
					'since' => $since, 
					'until' => $until));

		// Context validation
        $context = context_user::instance($USER->id);
        self::validate_context($context);

		// Query statements
		$response = tincan_fetch_statements($registration, $agent, $verb, $activity, $since, $until);

		return $response;
    }
	
    public static function fetch_statements_returns () {
        return new external_value(PARAM_RAW, 'Statements requested if exists');
    }

	// proxy for https://github.com/adlnet/xAPI-Spec/blob/master/xAPI.md#74-state-api
    public static function store_activity_state_parameters () {
        return new external_function_parameters(
                array(
                    'content' => new external_value(PARAM_RAW, 'State document to store'),
                    'activityId' => new external_value(PARAM_RAW, 'Activity ID associated with this state'),
                    'agent' => new external_value(PARAM_RAW, 'Agent associated with this state'),
                    'registration' => new external_value(PARAM_RAW, 'Registration ID associated with this state', VALUE_DEFAULT, null),
                    'stateId' => new external_value(PARAM_RAW, 'ID for the state, within the given context'),
                )
        );
    }
	
    public static function store_activity_state($content, $activityId, $agent, $registration, $stateId) {
        global $USER;
		
        // Parameter validation
        $params = self::validate_parameters(self::store_activity_state_parameters(), array(
					'content' => $content, 
					'activityId' => $activityId, 
					'agent' => $agent, 
					'registration' => $registration, 
					'stateId' => $stateId));

		// Context validation
        $context = context_user::instance($USER->id);
        self::validate_context($context);

		// Store activity state
		$response = tincan_store_activity_state($content, $activityId, $agent, $registration, $stateId);

		return $response;
	}
	
    public static function store_activity_state_returns() {
        return new external_value(PARAM_RAW, 'State ID of stored state data');
    }
	
	// proxy for https://github.com/adlnet/xAPI-Spec/blob/master/xAPI.md#74-state-api
    public static function retrieve_activity_state_parameters() {
        return new external_function_parameters(
                array(
                    'activityId' => new external_value(PARAM_RAW, 'Activity ID associated with this state'),
                    'agent' => new external_value(PARAM_RAW, 'Agent associated with this state'),
                    'stateId' => new external_value(PARAM_RAW, 'ID for the state, within the given context')
                )
        );
    }
	
    public static function retrieve_activity_state($activityId, $agent, $stateId) {
        global $USER;
		
        // Parameter validation
        $params = self::validate_parameters(self::retrieve_activity_state_parameters(), array(
					'activityId' => $activityId, 
					'agent' => $agent, 
					'stateId' => $stateId));

		// Context validation
        $context = context_user::instance($USER->id);
        self::validate_context($context);

		// Retrieve activity state
		$response = tincan_retrieve_activity_state($activityId, $agent, $stateId);

		return $response;
    }

    public static function retrieve_activity_state_returns() {
        return new external_value(PARAM_RAW, 'Activity state value');
    }

	// proxy for https://github.com/adlnet/xAPI-Spec/blob/master/xAPI.md#74-state-api
    public static function fetch_activity_states_parameters() {
        return new external_function_parameters(
                array(
                    'activityId' => new external_value(PARAM_RAW, 'Activity ID associated with state(s)'),
                    'agent' => new external_value(PARAM_RAW, 'Agent associated with state(s)')
                )
        );
    }
	
    public static function fetch_activity_states($activityId, $agent) {
        global $USER;
		
        // Parameter validation
        $params = self::validate_parameters(self::fetch_activity_states_parameters(), array(
					'activityId' => $activityId, 
					'agent' => $agent));

		// Context validation
        $context = context_user::instance($USER->id);
        self::validate_context($context);

		// Fetch states
		$response = tincan_fetch_activity_states($activityId, $agent);

		return $response;
    }

    public static function fetch_activity_states_returns() {
        return new external_multiple_structure(
			new external_value(PARAM_RAW, 'Activity state ID of stored state')
		);
    }

	// proxy for https://github.com/adlnet/xAPI-Spec/blob/master/xAPI.md#74-state-api
    public static function delete_activity_state_parameters() {
        return new external_function_parameters(
                array(
                    'activityId' => new external_value(PARAM_RAW, 'Activity ID associated with this state'),
                    'agent' => new external_value(PARAM_RAW, 'Agent associated with this state'),
                    'stateId' => new external_value(PARAM_RAW, 'ID for the state, within the given context')
				)
        );
    }
    public static function delete_activity_state($activityId, $agent, $stateId) {
        global $USER;
		
        // Parameter validation
        $params = self::validate_parameters(self::delete_activity_state_parameters(), array(
					'activityId' => $activityId, 
					'agent' => $agent, 
					'stateId' => $stateId));

		// Context validation
        $context = context_user::instance($USER->id);
        self::validate_context($context);

		// Delete state
		$response = tincan_delete_activity_state($activityId, $agent, $stateId);

		return $response;
    }

    public static function delete_activity_state_returns() {
        return new external_value(PARAM_BOOL, 'Success or Failure');
    }

	// proxy for https://github.com/adlnet/xAPI-Spec/blob/master/xAPI.md#74-state-api
    public static function clear_activity_states_parameters() {
        return new external_function_parameters(
                array(
                    'activityId' => new external_value(PARAM_RAW, 'Activity ID associated with this state'),
                    'agent' => new external_value(PARAM_RAW, 'Agent associated with this state')
                )
        );
    }
    public static function clear_activity_states($activityId, $agent) {
        global $USER;
		
        // Parameter validation
        $params = self::validate_parameters(self::clear_activity_states_parameters(), array(
					'activityId' => $activityId, 
					'agent' => $agent));

		// Context validation
        $context = context_user::instance($USER->id);
        self::validate_context($context);

		// Clear states
		$response = tincan_clear_activity_states($activityId, $agent);

		return $response;
    }

    public static function clear_activity_states_returns() {
        return new external_value(PARAM_BOOL, 'Success or Failure');
    }

}