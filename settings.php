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
 * @package   mod_pdfannotator
 * @copyright 2018 RWTH Aachen (see README.md)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 */
defined('MOODLE_INTERNAL') || die; // Prevents crashes on misconfigured production server.

if ($ADMIN->fulltree) {

    $settings->add(new admin_setting_configcheckbox('mod_pdfannotator/usevotes',
            get_string('global_setting_usevotes', 'pdfannotator'), get_string('global_setting_usevotes_desc', 'pdfannotator'), 1));

    $settings->add(new admin_setting_configcheckbox('mod_pdfannotator/useprint',
            get_string('global_setting_useprint', 'pdfannotator'), get_string('global_setting_useprint_desc', 'pdfannotator'), 0));

    $settings->add(new admin_setting_configcheckbox('mod_pdfannotator/useprintcomments',
            get_string('global_setting_useprint_comments', 'pdfannotator'), get_string('global_setting_useprint_comments_desc', 'pdfannotator'), 0));

    $settings->add(new admin_setting_configcheckbox('mod_pdfannotator/use_studenttextbox',
            get_string('global_setting_use_studenttextbox', 'pdfannotator'),
            get_string('global_setting_use_studenttextbox_desc', 'pdfannotator'), 0));

    $settings->add(new admin_setting_configcheckbox('mod_pdfannotator/use_studentdrawing',
            get_string('global_setting_use_studentdrawing', 'pdfannotator'),
            get_string('global_setting_use_studentdrawing_desc', 'pdfannotator'), 0));
}