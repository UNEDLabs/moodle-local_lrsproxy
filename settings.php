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
 
defined('MOODLE_INTERNAL') || die;

if ($hassiteconfig) {
    $settings = new admin_settingpage('local_lrsproxy', new lang_string('pluginname', 'local_lrsproxy'));
    $ADMIN->add('localplugins', $settings);

    // Endpoint
    $settings->add(new admin_setting_configtext('lrsproxy/endpoint',
        get_string('endpoint', 'local_lrsproxy'), '',
        'http://your.domain.com/endpoint/location/', PARAM_URL));

    // Username
    $settings->add(new admin_setting_configtext('lrsproxy/username',
        get_string('username', 'local_lrsproxy'), '', 'username', PARAM_TEXT));

	// Key or password
    $settings->add(new admin_setting_configtext('lrsproxy/password',
        get_string('password', 'local_lrsproxy'), '', 'password', PARAM_TEXT));
}
