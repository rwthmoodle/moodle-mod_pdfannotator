<?php
/**
 * In this file, incoming AJAX request from the Store Adapter in index.js are handled.
 * These requests concern the creation, retrieval and deletion of annotations
 * and comments as well as the editing/shifting of annotations and the reporting
 * of comments that are deemed inappropriate.
 * 
 * The file also handles incoming AJAX requests from teacheroverview.js and (in the future)
 * studentoverview.js, which control the behaviour of the teacher/student overview page.
 * These requests are concerned with
 * 1. teacheroverview: hide, redisplay and delete reports
 * 2. studentoverview: hide, redisplay and delete answer notifications (yet to be completed)
 *
 * @package   mod_pdfannotator
 * @copyright 2018 RWTH Aachen, Rabea de Groot and Anna Heynkes (see README.md)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../config.php');
require_once('model/annotation.class.php');
require_once('model/comment.class.php');
require_once('reportform.php');

$documentid = required_param('documentId', PARAM_PATH);
$action = required_param('action', PARAM_TEXT); // '$action' determines what is to be done; see below:

$pdfannotator = $DB->get_record('pdfannotator', array('id'=>$documentid), '*', MUST_EXIST);
$cm = get_coursemodule_from_instance('pdfannotator', $documentid, $pdfannotator->course, false, MUST_EXIST);
require_course_login($pdfannotator->course, true, $cm);


/******************************************** 1. HANDLING ANNOTATIONS *******************************************/
/****************************************************************************************************************/

/******************************************** Retrieve all annotations from db for display *******************************************/

if ($action === 'read') {
    
    global $DB;
    global $USER;
    
    $page = optional_param('page_Number', 1, PARAM_INT); // default page number is 1

    $annotations = array();
    
    $records = $DB->get_records('pdfannotator_annotationsneu', array('pdfannotatorid'=>$documentid, 'page'=>$page));
    
    foreach($records as $record) {
        
            $entry = json_decode($record->data); // stdClass Object containing data that is specific to the respective annotation type
            
            // add general annotation data
            $entry->type = get_name_of_annotationtype($record->annotationtypeid);
                // The following 3 lines can be removed after deletion of the original annotation tables
                if ($entry->type == 'pin') {
                    $entry->type = 'point';
                }
            $entry->class = "Annotation";
            $entry->page = $page;
            $entry->uuid = $record->id;
            
            $entry->owner = $record->userid == $USER->id;
            
            $annotations[] = $entry;
    }
    
    $data = array('documentId' => $documentid, 'pageNumber' => $page, 'annotations' => $annotations);
    echo json_encode($data);
    
}

/******************************************** Select a single annotation from db for shifting *******************************************/

if ($action === 'readsingle') {
    
    global $DB;
    
    $annotationid = required_param('annotationId', PARAM_INT);
    
    $page = optional_param('page_Number', 1, PARAM_INT); // default page is 1
    
    $records = $DB->get_records('pdfannotator_annotationsneu', array('id'=>$annotationid));

    foreach($records as $record) {
        
            $annotation = json_decode($record->data); // stdClass Object containing data that is specific to the respective annotation type
            
            // add general annotation data
            $annotation->type = get_name_of_annotationtype($record->annotationtypeid);
                // The following 3 lines can be removed after deletion of the original annotation tables
                if ($annotation->type == 'pin') {
                    $annotation->type = 'point';
                }
            $annotation->class = "Annotation";
            $annotation->page = $record->page;
            $annotation->uuid = $record->id;
            
            $data = array('documentId' => $documentid,'annotation' => $annotation);
            echo json_encode($data);
    
            return;
    }
       
}

/******************************************** Save (1) and display (2) a new annotation *******************************************/

if ($action === 'create') {

    global $DB;
    global $USER;
    
    $table = "pdfannotator_annotationsneu";
    
    $pageid = required_param('page_Number', PARAM_INT);
    
    // 1.1 Get the annotation data and decode the json wrapper
    $annotationJS = required_param('annotation', PARAM_TEXT);
    $annotation = json_decode($annotationJS,true);
    
    // 1.2 Determine the type of the annotation
    $type = $annotation['type'];
    $typeid = get_id_of_annotationtype($type);
    
    // 1.3 Set the type-specific data of the annotation
    $data = [];
   
   switch($type) {
        
        case 'area':
            $data['x'] = $annotation['x'];
            $data['y'] = $annotation['y'];
            $data['width'] = $annotation['width'];
            $data['height'] = $annotation['height'];
            break;
        
        case 'drawing':
            $data['width'] = $annotation['width'];
            $data['color'] = $annotation['color'];
            $data['lines'] = $annotation['lines'];
            break;
        
        case 'highlight':
            $data['color'] = $annotation['color'];
            $data['rectangles'] = $annotation['rectangles'];
            break;
        
        case 'point':
            $data['x'] = $annotation['x'];
            $data['y'] = $annotation['y'];
            break;
        
        case 'strikeout':
            $data['color'] = $annotation['color'];
            $data['rectangles'] = $annotation['rectangles'];
            break;
        
        case 'textbox':
            $data['x'] = $annotation['x'];
            $data['y'] = $annotation['y'];
            $data['width'] = $annotation['width'];
            $data['height'] = $annotation['height'];
            $data['size'] = $annotation['size'];
            $data['color'] = $annotation['color'];
            $data['content'] = $annotation['content'];
            break;
    }
    $insertiondata = json_encode($data);
    
    // 1.4 Insert a new record into mdl_pdfannotator_annotationsneu
    $newannotationid = $DB->insert_record($table, array("pdfannotatorid" => $documentid, "page" => $pageid, "userid" => $USER->id, "annotationtypeid" => $typeid, "data" => $insertiondata, "timecreated" => time()), true, false);
    
    // 2. If the insertion was successfull...
    if (isset($newannotationid) && $newannotationid !== false && $newannotationid > 0) {
        
        // 2.1 set additional data to send back to the client
            $data['uuid'] = $newannotationid;
            $data['type'] = $type;
            if ($type == 'pin') {
                $data['type'] = 'point';
            }
            $data['class'] = "Annotation";
            $data['page'] = $pageid;
        
        // 2.2 and send it off for display
            echo json_encode($data);

    } else { // If not, return an error message
        echo json_encode(['status' => 'error']);

    }

}

/******************************************** Update an annotation *******************************************/

if ($action === 'update') {
    
    // 1. Get the id of the annotation that is to be shifted in position
    $annotationid = required_param('annotationId', PARAM_INT);
    
    // 2. Get the updated annotation data received for storage and decode its json wrapper
    $dataJS = required_param('annotation', PARAM_TEXT);
    $data = json_decode($dataJS,true);

    // 3. Check whether the current user is allowed to shift this annotation, i.e. whether it's his and hasn't been commented by other people
    if(annotation::shiftingAllowed($annotationid)) {
        
        $annotation = $data['annotation'];
        $type = $annotation['type'];
        $newdata = [];
        
        // 4. If so, update the annotations 'data' attribute in mdl_pdfannotator_annotationsneu.
        //    Note that while only part of the data may change, the whole JSON-string has to be construced anew.
        //    e.g. drawing: Only the 'lines' actually change, but the database stores them together with width and color in a single JSON-string called 'data'.
        switch($type) {
        
            case 'area':
                $newdata['x'] = $annotation['x'];
                $newdata['y'] = $annotation['y'];
                $newdata['width'] = $annotation['width'];
                $newdata['height'] = $annotation['height'];
                break;

            case 'drawing':
                $newdata['width'] = $annotation['width'];
                $newdata['color'] = $annotation['color'];
                $newdata['lines'] = $annotation['lines'];
                break;

            case 'point':
                $newdata['x'] = $annotation['x'];
                $newdata['y'] = $annotation['y'];
                break;

            case 'textbox':
                $newdata['x'] = $annotation['x'];
                $newdata['y'] = $annotation['y'];
                $newdata['width'] = $annotation['width'];
                $newdata['height'] = $annotation['height'];
                $newdata['size'] = $annotation['size'];
                $newdata['color'] = $annotation['color'];
                $newdata['content'] = $annotation['content'];
                break;
        }
        
        $success = annotation::update($annotationid, $newdata);
        
        // 5. If the updated data received from the Store Adapter could successfully be inserted in db, send it back for display
        if ($success != null && $success == 1) {
            echo json_encode($data);
        } else {
            echo json_encode(['status' => 'error']);
        }
        
    } else {
        
        echo json_encode(['status' => 'error']);
    }
        
        
}     

/******************************************** Delete an annotation *******************************************/

if ($action === 'delete') {
    
    // get current user
    global $USER;
    $thisuser = $USER->id;

    // get annotation itemid and course module id
    $annotationid = required_param('annotation', PARAM_INT);

    // delete annotation if user is permitted to do so
    $success = annotation::delete($annotationid, $cm->id);

    // For completeness's sake...
    if($success === true){
        echo json_encode(['status' => 'success']);
    
    }else{
        echo json_encode(['status' => 'error', 'reason' => $success]);
    
    }

}

/******************************************** Retrieve all questions of a specific page *******************************************/

if ($action === 'getQuestions') {
    
    $pageid = optional_param('page_Number', 1, PARAM_INT); // default is 1

    $questions = pdfannotator_comment::getQuestions($documentid, $pageid);
    
    echo json_encode($questions);
    
}

/******************************************** 2. HANDLING COMMENTS *******************************************/
/****************************************************************************************************************/

/******************************************** Save a new comment and return it for display *******************************************/

if ($action === 'addComment') {
    
    // Get the annotation to be commented
    $annotationid = required_param('annotationId', PARAM_TEXT);
    $context = context_module::instance($cm->id);
    $PAGE->set_context($context);
    
    // Get the comment data
    $content = required_param('content', PARAM_TEXT);
    $visibility = required_param('visibility', PARAM_TEXT);
    $isquestion = required_param('isquestion', PARAM_TEXT);

    // Insert the comment into the mdl_pdfannotator_comments table and get its record id
    $commentid = pdfannotator_comment::create($documentid, $annotationid, $content, $visibility, $isquestion,$cm, $context);
    
    // Get username or label 'anonymous' // Could perhaps be delegated to a moodle setting
    $username = $USER->username;
    if ($visibility === 'anonymous') {
        $username = get_string('anonymous', 'pdfannotator');
    }

    // If successful, create a comment array and return it as json
    if (isset($commentid) && $commentid !== false && $commentid > 0) {

        global $USER;
        $comment = [];
        $comment['class'] = 'Comment';
        $comment['uuid'] = $commentid;
        $comment['annotation'] = $annotationid;
        $comment['content'] = $content;
        $comment['userid'] = $USER->id;
        $comment['username'] = $username;
        $comment['timecreated'] = pdfannotator_comment::getUserDateTime($commentid);
        $comment['visibility'] = $visibility;
        $comment['isquestion'] = $isquestion;
        
        echo json_encode($comment);

    } else {
        if($commentid == -1){
            echo json_encode(['status' => '-1']);
        }else{
            echo json_encode(['status' => 'error']);
        }
    }
     
}

/******************************************** Retrieve all comments for a specific annotation from db *******************************************/

if ($action === 'getComments') {
    
    $annotationid = required_param('annotationId', PARAM_INT);
  
    // Create an array of all comment objects on the specified page and annotation
    $comments = pdfannotator_comment::read($documentid, $annotationid);
    echo json_encode($comments);
    
}

/******************************************** Delete a comment *******************************************/

if ($action === 'deleteComment') {
    
    global $DB;
    global $USER;
    
    $commentid = required_param('commentId', PARAM_INT);
    
    $context = context_module::instance($cm->id);
    
    // 1. Retrieve comment from db (return false if it doesn't exist)
    $comment = $DB->get_record('pdfannotator_comments', array('id' => $commentid), '*', $strictness=IGNORE_MISSING);
    
    if (!$comment) {
        echo json_encode(['status' => 'error']);
        return;
    }
    
    // 2. To delete or not to delete, that is the question
    $annotationid = $comment->annotationid;
    $isquestion = $comment->isquestion;

    $isteacher = has_capability('mod/pdfannotator:administrateuserinput', $context);
    $isstudent = !$isteacher;
    
    $wasanswered = $DB->record_exists_select('pdfannotator_comments', "annotationid = ? AND timecreated > ?", array($annotationid, $comment->timecreated));
    $wasreported = $DB->record_exists('pdfannotator_reports', ['commentid' => $commentid]);
    
    $success = 0;
    
    $toBeDeletedAsWell = [];
    
    // 2.1 Students may delete their own comments as long as no one else has answered.
    if ($isstudent && $USER->id == $comment->userid) {
        
        if($wasanswered) {
            echo json_encode(['status' => 'error']);
            return;
        }
        $success = $DB->delete_records('pdfannotator_comments', array("id" => $commentid));
        
    }
       
    // 2.1 Teachers may delete any comment.
    if ($isteacher) {
        
        // If it's an initial comment, i.e. a question 
        // (normally in this case action equals 'delete' and not 'deletecomment' but to be safe 
        if ($isquestion) {
            
            // Delete the question and all answers to it
            $comments = $DB->get_records('pdfannotator_comments', array("annotationid" => $annotationid));
            foreach($comments as $commentdata){
                //if the comment was not deleted, but reported, then insert the record into the archive
                if($commentdata->isdeleted == 0 && $DB->record_exists('pdfannotator_reports', ['commentid' => $commentdata->id])){
                    unset($commentdata->id);
                    $DB->insert_record('pdfannotator_comments_archiv',$commentdata);
                }
            }
            $success = $DB->delete_records('pdfannotator_comments', array("annotationid" => $annotationid));
                    
                // If that worked, delete the underlying annotation
                if($success == 1) {   
                    $success = $DB->delete_records('pdfannotator_annotationsneu', array("id" => $annotationid));
                }
        
        
        } else {
            
            // If the comment was answered, mark it as deleted for a special display
            if ($wasanswered == 1) {
                //Before updating, insert the comment into the archive, if it was reported
                if($wasreported){
                    $reportedComment = clone $comment;
                    unset($reportedComment->id);
                    $DB->insert_record('pdfannotator_comments_archiv',$reportedComment);
                }
                $success = $DB->update_record('pdfannotator_comments', array("id" => $commentid, "isdeleted" => 1), $bulk=false);
                
            // If not, just delete it
            } else {
                
                // But first: Check if the predecessor was already marked as deleted, too and if so, delete it completely
                
                $sql = "SELECT id, isdeleted from {pdfannotator_comments} WHERE annotationid = ? AND isquestion = ? AND timecreated < ? ORDER BY id DESC";
                $params = array($annotationid, 0, $comment->timecreated);
                
                $predecessors = $DB->get_records_sql($sql, $params);
                 
                if (!empty($predecessors)) {      
                    foreach($predecessors as $predecessor) {
                        if($predecessor->isdeleted != 0) {
                            $workingfine = $DB->delete_records('pdfannotator_comments', array("id" => $predecessor->id));
                            if ($workingfine != 0) {
                                $toBeDeletedAsWell[] = $predecessor->id;
                            }
                        } else {
                            break;
                        }
                    }
                }
                //Before deleting: If the comment was reported, the comment should be inserted in the archive
                if($wasreported){
                    $reportedComment = clone $comment;
                    $DB->insert_record('pdfannotator_comments_archiv',$reportedComment);
                }
                // Now delete the selected comment
                $success = $DB->delete_records('pdfannotator_comments', array("id" => $commentid)); 
                
            }
        }
    }
    
    if($success == 1){     
        
        echo json_encode(['status' => 'success', 'wasanswered' => $wasanswered, 'followups' => $toBeDeletedAsWell]);
    
    } else {
        echo json_encode(['status' => 'error']);
    }
        
}

/******************************************** Report a comment (currently unused) *******************************************/

if ($action === 'reportComment') {
    
    $PAGE->context = $context;
    echo "Arrived in action.php: reportComment";
    
    require_once($CFG->dirroot.'/mod/pdfannotator/reportform.php');

    $mform = new pdfannotator_reportform();

    if ($mform->is_cancelled()) {
        echo "Form was cancelled";
        
    } else if ($formdata = $mform->get_data()) {
        echo "Form data received";
    } else {

        echo $output->header();
        echo $output->heading("Fancy heading");
        
        $mform->display();
        
        echo $output->footer();
        
        exit();
    }
}
    
 /******************************************** Vote for a comment *******************************************/

if ($action === 'voteComment') {
    
    global $DB;

    $commentid = required_param('commentid', PARAM_INT); 
    
            $sql = "SELECT annotationid FROM {pdfannotator_comments} WHERE id = ?";
            $params = [];
            $params[] = $commentid;
    
    $annotationid = $DB->get_field_sql($sql, $params, MUST_EXIST);
    
            $sql = "SELECT pdfannotatorid FROM {pdfannotator_annotationsneu} WHERE id = ?";
            $params = [];
            $params[] = $annotationid;
    
    $pdfannotatorid = $DB->get_field_sql($sql, $params, MUST_EXIST);
    
    $numberVotes = pdfannotator_comment::insertVote($documentid, $commentid);
    
    if($numberVotes){
        echo json_encode(['status' => 'success', 'commentid' => $commentid, 'pdfannotatorid' => $pdfannotatorid, 'numberVotes'=>$numberVotes]);
    
    } else {
        echo json_encode(['status' => 'error']);
    }
    
}

/******************************************** 3. HANDLING REPORTS (teacheroverview page) *******************************************/
/****************************************************************************************************************/

/**************************************** 3.1 Mark a report as seen and don't display it any longer *********************************/

if ($action === 'markReportAsSeen') {
    
    require_once($CFG->dirroot.'/mod/pdfannotator/classes/output/teacheroverview.php');
    require_once($CFG->dirroot.'/mod/pdfannotator/model/pdfannotator.php');
    
    global $DB;
    $reportid = required_param('reportid', PARAM_INT);
    $openannotator = required_param('openannotator', PARAM_INT);
     
    if($DB->update_record('pdfannotator_reports', array("id" => $reportid, "seen" => 1), $bulk=false)){
    
        $myrenderer = $PAGE->get_renderer('mod_pdfannotator');
        $templatable = new teacheroverviewUpdateHiddenEntries($pdfannotator->course, $openannotator);
        $newdata = $templatable->export_for_template($myrenderer);

        echo json_encode(['status' => 'success', 'reportid' => $reportid, 'pdfannotatorid' => $documentid, 'newdata' => $newdata]);
    
    } else {
        echo json_encode(['status' => 'error']);
    }
    
}


/**************************************** 3.2 Mark a hidden report as unseen and display it once more *******************************/

if ($action === 'markReportAsUnseen') {
    
    require_once($CFG->dirroot.'/mod/pdfannotator/classes/output/teacheroverview.php');
    require_once($CFG->dirroot.'/mod/pdfannotator/model/pdfannotator.php');
    
    global $DB;
    $reportid = required_param('reportid', PARAM_INT);
    $openannotator = required_param('openannotator', PARAM_INT);
    
    if($DB->update_record('pdfannotator_reports', array("id" => $reportid, "seen" => 0), $bulk=false)){
        
        $myrenderer = $PAGE->get_renderer('mod_pdfannotator');
        $templatable = new teacheroverviewUpdateReports($pdfannotator->course, $openannotator);
        $newdata = $templatable->export_for_template($myrenderer);
        
        echo json_encode(['status' => 'success', 'reportid' => $reportid, 'pdfannotatorid' => $documentid, 'newdata' => $newdata]);
    
    } else {
        echo json_encode(['status' => 'error']);
    }
    
}


/**************************************** 3.3 Delete a displayed or hidden report permanently *******************************/

if ($action === 'deleteReport') {
    
    global $DB;
    
    $reportid = required_param('reportid', PARAM_INT);
    
    if($DB->delete_records('pdfannotator_reports', array("id" => $reportid))){            
        echo json_encode(['status' => 'success', 'reportid' => $reportid, 'pdfannotatorid' => $documentid]);
    
    } else {
        echo json_encode(['status' => 'error']);
    }
    
}

if ($action === 'test') {
    
    $courseid = required_param('$courseid', PARAM_INT);
    
    $myrenderer = $PAGE->get_renderer('mod_pdfannotator');
    echo $myrenderer->render_teacheroverview(new teacheroverview($courseid));
    
}

/******************************************** 3. HANDLING ANSWERS TO ONE'S QUESTIONS (studentoverview page) *******************************************/
/****************************************************************************************************************/

/******************************************** Mark a question as seen and don't display it any longer *******************************************/

// Students can mark answers to their own questions as seen on their personal overview page.

if ($action === 'markAnswerAsSeen') {
    
    require_once($CFG->dirroot.'/mod/pdfannotator/classes/output/studentoverview.php');
    require_once($CFG->dirroot.'/mod/pdfannotator/model/pdfannotator.php');
    
    global $DB;
 
    $answerid = required_param('answerid', PARAM_INT);
    $openannotator = required_param('openannotator', PARAM_INT);
    
    if($DB->update_record('pdfannotator_comments', array("id" => $answerid, "seen" => 1), $bulk=false)){
                
        $myrenderer = $PAGE->get_renderer('mod_pdfannotator');
        $templatable = new studentoverviewUpdateHiddenEntries($pdfannotator->course, $openannotator);
        $newdata = $templatable->export_for_template($myrenderer);       
        
        echo json_encode(['status' => 'success', 'answerid' => $answerid, 'pdfannotatorid' => $documentid, 'newdata' => $newdata]);
    
    } else {
        echo json_encode(['status' => 'error']);
    }
    
}


if ($action === 'markAnswerAsUnseen') {
    
    require_once($CFG->dirroot.'/mod/pdfannotator/classes/output/studentoverview.php');
    require_once($CFG->dirroot.'/mod/pdfannotator/model/pdfannotator.php');
    
    global $DB;
 
    $answerid = required_param('answerid', PARAM_INT);
    $openannotator = required_param('openannotator', PARAM_INT);
    
    $success = $DB->update_record('pdfannotator_comments', array("id" => $answerid, "seen" => 0), $bulk=false);
    
    if($success == 1){
        
        $myrenderer = $PAGE->get_renderer('mod_pdfannotator');
        $templatable = new studentoverviewUpdateAnswers($pdfannotator->course, $openannotator);
        $newdata = $templatable->export_for_template($myrenderer);
        
        echo json_encode(['status' => 'success', 'answerid' => $answerid, 'pdfannotatorid' => $documentid, 'newdata' => $newdata]);
    
    } else {
        echo json_encode(['status' => 'error']);
    }
    
}
