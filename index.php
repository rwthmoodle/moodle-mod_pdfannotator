<?php
/**
 * @package   mod_pdfannotator
 * @copyright 2018 RWTH Aachen (see README.md)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once('../../config.php');
require_once('locallib.php');
 
$id = required_param('id', PARAM_INT);           // Course ID
 
// Ensure that the course specified is valid
if (!$course = $DB->get_record('course', array('id'=> $id))) {
    print_error('Course ID is incorrect');
}

//$course = $DB->get_record('course', array('id'=>$id), '*', MUST_EXIST);
require_course_login($course, true);
$PAGE->set_pagelayout('incourse');

$params = array(
    'context' => context_course::instance($course->id)
);
$event = \mod_pdfannotator\event\course_module_instance_list_viewed::create($params);
$event->add_record_snapshot('course', $course);
$event->trigger();

$strpdfannotator     = get_string('modulename', 'pdfannotator');
$strpdfannotators    = get_string('modulenameplural', 'pdfannotator');
$strsectionname  = get_string('sectionname', 'format_'.$course->format);
$strname         = get_string('name');
$strintro        = get_string('moduleintro');
$strlastmodified = get_string('lastmodified');

$PAGE->set_url('/mod/pdfannotator/index.php', array('id' => $course->id));
$PAGE->set_title($course->shortname.': '.$strpdfannotators);
$PAGE->set_heading($course->fullname);
$PAGE->navbar->add($strpdfannotators);
echo $OUTPUT->header();
echo $OUTPUT->heading($strpdfannotators);

if (!$pdfannotators = get_all_instances_in_course('pdfannotator', $course)) {
    notice(get_string('thereareno', 'moodle', $strpdfannotators), "$CFG->wwwroot/course/view.php?id=$course->id");
    exit;
}

$usesections = course_format_uses_sections($course->format);

$table = new html_table();
$table->attributes['class'] = 'generaltable mod_index';

if ($usesections) {
    $table->head  = array ($strsectionname, $strname, $strlastmodified, $strintro);
    $table->align = array ('center', 'left', 'left', 'left');
} else {
    $table->head  = array ($strlastmodified, $strname, $strintro);
    $table->align = array ('left', 'left', 'left');
}

$modinfo = get_fast_modinfo($course);

$currentsection = '';

foreach ($pdfannotators as $pdfannotator) {
    $cm = $modinfo->cms[$pdfannotator->coursemodule];
    $infor=get_number_of_new_activities($pdfannotator->id);
    if ($usesections) {
        $printsection = '';
        if ($pdfannotator->section !== $currentsection) {
            if ($pdfannotator->section) {
                $printsection = get_section_name($course, $pdfannotator->section);
            }
            if ($currentsection !== '') {
                $table->data[] = 'hr';
            }
            $currentsection = $pdfannotator->section;
        }
    } else {
        $printsection = '<span class="smallinfo">'.userdate($pdfannotator->timemodified)."</span>";
    }

    $extra = empty($cm->extra) ? '' : $cm->extra;
    
    $icon = '<img src="'.$cm->get_icon_url().'" class="activityicon" alt="'.$cm->get_module_type_name().'" /> ';
   

    $class = $pdfannotator->visible ? '' : 'class="dimmed"'; // hidden modules are dimmed
    $newinfo=" ";
    $lastmodified=get_datetime_of_last_modification($pdfannotator->id);
    if($infor >0)
        $newinfo= "<img src=\"pix/new.png\">($infor)</img>";
    else if($lastmodified>=strtotime("-1 day"))
        $newinfo= "<img src=\"pix/new.gif\"></img>";
    $table->data[] = array (
        $printsection,
        "<a $class $extra href=\"view.php?id=$cm->id\">".$icon.format_string($pdfannotator->name).$newinfo."</a>", userdate($lastmodified),
        format_module_intro('pdfannotator', $pdfannotator, $cm->id));
}

echo html_writer::table($table);

echo $OUTPUT->footer();
