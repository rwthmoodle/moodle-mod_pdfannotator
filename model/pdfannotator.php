<?php

/**
 * @package   mod_pdfannotator
 * @copyright 2018 RWTH Aachen, Rabea de Groot and Anna Heynkes(see README.md)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 */
require_once($CFG->dirroot . '/mod/pdfannotator/locallib.php');
require_once($CFG->dirroot . '/mod/pdfannotator/renderable.php');

/**
 * This class represents an instance of the pdfannotator module.
 *
 */
class pdfannotator_instance {

    private $id;
    private $coursemodule;
    private $name;
    private $answers; // questions asked by the current user
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
    public function set_answers($rerender = null) {

        global $DB, $USER;

        $sql = "SELECT c.id AS answerid, c.content AS answer, a.id AS annoid, a.page, q.content AS answeredquestion "
                . "FROM {pdfannotator_subscriptions} s "
                . "JOIN {pdfannotator_annotations} a ON a.id = s.annotationid "
                . "JOIN {pdfannotator_comments} q ON q.annotationid = a.id "
                . "JOIN {pdfannotator_comments} c ON c.annotationid = a.id "
                . "WHERE s.userid = ? AND q.isquestion AND NOT c.isquestion AND a.pdfannotatorid = ? AND NOT c.isdeleted AND NOT c.seen";

        $entries = $DB->get_records_sql($sql, array($USER->id, $this->id));

        foreach ($entries as $entry) {
            if ($rerender) {
                $entry->link = "/moodle/mod/pdfannotator/view.php?id={$this->coursemodule}&page={$entry->page}&annoid={$entry->annoid}&commid={$entry->answerid}";
            } else {
                $entry->link = new moodle_url('/mod/pdfannotator/view.php', 
                        array('id' => $this->coursemodule, 'page' => $entry->page, 'annoid' => $entry->annoid, 'commid' => $entry->answerid));
            }
            $this->answers[] = $entry;
        }
    }

    public function set_reports($courseid, $rerender = null) {

        global $DB;

        // Retrieve reports from db as an array of stdClass objects, representing a report record each.
        $sql = "SELECT r.id as reportid, r.commentid, r.message as report, a.page, c.annotationid, c.userid AS commentauthor, c.content AS reportedcomment, c.timecreated AS commenttime, c.visibility "
                . "FROM {pdfannotator_reports} r JOIN {pdfannotator_comments} c ON r.commentid = c.id "
                . "JOIN {pdfannotator_annotations} a ON c.annotationid = a.id "
                . "WHERE r.courseid = ? AND r.pdfannotatorid = ? AND r.seen = ?";
        $reports = $DB->get_records_sql($sql, array($courseid, $this->id, 0));

        foreach ($reports as $report) {
            if ($rerender) {
                $report->link = "/moodle/mod/pdfannotator/view.php?id={$this->coursemodule}&page={$report->page}&annoid={$report->annotationid}&commid={$report->commentid}";
            } else {
                $report->link = new moodle_url('/mod/pdfannotator/view.php', array('id' => $this->coursemodule, 'page' => $report->page, 'annoid' => $report->annotationid, 'commid' => $report->commentid));
            }
            $this->reports[] = $report;

            /*
              // optional: who wrote what and when? (->adjust SQL)
              if ($report->visibility === 'anonymous') {
              $report->commentauthor = get_string('anonymous', 'pdfannotator');
              } else { // The try catch might be unnecessary if you tick 'anonymise userinfo' during backup.
              try {
              $report->commentauthor = get_username($report->commentauthor);
              } catch (Exception $ex) {
              $report->commentauthor = get_string('unknownuser', 'pdfannotator');
              }
              }
              $report->commenttime = userdate($report->commenttime, $format = '', $timezone = 99, $fixday = true, $fixhour = true);

              // optional: who reported it and when? (->adjust SQL)
              $report->userid = get_username($report->userid);
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

        $sql = "SELECT a.id as annotationid, a.page, c.id as commentid, c.content FROM {pdfannotator_annotations} a "
                . "JOIN {pdfannotator_comments} c ON c.annotationid = a.id "
                . "WHERE c.isquestion AND a.pdfannotatorid = ? AND c.timecreated >= ?";

        $newquestions = $DB->get_records_sql($sql, array($this->id, strtotime($timestring))); // strtotime("-1 week");

        foreach ($newquestions as $question) {
            $params = array('id' => $this->coursemodule, 'page' => $question->page, 'annoid' => $question->annotationid, 'commid' => $question->commentid);
            $question->link = new moodle_url('/mod/pdfannotator/view.php', $params);
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
            $link = new moodle_url('/mod/pdfannotator/view.php', $params);
            $this->userposts[] = array('content' => $userpost->content, 'link' => $link);
        }
    }

    /**
     *
     * @global type $DB
     * @global type $USER
     * @param type $rerender
     */
    public function set_hidden_answers($rerender = null) {

        global $DB, $USER;

        $sql2 = "SELECT c.id AS hiddenentrysid, c.content AS hiddenentry, a.id AS annoid, a.page, q.content AS hiddenentrysubjectline "
                . "FROM {pdfannotator_comments} q "
                . "JOIN {pdfannotator_annotations} a ON q.annotationid = a.id "
                . "JOIN {pdfannotator_comments} c ON c.annotationid = a.id "
                . "WHERE a.userid = ? AND q.isquestion AND a.pdfannotatorid = ? AND NOT c.isquestion AND NOT c.isdeleted AND c.seen";
        $hiddenentries = $DB->get_records_sql($sql2, array($USER->id, $this->id));

        foreach ($hiddenentries as $hiddenentry) {
            if ($rerender) {
                $hiddenentry->link = "/moodle/mod/pdfannotator/view.php?id={$this->coursemodule}&page={$hiddenentry->page}&annoid={$hiddenentry->annoid}&commid={$hiddenentry->hiddenentrysid}";
            } else {
                $hiddenentry->link = new moodle_url('/mod/pdfannotator/view.php', array('id' => $this->coursemodule, 'page' => $hiddenentry->page, 'annoid' => $hiddenentry->annoid, 'commid' => $hiddenentry->hiddenentrysid));
            }
            $this->hiddenanswers[] = $hiddenentry;
        }
    }

    public function set_hidden_reports($rerender = null) {
        global $DB;

        $sql = "SELECT r.*, c.annotationid, c.userid AS commentauthor, c.content AS commentcontent, c.timecreated AS commenttime, c.visibility, a.page "
                . "FROM {pdfannotator_reports} r JOIN {pdfannotator_comments} c ON r.commentid = c.id JOIN {pdfannotator_annotations} a ON c.annotationid = a.id "
                . "WHERE r.pdfannotatorid = ? AND r.seen = ?";
        $hiddenreports = $DB->get_records_sql($sql, array($this->id, 1));

        foreach ($hiddenreports as $report) {
            if ($rerender) {
                $link = "/moodle/mod/pdfannotator/view.php?id={$this->coursemodule}&page={$report->page}&annoid={$report->annotationid}&commid={$report->commentid}";
            } else {
                $params = array('id' => $this->coursemodule, 'page' => $report->page, 'annoid' => $report->annotationid, 'commid' => $report->commentid);
                $link = new moodle_url('/mod/pdfannotator/view.php', $params);
            }
            $this->hiddenreports[] = array('hiddenreportsubjectline' => $report->commentcontent, 'hiddenreport' => $report->message, 'hiddenreportid' => $report->id, 'link' => $link);
        }
    }

}
