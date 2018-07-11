<?php

/**
 * @package   mod_pdfannotator
 * @copyright 2018 RWTH Aachen, Anna Heynkes (see README.md)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 */
defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/formslib.php');

/**
 * Description of reportform
 *
 * @author Admin
 */
class pdfannotator_reportform extends moodleform {

    public function definition() {
        global $CFG;

        $mform = $this->_form; // Don't forget the underscore!
        // Pass contextual parameters to the form (via set_data() in controller.php)
        // Course module id.
        $mform->addElement('hidden', 'id');
        $mform->setType('id', PARAM_INT);
        // Course id.
        $mform->addElement('hidden', 'course');
        $mform->setType('course', PARAM_INT);
        // Pdf id.
        $mform->addElement('hidden', 'pdfannotatorid');
        $mform->setType('pdfannotatorid', PARAM_INT);
        // Pdfname.
        $mform->addElement('hidden', 'pdfname');
        $mform->setType('pdfname', PARAM_TEXT);
        // Comment id.
        $mform->addElement('hidden', 'commentid');
        $mform->setType('commentid', PARAM_INT);
        // action = 'report'
        $mform->addElement('hidden', 'action');
        $mform->setType('action', PARAM_TEXT);

        // Add a headline.
        $mform->addElement('header', 'bookwithmessagetitle', get_string('subtitleforreportcommentform', 'pdfannotator'));

        // Add a textarea for explaining the reason of complaint.
        $mform->addElement('textarea', 'introduction', get_string('reason', 'pdfannotator'), 'wrap="virtual" rows="5'
                . '" cols="109"');

        // Add submit and cancel buttons.
        $this->add_action_buttons($cancel = true, get_string('reportsendbutton', 'pdfannotator'));
    }

    function display() {
        $this->_form->display();
    }

}
