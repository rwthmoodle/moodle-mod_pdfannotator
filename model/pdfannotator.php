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
 * Getting instances details
 * @package   mod_pdfannotator
 * @copyright 2018 RWTH Aachen (see README.md)
 * @author    Rabea de Groot and Anna Heynkes
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 */
defined('MOODLE_INTERNAL') || die();
require_once($CFG->dirroot . '/mod/pdfannotator/locallib.php');
require_once($CFG->dirroot . '/mod/pdfannotator/renderable.php');

/**
 * This class represents an instance of the pdfannotator module.
 */
class pdfannotator_instance {

    /**
     * @var int
     */
    private $id;
    /**
     * @var cm_info
     */
    private $coursemodule;
    /**
     * @var string
     */
    private $name;
    /**
     * @var array
     */
    private $answers; // Questions asked by the current users.
    /**
     * @var array
     */
    private $unsolvedquestions;
    /**
     * @var array
     */
    private $reports;
    /**
     * @var array
     */
    private $userposts;
    /**
     * @var array
     */
    private $hiddenanswers;
    /**
     * @var array
     */
    private $hiddenreports;

    /**
     * Constructor for the pdfannotator class.
     *
     * @param stdClass $dbrecord
     */
    public function __construct($dbrecord) {
        $this->id = $dbrecord->id;
        $this->coursemodule = $dbrecord->coursemodule;
        $this->name = $dbrecord->name;
        $this->answers = [];
        $this->reports = [];
        $this->unsolvedquestions = [];
        $this->userposts = [];
        $this->hiddenanswers = [];
        $this->hiddenreports = [];
    }

    /*     * **************************** static methods ***************************** */

    /**
     * This method returns an array containing one pdfannotator_instance object
     * for each annotator in the specified course.
     *
     * @param int $courseid
     * @param int $beginwith optional parameter that specifies the (current) pdfannotator that should come first in the list
     * @return \pdfannotator_instance: array of pdfannotator_instance objects
     */
    public static function get_pdfannotator_instances($courseid, $beginwith = null) {

        global $DB;

        $course = get_course($courseid);
        $result = get_all_instances_in_course('pdfannotator', $course);

        $pdfannotatorlist = [];

        foreach ($result as $pdfannotator) {
            $pdfannotatorlist[] = new pdfannotator_instance($pdfannotator);
        }

        if ($beginwith) {
            foreach ($pdfannotatorlist as $index => $annotator) {
                if ($annotator->get_id() == $beginwith && $index != 0) {
                    $temp = $pdfannotatorlist[0];
                    $pdfannotatorlist[0] = $annotator;
                    $pdfannotatorlist[$index] = $temp;
                    break;
                }
            }
        }

        return $pdfannotatorlist;
    }

    /**
     * Returns an array containing information about the course modules
     *
     * @param int $courseid
     * @return array
     * @throws dml_exception
     * @throws moodle_exception
     */
    public static function get_cm_info($courseid) {
        global $USER;
        $info = [];

        $userid = $USER->id;
        $course = get_course($courseid);
        $instances = get_all_instances_in_course('pdfannotator', $course, $userid);
        $modinfo = get_fast_modinfo($course);

        foreach ($instances as $instance) {
            $cmid = $instance->coursemodule;
            $cm = $modinfo->get_cm($cmid);
            $cminfo = [];
            $cminfo['visible'] = $cm->visible;
            $cminfo['availableinfo'] = $cm->availableinfo;
            $info[$cmid] = $cminfo;
        }
        return $info;
    }

    /**
     * Checks if the pdfannotator instance with the given document ID uses votes.
     *
     * @param int $documentid
     * @return bool
     * @throws dml_exception
     */
    public static function use_votes($documentid) {
        global $DB;
        return $DB->record_exists('pdfannotator', ['id' => $documentid, 'usevotes' => '1']);
    }

    /*     * **************************** (attribute) getter methods ***************************** */

    /**
     * Returns the ID of the pdfannotator instance.
     *
     * @return mixed
     */
    public function get_id() {
        return $this->id;
    }

    /**
     * Returns the name of the pdfannotator instance.
     *
     * @return mixed
     */
    public function get_name() {
        return $this->name;
    }

    /**
     * Returns the conversations of the pdfannotator instance.
     *
     * @param int $pdfannotatorid
     * @param context $context
     * @return array|int
     * @throws coding_exception
     * @throws dml_exception
     */
    public static function get_conversations($pdfannotatorid, $context) {

        global $DB;

        $sql = "SELECT q.id, q.content AS answeredquestion, q.timemodified, q.userid, q.visibility,"
                . " a.id AS annoid, a.page, a.annotationtypeid, q.isquestion "
                . "FROM {pdfannotator_annotations} a "
                . "JOIN {pdfannotator_comments} q ON q.annotationid = a.id "
                . "WHERE q.isquestion = 1 AND a.pdfannotatorid = ? AND NOT q.isdeleted = 1 "
                . "ORDER BY a.page ASC";

        try {
            $questions = $DB->get_records_sql($sql, [$pdfannotatorid]);
        } catch (Exception $ex) {
            return -1;
        }

        $res = [];

        foreach ($questions as $question) {

            if (!pdfannotator_can_see_comment($question, $context)) {
                continue;
            }

            $question->answeredquestion = html_entity_decode($question->answeredquestion);
            $question->timemodified = pdfannotator_get_user_datetime($question->timemodified);
            $question->answeredquestion = pdfannotator_get_relativelink($question->answeredquestion, $question->id, $context);
            if ($question->visibility === 'anonymous') {
                $question->author = get_string('anonymous', 'pdfannotator');
            } else {
                $question->author = pdfannotator_get_username($question->userid);
            }

            $sql = "SELECT c.id, c.content AS answer, c.userid, c.timemodified, c.visibility FROM {pdfannotator_comments} c "
                . "WHERE c.pdfannotatorid = ? AND c.annotationid = ? AND NOT c.isquestion = 1 AND NOT c.isdeleted = 1";

            try {
                $answers = $DB->get_records_sql($sql, [$pdfannotatorid, $question->annoid]);
            } catch (Exception $ex) {
                return -1;
            }

            foreach ($answers as $answer) {
                $answer->answer = pdfannotator_get_relativelink($answer->answer, $answer->id, $context);
                $answer->answer = html_entity_decode($answer->answer);
                $answer->timemodified = pdfannotator_get_user_datetime($answer->timemodified);
                if ($answer->visibility === 'anonymous') {
                    $answer->author = get_string('anonymous', 'pdfannotator');
                } else {
                    $answer->author = pdfannotator_get_username($answer->userid);
                }
                unset($answer->visibility);
                unset($answer->userid);
            }
            unset($question->visibility);
            unset($question->userid);
            unset($question->annoid);

            $question->answers = $answers;
            $res[] = $question;
        }
        return $res;
    }
}
