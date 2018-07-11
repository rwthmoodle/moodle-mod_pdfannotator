<?php

/**
 * @package   mod_pdfannotator
 * @copyright 2018 RWTH Aachen, Rabea de Groot and Anna Heynkes(see README.md)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 */
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
                $messagetext->text = format_notification_message_text($course, $cm, $context, get_string('modulename', 'pdfannotator'), $cm->name, $comment, 'newanswer');
                $messagetext->html = format_notification_message_html($course, $cm, $context, get_string('modulename', 'pdfannotator'), $cm->name, $comment, 'newanswer');
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
                  $messagetext->text = format_notification_message_text($course, $cm, $context, get_string('modulename', 'pdfannotator'), $cm->name, $question, 'newquestion');
                  $messagetext->html = format_notification_message_html($course, $cm, $context, get_string('modulename', 'pdfannotator'), $cm->name, $question, 'newquestion');
                  $messagetext->url = $question->urltoanswer;
                  foreach($recipients as $recipient){
                  if($recipient == $USER){
                  continue;
                  }
                  $messageid = pdfannotator_notify_manager($recipient, $course, $cm, 'newquestion', $messagetext, $anonymous);
                  }
                 */
            }

            return $commentuuid;
        } else {
            // Return -1 for missing annotation.
            return -1;
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
        // $sql = "SELECT id, content, userid, visibility, isquestion, isdeleted FROM {pdfannotator_comments} WHERE annotationid = ?";
        $sql = "SELECT c.id, content, c.userid, visibility, isquestion, isdeleted, SUM(vote) AS votes "
                . "FROM {pdfannotator_comments} c LEFT JOIN {pdfannotator_votes} votes"
                . " ON c.id=votes.commentid WHERE annotationid = ? GROUP BY c.id";
        $a = array();
        $a[] = $annotationid;
        $comments = $DB->get_records_sql($sql, $a); // Records taken from table 'comments' as an array of objects.
        $usevotes = pdfannotator_instance::use_votes($documentid);
        
        $result = array();
        foreach ($comments as $data) {
            $comment = new stdClass();

            // Check permission to read the comment and set first attributes.
            $comment->userid = $data->userid; // Author of comment.
            $comment->visibility = $data->visibility;
            $comment->isquestion = $data->isquestion;
            if (!self::allowed_to_read($comment)) {
                continue;
            }
            // Add the missing attributes to the comment.
            $comment->annotation = $annotationid;
            $comment->class = "Comment";
            $comment->isdeleted = $data->isdeleted;
            $comment->uuid = $data->id;
            $timestamp = self::get_timestamp($data->id);
            $comment->timecreated = get_user_date_time($timestamp); // E.g.: 'Dienstag, 26. September 2017, 14:10'.
            $comment->timemodified = $comment->timecreated;
            if ($data->isdeleted) {
                $comment->visibility = 'deleted';
                $comment->content = get_string('deletedComment', 'pdfannotator');
            } else {
                $comment->content = $data->content;
            }
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
        switch ($comment->visibility) {
            case 'public':
                $authorid = self::get_authorid($comment->uuid);
                $comment->username = get_username($authorid); // self::getAuthorName($comment->uuid);
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

    public static function find($annotationid) {

        // Get id and userid of each comment attached to this particular annotation.
        global $DB;

        // Get the ids and text content of all comments attached to this annotation.
        return $DB->get_records('pdfannotator_comments', array('annotationid' => $annotationid), null, 'id,userid');
    }

    public static function deletion_allowed($annotationid, $cmid) {

        global $DB;
        global $USER;

        $thisuser = $USER->id;
        $annotationauthor = pdfannotator_annotation::get_author($annotationid);

        if (!$cm = get_coursemodule_from_id('pdfannotator', $cmid)) {
            error("Course module ID was incorrect");
        }
        $context = context_module::instance($cm->id);

        // If user has admin rights with regard to annotations/comments: Allow deletion.
        if (has_capability('mod/pdfannotator:administrateuserinput', $context)) {
            return true;
        }

        // If not:
        // Check user permission to delete the annotation itself.
        if ($thisuser != $annotationauthor) {
            return false;
        }
        // Check whether other people have commented this annotation.
        $attachedcomments = self::find($annotationid);
        if ($attachedcomments && $attachedcomments !== null) {
            foreach ($attachedcomments as $comment) {
                if ($thisuser != $comment->userid) {
                    return false;
                }
            }
        }

        return true;
    }

    public static function delete_comment($commentid, $context) {

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
        $isquestion = $comment->isquestion;

        $isteacher = has_capability('mod/pdfannotator:administrateuserinput', $context);
        $isstudent = !$isteacher;

        $wasanswered = $DB->record_exists_select('pdfannotator_comments', "annotationid = ? AND timecreated > ? AND isdeleted = ?", array($annotationid, $comment->timecreated, 0));
        $wasreported = $DB->record_exists('pdfannotator_reports', ['commentid' => $commentid]);

        $tobedeletedaswell = [];

        // 2.1 Students may delete their own comments as long as no one else has answered.
        if ($isstudent && $USER->id === $comment->userid) {

            if ($wasanswered) {
                echo json_encode(['status' => 'error']);
                return;
            }
            $success = $DB->delete_records('pdfannotator_comments', array("id" => $commentid));
        }

        // 2.1 Teachers may delete any comment.
        if ($isteacher) {

            // 2.1.1 If it's an initial comment, i.e. a question .
            // (normally in this case action equals 'delete' and not 'deletecomment' but to be safe.
            if ($isquestion) { // XXX never accessed :(
                // Delete the question and all answers to it.
                $comments = $DB->get_records('pdfannotator_comments', array("annotationid" => $annotationid));
                foreach ($comments as $commentdata) {
                    // If the comment was not deleted, but reported, then insert the record into the archive.
                    if ($commentdata->isdeleted === 0 &&
                            $DB->record_exists('pdfannotator_reports', ['commentid' => $commentdata->id])) {
                        unset($commentdata->id);
                        $DB->insert_record('pdfannotator_comments_archiv', $commentdata);
                    }
                }
                $success = $DB->delete_records('pdfannotator_comments', array("annotationid" => $annotationid));
            } else { // 2.1.2 If it's a regular comment.
                // If the comment was answered, mark it as deleted for a special display.
                if ($wasanswered == 1) {

                    // Before updating, insert the comment into the archive, if it was reported.
                    if ($wasreported) {
                        $reportedcomment = clone $comment;
                        unset($reportedcomment->id);
                        $DB->insert_record('pdfannotator_comments_archiv', $reportedcomment);
                    }
                    $params = array("id" => $commentid, "isdeleted" => 1);
                    $success = $DB->update_record('pdfannotator_comments', $params, $bulk = false);

                    // If not, just delete it.
                } else {

                    // But first: Check if the predecessor was already marked as deleted, too and if so, delete it completely.

                    $sql = "SELECT id, isdeleted from {pdfannotator_comments} "
                            . "WHERE annotationid = ? AND isquestion = ? AND timecreated < ? ORDER BY id DESC";
                    $params = array($annotationid, 0, $comment->timecreated);

                    $predecessors = $DB->get_records_sql($sql, $params);

                    if (!empty($predecessors)) {
                        foreach ($predecessors as $predecessor) {
                            if ($predecessor->isdeleted != 0) {
                                $workingfine = $DB->delete_records('pdfannotator_comments', array("id" => $predecessor->id));
                                if ($workingfine != 0) {
                                    $tobedeletedaswell[] = $predecessor->id;
                                }
                            } else {
                                break;
                            }
                        }
                    }
                    // Before deleting: If the comment was reported, the comment should be inserted into the archive.
                    if ($wasreported) {
                        $reportedcomment = clone $comment;
                        $DB->insert_record('pdfannotator_comments_archiv', $reportedcomment);
                    }
                    // Now delete the selected comment.
                    $success = $DB->delete_records('pdfannotator_comments', array("id" => $commentid));
                }
            }
        }

        if ($success == 1) {
            $DB->delete_records('pdfannotator_votes', array("commentid" => $commentid));
            echo json_encode(['status' => 'success', 'wasanswered' => $wasanswered, 'followups' => $tobedeletedaswell]);
        } else {
            echo json_encode(['status' => 'error']);
        }
    }
    /**
     * inserts a vote into the db
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

        // Check comment's existance.
        if (!$DB->record_exists('pdfannotator_comments', array('id' => $commentid))) {
            return false;
        }

        // Create a new record in 'mdl_pdfannotator_votes'.
        $datarecord = new stdClass();
        $datarecord->commentid = $commentid;
        $datarecord->userid = $USER->id;

        // Create a new record in the table named 'votes' and return its id, which is created by autoincrement.
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

    //**************************************** Getter methods (static) from here on ****************************************

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
     *
     * @global type $DB
     * @param type $commentId
     * @return type
     */
    public static function get_timestamp($commentid) {
        global $DB;
        return $DB->get_field('pdfannotator_comments', 'timecreated', array('id' => $commentid), $strictness = MUST_EXIST);
    }

}
