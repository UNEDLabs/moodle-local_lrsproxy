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
 * Web service local plugin lrsproxy external functions and service definitions.
 *
 * @package     local
 * @subpackage  lrsproxy
 * @copyright   2016, Felix J. Garcia <fgarcia@um.es>
 *					  Luis de la Torre Cubillo <ldelatorre@dia.uned.es>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// We define the web service functions to install
$functions = array(
        'lrsproxy_store_statement' => array(
                'classname'   => 'lrsproxy_external',
                'methodname'  => 'store_statement',
                'description' => 'Return statementId after storing state statement.',
                'type'        => 'write',
        )
);

// We define the services to install as pre-built services. A pre-built service is not editable by administrator
$services = array(
        'LRS Proxy' => array(
                'functions' => array ('lrsproxy_store_statement'),
                'restrictedusers' => 1,
                'enabled' => 0
        )
);