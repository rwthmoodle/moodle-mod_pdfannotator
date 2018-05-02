<?php
/**
 * @package   mod_pdfannotator
 * @copyright 2018 RWTH Aachen (see README.md)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * 
 */
if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
}
 
require_once($CFG->dirroot.'/course/moodleform_mod.php');
require_once($CFG->dirroot.'/mod/pdfannotator/lib.php');
require_once($CFG->libdir.'/filelib.php');
class mod_pdfannotator_mod_form extends moodleform_mod {
 
    function definition() {
        
        global $CFG, $USER, $COURSE;
        $mform = $this->_form;
        $this->context = context_system::instance();

        $mform -> addElement('hidden', 'idcreator', $USER -> id);
        $mform -> setType('idcreator', PARAM_TEXT);

        $mform -> addElement('hidden', 'idCourse', $COURSE -> id);
        $mform -> setType('idCourse', PARAM_TEXT);

        $mform -> addElement('header', 'general', get_string('general', 'form'));
        $mform -> setType('general', PARAM_TEXT);
        $mform->addElement('text', 'name', get_string('setting_alternative_name', 'pdfannotator'), array('size'=>'48'));
        if (!empty($CFG->formatstringstriptags)) {
            $mform->setType('name', PARAM_TEXT);
        } else {
            $mform->setType('name', PARAM_CLEANHTML);
        }
        $mform->addRule('name', null, 'required', null, 'client');
        $mform->addRule('name', get_string('maximumchars', '', 255), 'maxlength', 255, 'client');
        
        // Description 
        $this->standard_intro_elements();
        
        $element = $mform->getElement('introeditor');
        $attributes = $element->getAttributes();
        $attributes['rows'] = 5;
        $element->setAttributes($attributes);

        // add a filemanager for drag-and-drop file upload
        //$fileoptions = array('subdirs' => 0, 'maxbytes' => 0, 'areamaxbytes' => 10485760, 'maxfiles' => 1,
        //                     'accepted_types' => '.pdf', 'return_types' => 1 | 2); // FILE_INTERNAL | FILE_EXTERNAL wurde durch 1|2 ersetzt, da moodle FILE_INTERNAL bzw. FILE_EXTERNAL hier nicht kennt (komischerweise)
        $filemanager_options = array();
        $filemanager_options['accepted_types'] = '.pdf';
        $filemanager_options['maxbytes'] = 0;
        $filemanager_options['maxfiles'] = 1;//nur eine Datei kann hochgeladen werden
        $filemanager_options['mainfile'] = true;
        
        $mform->addElement('filemanager', 'files', get_string('setting_fileupload', 'pdfannotator'), null, $filemanager_options); // params: 1. type of the element, 2. (html) elementname, 3. label

        $choices = array(
            1 => '1 day',
            2 => '2 days',
            3 => '3 days',
            7 => '1 week',
            14 => '2 weeks',
            21 => '3 weeks',
            28 => '4 weeks',
        );   
        
        $mform->addElement('select', 'newsspan', get_string('setting_choosetimespanfornews', 'pdfannotator'), $choices);
        $mform->setDefault('newsspan', 3);
        
        $mform->addElement('checkbox', 'usevotes', get_string('setting_usevotes', 'pdfannotator'));
        $mform->setType('usevotes', PARAM_TEXT);
        $mform->setDefault('usevotes', 0);
        $mform->addHelpButton('usevotes', 'setting_usevotes', 'pdfannotator');
        
//        $mform->addElement('checkbox', 'allowannotating', get_string('setting_col_annotating_enabled', 'pdfannotator'));
//        $mform -> setType('annotating_enabled', PARAM_TEXT);
//        $mform->setDefault('annotating_enabled', true);
//
//        $mform->addElement('checkbox', 'allowanonymous', get_string('setting_anonymous', 'pdfannotator'));
//        $mform -> setType('anonymous_enabled', PARAM_TEXT);
//        $mform->setDefault('anonymous_enabled', true);
        // add legacy files flag only if used
        if (isset($this->current->legacyfiles) and $this->current->legacyfiles != RESOURCELIB_LEGACYFILES_NO) {
            $options = array(RESOURCELIB_LEGACYFILES_DONE   => get_string('legacyfilesdone', 'pdfannotator'),
                             RESOURCELIB_LEGACYFILES_ACTIVE => get_string('legacyfilesactive', 'pdfannotator'));
            $mform->addElement('select', 'legacyfiles', get_string('legacyfiles', 'pdfannotator'), $options);
        }
        
        $this->standard_coursemodule_elements();
 
        $this->add_action_buttons();
         //-------------------------------------------------------
        $mform->addElement('hidden', 'revision'); // hard-coded as 1; should be changed if version becomes important
        $mform->setType('revision', PARAM_INT);
        $mform->setDefault('revision', 1);
    }
     function data_preprocessing(&$default_values) {// loads the old file in the filemanager
        
        if ($this->current->instance) {
            $contextID=$this->context->id;
            if($contextID==1){
                $contextID=context_module::instance($this->_cm->id)->id;
            }
            $draftitemid = file_get_submitted_draft_itemid('files');            
            file_prepare_draft_area($draftitemid,$contextID , 'mod_pdfannotator', 'content', 0, array('subdirs'=>true));
            $default_values['files'] = $draftitemid;   
        }       
    }

    function definition_after_data() {
        if ($this->current->instance) {
            return;
        }

        parent::definition_after_data();
    }

    function validation($data, $files)  {
        global $USER;

        $errors = parent::validation($data, $files);

        $usercontext = context_user::instance($USER->id);
        $fs = get_file_storage();
        if (!$files = $fs->get_area_files($usercontext->id, 'user', 'draft', $data['files'], 'sortorder, id', false)) {
            $errors['files'] = get_string('required');
            return $errors;
        }
        if (count($files) == 1) {
            // no need to select main file if only one picked
            return $errors;
        } else if(count($files) > 1) {
            $mainfile = false;
            foreach($files as $file) {
                if ($file->get_sortorder() == 1) {
                    $mainfile = true;
                    break;
                }
            }
            // set a default main file
            if (!$mainfile) {
                $file = reset($files);
                file_set_sortorder($file->get_contextid(), $file->get_component(), $file->get_filearea(), $file->get_itemid(),
                                   $file->get_filepath(), $file->get_filename(), 1);
            }
        }
        return $errors;
    }
}