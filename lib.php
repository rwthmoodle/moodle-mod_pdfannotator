<?php
/**
 * @package   mod_pdfannotator
 * @copyright 2018 RWTH Aachen (see README.md)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

/**
 * List of features supported in pdfannotator module
 * @param string $feature FEATURE_xx constant for requested feature
 * @return mixed True if module supports feature, false if not, null if doesn't know
 */
function pdfannotator_supports($feature) {
    switch($feature) {
        //case FEATURE_MOD_ARCHETYPE:           return MOD_ARCHETYPE_RESOURCE;
        case FEATURE_GROUPS:                  return false;
        case FEATURE_GROUPINGS:               return false;
        case FEATURE_MOD_INTRO:               return true;
        case FEATURE_COMPLETION_TRACKS_VIEWS: return true;
        case FEATURE_GRADE_HAS_GRADE:         return false;
        case FEATURE_GRADE_OUTCOMES:          return false;
        case FEATURE_BACKUP_MOODLE2:          return true;
        case FEATURE_SHOW_DESCRIPTION:        return true;

        default: return null;
    }
}

/**
 * Returns all other caps used in module
 * @return array
 */
function pdfannotator_get_extra_capabilities() {
    return array('moodle/site:accessallgroups');
}

/**
 * This function is used by the reset_course_userdata function in moodlelib.
 * @param $data the data submitted from the reset course.
 * @return array status array
 */
function pdfannotator_reset_userdata($data) {
    return array();
}

/**
 * List the actions that correspond to a view of this module.
 * This is used by the participation report.
 *
 * Note: This is not used by new logging system. Event with
 *       crud = 'r' and edulevel = LEVEL_PARTICIPATING will
 *       be considered as view action.
 *
 * @return array
 */
function pdfannotator_get_view_actions() {
    return array('view','view all');
}

/**
 * List the actions that correspond to a post of this module.
 * This is used by the participation report.
 *
 * Note: This is not used by new logging system. Event with
 *       crud = ('c' || 'u' || 'd') and edulevel = LEVEL_PARTICIPATING
 *       will be considered as post action.
 *
 * @return array
 */
function pdfannotator_get_post_actions() {
    return array('update', 'add');
}

/**
 * Add pdfannotator instance.
 * @param object $data
 * @param mod_pdfannotator_mod_form $mform
 * @return int new pdfannotator instance id
 */
function pdfannotator_add_instance($data, $mform) {
    global $CFG, $DB;
    require_once("$CFG->libdir/resourcelib.php");
    require_once("$CFG->dirroot/mod/pdfannotator/locallib.php");
    $cmid = $data->coursemodule;
    $data->timemodified = time();
    pdfannotator_set_display_options($data);//todo

    pdfannotator_set_annotationtypes();
    
    $data->id = $DB->insert_record('pdfannotator', $data);

    // we need to use context now, so we need to make sure all needed info is already in db
    $DB->set_field('course_modules', 'instance', $data->id, array('id'=>$cmid));
    pdfannotator_set_mainfile($data);

    $completiontimeexpected = !empty($data->completionexpected) ? $data->completionexpected : null;
    \core_completion\api::update_completion_date_event($cmid, 'pdfannotator', $data->id, $completiontimeexpected);

    return $data->id;
}

/**
 * Function is called when a pdfannotator instance is created. It checks whether
 * the annotationtypes table has already been filled. If not, it does so.
 * 
 * @global type $DB
 */
function pdfannotator_set_annotationtypes() {
    global $DB;
    $table = "pdfannotator_annotationtypes";
    $condition = [];
    $types = $DB->record_exists($table, $condition);
    if (!$types) {
        $DB->insert_record($table, array("name" => 'area'), false, false);
        $DB->insert_record($table, array("name" => 'drawing'), false, false);
        $DB->insert_record($table, array("name" => 'highlight'), false, false);
        $DB->insert_record($table, array("name" => 'pin'), false, false);
        $DB->insert_record($table, array("name" => 'strikeout'), false, false);
        $DB->insert_record($table, array("name" => 'textbox'), false, false);
    }
}

/**
 * Update pdfannotator instance.
 * @param object $data
 * @param object $mform
 * @return bool true
 */
function pdfannotator_update_instance($data, $mform) {
    global $CFG, $DB;
    require_once("$CFG->libdir/resourcelib.php");
    require_once("$CFG->dirroot/mod/pdfannotator/locallib.php");
    $data->timemodified = time();
    $data->id           = $data->instance;
    $data->revision++;

    pdfannotator_set_display_options($data);//Todo

    $DB->update_record('pdfannotator', $data);
    pdfannotator_set_mainfile($data);

    $completiontimeexpected = !empty($data->completionexpected) ? $data->completionexpected : null;
    \core_completion\api::update_completion_date_event($data->coursemodule, 'pdfannotator', $data->id, $completiontimeexpected);

    return true;
}

/**
 * Updates display options based on form input.
 *
 * Shared code used by pdfannotator_add_instance and pdfannotator_update_instance.
 *
 * @param object $data Data object
 */
function pdfannotator_set_display_options($data) {// hier könnnten mehrere Ansichten definiert werden
    $displayoptions = array();
    
        $displayoptions['printintro']   = (int)!empty($data->printintro);
    
    $data->displayoptions = serialize($displayoptions);
}

/**
 * Delete pdfannotator instance.
 * @param int $id in mdl_pdfannotator
 * @return bool true
 */
function pdfannotator_delete_instance($id) {
    
    global $DB;

    if (!$pdfannotator = $DB->get_record('pdfannotator', array('id'=>$id))) {
        return false;
    }

    $cm = get_coursemodule_from_instance('pdfannotator', $id);
    \core_completion\api::update_completion_date_event($cm->id, 'pdfannotator', $id, null);

    // note: all context files are deleted automatically

    if(!$DB->delete_records('pdfannotator', array('id'=>$id)) == 1) {
        return false;
    }
    
    //get all annotations of the annotator
//    $records = $DB->get_records('pdfannotator_annotationsneu', ['pdfannotatorid'=>$id]);
    
    if (!$records = $DB->get_records('pdfannotator_annotationsneu', ['pdfannotatorid'=>$id])) {
        return false;
    }
    
    //for every annotation delete all comments attached to it.
    foreach($records as $key => $dataset){
        //deleting also the comments of the annotation
        if(!$DB->delete_records('pdfannotator_comments',['annotationid'=> $dataset->id]) == 1) {
            return false;
        }
    }
    //Deleting all the reports
    if(!$DB->delete_records('pdfannotator_reports',['pdfannotatorid'=>$id])){
        return false;
    } 
    
    //finally delete the annotation of the annotation-table
    if(!$DB->delete_records('pdfannotator_annotationsneu', ['pdfannotatorid'=>$id]) == 1) {
        return false;
    }

    
    return true;
}

/**
 * Given a course_module object, this function returns any
 * "extra" information that may be needed when printing
 * this activity in a course listing.
 *
 * See {@link get_array_of_activities()} in course/lib.php
 *
 * @param stdClass $coursemodule
 * @return cached_cm_info info
 */
function pdfannotator_get_coursemodule_info($coursemodule) {
    global $CFG, $DB;
    require_once("$CFG->libdir/filelib.php");
    require_once("$CFG->dirroot/mod/pdfannotator/locallib.php");
    require_once($CFG->libdir.'/completionlib.php');

    $context = context_module::instance($coursemodule->id);

    if (!$pdfannotator = $DB->get_record('pdfannotator', array('id'=>$coursemodule->instance),
            'id, name, course, timemodified, timecreated, intro, introformat')) {
        return NULL;
    }

    $info = new cached_cm_info();
    $info->name = $pdfannotator->name;
    if ($coursemodule->showdescription) {
        // Convert intro to html. Do not filter cached version, filters run at display time.
        $info->content = format_module_intro('pdfannotator', $pdfannotator, $coursemodule->id, false);
    }

    // See if there is at least one file.
    $fs = get_file_storage();
    $files = $fs->get_area_files($context->id, 'mod_pdfannotator', 'content', 0, 'sortorder DESC, id ASC', false, 0, 0, 1);
    if (count($files) >= 1) {
        $mainfile = reset($files);
        //$info->icon = file_file_icon($mainfile, 24);//hier auskommentieren fals das PDF Icon benutzt werden soll
        $pdfannotator->mainfile = $mainfile->get_filename();
    }

    $display = pdfannotator_get_final_display_type($pdfannotator);// hier hard codieren fals keine weiteren Ansichten nötig
/*
    if ($display == RESOURCELIB_DISPLAY_POPUP) {
        $fullurl = "$CFG->wwwroot/mod/pdfannotator/view.php?id=$coursemodule->id&amp;redirect=1";
        $options = empty($pdfannotator->displayoptions) ? array() : unserialize($pdfannotator->displayoptions);
        $width  = empty($options['popupwidth'])  ? 620 : $options['popupwidth'];
        $height = empty($options['popupheight']) ? 450 : $options['popupheight'];
        $wh = "width=$width,height=$height,toolbar=no,location=no,menubar=no,copyhistory=no,status=no,directories=no,scrollbars=yes,resizable=yes";
        $info->onclick = "window.open('$fullurl', '', '$wh'); return false;";

    } else if ($display == RESOURCELIB_DISPLAY_NEW) {
        $fullurl = "$CFG->wwwroot/mod/pdfannotator/view.php?id=$coursemodule->id&amp;redirect=1";
        $info->onclick = "window.open('$fullurl'); return false;";

    }
	*/

    // If any optional extra details are turned on, store in custom data,
    // add some file details as well to be used later by pdfannotator_get_optional_details() without retriving.
    // Do not store filedetails if this is a reference - they will still need to be retrieved every time.
    
    return $info;
}

/**
 * Called when viewing course page. Shows extra details after the link if
 * enabled.
 *
 * @param cm_info $cm Course module information
 */
function pdfannotator_cm_info_view(cm_info $cm) {
    global $CFG;
    require_once($CFG->dirroot . '/mod/pdfannotator/locallib.php');

    $pdfannotator = (object)array('displayoptions' => $cm->customdata);
    
        $cm->set_after_link(' ' . html_writer::tag('span', '',  //hier kämen Ddetails zum Anzeigen
                array('class' => 'pdfannotatorlinkdetails')));
    
}

/**
 * Lists all browsable file areas
 *
 * @package  mod_pdfannotator
 * @category files
 * @param stdClass $course course object
 * @param stdClass $cm course module object
 * @param stdClass $context context object
 * @return array
 */
function pdfannotator_get_file_areas($course, $cm, $context) {
    $areas = array();
    $areas['content'] = get_string('pdfannotatorcontent', 'pdfannotator');
    return $areas;
}

/**
 * File browsing support for pdfannotator module content area.
 *
 * @package  mod_pdfannotator
 * @category files
 * @param stdClass $browser file browser instance
 * @param stdClass $areas file areas
 * @param stdClass $course course object
 * @param stdClass $cm course module object
 * @param stdClass $context context object
 * @param string $filearea file area
 * @param int $itemid item ID
 * @param string $filepath file path
 * @param string $filename file name
 * @return file_info instance or null if not found
 */
function pdfannotator_get_file_info($browser, $areas, $course, $cm, $context, $filearea, $itemid, $filepath, $filename) {
    global $CFG;

    if (!has_capability('moodle/course:managefiles', $context)) {
        // students can not peak here!
        return null;
    }

    $fs = get_file_storage();

    if ($filearea === 'content') {
        $filepath = is_null($filepath) ? '/' : $filepath;
        $filename = is_null($filename) ? '.' : $filename;

        $urlbase = $CFG->wwwroot.'/pluginfile.php';
        if (!$storedfile = $fs->get_file($context->id, 'mod_pdfannotator', 'content', 0, $filepath, $filename)) {
            if ($filepath === '/' and $filename === '.') {
                $storedfile = new virtual_root_file($context->id, 'mod_pdfannotator', 'content', 0);
            } else {
                // not found
                return null;
            }
        }
        require_once("$CFG->dirroot/mod/pdfannotator/locallib.php");
        return new pdfannotator_content_file_info($browser, $context, $storedfile, $urlbase, $areas[$filearea], true, true, true, false);
    }

    // note: pdfannotator_intro handled in file_browser automatically

    return null;
}

/**
 * Serves the pdfannotator files.
 *
 * @package  mod_pdfannotator
 * @category files
 * @param stdClass $course course object
 * @param stdClass $cm course module object
 * @param stdClass $context context object
 * @param string $filearea file area
 * @param array $args extra arguments
 * @param bool $forcedownload whether or not force download
 * @param array $options additional options affecting the file serving
 * @return bool false if file not found, does not return if found - just send the file
 */
function pdfannotator_pluginfile($course, $cm, $context, $filearea, $args, $forcedownload, array $options=array()) {
    global $CFG, $DB;
    require_once("$CFG->libdir/resourcelib.php");

    if ($context->contextlevel != CONTEXT_MODULE) {
        return false;
    }

    require_course_login($course, true, $cm);
    if (!has_capability('mod/pdfannotator:view', $context)) {
        return false;
    }

    if ($filearea !== 'content') {
        // intro is handled automatically in pluginfile.php
        return false;
    }

    array_shift($args); // ignore revision - designed to prevent caching problems only

    $fs = get_file_storage();
    $relativepath = implode('/', $args);
    $fullpath = rtrim("/$context->id/mod_pdfannotator/$filearea/0/$relativepath", '/');
    do {
        if (!$file = $fs->get_file_by_hash(sha1($fullpath))) {
            if ($fs->get_file_by_hash(sha1("$fullpath/."))) {
                if ($file = $fs->get_file_by_hash(sha1("$fullpath/index.htm"))) {
                    break;
                }
                if ($file = $fs->get_file_by_hash(sha1("$fullpath/index.html"))) {
                    break;
                }
                if ($file = $fs->get_file_by_hash(sha1("$fullpath/Default.htm"))) {
                    break;
                }
            }
            $pdfannotator = $DB->get_record('pdfannotator', array('id'=>$cm->instance), 'id, legacyfiles', MUST_EXIST);
            if ($pdfannotator->legacyfiles != RESOURCELIB_LEGACYFILES_ACTIVE) {
                return false;
            }
            if (!$file = resourcelib_try_file_migration('/'.$relativepath, $cm->id, $cm->course, 'mod_pdfannotator', 'content', 0)) {
                return false;
            }
            // file migrate - update flag
            $pdfannotator->legacyfileslast = time();
            $DB->update_record('pdfannotator', $pdfannotator);
        }
    } while (false);

    // should we apply filters?
    $mimetype = $file->get_mimetype();
    
        $filter = 0;
    

    // finally send the file
    send_stored_file($file, null, $filter, $forcedownload, $options);
}

/**
 * Return a list of page types
 * @param string $pagetype current page type
 * @param stdClass $parentcontext Block's parent context
 * @param stdClass $currentcontext Current context of block
 */
function pdfannotator_page_type_list($pagetype, $parentcontext, $currentcontext) {
    $module_pagetype = array('mod-pdfannotator-*'=>get_string('page-mod-pdfannotator-x', 'pdfannotator'));
    return $module_pagetype;
}

/**
 * Export file pdfannotator contents
 *
 * @return array of file content
 */
function pdfannotator_export_contents($cm, $baseurl) {
    global $CFG, $DB;
    $contents = array();
    $context = context_module::instance($cm->id);
    $pdfannotator = $DB->get_record('pdfannotator', array('id'=>$cm->instance), '*', MUST_EXIST);

    $fs = get_file_storage();
    $files = $fs->get_area_files($context->id, 'mod_pdfannotator', 'content', 0, 'sortorder DESC, id ASC', false);

    foreach ($files as $fileinfo) {
        $file = array();
        $file['type'] = 'file';
        $file['filename']     = $fileinfo->get_filename();
        $file['filepath']     = $fileinfo->get_filepath();
        $file['filesize']     = $fileinfo->get_filesize();
        $file['fileurl']      = file_encode_url("$CFG->wwwroot/" . $baseurl, '/'.$context->id.'/mod_pdfannotator/content/'.$pdfannotator->revision.$fileinfo->get_filepath().$fileinfo->get_filename(), true);
        $file['timecreated']  = $fileinfo->get_timecreated();
        $file['timemodified'] = $fileinfo->get_timemodified();
        $file['sortorder']    = $fileinfo->get_sortorder();
        $file['userid']       = $fileinfo->get_userid();
        $file['author']       = $fileinfo->get_author();
        $file['license']      = $fileinfo->get_license();
        $file['mimetype']     = $fileinfo->get_mimetype();
        $file['isexternalfile'] = $fileinfo->is_external_file();
        if ($file['isexternalfile']) {
            $file['repositorytype'] = $fileinfo->get_repository_type();
        }
        $contents[] = $file;
    }

    return $contents;
}

/**
 * Register the ability to handle drag and drop file uploads
 * @return array containing details of the files / types the mod can handle
 */
//function pdfannotator_dndupload_register() {
//    return array('files' => array(
//                     array('extension' => 'pdf', 'message' => get_string('dnduploadpdfannotator', 'mod_pdfannotator'))
//                 ));
//}

/**
 * Handle a file that has been uploaded
 * @param object $uploadinfo details of the file / content that has been uploaded
 * @return int instance id of the newly created mod
 */
//function pdfannotator_dndupload_handle($uploadinfo) {
//    // Gather the required info.
//    $data = new stdClass();
//    $data->course = $uploadinfo->course->id;
//    $data->name = $uploadinfo->displayname;
//    $data->intro = '';
//    $data->introformat = FORMAT_HTML;
//    $data->coursemodule = $uploadinfo->coursemodule;
//    $data->files = $uploadinfo->draftitemid;
//
//    // Set the display options to the site defaults.
//    $config = get_config('pdfannotator');//
//
//    return pdfannotator_add_instance($data, null);
//}

/**
 * Mark the activity completed (if required) and trigger the course_module_viewed event.
 *
 * @param  stdClass $pdfannotator   pdfannotator object
 * @param  stdClass $course     course object
 * @param  stdClass $cm         course module object
 * @param  stdClass $context    context object
 * @since Moodle 3.0
 */
function pdfannotator_view($pdfannotator, $course, $cm, $context) {

    // Trigger course_module_viewed event.
    $params = array(
        'context' => $context,
        'objectid' => $pdfannotator->id
    );

    $event = \mod_pdfannotator\event\course_module_viewed::create($params);
    $event->add_record_snapshot('course_modules', $cm);
    $event->add_record_snapshot('course', $course);
    $event->add_record_snapshot('pdfannotator', $pdfannotator);
    $event->trigger();

    // Completion.
    $completion = new completion_info($course);
    $completion->set_module_viewed($cm);
}

/**
 * Check if the module has any update that affects the current user since a given time.
 *
 * @param  cm_info $cm course module data
 * @param  int $from the time to check updates from
 * @param  array $filter  if we need to check only specific updates
 * @return stdClass an object with the different type of areas indicating if they were updated or not
 * @since Moodle 3.2
 */
function pdfannotator_check_updates_since(cm_info $cm, $from, $filter = array()) {
    $updates = course_check_module_updates_since($cm, $from, array('content'), $filter);
    return $updates;
}

/**
 * This function receives a calendar event and returns the action associated with it, or null if there is none.
 *
 * This is used by block_myoverview in order to display the event appropriately. If null is returned then the event
 * is not displayed on the block.
 *
 * @param calendar_event $event
 * @param \core_calendar\action_factory $factory
 * @return \core_calendar\local\event\entities\action_interface|null
 */
function mod_pdfannotator_core_calendar_provide_event_action(calendar_event $event,
                                                      \core_calendar\action_factory $factory) {
    $cm = get_fast_modinfo($event->courseid)->instances['pdfannotator'][$event->instance];

    $completion = new \completion_info($cm->get_course());

    $completiondata = $completion->get_data($cm, false);

    if ($completiondata->completionstate != COMPLETION_INCOMPLETE) {
        return null;
    }

    return $factory->create_instance(
        get_string('view'),
        new \moodle_url('/mod/pdfannotator/view.php', ['id' => $cm->id]),
        1,
        true
    );
}
