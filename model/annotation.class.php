<?php
/**
 * Description of Annotation
 * Methods:
 * create_annotation
 * delete
 * read (abstract)
 * 
 * @package   mod_pdfannotator
 * @copyright 2018 RWTH Aachen, Rabea de Groot and Anna Heynkes(see README.md)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * 
 */
abstract class annotation {
    
    public $id;
    public $pdfannotatorid; // id of the pdfannotator instance
    public $page;
    public $userid;
    public $annotationtypeid;
    public $data;
    public $timecreated;
    public $timemodified;
    
    public function __construct($id) {
        global $DB;
        $record = $DB->get_record('pdfannotator_annotationsneu',['id' => $id],'*',MUST_EXIST);
        
        $this->id= $id;
        $this->pdfannotatorid = $record->pdfannotatorid;
        $this->page = $record->page;
        $this->userid = $record->userid;
        $this->annotationtypeid = $record->annotationtypeid;
        $this->data = json_decode($record->data);
        $this->timecreated = $record->timecreated;
        $this->timemodified = $record->timemodified;
    }
    /**
     * This method creates a new record in the database table named mdl_pdfannotator_annotations and returns its id
     * 
     * @global type $DB
     * @global type $USER
     * @param type $documentid specifies the pdf file to which this annotation belongs
     * @param type $pageid specifies the page within that pdf file
     * @param type $type child class (highlight, strikeout, area, textbox, drawing, comment or point)
     * @param type $itemid identifies the record in the respective child class table, e.g. highlights
     * @return int (or boolean false)
     */
    public static function create_annotation($documentid, $pageid, $type, $itemid){
        
        global $DB;
        global $USER;
        $dataRecord = new stdClass();
        $dataRecord->userid = $USER->id;
        $dataRecord->documentid = $documentid;
        $dataRecord->pageid = $pageid;
        $dataRecord->type = $type;
        $dataRecord->itemid = $itemid;
        // A: create a new record in the table named 'annotations' and return its id, which is created by autoincrement:
        $annotationID = $DB->insert_record('pdfannotator_annotations', $dataRecord, $returnid=true);
        return $annotationID;
        
    }
    
  
    /**
     * Method updates data attribute (consisting of width, color and lines)
     * in mdl_pdfannotator_drawings after a drawing was shifted in position
     * 
     * @global type $DB
     * @param type $annotationid
     * @param type $newdata
     * @return type int 1 for success
     */
    public static function update($annotationid, $newdata) {
        
        global $DB;
        $dataobject = array("id" => $annotationid, "data" => json_encode($newdata));
        return $DB->update_record('pdfannotator_annotationsneu', $dataobject, $bulk=false);
        
    }
    
        /**
         * Method deletes the specified annotation and all comments attached to it,
         * if the user is allowed to do so
         * 
         * @global type $DB
         * @param type $annotationId
         * @param type $cmid
         * @return boolean
         */
        public static function delete($annotationId, $cmid){
        
        global $DB;
        $table1 = 'pdfannotator_annotationsneu';
        $table2 = 'pdfannotator_comments';
        
        if (! $DB->record_exists($table1, array('id' => $annotationId)) ) {
            return false;
        }
        
        // Check user rights to delete this annotation and all its attached comments
        $deletionAllowed = annotation::deletionAllowed($annotationId, $cmid);
        
        // Delete annotation
        if ($deletionAllowed[0] === true) {
            
            $success = $DB->delete_records($table1, array("id" => $annotationId)); 
            
            if ($success == null || $success != 1) {
                return false;
                
            }
            
            //Delete all comments of this annotation
            //But first insert reported comments into the archive
            $comments = $DB->get_records('pdfannotator_comments', array("annotationid" => $annotationId));
            foreach($comments as $commentdata){
                //if the comment was not deleted, but reported, then insert the record into the archive
                if($commentdata->isdeleted == 0 && $DB->record_exists('pdfannotator_reports', ['commentid' => $commentdata->id])){
                    unset($commentdata->id);
                    $DB->insert_record('pdfannotator_comments_archiv',$commentdata);
                }
            }
            $success = $DB->delete_records($table2, array("annotationid" => $annotationId)); 
            
            if ($success == null || $success != 1) {
                return false;
                
            }
            return true;
        }else{
            return $deletionAllowed[1];
        }
        
        
    }
    
    
    /**
     * Method checks whether the annotation as well as possible comments attached to it
     * belong to the current user
     * 
     * @return
     * 
     */
    public static function deletionAllowed($annotationId, $cmid) {
        
        global $DB;
        $table = 'pdfannotator_annotations';
        
        global $USER;
        $thisuser = $USER->id;
        $annotationAuthor = annotation::getAuthor($annotationId);
        
        $result = [];
        
        // If user has admin rights with regard to annotations/comments: Allow deletion
        if (!$cm = get_coursemodule_from_id('pdfannotator', $cmid)) {
            error("Course module ID was incorrect");
        }
        $context = context_module::instance($cm->id);
        
        if (has_capability('mod/pdfannotator:administrateuserinput', $context)) {
            $result[] = true;
            return $result;
        }
        
        // If not:

        // Check user permission to delete the annotation itself
        if ($thisuser != $annotationAuthor) {
            $result[] = false;
            $result[] = get_string('onlyDeleteOwnAnnotations', 'pdfannotator');
            return $result;
        }        
        // Check whether other people have commented this annotation
        $attached_comments = pdfannotator_comment::find($annotationId);
        if ($attached_comments && $attached_comments !== null) {
            foreach ($attached_comments as $comment) {
                if ($thisuser != $comment->userid) {
                    $result[] = false;
                    $result[] = get_string('onlyDeleteUncommentedPosts', 'pdfannotator');
                    return $result;
                }
            }
        }  
        
        $result[] = true;
        return $result;
    }
    /**
     * Method checks whether the annotation in question may be shifted in position.
     * It returns true if the annotation was made by the user who is trying to shift it
     * and not yet commented by other people.
     * 
     * @global type $USER
     * @param type $annotationId
     * @return boolean
     */
    public static function shiftingAllowed($annotationId) {
        
        global $DB;
        global $USER;
        
        $annotationAuthor = annotation::getAuthor($annotationId);
        
        // Check user permission to delete the annotation itself
        if ($USER->id != $annotationAuthor) {
            return false;
        }        
        
        // Check whether other people have commented this annotation
        if ($DB->record_exists_select('pdfannotator_comments', "annotationid = ? AND userid != ?", array($annotationId, $USER->id))) {
            return false;
        }        
        return true;
    }
    /**
     * Method takes the annotation id and returns the type specific table name, e.g. pdfannotator_drawings
     * 
     * @param type $annotationId
     * @return string
     */
    public static function getTypeTable($annotationId) {
        
        $annotation_type = annotation::getType($annotationId);
        
        switch($annotation_type) {
            case 'area':
                $tablename = 'pdfannotator_areas';
                break;
            case 'drawing':
                $tablename = 'pdfannotator_drawings';
                break;
            case 'highlight':
                $tablename = 'pdfannotator_highlights';
                break;
            case 'point':
                $tablename = 'pdfannotator_points';
                break;
            case 'strikeout':
                $tablename = 'pdfannotator_strikeouts';
                break;
            case 'textbox':
                $tablename = 'pdfannotator_textboxes';
                break;
        }
        return $tablename;
    }
    /**
     * Method takes an annotation's id and returns the user id of its author
     * 
     * @global type $DB
     * @param type $itemid
     * @return type
     */
    public static function getAuthor($annotationId){
            
            global $DB;
            return $DB->get_field('pdfannotator_annotationsneu', 'userid', array('id' => $annotationId), $strictness=MUST_EXIST);
            
    }
    /**
     * Method takes an annotation's id and returns its type, e.g. 3 for 'highlight'
     * 
     * @global type $DB
     * @param type $itemid
     * @return type
     */
    public static function getType($annotationId){            
            global $DB;     
            return $DB->get_field('pdfannotator_annotationsneu', 'annotationtypeid', array('id' => $annotationId), $strictness=MUST_EXIST);           
    }
    /**
     * Method takes an annotation's id and returns the id of the annotator instance in which it was made
     * 
     * @global type $DB
     * @param type $annotationId
     * @return type
     */
    public static function getDocumentID($annotationId){            
            global $DB;
            return $DB->get_field('pdfannotator_annotationsneu', 'documentid', array('id' => $annotationId), $strictness=MUST_EXIST);            
    }
    /**
     * Method takes an annotation's id and returns the page it was made on
     * 
     * @global type $DB
     * @param type $annotationId
     * @return type
     */
    public static function getPageID($annotationId){
        global $DB;
        return $DB->get_field('pdfannotator_annotationsneu', 'page', array('id' => $annotationId), $strictness=IGNORE_MISSING);        
    }
    /**
     * Method takes an annotation's id and returns the content of the underlying question comemnt
     * 
     * @global type $DB
     * @param type $annotationId
     * @return type
     */
    public static function getQuestion($annotationId){
        global $DB;
        $question = $DB->get_record('pdfannotator_comments',['annotationid' => $annotationId,'isquestion'=>1],'content');      
        return $question->content;
    }
    /**
     * Method retrieves selected attributes of all questions, i.e. annotation with initial comments,
     * of this user, from db.
     * 
     * @global type $DB
     * @global type $USER
     * @return type array of annotation objects
     */
    public static function getQuestionsOfUser() {
        global $DB;
        global $USER;
        $thisuser = $USER->id;
        $sql = "SELECT a.id, a.documentid, a.pageid, c.content, c.timecreated FROM {pdfannotator_annotationsneu} a JOIN {pdfannotator_comments} c ON c.annotationid = a.id WHERE a.userid = ? AND c.questioncomment";
        $a = array();
        $a[] = $thisuser;
        $records = $DB->get_records_sql($sql, $a);
        return $records;
    }

}