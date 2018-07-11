<?php
/**
 * In this file, incoming AJAX request from the Store Adapter in index.js are handled.
 * These requests concern the creation, retrieval and deletion of annotations
 * and comments as well as the editing/shifting of annotations and the reporting
 * of comments that are deemed inappropriate.
 *
 * The file also handles incoming AJAX requests from overview.js,
 * which control the behaviour of the overview page. These requests are concerned with
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
require_once($CFG->dirroot . '/mod/pdfannotator/locallib.php');

$documentid = required_param('documentId', PARAM_PATH);
$action = required_param('action', PARAM_TEXT); // '$action' determines what is to be done; see below:

$pdfannotator = $DB->get_record('pdfannotator', array('id' => $documentid), '*', MUST_EXIST);
$cm = get_coursemodule_from_instance('pdfannotator', $documentid, $pdfannotator->course, false, MUST_EXIST);
require_course_login($pdfannotator->course, true, $cm);


/* * ****************************************** 1. HANDLING ANNOTATIONS ****************************************** */
/* * ************************************************************************************************************* */

/* * ****************************************** Retrieve all annotations from db for display ****************************************** */

if ($action === 'read') {

    global $DB;
    global $USER;

    $page = optional_param('page_Number', 1, PARAM_INT); // Default page number is 1.

    $annotations = array();

    $records = $DB->get_records('pdfannotator_annotations', array('pdfannotatorid' => $documentid, 'page' => $page));

    foreach ($records as $record) {

        $entry = json_decode($record->data); // StdClass Object containing data that is specific to the respective annotation type.
        // Add general annotation data.
        $entry->type = get_name_of_annotationtype($record->annotationtypeid);
        // The following 3 lines can be removed after deletion of the original annotation tables.
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

/* * ************************************ Select a single annotation from db for shifting ****************************************** */

if ($action === 'readsingle') {

    global $DB;
    $annotationid = required_param('annotationId', PARAM_INT);
    $page = optional_param('page_Number', 1, PARAM_INT);

    $records = $DB->get_records('pdfannotator_annotations', array('id' => $annotationid));
    foreach ($records as $record) {
        $annotation = json_decode($record->data);
        // Add general annotation data.
        $annotation->type = get_name_of_annotationtype($record->annotationtypeid);
        // The following 3 lines can be removed after deletion of the original annotation tables.
        if ($annotation->type == 'pin') {
            $annotation->type = 'point';
        }
        $annotation->class = "Annotation";
        $annotation->page = $record->page;
        $annotation->uuid = $record->id;
        $data = array('documentId' => $documentid, 'annotation' => $annotation);
        echo json_encode($data);
        return;
    }
}

/* * ****************************************** Save (1) and display (2) a new annotation ****************************************** */

if ($action === 'create') {

    global $DB;
    global $USER;
    
    $context = context_module::instance($cm->id);
    
    $isteacher = has_capability('mod/pdfannotator:administrateuserinput', $context);

    $table = "pdfannotator_annotations";

    $pageid = required_param('page_Number', PARAM_INT);

    // 1.1 Get the annotation data and decode the json wrapper.
    $annotationjs = required_param('annotation', PARAM_TEXT);
    $annotation = json_decode($annotationjs, true);
    // 1.2 Determine the type of the annotation.
    $type = $annotation['type'];
    $typeid = get_id_of_annotationtype($type);
    // 1.3 Set the type-specific data of the annotation.
    $data = [];
    switch ($type) {
        case 'area':
            $data['x'] = $annotation['x'];
            $data['y'] = $annotation['y'];
            $data['width'] = $annotation['width'];
            $data['height'] = $annotation['height'];
            break;
        case 'drawing':
            $studentdrawingsallowed = $DB->get_field('pdfannotator', 'use_studentdrawing', array('id' => $documentid), $strictness=MUST_EXIST);
            if ($studentdrawingsallowed !== 1 && !$isteacher) {
                echo json_encode(['status' => 'error', 'reason' => get_string('studentdrawingforbidden', 'pdfannotator')]);
                return;
            }
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
            $studenttextboxesallowed = $DB->get_field('pdfannotator', 'use_studenttextbox', array('id' => $documentid), $strictness=MUST_EXIST);
            if ($studenttextboxesallowed !== 1 && !$isteacher) {
                echo json_encode(['status' => 'error', 'reason' => get_string('studenttextboxforbidden', 'pdfannotator')]);
                return;
            }
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

    // 1.4 Insert a new record into mdl_pdfannotator_annotations.
    $newannotationid = $DB->insert_record($table, array("pdfannotatorid" => $documentid, "page" => $pageid, "userid" => $USER->id,
        "annotationtypeid" => $typeid, "data" => $insertiondata, "timecreated" => time()), true, false);
    // 2. If the insertion was successful...
    if (isset($newannotationid) && $newannotationid !== false && $newannotationid > 0) {
        // 2.1 set additional data to send back to the client.
        $data['uuid'] = $newannotationid;
        $data['type'] = $type;
        if ($type == 'pin') {
            $data['type'] = 'point';
        }
        $data['class'] = "Annotation";
        $data['page'] = $pageid;
        $data['status'] = 'success';

        // 2.2 and send it off for display.
        echo json_encode($data);
    } else { // If not, return an error message.
        echo json_encode(['status' => 'error']);
    }
}

/* * ****************************************** Update an annotation ****************************************** */

if ($action === 'update') {

    // 1. Get the id of the annotation that is to be shifted in position.
    $annotationid = required_param('annotationId', PARAM_INT);

    // 2. Get the updated annotation data received for storage and decode its json wrapper.
    $datajs = required_param('annotation', PARAM_TEXT);
    $data = json_decode($datajs, true);

    // 3. Check whether the current user is allowed to shift this annotation, i.e. whether it's his and hasn't been commented by other people.
    if (pdfannotator_annotation::shifting_allowed($annotationid)) {

        $annotation = $data['annotation'];
        $type = $annotation['type'];
        $newdata = [];

        // 4. If so, update the annotations 'data' attribute in mdl_pdfannotator_annotations.
        // Note that while only part of the data may change, the whole JSON-string has to be construced anew.
        // e.g. drawing: Only the 'lines' actually change, but the database stores them together with width and color in a single JSON-string called 'data'.
        switch ($type) {

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

        $success = pdfannotator_annotation::update($annotationid, $newdata);

        // 5. If the updated data received from the Store Adapter could successfully be inserted in db, send it back for display.
        if ($success != null && $success == 1) {
            echo json_encode($data);
        } else {
            echo json_encode(['status' => 'error']);
        }
    } else {

        echo json_encode(['status' => 'error']);
    }
}

/* * ****************************************** Delete an annotation ****************************************** */

if ($action === 'delete') {

    // Get current user.
    global $USER;
    $thisuser = $USER->id;

    // Get annotation itemid and course module id.
    $annotationid = required_param('annotation', PARAM_INT);

    // Delete annotation if user is permitted to do so.
    $success = pdfannotator_annotation::delete($annotationid, $cm->id);

    // For completeness's sake...
    if ($success === true) {
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'reason' => $success]);
    }
}

/* * ****************************************** Retrieve all questions of a specific page or document ****************************************** */

if ($action === 'getQuestions') {

    $pageid = optional_param('page_Number', -1, PARAM_INT); // Default is 1.
    if ($pageid == -1) {
        $questions = pdfannotator_comment::get_all_questions($documentid);
        $pdfannotatorname = $DB->get_field('pdfannotator', 'name', array('id' => $documentid), $strictness = MUST_EXIST);
        $result = array('questions' => $questions, 'pdfannotatorname' => $pdfannotatorname);
        echo json_encode($result);
    } else {
        $questions = pdfannotator_comment::get_questions($documentid, $pageid);
        echo json_encode($questions);
    }
}

/* * ****************************************** 2. HANDLING COMMENTS ****************************************** */
/* * ************************************************************************************************************* */

/* * ****************************************** Save a new comment and return it for display ****************************************** */

if ($action === 'addComment') {

    // Get the annotation to be commented.
    $annotationid = required_param('annotationId', PARAM_TEXT);
    $context = context_module::instance($cm->id);
    $PAGE->set_context($context);

    // Get the comment data.
    $content = required_param('content', PARAM_TEXT);
    $visibility = required_param('visibility', PARAM_TEXT);
    $isquestion = required_param('isquestion', PARAM_TEXT);

    // Insert the comment into the mdl_pdfannotator_comments table and get its record id.
    $commentid = pdfannotator_comment::create($documentid, $annotationid, $content, $visibility, $isquestion, $cm, $context);

    // Get username or label 'anonymous' // XXX Could perhaps be delegated to a moodle setting.
    $username = $USER->username;
    if ($visibility === 'anonymous') {
        $username = get_string('anonymous', 'pdfannotator');
    }

    // If successful, create a comment array and return it as json.
    if (isset($commentid) && $commentid !== false && $commentid > 0) {

        global $USER;
        $comment = [];
        $comment['class'] = 'Comment';
        $comment['uuid'] = $commentid;
        $comment['annotation'] = $annotationid;
        $comment['content'] = $content;
        $comment['userid'] = $USER->id;
        $comment['username'] = $username;
        $timestamp = pdfannotator_comment::get_timestamp($commentid);
        $comment['timecreated'] = get_user_date_time($timestamp);
        $comment['visibility'] = $visibility;
        $comment['isquestion'] = $isquestion;
        $comment['usevotes'] = pdfannotator_instance::use_votes($documentid);

        echo json_encode($comment);
    } else {
        if ($commentid == -1) {
            echo json_encode(['status' => '-1']);
        } else {
            echo json_encode(['status' => 'error']);
        }
    }
}

/* * ****************************************** Retrieve information about a specific annotation from db ****************************************** */

if ($action === 'getInformation') {

    $annotationid = required_param('annotationId', PARAM_INT);

    // Get the annotation information only if it is a drawing or textbox.
    $annotationobject = new pdfannotator_annotation($annotationid);

    if ($annotationobject->get_annotationtype() === "drawing" || $annotationobject->get_annotationtype() === "textbox") {
        $returnanno = [];
        $returnanno['type'] = $annotationobject->get_annotationtype();
        $returnanno['class'] = "Annotation";
        $returnanno['page'] = $annotationobject->get_page_of_annotation();
        $returnanno['uuid'] = $annotationid;
        $returnanno['user'] = get_username($annotationobject->userid);
        $returnanno['userid'] = $annotationobject->userid;
        $returnanno['timecreated'] = userdate($annotationobject->timecreated, $format = '', $timezone = 99, $fixday = true, $fixhour = true);
        ;
        echo json_encode($returnanno);
    } else {
        echo json_encode(['status' => 'error']);
    }
}

/* * ****************************************** Retrieve all comments for a specific annotation from db ****************************************** */

if ($action === 'getComments') {

    $annotationid = required_param('annotationId', PARAM_INT);

    // Create an array of all comment objects on the specified page and annotation.
    $comments = pdfannotator_comment::read($documentid, $annotationid);
    echo json_encode($comments);
}

/* * ****************************************** Delete a comment ****************************************** */

if ($action === 'deleteComment') {

    $commentid = required_param('commentId', PARAM_INT);

    $context = context_module::instance($cm->id);

    pdfannotator_comment::delete_comment($commentid, $context);
}

/* * ****************************************** Vote for a comment ****************************************** */

if ($action === 'voteComment') {

    global $DB;

    $commentid = required_param('commentid', PARAM_INT);

    $numbervotes = pdfannotator_comment::insert_vote($documentid, $commentid);

    if ($numbervotes) {
        echo json_encode(['status' => 'success', 'numberVotes' => $numbervotes]);
    } else {
        echo json_encode(['status' => 'error']);
    }
}

/* * ****************************************** Subscribe to a question  ****************************************** */

if ($action === 'subscribeQuestion') {
    global $DB;
    $annotationid = required_param('annotationid', PARAM_INT);

    $subscriptionid = pdfannotator_comment::insert_subscription($annotationid);

    if ($subscriptionid) {
        echo json_encode(['status' => 'success', 'annotationid' => $annotationid, 'subscriptionid' => $subscriptionid]);
    } else {
        echo json_encode(['status' => 'error']);
    }
}

/* * ****************************************** Unsubscribe from a question  ****************************************** */

if ($action === 'unsubscribeQuestion') {
    global $DB;
    $annotationid = required_param('annotationid', PARAM_INT);

    $subscriptionid = pdfannotator_comment::delete_subscription($annotationid);

    if ($subscriptionid) {
        echo json_encode(['status' => 'success', 'annotationid' => $annotationid, 'subscriptionid' => $subscriptionid]);
    } else {
        echo json_encode(['status' => 'error']);
    }
}


/* * ****************************************** 3. HANDLING REPORTS (teacheroverview) ****************************************** */
/* * ************************************************************************************************************* */

/* * ************************************** 3.1 Mark a report as seen and don't display it any longer ******************************** */

if ($action === 'markReportAsSeen') {
    
    require_once($CFG->dirroot.'/mod/pdfannotator/classes/output/overview.php');
    require_once($CFG->dirroot.'/mod/pdfannotator/model/pdfannotator.php');
    
    global $DB;
    $reportid = required_param('reportid', PARAM_INT);
    $openannotator = required_param('openannotator', PARAM_INT);

    if ($DB->update_record('pdfannotator_reports', array("id" => $reportid, "seen" => 1), $bulk = false)) {

        $myrenderer = $PAGE->get_renderer('mod_pdfannotator');
        $templatable = new overviewUpdateHiddenReports($pdfannotator->course, $openannotator);
        $newdata = $templatable->export_for_template($myrenderer);

        echo json_encode(['status' => 'success', 'reportid' => $reportid, 'pdfannotatorid' => $documentid, 'newdata' => $newdata]);
    } else {
        echo json_encode(['status' => 'error']);
    }
}


/* * ************************************** 3.2 Mark a hidden report as unseen and display it once more ****************************** */

if ($action === 'markReportAsUnseen') {
    
    require_once($CFG->dirroot.'/mod/pdfannotator/classes/output/overview.php');
    require_once($CFG->dirroot.'/mod/pdfannotator/model/pdfannotator.php');
    
    global $DB;
    $reportid = required_param('reportid', PARAM_INT);
    $openannotator = required_param('openannotator', PARAM_INT);

    if ($DB->update_record('pdfannotator_reports', array("id" => $reportid, "seen" => 0), $bulk = false)) {

        $myrenderer = $PAGE->get_renderer('mod_pdfannotator');
        $templatable = new overviewUpdateReports($pdfannotator->course, $openannotator);
        $newdata = $templatable->export_for_template($myrenderer);

        echo json_encode(['status' => 'success', 'reportid' => $reportid, 'pdfannotatorid' => $documentid, 'newdata' => $newdata]);
    } else {
        echo json_encode(['status' => 'error']);
    }
}


/* * ************************************** 3.3 Delete a displayed or hidden report permanently ****************************** */

if ($action === 'deleteReport') {

    global $DB;

    $reportid = required_param('reportid', PARAM_INT);

    if ($DB->delete_records('pdfannotator_reports', array("id" => $reportid))) {
        echo json_encode(['status' => 'success', 'reportid' => $reportid, 'pdfannotatorid' => $documentid]);
    } else {
        echo json_encode(['status' => 'error']);
    }
}

/******************************************** 3. HANDLING ANSWERS TO ONE'S QUESTIONS (studentoverview) *******************************************/
/****************************************************************************************************************/

/* * ****************************************** Mark a question as seen and don't display it any longer ****************************************** */

// Students can mark answers to their own questions as seen on their personal overview page.

if ($action === 'markAnswerAsSeen') {
    
    require_once($CFG->dirroot.'/mod/pdfannotator/classes/output/overview.php');
    require_once($CFG->dirroot.'/mod/pdfannotator/model/pdfannotator.php');
    
    global $DB;

    $answerid = required_param('answerid', PARAM_INT);
    $openannotator = required_param('openannotator', PARAM_INT);

    if ($DB->update_record('pdfannotator_comments', array("id" => $answerid, "seen" => 1), $bulk = false)) {

        $myrenderer = $PAGE->get_renderer('mod_pdfannotator');
        $templatable = new overviewUpdateHiddenAnswers($pdfannotator->course, $openannotator);
        $newdata = $templatable->export_for_template($myrenderer);       
        
        echo json_encode(['status' => 'success', 'answerid' => $answerid, 'pdfannotatorid' => $documentid, 'newdata' => $newdata]);
    } else {
        echo json_encode(['status' => 'error']);
    }
}


if ($action === 'markAnswerAsUnseen') {
    
    require_once($CFG->dirroot.'/mod/pdfannotator/classes/output/overview.php');
    require_once($CFG->dirroot.'/mod/pdfannotator/model/pdfannotator.php');
    
    global $DB;

    $answerid = required_param('answerid', PARAM_INT);
    $openannotator = required_param('openannotator', PARAM_INT);

    $success = $DB->update_record('pdfannotator_comments', array("id" => $answerid, "seen" => 0), $bulk = false);

    if ($success == 1) {

        $myrenderer = $PAGE->get_renderer('mod_pdfannotator');
        $templatable = new overviewUpdateAnswers($pdfannotator->course, $openannotator);
        $newdata = $templatable->export_for_template($myrenderer);

        echo json_encode(['status' => 'success', 'answerid' => $answerid, 'pdfannotatorid' => $documentid, 'newdata' => $newdata]);
    } else {
        echo json_encode(['status' => 'error']);
    }
}
