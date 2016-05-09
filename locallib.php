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
require_once("$CFG->dirroot/local/lrsproxy/TinCanPHP/autoload.php");

/**
 * Send a statement 
 *
 * @return TinCan LRS Response
 */
function tincan_store_statement() {

	$url = $this->get_config('endpoint', '');
    $version = '1.0.1';
    $username = $this->get_config('username', '');
    $password = $this->get_config('password', '');	

	$lrs = new \TinCan\RemoteLRS($url, $version, $username, $password);
	
	$statement = new \TinCan\Statement(
		array(
			'actor' => array(
				'mbox' => 'mailto:fgarcia@um.es'
			),
			'verb' => array(
				'id' => 'http://unilabs.dia.uned.es/xapi/verbs/action',
				'display' => array(
					'en-US' => 'action'
				)
			),
			'object' => array(
				'id' =>  "http://unilabs.dia.uned.es/xapi/example/CPULoad",
				'objectType' => "Activity"
			),
			"timestamp" => date(DATE_ATOM)
		)
	);
	
	$response = $lrs->saveStatement($statement);
	return $response;
}