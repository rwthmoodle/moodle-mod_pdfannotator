<?php
/**
 * @package   mod_pdfannotator
 * @copyright 2018 RWTH Aachen, Rabea de Groot and Anna Heynkes(see README.md)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * 
 */

require_once($CFG->dirroot.'/mod/pdfannotator/locallib.php');
require_once($CFG->dirroot.'/mod/pdfannotator/renderable.php');

/**
 * This class represents an instance of the pdfannotator module.
 *
 */
class pdfannotator_instance {
    
    private $id;
    private $coursemodule;
    private $name;
    private $myquestions; // questions asked by the current user
    private $latestquestions;
    private $reports;


    public function __construct($dbrecord) {
        $this->id = $dbrecord->id;
        $this->coursemodule = $dbrecord->coursemodule;
        $this->name = $dbrecord->name;
        $this->myquestions = array();
        $this->latestquestions = array();
        $this->reports = array();
    }


    public function get_id() {
        return $this->id;
    }
    
    public function get_coursemoduleid() {
        return $this->coursemodule;
    }
    
    public function get_name() {
        return $this->name;
    }
    
    public function get_myquestions() {
        return $this->myquestions;
    }
    
    public function get_reports(){
        return $this->reports;
    }
    
    public function get_latest_questions(){
        return $this->latestquestions;
    }
    
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
              
        $pdfannotator_list = array();
        
        foreach($result as $pdfannotator) {
            $pdfannotator_list[] = new pdfannotator_instance($pdfannotator);
        }
        
        if ($beginwith) {
            foreach ($pdfannotator_list as $index => $annotator) {
                if($annotator->get_id() == $beginwith && $index != 0) {
                    $temp = $pdfannotator_list[0];
                    $pdfannotator_list[0] = $annotator;
                    $pdfannotator_list[$index] = $temp;
                    break;
                }
            }
        }
        
        return $pdfannotator_list;
    }
    
    
    public function set_reports($courseid) {
     
        global $DB;
        
        $reports = $this->retrieve_reports($courseid); // db query
           
        foreach($reports as $report) { // add and transform info as needed
            
            // where is the reported comment?
            $report->coursename = get_course_name_by_id($report->courseid);
            $report->topic = 'dummy topic';
            $report->coursemodule = $this->coursemodule;
            $report->pdfannotatorname = $this->name;
            
            // who wrote what and when?
            if ($report->visibility === 'anonymous') {
                $report->commentauthor = get_string('anonymous', 'pdfannotator');
            } else { // the try catch might be unnecessary if you tick 'anonymise userinfo' during backup
                try {
                    $report->commentauthor = get_username($report->commentauthor);
                } catch (Exception $ex) {
                    $report->commentauthor = get_string('unknownuser', 'pdfannotator');
                } 
            }
            $report->commenttime = userdate($report->commenttime, $format = '', $timezone = 99, $fixday = true, $fixhour = true);
            
            // who reported it and when?
            $report->userid = get_username($report->userid);
            $report->timereported = userdate($report->timecreated, $format = '', $timezone = 99, $fixday = true, $fixhour = true);
            $report->seen = $report->seen;
            
            $this->reports[] = new pdfannotator_report_info($report); // create renderable and save it in the annotator
            
        }
           
    }
    /**
     * This method returns an array of stdClass objects, each of which represents/mirrors
     * a report record in this course.
     * 
     * @global type $DB
     * @param type $courseid
     * @return type
     */
    public function retrieve_reports($courseid) {
        
        global $DB;
        
        $a = array();
        $a[] = $courseid;
        $a[] = $this->id;
        
        $sql = "SELECT r.*, c.annotationid, c.userid `commentauthor`, c.content `commentcontent`, c.timecreated `commenttime`, c.visibility FROM {pdfannotator_reports} r JOIN {pdfannotator_comments} c ON r.commentid = c.id WHERE r.courseid = ? AND r.pdfannotatorid = ?";
        
        return $DB->get_records_sql($sql, $a);
        
    }
    
    public function get_hidden_reports() {
        
        global $DB;
        
        $a = array($this->id, 1);
        $sql = "SELECT r.*, c.annotationid, c.userid `commentauthor`, c.content `commentcontent`, c.timecreated `commenttime`, c.visibility FROM {pdfannotator_reports} r JOIN {pdfannotator_comments} c ON r.commentid = c.id WHERE r.pdfannotatorid = ? AND r.seen = ?";
        
        return $DB->get_records_sql($sql, $a);
        
    }
    
    public function get_report_list($courseid){ 
        
        global $DB;
        $reports = $DB->get_records('pdfannotator_reports',array('course'=>$courseid));
        
        // ... convert each into an object and collect all of them in the $reports array attribute
        $result = array();
        foreach ($reports as $data){
            $this->reports[] = new report($data->id);
        }
        return $result;
        
    }
    
    /**
     * Method retrieves an array of all questions asked in this annotator during the past week
     * and saves it in the annotator's "lastesquestions" attribute
     * 
     * @global type $DB
     * @param int $timespan number of days
     */
    public function set_latest_questions($timespan) {
        
        global $DB;
        
        switch($timespan) {
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
        
        $a = array();
            $a[] = $this->id;
            $a[] = strtotime($timestring); // strtotime("-1 week");
        
        $sql = "SELECT a.id as annotationid, a.page, c.id as commentid, c.content FROM {pdfannotator_annotationsneu} a JOIN {pdfannotator_comments} c ON c.annotationid = a.id WHERE c.isquestion AND a.pdfannotatorid = ? AND c.timecreated >= ?";
        
        $this->latestquestions[] = $DB->get_records_sql($sql, $a);
        
    }
    
    /**
     * Method retrieves selected attributes of all of this user's questions (i.e. annotations with initial comments)
     * from db and creates a pdfannotator_question_info object for each. These are saved in the annotators
     * myquestions attribute.
     * 
     * @global type $DB
     * @global type $USER
     * @return type array of annotation objects
     */
    public function set_myQuestions() {
        
        global $DB;
        
        $questions = $this->get_users_questions(); // array of stdClassObjects containing annotation id, 
        
        foreach($questions as $question) {
            
            $question->coursemoduleid = $this->coursemodule;
            $question->pdfannotatorname = $this->name;
            $question->answers = self::get_answers_to_this_question($question->id);
            
            foreach($question->answers as $answer) {
                $answer = new pdfannotator_answer_info($answer);
            }
            
            $this->myquestions[] =  new pdfannotator_question_info($question);
        }
        
    }
    /**
     * Helper Method
     * 
     * @global type $DB
     * @global type $USER
     * @return type
     */
    private function get_users_questions() {
        
        global $DB;
        global $USER;
        
        $a = array();
        $a[] = $USER->id;
        $a[] = $this->id;
        
        $sql = "SELECT a.id, a.page, c.content FROM {pdfannotator_annotationsneu} a JOIN {pdfannotator_comments} c ON c.annotationid = a.id WHERE a.userid = ? AND c.isquestion AND a.pdfannotatorid = ?";
        
        return $DB->get_records_sql($sql, $a);
    }
    
    
    private static function get_answers_to_this_question($annotationid) {
        global $DB;
        return $DB->get_records('pdfannotator_comments',array('annotationid'=>$annotationid, 'isquestion' => 0),null,'id, content, timecreated, isdeleted, seen, userid');
    }
    

    private function __construct2($q) {
               
        $this->questionid = $q->id;
        
        // where is the user's question?
        $this->coursename = get_coursename($q->documentid);
        $this->pdfannotatorname = get_pdfannotator_instance_name($q->documentid);
        $this->page = $q->pageid; // ist die page numer -> in db umbenennen, sonst verwirrend, da keine id

        // what was the question and when was it asked?
        $this->questioncontent = $q->content;
        $this->userdatetime = userdate($q->timecreated, $format = '', $timezone = 99, $fixday = true, $fixhour = true);// when was the original question posted?

        // what was answered and when?
        $this->answers = pdfannotator_answer_info::make_list($this->questionid);
  
    }
    
    
    public static function get_answers($annotationid) {
        global $DB;
        return $DB->get_records('pdfannotator_comments',array('annotationid'=>$annotationid, 'isquestion'=>0),null,'id, content, timecreated');
    }
    
    /**
     * Method retrieves all posts from db that were posted by the specified user
     * in the current pdfannotator
     * 
     * @global type $DB
     * @param type $userid
     * @return type
     */
    public function get_posts_by_user($userid) {
        
        global $DB;
        
        $sql = "SELECT c.id as commid, c.annotationid, c.content, a.page FROM {pdfannotator_comments} c JOIN {pdfannotator_annotationsneu} a ON c.annotationid = a.id WHERE c.userid = ? AND a.pdfannotatorid = ?";
        $a = array();
        $a[] = $userid;
        $a[] = $this->id;
        
        $records = $DB->get_records_sql($sql, $a);
        return $records;
    }
    
    public static function useVotes($documentid){
        global $DB;
        return $DB->record_exists('pdfannotator', array('id' => $documentid, 'usevotes' => '1'));
    }
    
}
