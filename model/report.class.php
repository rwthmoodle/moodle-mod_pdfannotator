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
 * This class represents user reports of comments deemed inappropriate.
 *
 * @package   mod_pdfannotator
 * @copyright 2018 RWTH Aachen, Rabea de Groot and Anna Heynkes(see README.md)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 */
defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/mod/pdfannotator/locallib.php');

class pdfannotator_report {

    public $id; // Id of entry in mdl_pdfannotator_reports.
    // Attributes concerning the reported comment.
    public $commentid;
    public $course;
    public $pdfannotatorid;
    public $page;
    // Attributes concerning the report(ing user).
    public $message;
    public $userid;
    public $timecreated;

    /**
     * The constructor function takes the db record of a report and
     * returns a report object with precisely the same attributes.
     *
     * @global type $DB
     * @param type $id
     */
    public function __construct($id) {

        // Get db record of this report.
        global $DB;
        $result = $DB->get_record('pdfannotator_reports', array('id' => $id), $fields = '*', $strictness = MUST_EXIST);

        // Set attributes.
        $this->id = $id;

        // ... concerning the reported comment.
        $this->commentid = $result->commentid;
        $this->course = $result->courseid;
        $this->pdfannotatorid = $result->pdfannotatorid;
        $this->page = $result->page;

        // ... concerning the report and reporting user.
        $this->timecreated = $result->timecreated;
        $this->userid = $result->userid; // getName
        $this->message = $result->message;

        return $this;
    }

    /**
     * This function creates a new entry/record in the 'pdfannotator_reports' table
     *
     * @global type $DB
     * @global type $USER
     * @param type $report
     * @param type $commentid
     * @return type
     */
    public static function create($report, $reportedcomment) {

        global $DB;
        global $USER;
    }

}
