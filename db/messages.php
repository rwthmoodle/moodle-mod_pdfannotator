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
 * @copyright 2018 RWTH Aachen (see README.md)
 * @author   Rabea de Groot, Anna Heynkes and Friederike Schwager
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 */
defined('MOODLE_INTERNAL') || die();

$messageproviders = [

    'newquestion' => [
        'capability'  => 'mod/pdfannotator:recievenewquestionnotifications', // All capabilities.
        'defaults' => [
            'popup' => MESSAGE_PERMITTED + MESSAGE_DEFAULT_ENABLED,
            'email' => MESSAGE_PERMITTED,
        ],
    ],

    // Concerns answers to questions the student subscribed to.
    'newanswer' => [
        'capability'  => 'mod/pdfannotator:viewanswers', // Student capability.
    ],

    // Notify teacher about a newly reported comment.
    'newreport' => [
        'capability'  => 'mod/pdfannotator:viewreports', // Teacher capability.
    ],

    // Notify when receiving a forwarded question.
    'forwardedquestion' => [
        'capability'  => 'mod/pdfannotator:getforwardedquestions', // Teacher capability.
    ],

];
