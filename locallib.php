<?php
/**
 * @package   mod_pdfannotator
 * @copyright 2018 CiL RWTH Aachen
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die;

require_once("$CFG->libdir/filelib.php");
require_once("$CFG->libdir/resourcelib.php");
require_once("$CFG->dirroot/mod/pdfannotator/lib.php");

/**
 * Display embedded pdfannotator file.
 * @param object $pdfannotator
 * @param object $cm
 * @param object $course
 * @param stored_file $file main file
 * @return does not return
 */
function pdfannotator_display_embed($pdfannotator, $cm, $course, $file, $page = 1, $annoid = null, $commid = null) {
    global $CFG, $PAGE, $OUTPUT, $USER;
    require_once($CFG->dirroot . '/mod/pdfannotator/classes/output/index.php');

    // The revision attribute's existance is demanded by moodle for versioning and could be saved in the pdfannotator table in the future.
    // Note, however, that we forbid file replacement in order to prevent a change of meaning in other people's comments.
    $pdfannotator->revision = 1;

    $context = context_module::instance($cm->id);
    $path = '/' . $context->id . '/mod_pdfannotator/content/' . $pdfannotator->revision . $file->get_filepath() . $file->get_filename();
    $fullurl = file_encode_url($CFG->wwwroot . '/pluginfile.php', $path, false);

    $documentobject = new stdClass();
    $documentobject->annotatorid = $pdfannotator->id;
    $documentobject->fullurl = $fullurl;

    $stringman = get_string_manager();
    // With this method you get the strings of the language-Files.
    $strings = $stringman->load_component_strings('pdfannotator', 'en');
    // Method to use the language-strings in javascript.
    $PAGE->requires->strings_for_js(array_keys($strings), 'pdfannotator');
    // Load and execute the javascript files.
    $PAGE->requires->js(new moodle_url("/mod/pdfannotator/shared/pdf.js"));
    $PAGE->requires->js(new moodle_url("/mod/pdfannotator/shared/pdf_viewer.js"));
    $PAGE->requires->js(new moodle_url("/mod/pdfannotator/shared/textclipper.js"));
    $PAGE->requires->js(new moodle_url("/mod/pdfannotator/shared/index.js?ver=00007"));
    $PAGE->requires->js(new moodle_url("/mod/pdfannotator/shared/locallib.js?ver=00001"));

    if (has_capability('mod/pdfannotator:administrateuserinput', $context)) {
        $administratesuserinput = true;
    } else {
        $administratesuserinput = false;
    }

    $toolbarsettings = new stdClass();
    $toolbarsettings->use_studenttextbox = $pdfannotator->use_studenttextbox;
    $toolbarsettings->use_studentdrawing = $pdfannotator->use_studentdrawing;
    // Pass parameters from PHP to JavaScript.
    $params = array($cm, $documentobject, $USER->id, $administratesuserinput, $toolbarsettings, $page, $annoid, $commid);
    $PAGE->requires->js_init_call('adjustPdfannotatorNavbar', null, true);
    $PAGE->requires->js_init_call('startIndex', $params, true);
    // The renderer renders the original index.php / takes the template and renders it.
    $myrenderer = $PAGE->get_renderer('mod_pdfannotator');
    echo $myrenderer->render_index(new index($pdfannotator, $administratesuserinput));

    pdfannotator_print_intro($pdfannotator, $cm, $course);

    echo $OUTPUT->footer();
    die;
}

function get_pdfannotator_instance_name($id) {

    global $DB;
    return $DB->get_field('pdfannotator', 'name', array('id' => $id), $strictness = MUST_EXIST);
}

function get_coursename($documentid) {
    global $DB;
    $sql = "SELECT c.fullname FROM {course} c JOIN {pdfannotator} p ON p.course = c.id WHERE p.id = $documentid";
    $record = $DB->get_record_sql($sql, array());
    return $record->fullname;
}

function get_course_name_by_id($courseid) {

    global $DB;
    return $DB->get_field('course', 'fullname', array('id' => $courseid), $strictness = MUST_EXIST);
}

function get_username($userid) {
    global $DB;
    $user = $DB->get_record('user', array('id' => $userid));
    return fullname($user);
}

function get_id_of_annotationtype($typename) {
    global $DB;
    if ($typename == 'point') {
        $typename = 'pin';
    }
    $result = $DB->get_records('pdfannotator_annotationtypes', array('name' => $typename));
    foreach ($result as $r) {
        return $r->id;
    }
}

function get_name_of_annotationtype($typeid) {
    global $DB;
    $result = $DB->get_records('pdfannotator_annotationtypes', array('id' => $typeid));
    foreach ($result as $r) {
        return $r->name;
    }
}

function get_typename_of_annotationtype($annotationid) {

    global $DB;
    $result = $DB->get_records('pdfannotator_annotations', array('id' => $annotationid));

    return get_name_of_annotationtype($result[$annotationid]->annotationtypeid);
}

function pdfannotator_notify_manager($recipient, $course, $cm, $name, $messagetext, $anonymous = false) {
    global $USER;
    global $CFG;
    $userfrom = $USER;
    if ($anonymous) {
        $userfrom = clone($USER);
        $userfrom->firstname = get_string('pdfannotatorname', 'pdfannotator') . ':';
        $userfrom->lastname = $cm->name;
    }
    $message = new \core\message\message();
    $message->component = 'mod_pdfannotator';
    $message->name = $name;
    $message->courseid = $course->id;
    $message->userfrom = $userfrom;
    $message->userto = $recipient;
    $message->subject = get_string('notificationsubject:' . $name, 'pdfannotator', $cm->name);
    $message->fullmessage = $messagetext->text;
    $message->fullmessageformat = FORMAT_PLAIN;
    $message->fullmessagehtml = $messagetext->html;
    $message->smallmessage = get_string('notificationsubject:' . $name, 'pdfannotator', $cm->name);
    $message->notification = 1; // For personal messages '0' important: the 1 without '' and 0 with ''.
    $message->contexturl = $messagetext->url;
    $message->contexturlname = 'Context name';
    $content = array('*' => array('header' => ' test ', 'footer' => ' test ')); // Extra content for specific processor.

    $messageid = message_send($message);

    return $messageid;
}

function format_notification_message_text($course, $cm, $context, $modulename, $pdfannotatorname, $paramsforlanguagestring, $messagetype) {
    global $CFG;
    $formatparams = array('context' => $context->get_course_context());
    $posttext = format_string($course->shortname, true, $formatparams) .
            ' -> ' .
            $modulename .
            ' -> ' .
            format_string($pdfannotatorname, true, $formatparams) . "\n";
    $posttext .= '---------------------------------------------------------------------' . "\n";
    $posttext .= "\n";
    $posttext .= get_string($messagetype . 'text', 'pdfannotator', $paramsforlanguagestring) . "\n---------------------------------------------------------------------\n";
    return $posttext;
}

/**
 * Format a notification for HTML.
 *
 * @param string $messagetype
 * @param stdClass $info
 * @param stdClass $course
 * @param stdClass $context
 * @param string $modulename
 * @param stdClass $coursemodule
 * @param string $assignmentname
 */
function format_notification_message_html($course, $cm, $context, $modulename, $pdfannotatorname, $report, $messagetype) {
    global $CFG, $USER;
    $formatparams = array('context' => $context->get_course_context());
    $posthtml = '<p><font face="sans-serif">' .
            '<a href="' . $CFG->wwwroot . '/course/view.php?id=' . $course->id . '">' .
            format_string($course->shortname, true, $formatparams) .
            '</a> ->' .
            '<a href="' . $CFG->wwwroot . '/mod/pdfannotator/index.php?id=' . $course->id . '">' .
            $modulename .
            '</a> ->' .
            '<a href="' . $CFG->wwwroot . '/mod/pdfannotator/view.php?id=' . $cm->id . '">' .
            format_string($pdfannotatorname, true, $formatparams) .
            '</a></font></p>';
    $posthtml .= '<hr /><font face="sans-serif">';
    $report->urltoreport = $CFG->wwwroot . '/mod/pdfannotator/view.php?id=' . $cm->id . '&action=overview';
    $posthtml .= '<p>' . get_string($messagetype . 'html', 'pdfannotator', $report) . '</p>';
    $linktonotificationsettingspage = new moodle_url('/message/notificationpreferences.php', array('userid' => $USER->id));
    $linktonotificationsettingspage = $linktonotificationsettingspage->__toString();
    $posthtml .= '</font><hr />';
    $posthtml .= '<font face="sans-serif"><p>' . get_string('unsubscribe_notification', 'pdfannotator', $linktonotificationsettingspage) . '</p></font>';
    return $posthtml;
}

function embed_my_pdf($fullurl, $title, $clicktoopen) {
    global $CFG, $PAGE;
    $code = <<<EOT
            <div class="resourcecontent resourcepdf">
  <object id="resourceobject" data="http://localhost/moodle/mod/pdfannotator/pdf-annotate/index.php?fileid=$fullurl" type="application/pdf" width="800" height="600">
    <param name="src" value="$fullurl" />
    $clicktoopen
  </object>
</div>
EOT;

    // The size is hardcoded in the object above intentionally because it is adjusted by the following function on-the-fly.
    $PAGE->requires->js_init_call('M.util.init_maximised_embed', array('resourceobject'), true);

    return $code;
}

/**
 * Internal function - create click to open text with link.
 */
function pdfannotator_get_clicktoopen($file, $revision, $extra = '') {
    global $CFG;

    $filename = $file->get_filename();
    $path = '/' . $file->get_contextid() . '/mod_pdfannotator/content/' . $revision . $file->get_filepath() . $file->get_filename();
    $fullurl = file_encode_url($CFG->wwwroot . '/pluginfile.php', $path, false);

    $string = get_string('clicktoopen2', 'pdfannotator', "<a href=\"$fullurl\" $extra>$filename</a>");

    return $string;
}

/**
 * Internal function - create click to open text with link.
 */
function pdfannotator_get_clicktodownload($file, $revision) {
    global $CFG;

    $filename = $file->get_filename();
    $path = '/' . $file->get_contextid() . '/mod_pdfannotator/content/' . $revision . $file->get_filepath() . $file->get_filename();
    $fullurl = file_encode_url($CFG->wwwroot . '/pluginfile.php', $path, true);

    $string = get_string('clicktodownload', 'pdfannotator', "<a href=\"$fullurl\">$filename</a>");

    return $string;
}

/**
 * Print pdfannotator header.
 * @param object $pdfannotator
 * @param object $cm
 * @param object $course
 * @return void
 */
function pdfannotator_print_header($pdfannotator, $cm, $course) {
    global $PAGE, $OUTPUT;

    $PAGE->set_title($course->shortname . ': ' . $pdfannotator->name);
    $PAGE->set_heading($course->fullname);
    $PAGE->set_activity_record($pdfannotator);
    echo $OUTPUT->header();
}

/**
 * Gets details of the file to cache in course cache to be displayed using {@link pdfannotator_get_optional_details()}
 *
 * @param object $pdfannotator pdfannotator table row (only property 'displayoptions' is used here)
 * @param object $cm Course-module table row
 * @return string Size and type or empty string if show options are not enabled
 */
function pdfannotator_get_file_details($pdfannotator, $cm) {
    $filedetails = array();

    $context = context_module::instance($cm->id);
    $fs = get_file_storage();
    $files = $fs->get_area_files($context->id, 'mod_pdfannotator', 'content', 0, 'sortorder DESC, id ASC', false);
    // For a typical file pdfannotator, the sortorder is 1 for the main file
    // and 0 for all other files. This sort approach is used just in case
    // there are situations where the file has a different sort order.
    $mainfile = $files ? reset($files) : null;

    foreach ($files as $file) {
        // This will also synchronize the file size for external files if needed.
        $filedetails['size'] += $file->get_filesize();
        if ($file->get_repository_id()) {
            // If file is a reference the 'size' attribute can not be cached.
            $filedetails['isref'] = true;
        }
    }

    return $filedetails;
}

/**
 * Print pdfannotator introduction.
 * @param object $pdfannotator
 * @param object $cm
 * @param object $course
 * @param bool $ignoresettings print even if not specified in modedit
 * @return void
 */
function pdfannotator_print_intro($pdfannotator, $cm, $course, $ignoresettings = false) {
    global $OUTPUT;
    if ($ignoresettings) {
        $gotintro = trim(strip_tags($pdfannotator->intro));
        if ($gotintro || $extraintro) {
            echo $OUTPUT->box_start('mod_introbox', 'pdfannotatorintro');
            if ($gotintro) {
                echo format_module_intro('pdfannotator', $pdfannotator, $cm->id);
            }
            echo $extraintro;
            echo $OUTPUT->box_end();
        }
    }
}

/**
 * Print warning that file can not be found.
 * @param object $pdfannotator
 * @param object $cm
 * @param object $course
 * @return void, does not return
 */
function pdfannotator_print_filenotfound($pdfannotator, $cm, $course) {
    global $DB, $OUTPUT;

    pdfannotator_print_header($pdfannotator, $cm, $course);
    //   pdfannotator_print_heading($pdfannotator, $cm, $course);//TODO Methode ist noch nicht definiert
    pdfannotator_print_intro($pdfannotator, $cm, $course);
    echo $OUTPUT->notification(get_string('filenotfound', 'pdfannotator'));

    echo $OUTPUT->footer();
    die;
}

/**
 * Function returns the number of new comments, drawings and textboxes*
 * in this annotator. 'New' is defined here as 'no older than 24h' but
 * can easily be changed to another time span.
 * *Drawings and textboxes cannot be commented. In their case (only),
 * therefore, annotations are counted.
 *
 * @global type $DB
 */
function get_number_of_new_activities($annotatorid) {

    global $DB;

    $parameters = array();
    $parameters[] = $annotatorid;
    $parameters[] = strtotime("-1 day");

    $sql = "SELECT c.id FROM {pdfannotator_annotations} a JOIN {pdfannotator_comments} c ON c.annotationid = a.id "
            . "WHERE a.pdfannotatorid = ? AND c.timemodified >= ?";
    $sql2 = "SELECT a.id FROM {pdfannotator_annotations} a JOIN {pdfannotator_annotationtypes} t ON a.annotationtypeid = t.id "
            . "WHERE a.pdfannotatorid = ? AND a.timecreated >= ? AND t.name IN('drawing','textbox')";

    return ( count($DB->get_records_sql($sql, $parameters)) + count($DB->get_records_sql($sql2, $parameters)) );
}

/**
 * Function returns the datetime of the last modification on or in the specified annotator.
 * The modification can be the creation of the annotator, a change of title or description,
 * a new annotation or a new comment. Reports are not considered.
 *
 * @global type $DB
 * @param int $annotatorid
 * @return datetime $timemodified
 * The timestamp can be transformed into a readable string with this moodle method: userdate($timestamp, $format = '', $timezone = 99, $fixday = true, $fixhour = true);
 */
function get_datetime_of_last_modification($annotatorid) {

    global $DB;

    // 1. When was the last time the annotator itself (i.e. its title, description or pdf) was modified?
    $timemodified = $DB->get_record('pdfannotator', array('id' => $annotatorid), 'timemodified', MUST_EXIST);
    $timemodified = $timemodified->timemodified;

    // 2. When was the last time an annotation or a comment was added in the specified annotator?
    $sql = "SELECT max(a.timecreated) as 'last_annotation', max(c.timemodified) as 'last_comment' "
            . "FROM {pdfannotator_annotations} a LEFT OUTER JOIN {pdfannotator_comments} c ON a.id = c.annotationid "
            . "WHERE a.pdfannotatorid = ?";
    $newposts = $DB->get_records_sql($sql, array($annotatorid));

    if (!empty($newposts)) {

        foreach ($newposts as $entry) {

            // 2.a) If there is an annotation younger than the creation/modification of the annotator, set timemodified to the annotation time.
            if (!empty($entry->last_annotation) && ($entry->last_annotation > $timemodified)) {
                $timemodified = $entry->last_annotation;
            }
            // 2.b) If there is a comment younger than the creation/modification of the annotator or its newest annotation, set timemodified to the comment time.
            if (!empty($entry->last_comment) && ($entry->last_comment > $timemodified)) {
                $timemodified = $entry->last_comment;
            }

            return $timemodified;
        }
    }
}

/**
 * File browsing support class
 */
class pdfannotator_content_file_info extends file_info_stored {

    public function get_parent() {
        if ($this->lf->get_filepath() === '/' and $this->lf->get_filename() === '.') {
            return $this->browser->get_file_info($this->context);
        }
        return parent::get_parent();
    }

    public function get_visible_name() {
        if ($this->lf->get_filepath() === '/' and $this->lf->get_filename() === '.') {
            return $this->topvisiblename;
        }
        return parent::get_visible_name();
    }

}

function pdfannotator_set_mainfile($data) {
    global $DB;
    $fs = get_file_storage();
    $cmid = $data->coursemodule;
    $draftitemid = $data->files; // Name from the filemanger.

    $context = context_module::instance($cmid);
    if ($draftitemid) {
        file_save_draft_area_files($draftitemid, $context->id, 'mod_pdfannotator', 'content', 0, array('subdirs' => true));
    }
    $files = $fs->get_area_files($context->id, 'mod_pdfannotator', 'content', 0, 'sortorder', false);
    if (count($files) == 1) {
        // Only one file attached, set it as main file automatically.
        $file = reset($files);
        file_set_sortorder($context->id, 'mod_pdfannotator', 'content', 0, $file->get_filepath(), $file->get_filename(), 1);
    }
}

function render_listitem_actions(array $actions = null) {
    $menu = new action_menu();
    $menu->attributes['class'] .= ' course-item-actions item-actions';
    $hasitems = false;
    foreach ($actions as $key => $action) {
        $hasitems = true;
        $menu->add(new action_menu_link(
                $action['url'], $action['icon'], $action['string'], in_array($key, array()), array('data-action' => $key, 'class' => 'action-' . $key)
        ));
    }
    if (!$hasitems) {
        return '';
    }
    return render_action_menu($menu);
}

function render_action_menu($menu) {
    global $OUTPUT;
    return $OUTPUT->render($menu);
}

function spdfannotator_subscribe_all($annotatorid) {
    global $DB;
    $sql = "SELECT id FROM {pdfannotator_annotations} "
            . "WHERE pdfannotatorid = ? AND annotationtypeid NOT IN "
            . "(SELECT id FROM {pdfannotator_annotationtypes} WHERE name=? OR name=?)";
    $params = [$annotatorid, 'drawing', 'textbox'];
    $ids = $DB->get_fieldset_sql($sql, $params);
    foreach ($ids as $annotationid) {
        pdfannotator_comment::insert_subscription($annotationid);
    }
}

function pdfannotator_unsubscribe_all($annotatorid) {
    global $DB, $USER;
    $sql = "SELECT a.id FROM {pdfannotator_annotations} a JOIN {pdfannotator_subscriptions} s "
            . "ON s.annotationid= a.id AND s.userid = ? WHERE pdfannotatorid = ?";
    $ids = $DB->get_fieldset_sql($sql, [$USER->id, $annotatorid]);
    foreach ($ids as $annotationid) {
        pdfannotator_comment::delete_subscription($annotationid);
    }
}

function get_user_date_time($timestamp) {
    $userdatetime = userdate($timestamp, $format = '', $timezone = 99, $fixday = true, $fixhour = true); // Method in lib/moodlelib.php
    return $userdatetime;
}
