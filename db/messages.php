<?php
/**
 * The pdfannotator plugin is registered as a message provider and the messages
 * produced are defined.
 * 
 * @package   mod_pdfannotator
 * @copyright 2018 RWTH Aachen, Rabea de Groot and Anna Heynkes(see README.md)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * 
 */
defined('MOODLE_INTERNAL') || die();

$messageproviders = array (
    
    // Notify student that one of her/his questions received a new answer
    'newanswer' => array (
        'capability'  => 'mod/pdfannotator:submit' // student capability
    ),
    
    // Notify teacher about a newly reported comment
    'newreport' => array (
        'capability'  => 'mod/pdfannotator:administrateuserinput' // teacher capability
    ),
    
    
    'pdfannotator_feedback' => array (
        'capability'  => 'mod/pdfannotator:submit', // student capability
        'capability'  => 'mod/pdfannotator:administrateuserinput' // teacher capability
    ),
    
    'newquestion' => array (
        'capability'  => 'mod/pdfannotator:recievenewquestionnotifications' // all capability
    )
);