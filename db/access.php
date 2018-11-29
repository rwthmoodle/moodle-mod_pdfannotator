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
 * @copyright 2018 RWTH Aachen, Rabea de Groot and Anna Heynkes(see README.md)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

$capabilities = array(
    'mod/pdfannotator:view' => array( // The following archetypes are recommended for the view capability (to maintain consistancy).
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
            'teacher' => CAP_ALLOW,
            'editingteacher' => CAP_ALLOW,
            'manager' => CAP_ALLOW
        ),
        'clonepermissionsfrom' => 'moodle/course:manageactivities'
    ),

    'mod/pdfannotator:administrateuserinput' => array (
        'captype' => 'write',
        'contextlevel' => CONTEXT_MODULE, // ?
        'archetypes' => array(
            'teacher' => CAP_ALLOW,
            'editingteacher' => CAP_ALLOW,
            'manager' => CAP_ALLOW
        ),
    ),

    // Student capability.
    'mod/pdfannotator:submit' => array (
        'captype' => 'write',
        'contextlevel' => CONTEXT_MODULE, // ?
        'archetypes' => array(
            'student' => CAP_ALLOW,
        ),
    ),

    'mod/pdfannotator:create' => array ( // Create annotation or comment.
        'captype' => 'write',
        'contextlevel' => CONTEXT_MODULE, // ?
        'archetypes' => array(
            'student' => CAP_ALLOW,
            'teacher' => CAP_ALLOW,
            'editingteacher' => CAP_ALLOW,
            'manager' => CAP_ALLOW
        ),
    ),

    'mod/pdfannotator:delete' => array ( // Delete annotation or comment.
        'captype' => 'write',
        'contextlevel' => CONTEXT_MODULE, // ?
        'archetypes' => array(
            'student' => CAP_ALLOW,
            'teacher' => CAP_ALLOW,
            'editingteacher' => CAP_ALLOW,
            'manager' => CAP_ALLOW
        ),
    ),

    'mod/pdfannotator:edit' => array ( // Update/Edit an annotation or comment.
        'captype' => 'write',
        'contextlevel' => CONTEXT_MODULE, // ?
        'archetypes' => array(
            'student' => CAP_ALLOW,
            'teacher' => CAP_ALLOW,
            'editingteacher' => CAP_ALLOW,
            'manager' => CAP_ALLOW
        ),
    ),

    'mod/pdfannotator:editanypost' => array(
        'riskbitmask' => RISK_SPAM,
        'captype' => 'write',
        'contextlevel' => CONTEXT_MODULE,
        'archetypes' => array(
            'teacher' => CAP_ALLOW,
            'editingteacher' => CAP_ALLOW,
            'manager' => CAP_ALLOW
        )
    ),

    'mod/pdfannotator:vote' => array ( // Give an interesting question or a helpful comment your vote.
        'captype' => 'write',
        'contextlevel' => CONTEXT_MODULE, // ?
        'archetypes' => array(
            'student' => CAP_ALLOW,
            'teacher' => CAP_ALLOW,
            'editingteacher' => CAP_ALLOW,
            'manager' => CAP_ALLOW
        ),
    ),

    'mod/pdfannotator:subscribe' => array ( // Subscribe to a question for notifications about new answers.
        'captype' => 'write',
        'contextlevel' => CONTEXT_MODULE, // ?
        'archetypes' => array(
            'student' => CAP_ALLOW,
            'teacher' => CAP_ALLOW,
            'editingteacher' => CAP_ALLOW,
            'manager' => CAP_ALLOW
        ),
    ),

    'mod/pdfannotator:printdocument' => array ( // Download the pdf document.
        'captype' => 'write',
        'contextlevel' => CONTEXT_MODULE, // ?
        'archetypes' => array(
            'student' => CAP_ALLOW,
            'teacher' => CAP_ALLOW,
            'editingteacher' => CAP_ALLOW,
            'manager' => CAP_ALLOW
        ),
    ),

    'mod/pdfannotator:printcomments' => array ( // Download a pdf with all comments in this document.
        'captype' => 'write',
        'contextlevel' => CONTEXT_MODULE, // ?
        'archetypes' => array(
            'student' => CAP_ALLOW,
            'teacher' => CAP_ALLOW,
            'editingteacher' => CAP_ALLOW,
            'manager' => CAP_ALLOW
        ),
    ),

    // ********************** capabilities for viewing the overview page **********************

    // View reports of comments.
    'mod/pdfannotator:viewreports' => array (
        'captype' => 'write',
        'contextlevel' => CONTEXT_MODULE,
        'archetypes' => array(
            'manager' => CAP_ALLOW,
            'editingteacher' => CAP_ALLOW,
            'teacher' => CAP_ALLOW,
        ),
    ),

    // View answers to questions you wrote or subscribed to.
    'mod/pdfannotator:viewanswers' => array (
        'captype' => 'write',
        'contextlevel' => CONTEXT_MODULE,
        'archetypes' => array(
            'student' => CAP_ALLOW,
        ),
    ),

    // View all questions that are new in this course.
    'mod/pdfannotator:viewquestions' => array (
        'captype' => 'write',
        'contextlevel' => CONTEXT_MODULE,
        'archetypes' => array(
            'student' => CAP_ALLOW,
            'manager' => CAP_ALLOW,
            'editingteacher' => CAP_ALLOW,
            'teacher' => CAP_ALLOW,
        ),
    ),

    // View all self-written posts, be it questions or comments.
    'mod/pdfannotator:viewposts' => array (
        'captype' => 'write',
        'contextlevel' => CONTEXT_MODULE,
        'archetypes' => array(
            'student' => CAP_ALLOW,
            'manager' => CAP_ALLOW,
            'editingteacher' => CAP_ALLOW,
            'teacher' => CAP_ALLOW,
        ),
    ),

    // Get a notification about new questions.
    'mod/pdfannotator:recievenewquestionnotifications' => array (
        'captype' => 'read',
        'contextlevel' => CONTEXT_MODULE, // ?
        'archetypes' => array(
            'student' => CAP_ALLOW,
            'editingteacher' => CAP_ALLOW,
            'teacher' => CAP_ALLOW,
            'manager' => CAP_ALLOW
        ),
    ),

);