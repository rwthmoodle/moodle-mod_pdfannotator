<?php
/**
 * @package   mod_pdfannotator
 * @copyright 2018 RWTH Aachen, Ahmad Obeid and Anna Heynkes (see README.md)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require('../../config.php');
require_once($CFG->dirroot.'/mod/pdfannotator/locallib.php'); // requires lib.php in turn
require_once($CFG->libdir.'/completionlib.php');
require_once($CFG->dirroot.'/mod/pdfannotator/model/pdfannotator.php');
require_once('renderable.php');

$id       = optional_param('id', 0, PARAM_INT); // Course Module ID
$r        = optional_param('r', 0, PARAM_INT);  // pdfannotator instance ID
$redirect = optional_param('redirect', 0, PARAM_BOOL);

$page = optional_param('page', 1, PARAM_INT);
$annoid = optional_param('annoid', null, PARAM_INT);
$commid = optional_param('commid', null, PARAM_INT);

if ($r) {
    if (!$pdfannotator = $DB->get_record('pdfannotator', array('id'=>$r))) {
        print_error('invalidaccessparameter');
    }
    $cm = get_coursemodule_from_instance('pdfannotator', $pdfannotator->id, $pdfannotator->course, false, MUST_EXIST);

} else {
    if (!$cm = get_coursemodule_from_id('pdfannotator', $id)) {
        print_error('invalidcoursemodule');
    }
    $pdfannotator = $DB->get_record('pdfannotator', array('id'=>$cm->instance), '*', MUST_EXIST);
}

$course = $DB->get_record('course', array('id'=>$cm->course), '*', MUST_EXIST);
//$course = get_course($cm->course);

//$course->pdfannotator_list = pdfannotator_instance::get_pdfannotator_instances($course->id); // array containing all pdfannotator instance objects for this course

require_course_login($course, true, $cm);

$context = context_module::instance($cm->id);

require_capability('mod/pdfannotator:view', $context);

// Completion and trigger events.
pdfannotator_view($pdfannotator, $course, $cm, $context);

$PAGE->set_url('/mod/pdfannotator/view.php', array('id' => $cm->id));


$fs = get_file_storage();
$files = $fs->get_area_files($context->id, 'mod_pdfannotator', 'content', 0, 'sortorder DESC, id ASC', false); // TODO: this is not very efficient!!
if (count($files) < 1) {
    pdfannotator_print_filenotfound($pdfannotator, $cm, $course);
    die;
} else {
    $file = reset($files);
    unset($files);
}

$pdfannotator->mainfile = $file->get_filename();

// Fullscreen is now handled with JS
/*
if(optional_param('full', 0, PARAM_BOOL)){
    $PAGE->set_pagelayout('embedded');
}*/

// Set course name for display
$PAGE->set_heading($course->fullname);

// Display course name, navigation bar at the very top and "Dashboard->...->..." bar
echo $OUTPUT->header();

// Check role of current user // XXX This is gradually to be replaced by more specified capabilities
$isteacher = has_capability('mod/pdfannotator:administrateuserinput', $context);
$isstudent = has_capability('mod/pdfannotator:submit', $context);

include($CFG->dirroot.'/mod/pdfannotator/controller.php');

// Display navigation and settings bars on the left as well as the footer
echo $OUTPUT->footer();