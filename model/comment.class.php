<?php

/**
 * @package   mod_pdfannotator
 * @copyright 2018 RWTH Aachen, Rabea de Groot and Anna Heynkes(see README.md)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * 
 */
//require_once('../../../../config.php');
require_once($CFG->dirroot . '/mod/pdfannotator/lib.php');
require_once($CFG->dirroot . '/mod/pdfannotator/locallib.php');
require_once($CFG->libdir . '/completionlib.php');
require_once('model/annotation.class.php');
require_once('model/pdfannotator.php');

class pdfannotator_comment {

    // attributes that mirror the "mdl_pdfannotator_comments" table

    public $id;
    public $annotationid;
    public $userid;
    public $content;
    public $visibility;
    public $isquestion;
    public $timecreated;
    public $timemodified;
    // further attributes

    public $course;
    public $pdfid;
    public $pdfname;
    public $page;

    public function __construct($id) {

        $this->id = $id;
        $this->annotationid = self::getAnnotationID($id);
        $this->userid = self::getAuthorID($id);
        $this->content = self::getContent($id);
        $this->visibility = self::getVisibility($id);
        $this->timecreated = self::getTimestamp($id);
        $this->timemodified = $this->timecreated;
        $this->page = annotation::getPageID($this->annotationid);
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
        
        $course = $DB->get_record('course', array('id'=>$cm->course), '*', MUST_EXIST);
                
        // create a new record in 'pdfannotator_comments'
        $dataRecord = new stdClass();
        $dataRecord->annotationid = $annotationid;
        $dataRecord->userid = $USER->id;
        $dataRecord->content = $content;
        $dataRecord->timecreated = time(); // moodle method: DateTime::getTimestamp(); 
        $dataRecord->timemodified = $dataRecord->timecreated;
        $dataRecord->visibility = $visibility;
        $dataRecord->isquestion = $isquestion;
        $anno = $DB->get_record('pdfannotator_annotationsneu', ['id' => $annotationid]);
        if ($anno) {
            // create a new record in the table named 'comments' and return its id, which is created by autoincrement:        
            $commentUUID = $DB->insert_record('pdfannotator_comments', $dataRecord, $returnid=true);
            $anonymous = $visibility == 'anonymous' ? true : false;
            if($isquestion == 0){
                //notify questioner
                $questioner = annotation::getAuthor($annotationid);
                if($questioner != $USER->id){
                    $comment = new stdClass();
                    $comment->answeruser = $visibility == 'public' ? fullname($USER) : 'Anonymous';
                    $comment->content = $content;
                    $comment->question = annotation::getQuestion($annotationid);
                    $page = annotation::getPageID($annotationid);
                    $comment->urltoanswer = $CFG->wwwroot.'/mod/pdfannotator/view.php?id='.$cm->id.'&page='.$page.'&annoid='.$annotationid.'&commid='.$commentUUID;

                    $messagetext = new stdClass();
                    $messagetext->text = format_notification_message_text($course,$cm,$context,get_string('modulename','pdfannotator'),$cm->name,$comment,'newanswer');
                    $messagetext->html = format_notification_message_html($course,$cm,$context,get_string('modulename','pdfannotator'),$cm->name,$comment,'newanswer');
                    $messagetext->url = $comment->urltoanswer;
                    $messageid = pdfannotator_notify_manager($questioner, $course, $cm, 'newanswer', $messagetext, $anonymous);
                }
            }else{
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

            return $commentUUID;
        } else {
            //return -1 for missing annotation
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

        // Get the ids and text content of all comments attached to this annotation/highlight
        //  $sql = "SELECT id, content, userid, visibility, isquestion, isdeleted FROM {pdfannotator_comments} WHERE annotationid = ?";
        $sql = "SELECT comments.id, content, comments.userid, visibility, isquestion, isdeleted, SUM(vote) AS votes "
                . "FROM {pdfannotator_comments} AS comments LEFT JOIN {pdfannotator_votes} AS votes"
                . " ON comments.id=votes.commentid WHERE annotationid = ? GROUP BY comments.id";
        $a = array();
        $a[] = $annotationid;
        $comments = $DB->get_records_sql($sql, $a); // records taken from table 'comments' as an array of objects
        $useVotes = pdfannotator_instance::useVotes($documentid);
        // Create a new object for each comment and allot it a further id: the annotation's itemid, called highlightid here
        $result = array();
        foreach ($comments as $data) {
            $comment = new stdClass();

            // Check permission to read the comment and set first attributes
            $comment->userid = $data->userid; // author of comment
            $comment->visibility = $data->visibility;
            $comment->isquestion = $data->isquestion;
            if (!self::allowedToRead($comment)) {
                continue;
            }
            // Add the missing attributes to the comment
            $comment->annotation = $annotationid;
            $comment->class = "Comment";
            $comment->isdeleted = $data->isdeleted;
            $comment->uuid = $data->id;
            $comment->timecreated = pdfannotator_comment::getUserDateTime($data->id); // e.g.: 'Dienstag, 26. September 2017, 14:10'
            $comment->timemodified = $comment->timecreated;
            if ($data->isdeleted) {
                $comment->visibility = 'deleted';
                $comment->content = get_string('deletedComment', 'pdfannotator');
            } else {
                $comment->content = $data->content;
            }
            self::setUsername($comment);
            $comment->votes = $data->votes;
            $comment->isvoted = self::isVoted($data->id);
            $comment->usevotes = $useVotes;
            // Add the comment to the list
            $result[] = $comment;
        }

        return $result;
    }

    /**
     * Function sets the username to be passed to JavaScript according to comment visibility
     * 
     * @param type $comment
     */
    public static function setUsername($comment) {
        switch ($comment->visibility) {
            case 'public':
                $authorID = self::getAuthorID($comment->uuid);
                $comment->username = get_username($authorID); // self::getAuthorName($comment->uuid);
                break;
            case 'anonymous':
                $comment->username = get_string('anonymous', 'pdfannotator');
                break;
            case 'private':
                $comment->username = get_string('private', 'pdfannotator'); // XXX
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
     * @param type $authorId
     * @param type $visibility
     * @return boolean
     */
    public static function allowedToRead($comment) {

        global $USER;
        $reader = $USER->id;
        $author = $comment->userid;

        if ($reader === $author || $comment->visibility !== 'private') {
            return true;
        }

        return false;
    }

    public static function find($annotationid) {

        // Get id and userid of each comment attached to this particular annotation
        global $DB;
        
        // Get the ids and text content of all comments attached to this annotation/highlight
        return $DB->get_records('pdfannotator_comments',array('annotationid'=>$annotationid), null, 'id,userid'); // result is an array of objects
        
    }

    public static function deletionAllowed($annotationId, $cmid) {

        global $DB;
        global $USER;

        $thisuser = $USER->id;
        $annotationAuthor = annotation::getAuthor($annotationId);

        // If user has admin rights with regard to annotations/comments: Allow deletion
        if (!$cm = get_coursemodule_from_id('pdfannotator', $cmid)) {
            error("Course module ID was incorrect");
        }
        $context = context_module::instance($cm->id);

        if (has_capability('mod/pdfannotator:administrateuserinput', $context)) {
            return true;
        }

        // If not:
        // Check user permission to delete the annotation itself
        if ($thisuser != $annotationAuthor) {
            return false;
        }
        // Check whether other people have commented this annotation
        $attached_comments = pdfannotator_comment::find($annotationId);
        if ($attached_comments && $attached_comments !== null) {
            foreach ($attached_comments as $comment) {
                if ($thisuser != $comment->userid) {
                    return false;
                }
            }
        }

        return true;
    }
    
    
    public static function registerReport($documentid, $commentid){ 
        
        global $DB;
        global $USER;
        $table = 'pdfannotator_comments';

        // Check comment's existance
        if (!$DB->record_exists($table, array('id' => $commentid))) {
            return false;
        }

        // Create a new record in 'mdl_pdfannotator_reports'
        $dataRecord = new stdClass();
        $dataRecord->comment = $commentid;
        $dataRecord->user = $USER->id;

//        $dataRecord->reason = $reason;
//        $dataRecord->text = $text;

        $dataRecord->timecreated = time();
        $dataRecord->timemodified = $dataRecord->timecreated;

        // create a new record in the table named 'reports' and return its id, which is created by autoincrement:        
        $reportID = $DB->insert_record('pdfannotator_reports', $dataRecord, $returnid = true);
        return $reportID;
    }

    public static function notifyOfReport($documentid, $commentid) {
        
    }

    /**
     * inserts a vote into the db
     * @global type $DB
     * @global type $USER
     * @param type $commentid
     * @return boolean
     */
    public static function insertVote($documentid, $commentid) {
        
        global $DB;
        global $USER;

        //check if voting is allowed in this pdfannotator
        //if(!$DB->record_exists('pdfannotator', array('id' => $documentid, 'usevotes' => '1'))){   
        if(!(pdfannotator_instance::useVotes($documentid))) {
            return false;
        }
        
        // Check comment's existance
        if (!$DB->record_exists('pdfannotator_comments', array('id' => $commentid))) {
            return false;
        }

        // Create a new record in 'mdl_pdfannotator_votes'
        $dataRecord = new stdClass();
        $dataRecord->commentid = $commentid;
        $dataRecord->userid = $USER->id;

        // create a new record in the table named 'votes' and return its id, which is created by autoincrement:        
        $DB->insert_record('pdfannotator_votes', $dataRecord, $returnid = true);
        $countVotes = self::getNumberVotes($commentid);
        return $countVotes;
    }
    
    //**************************************** Getter methods (static) from here on ****************************************
    
    /**
     * returns if the user already voted a comment
     * @global type $DB
     * @global type $USER
     * @param type $commentid
     * @return type
     */
    public static function isVoted($commentid) {
        global $DB, $USER;
        return $DB->record_exists('pdfannotator_votes', array('commentid'=>$commentid, 'userid'=>$USER->id));
    }
    
    /*
     * returns the number of votes a comment got
     */
    public static function getNumberVotes($commentid) {
        global $DB;
        return $DB->count_records('pdfannotator_votes', array('commentid'=>$commentid));
    }
    
    
    public static function getAnnotationID($commentid) {
        global $DB;
        return $DB->get_field('pdfannotator_comments', 'annotationid', array('id' => $commentid), $strictness=MUST_EXIST);  
    }

    public static function getAuthorID($commentid) {
        global $DB;
        return $DB->get_field('pdfannotator_comments', 'userid', array('id' => $commentid), $strictness=MUST_EXIST);
    }
    
    public static function getContent($commentid) {
        global $DB;
        return $DB->get_field('pdfannotator_comments', 'content', array('id' => $commentid), $strictness=MUST_EXIST);
    }
    /**
     * 
     * @param type $commentId
     * @return type
     */
    public static function getUserDateTime($commentid) {

        $timestamp = self::getTimestamp($commentid);
        $userDateTime = userdate($timestamp, $format = '', $timezone = 99, $fixday = true, $fixhour = true); // method in lib/moodlelib.php
        return $userDateTime;
    }

    /**
     * 
     * @global type $DB
     * @param type $commentId
     * @return type
     */
    public static function getTimestamp($commentid) {
        global $DB;
        return $DB->get_field('pdfannotator_comments', 'timecreated', array('id' => $commentid), $strictness=MUST_EXIST);
    } 
    /**
     * 
     * @global type $DB
     * @param type $commentId
     * @return type
     */
    public static function getVisibility($commentid) {
        global $DB;
        return $DB->get_field('pdfannotator_comments', 'visibility', array('id' => $commentid), $strictness=MUST_EXIST);
    }

    public static function getAnswers($annotationid) {
        global $DB;
        return $DB->get_records('pdfannotator_comments',array('annotationid'=>$annotationid, 'isquestion' => 0),null,'content,timecreated');
    }

    public static function getQuestions($documentid, $pageNumber) {
        global $DB;
        //get all questions of a page with a subselect, where all ids of annotations of one page are selected
        $sql = "SELECT c.* FROM {pdfannotator_comments} c WHERE isquestion = 1 AND annotationid IN (SELECT id FROM {pdfannotator_annotationsneu} a WHERE a.page = :page AND a.pdfannotatorid = :docid)";
        $questions = $DB->get_records_sql($sql,array('page' => $pageNumber, 'docid' => $documentid));

        foreach ($questions as $question){            
            $count = $DB->count_records('pdfannotator_comments', array('isquestion' => 0, 'annotationid' => $question->annotationid));
            $question->answercount = $count;
        }

        return $questions;
    }
    
}
