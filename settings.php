<?php


defined('MOODLE_INTERNAL') || die; //Prevents crashes on misconfigured production server.

//require_once($CFG->dirroot . '/mod/evoting/lib.php'); //include required libraries

if ($ADMIN->fulltree) {
    /*
    $settings->add(new admin_setting_configcheckbox('mod_pdfannotator/allow_anonymous', get_string('global_setting_anonymous', 'pdfannotator'),
        get_string('global_setting_anonymous_desc', 'pdfannotator'), 1));
     * 
     */
}