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
 *
 */
defined('MOODLE_INTERNAL') || die();
require_once($CFG->dirroot . '/mod/pdfannotator/locallib.php');
require_once($CFG->dirroot . '/mod/pdfannotator/renderable.php');

/**
 * This class represents an instance of the pdfannotator module.
 */
class pdfannotator_instance {

    private $id;
    private $coursemodule;
    private $name;
    private $answers; // Questions asked by the current users
    private $latestquestions;
    private $reports;
    private $hiddenanswers;
    private $hiddenreports;

    public function __construct($dbrecord) {
        $this->id = $dbrecord->id;
        $this->coursemodule = $dbrecord->coursemodule;
        $this->name = $dbrecord->name;
        $this->answers = array();
        $this->reports = array();
        $this->latestquestions = array();
        $this->userposts = array();
        $this->hiddenanswers = array();
        $this->hiddenreports = array();
    }

    /*     * **************************** static methods ***************************** */

    /**
     * This method returns an array containing one pdfannotator_instance object
     * for each annotator in the specified course.
     *
     * @global type $DB
     * @param type $courseid
     * @param type $beginwith optional parameter that specifies the (current) pdfannotator that should come first in the list
     * @return \pdfannotator_instance: array of pdfannotator_instance objects
     */
    public static function get_pdfannotator_instances($courseid, $beginwith = null) {

        global $DB;

        $course = get_course($courseid);
        $result = get_all_instances_in_course('pdfannotator', $course);

        $pdfannotatorlist = array();

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

    public static function use_votes($documentid) {
        global $DB;
        return $DB->record_exists('pdfannotator', array('id' => $documentid, 'usevotes' => '1'));
    }

    public static function useprint($documentid) {
        global $DB;
        return $DB->record_exists('pdfannotator', array('id' => $documentid, 'useprint' => '1'));
    }

    /*     * **************************** (attribute) getter methods ***************************** */

    public function get_id() {
        return $this->id;
    }

    public function get_coursemoduleid() {
        return $this->coursemodule;
    }

    public function get_name() {
        return $this->name;
    }

    public function get_answers_for_me() {
        return $this->answers;
    }

    public function get_reports() {
        return $this->reports;
    }

    public function get_latest_questions() {
        return $this->latestquestions;
    }

    public function get_posts_by_user() {
        return $this->userposts;
    }

    public function get_hidden_reports() {
        return $this->hiddenreports;
    }

    public function get_hidden_answers() {
        return $this->hiddenanswers;
    }

    /*     * **************************** (attribute) setter methods ***************************** */

    /**
     * Function retrieves all answers to questions this user asked or subscribed to in this course.
     * (You automatically subscribe to your own questions.)
     *
     * @global type $DB
     * @global type $USER
     * @return type array of annotation objects
     */
    public function set_answers() {

        global $DB, $USER;

        $sql = "SELECT c.id AS answerid, c.content AS answer, a.id AS annoid, a.page, q.content AS answeredquestion, q.isdeleted AS questiondeleted "
                . "FROM {pdfannotator_subscriptions} s "
                . "JOIN {pdfannotator_annotations} a ON a.id = s.annotationid "
                . "JOIN {pdfannotator_comments} q ON q.annotationid = a.id "
                . "JOIN {pdfannotator_comments} c ON c.annotationid = a.id "
                . "WHERE s.userid = ? AND q.isquestion = 1 AND NOT c.isquestion = 1 AND a.pdfannotatorid = ? AND NOT c.isdeleted = 1 AND NOT c.seen = 1";

        $entries = $DB->get_records_sql($sql, array($USER->id, $this->id));
        foreach ($entries as $entry) {
            $entry->link = (new moodle_url('/mod/pdfannotator/view.php',
                        array('id' => $this->coursemodule, 'page' => $entry->page, 'annoid' => $entry->annoid, 'commid' => $entry->answerid)))->out();            
            if ($entry->questiondeleted == 1) {
                $entry->answeredquestion = get_string('deletedComment', 'pdfannotator');
            }
            $this->answers[] = $entry;
        }
    }

    public function set_reports($courseid) {

        global $DB;

        // Retrieve reports from db as an array of stdClass objects, representing a report record each.
        $sql = "SELECT r.id as reportid, r.commentid, r.message as report, a.page, c.annotationid, c.userid AS commentauthor, "
                . "c.content AS reportedcomment, c.timecreated AS commenttime, c.visibility "
                . "FROM {pdfannotator_reports} r JOIN {pdfannotator_comments} c ON r.commentid = c.id "
                . "JOIN {pdfannotator_annotations} a ON c.annotationid = a.id "
                . "WHERE r.courseid = ? AND r.pdfannotatorid = ? AND r.seen = ?";
        $reports = $DB->get_records_sql($sql, array($courseid, $this->id, 0));

        foreach ($reports as $report) {
            $report->link = (new moodle_url('/mod/pdfannotator/view.php',
                        array('id' => $this->coursemodule, 'page' => $report->page, 'annoid' => $report->annotationid, 'commid' => $report->commentid)))->out();            
            $this->reports[] = $report;

            /*
              // optional: who wrote what and when? (->adjust SQL)
              if ($report->visibility === 'anonymous') {
              $report->commentauthor = get_string('anonymous', 'pdfannotator');
              } else { // The try catch might be unnecessary if you tick 'anonymise userinfo' during backup.
              try {
              $report->commentauthor = pdfannotator_get_username($report->commentauthor);
              } catch (Exception $ex) {
              $report->commentauthor = get_string('unknownuser', 'pdfannotator');
              }
              }
              $report->commenttime = userdate($report->commenttime, $format = '', $timezone = 99, $fixday = true, $fixhour = true);

              // optional: who reported it and when? (->adjust SQL)
              $report->userid = pdfannotator_get_username($report->userid);
              $report->timereported = userdate($report->timecreated, $format = '', $timezone = 99, $fixday = true, $fixhour = true);
             */
        }
    }

    /**
     * Method retrieves an array of all questions asked in this annotator during the past week
     * and saves it in the annotator's "lastesquestions" attribute.
     *
     * @global type $DB
     * @param int $timespan number of days
     */
    public function set_latest_questions($timespan) {

        global $DB;

        switch ($timespan) {
            case 1:
                $timestring = "-1 day";
                break;
            case 2:
                $timestring = "-2 days";
                break;
            case 3:
                $timestring = "-3 days";
                break;
            case 7:
                $timestring = "-1 week";
                break;
            case 14:
                $timestring = "-2 weeks";
                break;
            case 21:
                $timestring = "-3 weeks";
                break;
            case 28:
                $timestring = "-4 weeks";
                break;
            default:
                $timestring = "-3 days";
        }

        $sql = "SELECT a.id as annoid, a.page, c.id as commentid, c.content FROM {pdfannotator_annotations} a "
                . "JOIN {pdfannotator_comments} c ON c.annotationid = a.id "
                . "WHERE c.isquestion = 1 AND a.pdfannotatorid = ? AND c.timecreated >= ?";

        $newquestions = $DB->get_records_sql($sql, array($this->id, strtotime($timestring))); // strtotime("-1 week");
        foreach ($newquestions as $question) {           
            $question->link = (new moodle_url('/mod/pdfannotator/view.php', array('id' => $this->coursemodule, 
                'page' => $question->page, 'annoid' => $question->annoid, 'commid' => $question->commentid)))->out();           
            $this->latestquestions[] = $question;
        }
    }

    /**
     * Method retrieves all posts from db that were posted by the specified user
     * in the current pdfannotator
     *
     * @global type $DB
     * @param type $userid
     * @return type
     */
    public function set_posts_by_user($userid) {

        global $DB;

        $sql = "SELECT c.id as commid, c.annotationid, c.content, a.page FROM {pdfannotator_comments} c "
                . "JOIN {pdfannotator_annotations} a ON c.annotationid = a.id WHERE c.userid = ? AND a.pdfannotatorid = ?";
        $userposts = $DB->get_records_sql($sql, array($userid, $this->id));
        foreach ($userposts as $userpost) {
            $params = array('id' => $this->coursemodule, 'page' => $userpost->page, 'annoid' => $userpost->annotationid, 'commid' => $userpost->commid);
            $link = (new moodle_url('/mod/pdfannotator/view.php', $params))->out();
            $this->userposts[] = array('content' => $userpost->content, 'link' => $link);
        }
    }

    /**
     *
     * @global type $DB
     * @global type $USER
     * @param type $rerender
     */
    public function set_hidden_answers() {

        global $DB, $USER;

        $sql2 = "SELECT c.id AS hiddenentrysid, c.content AS hiddenentry, a.id AS annoid, a.page, q.content AS hiddenentrysubjectline "
                . "FROM {pdfannotator_comments} q "
                . "JOIN {pdfannotator_annotations} a ON q.annotationid = a.id "
                . "JOIN {pdfannotator_comments} c ON c.annotationid = a.id "
                . "WHERE a.userid = ? AND q.isquestion = 1 AND a.pdfannotatorid = ? "
                . "AND NOT c.isquestion = 1 AND NOT c.isdeleted = 1 AND c.seen = 1";
        $hiddenentries = $DB->get_records_sql($sql2, array($USER->id, $this->id));

        foreach ($hiddenentries as $hiddenentry) {            
            $hiddenentry->link = (new moodle_url('/mod/pdfannotator/view.php',
                        array('id' => $this->coursemodule, 'page' => $hiddenentry->page, 'annoid' => $hiddenentry->annoid, 'commid' => $hiddenentry->hiddenentrysid)))->out();            
            $this->hiddenanswers[] = $hiddenentry;
        }
    }

    public function set_hidden_reports() {
        global $DB;

        $sql = "SELECT r.*, c.annotationid, c.userid AS commentauthor, c.content AS commentcontent, c.timecreated AS commenttime, c.visibility, a.page "
                . "FROM {pdfannotator_reports} r "
                . "JOIN {pdfannotator_comments} c ON r.commentid = c.id JOIN {pdfannotator_annotations} a ON c.annotationid = a.id "
                . "WHERE r.pdfannotatorid = ? AND r.seen = ?";
        $hiddenreports = $DB->get_records_sql($sql, array($this->id, 1));

        foreach ($hiddenreports as $report) {
            $params = array('id' => $this->coursemodule, 'page' => $report->page, 'annoid' => $report->annotationid, 'commid' => $report->commentid);
            $link = (new moodle_url('/mod/pdfannotator/view.php', $params))->out();
            $this->hiddenreports[] = ['hiddenreportsubjectline' => $report->commentcontent, 'hiddenreport' => $report->message, 'hiddenreportid' => $report->id, 'link' => $link];
        }
    }

    public static function get_conversations($pdfannotatorid) {

        global $DB;

        $sql = "SELECT q.id, q.content AS answeredquestion, q.timemodified, q.userid, q.visibility, a.id AS annoid, a.page, a.annotationtypeid "
                . "FROM {pdfannotator_annotations} a "
                . "JOIN {pdfannotator_comments} q ON q.annotationid = a.id "
                . "WHERE q.isquestion = 1 AND a.pdfannotatorid = ? AND NOT q.isdeleted = 1 "
                . "ORDER BY a.page ASC";

        try {
            $questions = $DB->get_records_sql($sql, array($pdfannotatorid));
        } catch (Exception $ex) {
            return -1;
        }

        foreach ($questions as $question) {

            $question->timemodified = pdfannotator_get_user_datetime($question->timemodified);
            if ($question->visibility === 'anonymous') {
                $question->author = get_string('anonymous', 'pdfannotator');
            } else {
                $question->author = pdfannotator_get_username($question->userid);
            }

            $sql = "SELECT c.id, c.content AS answer, c.userid, c.timemodified, c.visibility FROM {pdfannotator_comments} c "
                . "WHERE c.pdfannotatorid = ? AND c.annotationid = ? AND NOT c.isquestion = 1 AND NOT c.isdeleted = 1";

            try {
                $answers = $DB->get_records_sql($sql, array($pdfannotatorid, $question->annoid));
            } catch (Exception $ex) {
                return -1;
            }

            foreach ($answers as $answer) {
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

        }
        return $questions;
    }
}
