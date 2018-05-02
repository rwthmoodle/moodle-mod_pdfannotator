<?php
/**
 * @package   mod_pdfannotator
 * @copyright 2018 RWTH Aachen, Anna Heynkes (see README.md)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * 
 */
defined('MOODLE_INTERNAL') || die();


$action = optional_param('action', 'view', PARAM_TEXT);

$taburl = new moodle_url('/mod/pdfannotator/view.php', array('id' => $id));
//$PAGE->set_url($taburl);

$myrenderer = $PAGE->get_renderer('mod_pdfannotator');


/************************************************ Display student overview page ************************************************/

if ($action === 'overview') {
    
    // 1. Require templatable/renderable called studentoverview. This is used as a subcontroller or view controller
    require_once($CFG->dirroot.'/mod/pdfannotator/classes/output/studentoverview.php');
    
    // 2a. Set page title
    $PAGE->set_title("studentoverview");
    
    // 2b. Display tab navigation
    echo $myrenderer->render_pdfannotator_teacherview_tabs($taburl, $action);
    
    // 3a. Give javascript (see below) access to the language string repository
    $stringman = get_string_manager();
    $strings = $stringman->load_component_strings('pdfannotator', 'en');   // get the strings of the language files
    $PAGE->requires->strings_for_js(array_keys($strings), 'pdfannotator'); // method to use the language-strings in javascript
   
    // 3b. Add the javascript file that determines the dynamic behaviour of the page
    $PAGE->requires->js(new moodle_url("/mod/pdfannotator/shared/studentoverview.js"));
    
    // 3c. Call a javascript function and pass php data to it
    $params = array($pdfannotator->id);
    $PAGE->requires->js_init_call('markCurrent', $params, true); // 1. name of the JS function, 2. parameters
    
    // 4. Create a renderer and let it render the mustache template with data provided by the renderable/templatable
    $myrenderer = $PAGE->get_renderer('mod_pdfannotator');
    echo $myrenderer->render_studentoverview(new studentoverview($course->id, $pdfannotator->id, $pdfannotator->newsspan)); // parameter 'studentoverview' is a renderable/templatable
}

/************************************************ Display the pdf in its editor (default action) ************************************************/

if ($action === 'view') {
       
    echo $myrenderer->render_pdfannotator_teacherview_tabs($taburl, $action);
    $PAGE->set_title("annotatorview");
    echo $OUTPUT->heading(format_string($pdfannotator->name), 2);
    
    pdfannotator_display_embed($pdfannotator, $cm, $course, $file, $page, $annoid, $commid);
    
    
}

/************************************************ Display statistics ************************************************/

if ($action === 'statistic') {
    require_once($CFG->dirroot . '/mod/pdfannotator/model/statisticmodel.class.php');
    require_once($CFG->dirroot . '/mod/pdfannotator/classes/output/statistic.php');
    
    echo $myrenderer->render_pdfannotator_teacherview_tabs($taburl, $action);
    $PAGE->set_title("statisticview");
    echo $OUTPUT->heading(get_string('activities', 'pdfannotator'));

    // Give javascript (see below) access to the language string repository
    $stringman = get_string_manager();
    $strings = $stringman->load_component_strings('pdfannotator', 'en'); //with this method you get the strings of the language files   
    $PAGE->requires->strings_for_js(array_keys($strings), 'pdfannotator'); //method to use the language-strings in javascript
    $PAGE->requires->js(new moodle_url("/mod/pdfannotator/shared/statistic.js"));
     $myrenderer = $PAGE->get_renderer('mod_pdfannotator');

     echo $myrenderer->render_statistic(new statistic($cm->instance, $course->id, false));
}


/************************************************ Display form for reporting a comment  ************************************************/

if ($action === 'report') {
    
    require_once($CFG->dirroot.'/mod/pdfannotator/reportform.php');
    require_once($CFG->dirroot.'/mod/pdfannotator/model/comment.class.php');
    require_once($CFG->dirroot.'/mod/pdfannotator/model/report.class.php');
    
    // Get comment id
    $commentid = optional_param('commentid', 0, PARAM_INT);
    
    // Contextual data to pass on to the report form
    $data = new stdClass();    
        $data->course = $cm->course;
        $data->pdfid = $cm->instance;
        $data->pdfname = $cm->name;
        $data->commentid = $commentid;
        $data->id = $id;
        $data->action = 'report';
    
    // Initialise mform and pass on $data-object to it
    $mform = new pdfannotator_reportform();
        $mform->set_data($data);
    
    // -------------------------- Form processing and displaying is done here --------------------------
    if ($mform->is_cancelled()) {
        $action = 'view';
        echo $myrenderer->render_pdfannotator_teacherview_tabs($taburl, $action);
        pdfannotator_display_embed($pdfannotator, $cm, $course, $file);

    } else if ($report = $mform->get_data()) { //In this case you process validated data. $mform->get_data() returns data posted in form.
        
        // 1. save report in db
        $reportedcomment = new pdfannotator_comment($report->commentid);
        $reportID = report::create($report, $reportedcomment);
        
        // 2. notify the reporting user that their report has been sent off (display 'blue box'/toast at top of page)
        \core\notification::info(get_string('reportwassentoff', 'pdfannotator'));
        
        // 3. notify course manager(s)       
        $recipients = get_enrolled_users($context, 'mod/pdfannotator:administrateuserinput');
        $name = 'newreport';
        $report->reportinguser = fullname($USER);
        $report->url = $CFG->wwwroot.'/mod/pdfannotator/view.php?id='.$cm->id.'&action=overview';
        $messagetext = new stdClass();
        $messagetext->text = format_notification_message_text($course,$cm,$context,get_string('modulename','pdfannotator'),$cm->name,$report,'reportadded');
        $messagetext->html = format_notification_message_html($course,$cm,$context,get_string('modulename','pdfannotator'),$cm->name,$report,'reportadded');
        $messagetext->url = $report->url;
        try {
            foreach ($recipients as $recipient) {
                $messageid = pdfannotator_notify_manager($recipient, $course, $cm, $name, $messagetext);
            }
        } catch (Exception $ex) {
            echo "Test";
        }

        $action = 'view';
        echo $myrenderer->render_pdfannotator_teacherview_tabs($taburl, $action);
        pdfannotator_display_embed($pdfannotator, $cm, $course, $file);
        

    } else { // this branch is executed if the form is submitted but the data doesn't validate and the form should be redisplayed
             // or on the first display of the form.
        
        $PAGE->set_title("reportform");
        echo $OUTPUT->heading(get_string('titleforreportcommentform', 'pdfannotator'));
        
            // Get information about the comment to be reported
        $comment = new pdfannotator_comment($commentid);
            $info = pdfannotator_comment_info::make_from_comment($comment);

        // Display it in a table
        $myrenderer = $PAGE->get_renderer('mod_pdfannotator');
        echo $myrenderer->render_pdfannotator_comment_info($info);

        // Now display the complaint form itself
        $mform->display();
        
    }
    
    return;
}  
