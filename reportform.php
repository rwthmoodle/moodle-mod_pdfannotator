<?php
/**
 * @package   mod_pdfannotator
 * @copyright 2018 RWTH Aachen, Anna Heynkes (see README.md)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * 
 */
defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir.'/formslib.php');


/**
 * Description of reportform
 *
 * @author Admin
 */
class pdfannotator_reportform extends moodleform {
 
    public function definition() {
        global $CFG;    
        
        $mform = $this->_form; // Don't forget the underscore!
        
        // pass contextual parameters to the form (via set_data() in test.php)
            // course module id
            $mform->addElement('hidden', 'id');
            $mform->setType('id', PARAM_INT);
            // course id
            $mform->addElement('hidden', 'course');
            $mform->setType('course', PARAM_INT);
            // pdf id
            $mform->addElement('hidden', 'pdfid');
            $mform->setType('pdfid', PARAM_INT);
            // pdfname
            $mform->addElement('hidden', 'pdfname');
            $mform->setType('pdfname', PARAM_TEXT);
            // comment id
            $mform->addElement('hidden', 'commentid');
            $mform->setType('commentid', PARAM_INT);
            // action = 'report'
            $mform->addElement('hidden', 'action');
            $mform->setType('action', PARAM_TEXT);
        
        // add a headline
        $mform->addElement('header', 'bookwithmessagetitle', get_string('subtitleforreportcommentform', 'pdfannotator')); 
 
        // add a drop-down selct menu for the reason of complaint
        $selectoptions = [];
        $selectoptions['inaccurate'] = get_string('inaccurate', 'pdfannotator');
        $selectoptions['inappropriate'] = get_string('inappropriate', 'pdfannotator');
        $selectoptions['other reason'] = get_string('other_reason', 'pdfannotator');
        /*
         * The fourth param for this element is an array of options for the select box.
         * The keys are the values for the option and the value of the array is the text
         * for the option. The fifth param $attributes is optional, see text element for
         * description of attributes param.
         */
        $mform->addElement('select', 'type', get_string('reportreason', 'pdfannotator'), $selectoptions); //, $attributes);
        
        // add a textarea for explaining the reason of complaint
        $mform->addElement('textarea', 'introduction', get_string('reason', 'pdfannotator'), 'wrap="virtual" rows="5'
                . '" cols="109"');
            
        // add submit and cancel buttons
        $this->add_action_buttons($cancel = true, get_string('reportsendbutton','pdfannotator'));
    }
    
    function display() {
        $this->_form->display();
    }
    
}