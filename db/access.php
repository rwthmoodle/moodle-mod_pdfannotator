<?php
/**
 * @package   mod_pdfannotator
 * @copyright 2018 RWTH Aachen, Rabea de Groot and Anna Heynkes(see README.md)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

$capabilities = array(
    'mod/pdfannotator:view' => array( // The following archetypes are recommended for the view capability (to maintain consistancy)
        'captype' => 'read',
        'contextlevel' => CONTEXT_MODULE,
        'archetypes' => array(
            'guest' => CAP_ALLOW,
            'student' => CAP_ALLOW,
            'teacher' => CAP_ALLOW,
            'editingteacher' => CAP_ALLOW,
            'manager' => CAP_ALLOW
        )
    ),
 
    'mod/pdfannotator:addinstance' => array(
        'riskbitmask' => RISK_XSS,
        'captype' => 'write',
        'contextlevel' => CONTEXT_COURSE,
        'archetypes' => array(
            'editingteacher' => CAP_ALLOW,
            'manager' => CAP_ALLOW
        ),
        'clonepermissionsfrom' => 'moodle/course:manageactivities'
    ),

    
    'mod/pdfannotator:administrateuserinput' => array (
        'captype' => 'write',
        'contextlevel' => CONTEXT_MODULE, // ?
        'archetypes' => array(
            'editingteacher' => CAP_ALLOW,
            'manager' => CAP_ALLOW
        ),
    ),
    
    // student capability
    'mod/pdfannotator:submit' => array (
        'captype' => 'write',
        'contextlevel' => CONTEXT_MODULE, // ?
        'archetypes' => array(
            'student' => CAP_ALLOW,
        ),
    ),
    
    // get a notification about new questions
    'mod/pdfannotator:recievenewquestionnotifications' => array (
        'captype' => 'read',
        'contextlevel' => CONTEXT_MODULE, // ?
        'archetypes' => array(
            'student' => CAP_ALLOW,
            'editingteacher' => CAP_ALLOW,
            'manager' => CAP_ALLOW
        ),
    ),
    
);