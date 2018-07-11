<?php
/**
 * @package   mod_pdfannotator
 * @copyright 2018 RWTH Aachen (see README.md)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 */
defined('MOODLE_INTERNAL') || die; //Prevents crashes on misconfigured production server.

if ($ADMIN->fulltree) {

// $settings->add(new admin_setting_configcheckbox('mod_pdfannotator/allow_anonymous', get_string('global_setting_anonymous', 'pdfannotator'),
// get_string('global_setting_anonymous_desc', 'pdfannotator'), 1));

    $choices = array(
        1 => '1 ' . get_string('day'),
        2 => '2 ' . get_string('days'),
        3 => '3 ' . get_string('days'),
        7 => '1 ' . get_string('week'),
        14 => '2 ' . get_string('weeks'),
        21 => '3 ' . get_string('weeks'),
        28 => '4 ' . get_string('weeks'),
    );

    $settings->add(new admin_setting_configselect('mod_pdfannotator/newsspan',
            get_string('global_setting_newsspan', 'pdfannotator'), 
            get_string('global_setting_newsspan_desc', 'pdfannotator'), 7, $choices));

    $settings->add(new admin_setting_configcheckbox('mod_pdfannotator/usevotes', get_string('global_setting_usevotes', 'pdfannotator'), get_string('global_setting_usevotes_desc', 'pdfannotator'), 1));

    $settings->add(new admin_setting_configcheckbox('mod_pdfannotator/use_studenttextbox', get_string('global_setting_use_studenttextbox', 'pdfannotator'), get_string('global_setting_use_studenttextbox_desc', 'pdfannotator'), 0));

    $settings->add(new admin_setting_configcheckbox('mod_pdfannotator/use_studentdrawing', get_string('global_setting_use_studentdrawing', 'pdfannotator'), get_string('global_setting_use_studentdrawing_desc', 'pdfannotator'), 0));
}