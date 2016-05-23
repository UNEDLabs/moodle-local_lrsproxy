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
 
defined('MOODLE_INTERNAL') || die();

$string['endpoint'] = 'Endpoint';
$string['password'] = 'Password';
$string['username'] = 'Username';

$string['pluginname'] = 'LRS Proxy';
$string['settings'] = 'General Settings';
$string['pluginadministration'] = 'LRS Proxy administration';
$string['lrsproxy'] = 'LRS Proxy';

// services.php
$string['echo_text'] = 'Return the same text that was set as parameter.';
$string['store_statement'] = 'Return statementId after storing state statement.';
$string['store_statements'] = 'Return list of statementIds after storing state statements.';
$string['retrieve_statement'] = 'Return statement associated with specified statementId.';
$string['fetch_statements'] = 'Return statements associated with specified query.';
$string['store_activity_state'] = 'Return stateId after storing state data.';
$string['retrieve_activity_state'] = 'Return stored state data with specified stateId.';
$string['fetch_activity_states'] = 'Return list of stateIds with specified query.';
$string['delete_activity_state'] = 'Delete state data associated with specified actor and activity.';
$string['clear_activity_states'] = 'Delete state data associated with specified actor and activity.';