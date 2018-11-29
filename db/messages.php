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

    // Notify student that one of her/his questions received a new answer.
    'newanswer' => array (
        'capability'  => 'mod/pdfannotator:viewanswers' // Student capability.
    ),

    // Notify teacher about a newly reported comment.
    'newreport' => array (
        'capability'  => 'mod/pdfannotator:viewreports' // Teacher capability.
    ),

    'newquestion' => array (
        'capability'  => 'mod/pdfannotator:recievenewquestionnotifications' // All capabilities.
    )
);