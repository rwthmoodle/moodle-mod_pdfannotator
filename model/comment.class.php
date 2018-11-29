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
// require_once('../../../../config.php');
require_once($CFG->dirroot . '/mod/pdfannotator/lib.php');
require_once($CFG->dirroot . '/mod/pdfannotator/locallib.php');
require_once($CFG->libdir . '/completionlib.php');
require_once('model/annotation.class.php');
require_once('model/pdfannotator.php');

class pdfannotator_comment {

    public $id;
    public $annotationid;
    public $userid;
    public $content;
    public $visibility;
    public $isquestion;
    public $timecreated;
    public $timemodified;
    public $course;
    public $pdfid;
    public $pdfname;
    public $page;

    public function __construct($id) {

        $this->id = $id;
        $this->annotationid = self::get_annotationid($id);
        $this->userid = self::get_authorid($id);
        $this->content = self::get_content($id);
        $this->visibility = self::get_visibility($id);
        $this->timecreated = self::get_timestamp($id);
        $this->timemodified = $this->timecreated;
        $this->page = pdfannotator_annotation::get_pageid($this->annotationid);
    }

    /**
     * This method inserts a new record into mdl_pdfannotator_comments and returns its id
     *
     * @global type $DB
     * @global type $USER
     * @param type $documentid specifies the pdf
     * @param type $annotationid specifies the annotation (usually a highlight) to be commented
     * @param type $content the text or comment itself
     */
    public static function create($documentid, $annotationid, $content, $visibility, $isquestion, $cm, $context) {

        global $DB;
        global $USER;
        global $CFG;

        $course = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);

        // Create a new record in 'pdfannotator_comments'.
        $datarecord = new stdClass();
        $datarecord->pdfannotatorid = $documentid;
        $datarecord->annotationid = $annotationid;
        $datarecord->userid = $USER->id;
        $datarecord->content = $content;
        $datarecord->timecreated = time(); // Moodle method: DateTime::getTimestamp();
        $datarecord->timemodified = $datarecord->timecreated;
        $datarecord->visibility = $visibility;
        $datarecord->isquestion = $isquestion;
        $anno = $DB->get_record('pdfannotator_annotations', ['id' => $annotationid]);
        if ($anno) {
            // Create a new record in the table named 'comments' and return its id, which is created by autoincrement.
            $commentuuid = $DB->insert_record('pdfannotator_comments', $datarecord, $returnid = true);

            $datarecord->uuid = $commentuuid;
            $datarecord->username = $USER->username;
            if ($visibility === 'anonymous') {
                $datarecord->username = get_string('anonymous', 'pdfannotator');
            } else {
                $datarecord->username = get_string('me', 'pdfannotator');
            }
            $datarecord->timecreated = pdfannotator_get_user_datetime($datarecord->timecreated);
            $datarecord->timemodified = pdfannotator_get_user_datetime($datarecord->timemodified);
            $datarecord->usevotes = pdfannotator_instance::use_votes($documentid);
            $datarecord->votes = 0;
            $datarecord->isdeleted = false;

            $anonymous = $visibility == 'anonymous' ? true : false;
            if ($isquestion == 0) {
                // Notify subscribed users.
                $comment = new stdClass();
                $comment->answeruser = $visibility == 'public' ? fullname($USER) : 'Anonymous';
                $comment->content = $content;
                $comment->question = pdfannotator_annotation::get_question($annotationid);
                $page = pdfannotator_annotation::get_pageid($annotationid);
                $comment->urltoanswer = $CFG->wwwroot . '/mod/pdfannotator/view.php?id=' .
                        $cm->id . '&page=' . $page . '&annoid=' . $annotationid . '&commid=' . $commentuuid;

                $messagetext = new stdClass();
                $module = get_string('modulename', 'pdfannotator');
                $messagetext->text = pdfannotator_format_notification_message_text($course, $cm, $context, $module, $cm->name, $comment, 'newanswer');
                $messagetext->html = pdfannotator_format_notification_message_html($course, $cm, $context, $module, $cm->name, $comment, 'newanswer');
                $messagetext->url = $comment->urltoanswer;
                $recipients = self::get_subscribed_users($annotationid);
                foreach ($recipients as $recipient) {
                    if ($recipient != $USER->id) {
                        $messageid = pdfannotator_notify_manager($recipient, $course, $cm, 'newanswer', $messagetext, $anonymous);
                    }
                }
            } else {
                self::insert_subscription($annotationid);
                /*
                  // notify all users, that there is a new question
                  $recipients = get_enrolled_users($context, 'mod/pdfannotator:recievenewquestionnotifications');

                  $question = new stdClass();
                  $question->answeruser = $visibility == 'public' ? fullname($USER) : 'Anonymous';
                  $question->content = $content;
                  $page = annotation::getPageID($annotationid);
                  $question->urltoanswer = $CFG->wwwroot . '/mod/pdfannotator/view.php?id=' . $cm->id . '&page=' . $page . '&annoid=' . $annotationid . '&commid=' . $commentUUID;

                  $messagetext = new stdClass();
                  $messagetext->text = pdfannotator_format_notification_message_text($course, $cm, $context, get_string('modulename', 'pdfannotator'), $cm->name, $question, 'newquestion');
                  $messagetext->html = pdfannotator_format_notification_message_html($course, $cm, $context, get_string('modulename', 'pdfannotator'), $cm->name, $question, 'newquestion');
                  $messagetext->url = $question->urltoanswer;
                  foreach($recipients as $recipient){
                  if($recipient == $USER){
                  continue;
                  }
                  $messageid = pdfannotator_notify_manager($recipient, $course, $cm, 'newquestion', $messagetext, $anonymous);
                  }
                 */
            }

            // return $commentuuid;
            return $datarecord;
        } else {
            // Return -1 for missing annotation.
            return false;
        }
    }

    /**
     * This method returns an array of all comment objects belonging to the specified annotation.
     *
     * @global type $DB
     * @param type $documentid
     * @param type $highlightid
     * @return \stdClass
     */
    public static function read($documentid, $annotationid) {

        global $DB;

        // Get the ids and text content of all comments attached to this annotation/highlight.
        $sql = "SELECT c.id, c.content, c.userid, c.visibility, c.isquestion, c.isdeleted, c.timemodified, c.modifiedby, SUM(vote) AS votes "
                . "FROM {pdfannotator_comments} c LEFT JOIN {pdfannotator_votes} votes"
                . " ON c.id=votes.commentid WHERE annotationid = ? GROUP BY c.id ORDER BY c.timecreated";
        $a = array();
        $a[] = $annotationid;
        $comments = $DB->get_records_sql($sql, $a); // Records taken from table 'comments' as an array of objects.
        $usevotes = pdfannotator_instance::use_votes($documentid);

        $annotation = $DB->get_record('pdfannotator_annotations', array('id' => $annotationid), $fields='timecreated, timemodified, modifiedby', $strictness=MUST_EXIST);
        
        $result = array();
        foreach ($comments as $data) {
            $comment = new stdClass();

            // Check permission to read the comment and set first attributes.
            $comment->userid = $data->userid; // Author of comment.
            $comment->visibility = $data->visibility;
            $comment->isquestion = $data->isquestion;
            if ($comment->isquestion && !empty($annotation->timemodified) && $annotation->timemodified > $annotation->timecreated) {
                $comment->repositioned = true;
                $comment->timemoved = pdfannotator_get_user_datetime($annotation->timemodified);
                $comment->movedby = $annotation->modifiedby;
            }
            if (!self::allowed_to_read($comment)) {
                continue;
            }
            // Add the missing attributes to the comment.
            $comment->annotation = $annotationid;
            $comment->class = "Comment";
            $comment->isdeleted = $data->isdeleted;
            $comment->uuid = $data->id;
            $timestamp = self::get_timestamp($data->id);
            $comment->timecreated = pdfannotator_get_user_datetime($timestamp); // E.g.: 'Tuesday, 26. September 2017, 14:10'.
            $comment->timemodified = pdfannotator_get_user_datetime($data->timemodified);
            if ( ($comment->timemodified != $comment->timecreated) && !empty($data->modifiedby) )  {
                    $comment->modifiedby = $data->modifiedby;
            }
            if ($data->isdeleted) {
                $comment->visibility = 'deleted';
                $comment->content = get_string('deletedComment', 'pdfannotator');
            } else {
                $comment->content = $data->content;
            }
            $comment->userid = $data->userid;
            self::set_username($comment);
            $comment->votes = $data->votes;
            $comment->isvoted = self::is_voted($data->id);
            $comment->usevotes = $usevotes;
            $comment->issubscribed = self::is_subscribed($annotationid);
            // Add the comment to the list.
            $result[] = $comment;
        }

        return $result;
    }

    /**
     * Function sets the username to be passed to JavaScript according to comment visibility
     *
     * @param type $comment
     */
    public static function set_username($comment) {
        global $USER;
        switch ($comment->visibility) {
            case 'public':
                if ($comment->userid === $USER->id) {
                    $comment->username = get_string('me', 'pdfannotator');
                } else {
                    $comment->username = pdfannotator_get_username($comment->userid);
                }
                break;
            case 'anonymous':
                $comment->username = get_string('anonymous', 'pdfannotator');
                break;
            case 'private':
                $comment->username = get_string('private', 'pdfannotator'); // XXX.
                break;
            case 'deleted':
                $comment->username = '';
            default:
                $comment->username = '';
        }
    }

    /**
     * A user may read all of his/her own comments as well as all non-private
     * (i.e. public and anonymous*) comments of other users
     * *not implemented at present
     *
     * @global type $USER
     * @param type $authorid
     * @param type $visibility
     * @return boolean
     */
    public static function allowed_to_read($comment) {

        global $USER;
        $reader = $USER->id;
        $author = $comment->userid;

        if ($reader === $author || $comment->visibility !== 'private') {
            return true;
        }

        return false;
    }

    /**
     * Deletes a comment.
     * If the comment is answered, it will be displayed as deleted comment.
     * If the comment was reported it is inserted to the commentsarchive table.
     */
    public static function delete_comment($commentid, $cmid) {
        global $DB, $USER;
        $success = 0;
        // 1. Retrieve comment from db (return false if it doesn't exist).
        $comment = $DB->get_record('pdfannotator_comments', array('id' => $commentid), '*', $strictness = IGNORE_MISSING);

        if (!$comment) {
            echo json_encode(['status' => 'error']);
            return;
        }

        // 2. To delete or not to delete, that is the question.
        $annotationid = $comment->annotationid;

        $select = "annotationid = ? AND timecreated > ? AND isdeleted = ?";
        $wasanswered = $DB->record_exists_select('pdfannotator_comments', $select, [$annotationid, $comment->timecreated, 0]);
        $wasreported = $DB->record_exists('pdfannotator_reports', ['commentid' => $commentid]);

        $tobedeletedaswell = [];
        $deleteannotation = 0;

        // Before deleting: If the comment was reported, it should be inserted into the archive.
        if ($wasreported) {
            $reportedcomment = clone $comment;
            $DB->insert_record('pdfannotator_commentsarchive', $reportedcomment);
        }

        if ($wasanswered) { // If the comment was answered, mark it as deleted for a special display.
            $params = array("id" => $commentid, "isdeleted" => 1);
            $success = $DB->update_record('pdfannotator_comments', $params, $bulk = false);
        } else { // If not, just delete it.
            // But first: Check if the predecessor was already marked as deleted, too and if so, delete it completely.
            $sql = "SELECT id, isdeleted, isquestion from {pdfannotator_comments} "
                    . "WHERE annotationid = ? AND timecreated < ? ORDER BY id DESC";
            $params = array($annotationid, $comment->timecreated);
            $predecessors = $DB->get_records_sql($sql, $params);

            foreach ($predecessors as $predecessor) {
                if ($predecessor->isdeleted != 0) {
                    $workingfine = $DB->delete_records('pdfannotator_comments', array("id" => $predecessor->id));
                    if ($workingfine != 0) {
                        $tobedeletedaswell[] = $predecessor->id;
                        if ($predecessor->isquestion) {
                            pdfannotator_annotation::delete($annotationid, $cmid);
                            $deleteannotation = $annotationid;
                        }
                    }
                } else {
                    break;
                }
            }

            // If the comment is a question and has no answers, delete the annotion.
            if ($comment->isquestion) {
                pdfannotator_annotation::delete($annotationid, $cmid);
                $deleteannotation = $annotationid;
            }

            $success = $DB->delete_records('pdfannotator_comments', array("id" => $commentid));
        }
        // Delete votes to the comment.
        $DB->delete_records('pdfannotator_votes', array("commentid" => $commentid));

        if ($success == 1) {
            return ['status' => 'success', 'wasanswered' => $wasanswered, 'followups' => $tobedeletedaswell, 'deleteannotation' => $deleteannotation];
        } else {
            return ['status' => 'error'];
        }
    }

    public static function update($commentid, $content, $editanypost) {
        global $DB, $USER;
        $comment = $DB->get_record('pdfannotator_comments', ['id' => $commentid]);
        if ($comment && ( $comment->userid == $USER->id || $editanypost) && $comment->isdeleted == 0) {
            $comment->content = $content;
            $comment->timemodified = time();
            $comment->modifiedby = $USER->id;
            $time = pdfannotator_get_user_datetime($comment->timemodified);
            $success = $DB->update_record('pdfannotator_comments', $comment);
        } else {
            $success = false;
        }

        if ($success) {
            $result = array('status' => 'success', 'timemodified' => $time);
            if ($comment->userid != $USER->id) {
                $result['modifiedby'] = pdfannotator_get_username($USER->id);
            }
            return $result;
            //return ['status' => 'success', 'timemodified' => $time, 'modifiedby' => pdfannotator_get_username($USER->id)];
        } else {
            return ['status' => 'error'];
        }
    }

    /**
     * Inserts a vote into the db.
     * @global type $DB
     * @global type $USER
     * @param type $commentid
     * @return boolean
     */
    public static function insert_vote($documentid, $commentid) {

        global $DB;
        global $USER;

        // Check if voting is allowed in this pdfannotator and if comment was already voted.
        if (!(pdfannotator_instance::use_votes($documentid)) || (self::is_voted($commentid))) {
            return false;
        }

        // Check comment's existence.
        if (!$DB->record_exists('pdfannotator_comments', array('id' => $commentid))) {
            return false;
        }

        // Create a new record, insert it in the table named 'votes' and return its id, which is created by autoincrement.
        $datarecord = new stdClass();
        $datarecord->commentid = $commentid;
        $datarecord->userid = $USER->id;

        $DB->insert_record('pdfannotator_votes', $datarecord, $returnid = true);
        $countvotes = self::get_number_of_votes($commentid);
        return $countvotes;
    }

    /**
     * Inserts a subscription into the DB.
     * @global type $DB
     * @global type $USER
     * @param type $annotationid
     * @return boolean
     */
    public static function insert_subscription($annotationid) {
        global $DB, $USER;

        // Check if subscription already exists.
        if ($DB->record_exists('pdfannotator_subscriptions', array('annotationid' => $annotationid, 'userid' => $USER->id))) {
            return false;
        }

        $datarecord = new stdClass();
        $datarecord->annotationid = $annotationid;
        $datarecord->userid = $USER->id;

        $subscriptionid = $DB->insert_record('pdfannotator_subscriptions', $datarecord, $returnid = true);
        return $subscriptionid;
    }

    /**
     * Deletes a subscription.
     * @global type $DB
     * @global type $USER
     * @param type $annotationid
     * @return string
     */
    public static function delete_subscription($annotationid) {
        global $DB, $USER;

        $DB->delete_records('pdfannotator_subscriptions', array('annotationid' => $annotationid, 'userid' => $USER->id));

        return 'true';
    }

    // **************************************** Getter methods (static) from here on ****************************************

    /**
     * Returns if the user already voted a comment.
     * @global type $DB
     * @global type $USER
     * @param type $commentid
     * @return type
     */
    public static function is_voted($commentid) {
        global $DB, $USER;
        return $DB->record_exists('pdfannotator_votes', array('commentid' => $commentid, 'userid' => $USER->id));
    }

    /**
     * Returns the number of votes a comment got.
     * @global type $DB
     * @param type $commentid
     * @return type
     */
    public static function get_number_of_votes($commentid) {
        global $DB;
        return $DB->count_records('pdfannotator_votes', array('commentid' => $commentid));
    }

    /**
     * Returns if the user is subscribed to a question.
     * @global type $DB
     * @global type $USER
     * @param type $annotationid
     * @return type
     */
    public static function is_subscribed($annotationid) {
        global $DB, $USER;
        return $DB->record_exists('pdfannotator_subscriptions', array('annotationid' => $annotationid, 'userid' => $USER->id));
    }

    /**
     * Returns all subscribed users to a question.
     * @global type $DB
     * @param type $annotationid
     * @return type
     */
    public static function get_subscribed_users($annotationid) {
        global $DB;
        $select = 'annotationid = ?';
        return $DB->get_fieldset_select('pdfannotator_subscriptions', 'userid', $select, array($annotationid));
    }

    public static function get_annotationid($commentid) {
        global $DB;
        return $DB->get_field('pdfannotator_comments', 'annotationid', array('id' => $commentid), $strictness = MUST_EXIST);
    }

    public static function get_authorid($commentid) {
        global $DB;
        return $DB->get_field('pdfannotator_comments', 'userid', array('id' => $commentid), $strictness = MUST_EXIST);
    }

    public static function get_content($commentid) {
        global $DB;
        return $DB->get_field('pdfannotator_comments', 'content', array('id' => $commentid), $strictness = MUST_EXIST);
    }

    /**
     *
     * @global type $DB
     * @param type $commentId
     * @return type
     */
    public static function get_visibility($commentid) {
        global $DB;
        return $DB->get_field('pdfannotator_comments', 'visibility', array('id' => $commentid), $strictness = MUST_EXIST);
    }

    public static function get_questions($documentid, $pagenumber) {
        global $DB;
        // Get all questions of a page with a subselect, where all ids of annotations of one page are selected.
        $sql = "SELECT c.* FROM {pdfannotator_comments} c WHERE isquestion = 1 AND annotationid IN "
                . "(SELECT id FROM {pdfannotator_annotations} a WHERE a.page = :page AND a.pdfannotatorid = :docid)";
        $questions = $DB->get_records_sql($sql, array('page' => $pagenumber, 'docid' => $documentid));

        foreach ($questions as $question) {
            $params = array('isquestion' => 0, 'annotationid' => $question->annotationid);
            $count = $DB->count_records('pdfannotator_comments', $params);
            $question->answercount = $count;
            $question->page = $pagenumber;
        }

        return $questions;
    }

    public static function get_all_questions($documentid) {
        global $DB;
        // Get all questions of a page with a subselect, where all ids of annotations of one page are selected.
        $sql = "SELECT c.*, a.page FROM {pdfannotator_comments} c "
                . "JOIN (SELECT * FROM {pdfannotator_annotations} WHERE pdfannotatorid = :docid) a "
                . "ON a.id = c.annotationid WHERE isquestion = 1";
        $questions = $DB->get_records_sql($sql, array('docid' => $documentid));
        $ret = [];
        foreach ($questions as $question) {
            $ret[$question->page][] = $question;
        }
        return $ret;
    }

    /**
     * Get all questions in an annotator where a comment contains the pattern
     * @global type $DB
     * @param type $documentid
     * @param type $pattern
     */
    public static function get_questions_search($documentid, $pattern) {
        global $DB;
        $ret = [];
        $i = 0;
        $sql = "SELECT c.*, a.page FROM {pdfannotator_comments} c "
                . "JOIN {pdfannotator_annotations} a ON a.id = c.annotationid "
                . "WHERE isquestion = 1 AND c.pdfannotatorid = :docid AND "
                . "annotationid IN "
                . "(SELECT annotationid FROM {pdfannotator_comments} "
                . "WHERE " . $DB->sql_like('content', ':pattern', false) . " AND isdeleted = 0) "
                . "ORDER BY a.page, c.id";

        $params = ['docid' => $documentid, 'pattern' => '%' . $pattern . '%'];
        $questions = $DB->get_records_sql($sql, $params);

        foreach ($questions as $question) {
            $params = array('isquestion' => 0, 'annotationid' => $question->annotationid);
            $count = $DB->count_records('pdfannotator_comments', $params);
            $question->answercount = $count;
            $ret[$i] = $question;   // Without this array the order by page would get lost, because js sorts by id.
            $i++;
        }
        return $ret;
    }

    public static function get_timestamp($commentid) {
        global $DB;
        return $DB->get_field('pdfannotator_comments', 'timecreated', array('id' => $commentid), $strictness = MUST_EXIST);
    }

}
