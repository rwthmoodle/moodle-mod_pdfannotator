<?php
/**
 * @package   mod_pdfannotator
 * @copyright 2018 RWTH Aachen, Anna Heynkes (see README.md)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

$action = optional_param('action', 'view', PARAM_TEXT); // default action is 'view'

$taburl = new moodle_url('/mod/pdfannotator/view.php', array('id' => $id));

$myrenderer = $PAGE->get_renderer('mod_pdfannotator');

/* * ********************************************** Display overview page *********************************************** */

if ($action === 'overview') {

    require_once($CFG->dirroot . '/mod/pdfannotator/classes/output/teacheroverview.php');

    $PAGE->set_title("teacheroverview");

    // 1.1 Display tab navigation
    echo $myrenderer->render_pdfannotator_teacherview_tabs($taburl, $action);
    
    // 1.2 Give javascript (see below) access to the language string repository
    $stringman = get_string_manager();
    $strings = $stringman->load_component_strings('pdfannotator', 'en'); // method gets the strings of the language files   
    $PAGE->requires->strings_for_js(array_keys($strings), 'pdfannotator'); //method to use the language-strings in javascript
    
    // 1.3 Add the javascript file that determines the dynamic behaviour of the page
    $PAGE->requires->js(new moodle_url("/mod/pdfannotator/shared/teacheroverview.js"));

    $params = array($pdfannotator->id);
    $PAGE->requires->js_init_call('markCurrent',$params,true); // 1. Name der JS-Funktion, 2. Parameter
    
    // 1.4 Create a renderer, let it render the renderable/templatable teacheroverview
    $myrenderer = $PAGE->get_renderer('mod_pdfannotator');
    echo $myrenderer->render_teacheroverview(new teacheroverview($course->id, $pdfannotator->id, $pdfannotator->newsspan));
}

/* * ********************************************** Display the pdf in its editor (default action) *********************************************** */

if ($action === 'view') { // default
    $PAGE->set_title("annotatorview");
    echo $myrenderer->render_pdfannotator_teacherview_tabs($taburl, $action);
    echo $OUTPUT->heading(format_string($pdfannotator->name), 2);

    pdfannotator_display_embed($pdfannotator, $cm, $course, $file, $page, $annoid, $commid);
}

/* * ********************************************** Display statistics *********************************************** */

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

    echo $myrenderer->render_statistic(new statistic($cm->instance, $course->id, true)); // parameter 'statistic' is a renderable/templatable
}
