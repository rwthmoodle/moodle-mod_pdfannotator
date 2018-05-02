<?php

/**
 * This class represents user reports of comments deemed inappropriate.
 * 
 * @package   mod_pdfannotator
 * @copyright 2018 RWTH Aachen, Rabea de Groot and Anna Heynkes(see README.md)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * 
 */
require_once($CFG->dirroot.'/mod/pdfannotator/locallib.php');

class report {
    
    public $id; // id of entry in mdl_pdfannotator_reports
    
    // attributes concerning the reported comment
    public $commentid;
    public $course;
    public $pdfannotatorid;
    public $page;
    
    // attributes concerning the report(ing user)
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
        
        // get db record of this report
        global $DB;
        $result = $DB->get_record('pdfannotator_reports', array('id'=>$id), $fields='*', $strictness=MUST_EXIST);
  
        // set attributes...
        $this->id = $id;
        
        // ... concerning the reported comment
        $this->commentid = $result->commentid;
        $this->course = $result->courseid;
        $this->pdfannotatorid = $result->pdfannotatorid;
        $this->page = $result->page;
                    
        // ... concerning the report and reporting user
        $this->timecreated = $result->timecreated;
        $this->userid = $result->userid; // getName
        $this->message = $result->message;
           
        return $this;
            
    }
    /**
     * This function creates a new entry/record in the 'pdfannotator_reports' table
     * 
     * 
     * @global type $DB
     * @global type $USER
     * @param type $report
     * @param type $commentid
     * @return type
     */
    public static function create($report, $reportedcomment){ 
        
        global $DB;
        global $USER;
        
        $dataRecord = new stdClass();
        $dataRecord->commentid = $report->commentid; 
        $dataRecord->courseid = $report->course;
        $dataRecord->pdfannotatorid = $report->pdfid;
        $dataRecord->page = $reportedcomment->page;
        $dataRecord->message = $report->introduction;
        $dataRecord->userid = $USER->id;
        $dataRecord->timecreated = time(); // Moodle-Methode: DateTime::getTimestamp(); 
        
        // create a new record and return its id, which is created by autoincrement:        
        $reportID = $DB->insert_record('pdfannotator_reports', $dataRecord, $returnid=true);
        return $reportID;
        
    }
    public static function getAuthorName($reportid) {
        $authorID = self::getAuthorID($reportid);
        return get_username($authorID);
    }
    
    public static function getAuthorID($reportid){   
        global $DB;
        return $DB->get_field('pdfannotator_reports', 'reportinguser', array('id' => $reportid), $strictness=MUST_EXIST);
    }
    /**
     * 
     * @param type $reportId
     * @return type
     */
    public static function getUserDateTime($reportid) {
        
        $timestamp = self::getTimestamp($reportid);
        $userDateTime = userdate($timestamp, $format = '', $timezone = 99, $fixday = true, $fixhour = true); // method in lib/moodlelib.php
        return $userDateTime;
    }
    /**
     * 
     * @global type $DB
     * @param type $commentId
     * @return type
     */
    public static function getTimestamp($reportid) {
        global $DB;
        return $DB->get_field('pdfannotator_reports', 'timecreated', array('id' => $reportid), $strictness=MUST_EXIST);
    }
    
    public static function getContent($reportid) {
        global $DB;
        // TODO: Replace IGNORE_MISSING with MUST_EXIST (first: entering a message into the report form must become obligatory)
        return $DB->get_field('pdfannotator_reports', 'message', array('id' => $reportid), $strictness=IGNORE_MISSING);  
    }
    
}