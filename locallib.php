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
 * Internal library of functions for module lrsproxy
 *
 * All the lrsproxy specific functions, needed to implement the module
 * logic, should go here. Never include this file from your lib.php!
 *
 * @package 	local
 * @subpackage  lrsproxy
 * @copyright 	2016, Felix J. Garcia <fgarcia@um.es>
 *					  Luis de la Torre Cubillo <ldelatorre@dia.uned.es>
 * @license   	http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
 
defined('MOODLE_INTERNAL') || die();

// TinCanPHP - required for interacting with the LRS
require_once($CFG->dirroot. "/local/lrsproxy/TinCanPHP/autoload.php");

/**
 * Store a statement 
 *
 * @return Statement ID
 */
function tincan_store_statement($element) {
	// see: https://rusticisoftware.github.io/TinCanPHP/
	$statement = \TinCan\Statement::fromJSON($element);
	
	$lrs = get_remote_lrs();
	$response = $lrs->saveStatement($statement);
	
	if(($response instanceof \TinCan\LRSResponse) && ($response->success) && ($response->content instanceof \TinCan\Statement)) 
		return $response->content->getId();
	return -1;
}

/**
 * Retrieve a statement
 *
 * @return Statement
 */
function tincan_retrieve_statement($statementid) {
	$lrs = get_remote_lrs();
	$response = $lrs->retrieveStatement($statementid);

	// Get all statements
	if(($response instanceof \TinCan\LRSResponse) && ($response->success) && ($response->content instanceof \TinCan\Statement))
		return json_encode($response->content->asVersion(''));
	return null;
}

/**
 * Query statements 
 *
 * @return Statements
 */
function tincan_fetch_statements($registration, $agent, $verb, $activity, $since, $until) {
	// Build the query
	$query = array(
		// "related_activities" => "true",
		// "limit" => 1,
		"format"=>"canonical",
		"attachments"=>"false",
		'headers' => array(
			'Accept-language: ' => $_SERVER['HTTP_ACCEPT_LANGUAGE'] . ', *'
		)
	);
	if(!is_null($agent)) $query['agent'] = \TinCan\Agent::fromJSON($agent);
	if(!is_null($verb)) $query['verb'] = \TinCan\Verb::fromJSON($verb);
	if(!is_null($activity)) $query['activity'] = \TinCan\Activity::fromJSON($activity);
	if(!is_null($since)) $query['since'] = $since;
	if(!is_null($until)) $query['until'] = $until;
	
	// Do the query
	$lrs = get_remote_lrs();
	$response = $lrs->queryStatements($query);
	
	// Get all statements
	if(($response instanceof \TinCan\LRSResponse) && ($response->success) && ($response->content instanceof \TinCan\StatementsResult)) {
		$queryStatements = $response->content->getStatements();
		$moreStatementsURL = $response->content->getMore();
		while (!is_null($moreStatementsURL)) {
			$moreStmtsResponse = $this->moreStatements($moreStatementsURL);
			$moreStatements = $moreStmtsResponse->content->getStatements();
			$moreStatementsURL = $moreStmtsResponse->content->getMore();
			// Note: due to the structure of the arrays, array_merge does not work as expected.
			foreach ($moreStatements as $moreStatement) {
				array_push($queryStatements, $moreStatement);
			}
		}
		if(!is_null($queryStatements)) {
			$jsonstms = array_map(function($stm) use ($registration){
				$context = $stm->getContext();
				// Filter statements using registration
				if(is_null($registration) || (($context instanceof \TinCan\Context) && ($context->getRegistration() == $registration)))
					return $stm->asVersion(''); 					
			}, $queryStatements);
			return json_encode(array_filter($jsonstms, function($var){return !is_null($var);}));
		}
	}	
	return null;
}

/**
 * Store a statement 
 *
 * @return Statement ID
 */
function tincan_store_activity_state($content, $activityId, $agent, $registration, $stateId) {
	$agent = \TinCan\Agent::fromJSON($agent);
	$activity = new \TinCan\Activity(['id' => $activityId]);
	
	$lrs = get_remote_lrs();
	$response = $lrs->saveState($activity, $agent, $stateId, $content);
	
	if(($response instanceof \TinCan\LRSResponse) && ($response->success) && ($response->content instanceof \TinCan\State)) 
		return $response->content->getId();
	return -1;	
}

/**
 * Get Remote LRS 
 *
 * @return Remote LRS
 */
function get_remote_lrs() {
	$url = get_config('lrsproxy', 'endpoint');
    $version = '1.0.1';
    $username = get_config('lrsproxy', 'username');
    $password = get_config('lrsproxy', 'password');	
	$lrs = new \TinCan\RemoteLRS($url, $version, $username, $password);

	return $lrs;
}
