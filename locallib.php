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
 * @return TinCan LRS Response
 */
function tincan_store_statement($element) {
	// Get RemoteLRS
	$url = get_config('lrsproxy', 'endpoint');
    $version = '1.0.0';
    $username = get_config('lrsproxy', 'username');
    $password = get_config('lrsproxy', 'password');	
	$lrs = new \TinCan\RemoteLRS($url, $version, $username, $password);

	// see: https://rusticisoftware.github.io/TinCanPHP/
	$statement = \TinCan\Statement::fromJSON($element);
	
	$response = $lrs->saveStatement($statement);
	
	if(($response instanceof \TinCan\LRSResponse) && ($response->success) && ($response->content instanceof \TinCan\Statement)) 
		return $response->content->getId();
	return -1;
}