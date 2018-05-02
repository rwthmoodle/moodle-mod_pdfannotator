<?php
/**
 * This file contains the definition of renderable classes in the pdfannotator module.
 * The renderables will be replaced by templatables but are still used by the latter.
 * 
 * @package   mod_pdfannotator
 * @copyright 2018 RWTH Aachen, Anna Heynkes (see README.md)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * 
 */
defined('MOODLE_INTERNAL') || die();

require_once('model/comment.class.php');

class pdfannotator_comment_info implements renderable {

    public $pdfname;
    public $page;
    public $datetime;
    public $author;
    public $content;

    
    /**
     * Methode gibt ein Objekt zurueck, welches die Informationen
     * Tag und Zeit, Verfasser und Kommentarinhalt
     * 
     * @param comment $comment
     * @return \pdfannotator_comment_info
     */
    public static function make_from_comment(pdfannotator_comment $comment) { // Klasse umbenennen in pdfannotator_comment
            
        // determine author (possibly anonymous)
        if ($comment->visibility === 'public') {
              
            $authorID = pdfannotator_comment::getAuthorID($comment->id); 
            $author = get_username($authorID);
            
        } else {
            $author = get_string('anonymous', 'pdfannotator');
        }
        
        // create info object
        $info = new pdfannotator_comment_info();
        $info->datetime = pdfannotator_comment::getUserDateTime($comment->id);
        $info->author = $author;
        $info->content = $comment->content;

        return $info;
    }
    
        

    public static function get_location() {
        
        
        
    }
    
}

/**
 * 
 */
class pdfannotator_report_info implements renderable {

    public $reportid; // id of entry in mdl_pdfannotator_reports
    
    // where is the reported comment?
    public $coursename; // Course
    public $topic; // Topic
    public $pdfannotatorname; // Pdf name chosen by the user
    public $page;
    
    // who wrote what and when?
    public $commentauthor;
    public $commentcontent;
    public $userdatetime; // when was the original comment posted?
    
    // who reported it and why?
    public $reportcontent;
    public $reportinguser;
    public $timereported; // when was it reported?
    
    public $seen;
    
    //link info
    public $link;

    public function __construct($report) {
        
        $this->reportid = $report->id; // id of entry in mdl_pdfannotator_reports
        
        // where is the reported comment?
        $this->coursename = $report->coursename;
        $this->topic = $report->topic; 'Testtopic'; // Topic
        $this->pdfannotatorname = $report->pdfannotatorname;
        $this->page = $report->page;
    
        // who wrote what when?
        $this->commentauthor = $report->commentauthor; // may be 'anonymous'
//        $this->commentcontent = "<a href='/moodle/mod/pdfannotator/view.php?id=$report->coursemodule&page=$this->page&annoid=$report->annotationid&commid=$report->commentid'>".$report->commentcontent."</a>";
        $this->comment = $report->commentcontent;
        $this->link = new moodle_url('/mod/pdfannotator/view.php', array('id' => $report->coursemodule, 'page' => $this->page, 'annoid' => $report->annotationid, 'commid' => $report->commentid));
        $this->newlink = "view.php?id=$report->coursemodule&page=$this->page&annoid=$report->annotationid&commid=$report->commentid";
//          $this->link = "/moodle/mod/pdfannotator/view.php?id=$report->coursemodule&page=$this->page&annoid=$report->annotationid&commid=$report->commentid";
        $this->userdatetime = $report->commenttime; // when was the original comment posted?
    
        // who reported it why?
        $this->report = $report->message; // $this->reportcontent
        $this->reportinguser = $report->userid;
        $this->timereported = $report->timereported;
        
        $this->seen = $report->seen;
        
    }
    
    public static function make_list($reports) {
        $list = array();
        foreach($reports as $report) {
            $newentry = new pdfannotator_report_info($report);
            $list[] = $newentry;
        }
        return $list;
    }
    
    public static function get_course_name($courseid) {
        
        global $DB;
        return $DB->get_field('course', 'fullname', array('id' => $courseid), $strictness=MUST_EXIST);
                
    }
    
    
    public static function get_pdfannotator_instance_name($pdfannotatorid) {
        
        global $DB;
        return $DB->get_field('pdfannotator', 'name', array('id' => $pdfannotatorid), $strictness=MUST_EXIST);
        
    }
}
    
    /************************************************ Table displays questions that received answered ************************************************/
    
    
    class pdfannotator_question_info implements renderable {

        public $questionid; // id of entry in mdl_pdfannotator_annotations

        // where is the user's question?
        public $pdfannotatorname; // Pdf name
        public $page;

        // what was the question?
        public $questioncontent;
        
        // what was answered?
        public $answers; // 2D array

    /**
     * This method takes an array of standard objects representing a question each.
     * 
     * @param type $questions array of stdClass Objects, each representing a question and containing (annotation)id,documentid, pageid,content,timecreated
     * @return \pdfannotator_question_info
     * 
     */
    public static function make_list($questions) {
        $list = array();
        foreach($questions as $question) {
            $list[] = new pdfannotator_question_info($question);
        }
        return $list;
    }
    
    public function __construct($question) {
        
        // annotation id of the question
        $this->questionid = $question->id;
        
        // where is the user's question?
        $this->pdfannotatorname = $question-> pdfannotatorname;
        $this->page = $question->page; // XXX ist die page numer -> in db umbenennen, sonst verwirrend, da keine id

        // what was asked and answered?
        $this->questionlink = new moodle_url('/mod/pdfannotator/view.php', array('id' => $question->coursemoduleid, 'page' => $this->page, 'annoid' => $this->questionid));
    // "/moodle/mod/pdfannotator/view.php?id={$question->coursemoduleid}&page={$this->page}&annoid={$this->questionid}";
        $this->questioncontent = $question->content; // "<a href='/moodle/mod/pdfannotator/view.php?id={$question->coursemoduleid}&page={$this->page}&annoid={$this->questionid}'>" . $question->content . "</a>";
        $this->answers = $question->answers;
  
    }
    
}
    
class pdfannotator_answer_info implements renderable {

        private $answerid;
        private $answer;
        private $timeanswered;
        private $isdeleted;
        private $userid;
        private $seen;
    
    public function __construct($answer) {
        
        $this->answerid = $answer->id;
        
        // what was answered and when?
        $this->answer = $answer->content;
        $this->isdeleted = $answer->isdeleted;
        $this->userid = $answer->userid;
        $this->seen = $answer->seen;
        
    }
    
    public function get_answer_content() {
        return $this->answer;
    }
    
    public function get_answertime() {
        return $this->timeanswered;
    }
    
    public function get_link() {
        return $this->link;
    }
    
}
    
    
