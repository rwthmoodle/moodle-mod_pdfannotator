<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * The purpose of this script is to collect the output data for the index.mustache template
 * and make it available to the renderer. The data is collected via the pdfannotator model
 * and then processed. Therefore, class teacheroverview can be seen as a view controller.
 *
 * @package   mod_pdfannotator
 * @copyright 2018 RWTH Aachen, Anna Heynkes (see README.md)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * Description of index
 *
 * @author degroot
 */
defined('MOODLE_INTERNAL') || die();

class index implements renderable, templatable { // Class should be placed elsewhere.

    private $usestudenttextbox;
    private $usestudentdrawing;
    private $useprint;
    private $printurl;
    private $pdfannotatortextboxvisibility;
    private $pdfannotatorpenvisibility;
    private $pdfannotatorprintvisibility;

    public function __construct($pdfannotator, $isallowedforuser, $file) {

        global $CFG, $USER;

        // If the textbox/drawing is allowed for students, the array should have a single value.
        $this->usestudenttextbox = array();
        if ($pdfannotator->use_studenttextbox || $isallowedforuser) {
            $this->usestudenttextbox = array('use');
            if (!$pdfannotator->use_studenttextbox) {
                $this->pdfannotatortextboxvisibility = 'teachersonly';
            } else {
                $this->pdfannotatortextboxvisibility = '';
            }
        }
        $this->usestudentdrawing = array();
        if ($pdfannotator->use_studentdrawing || $isallowedforuser) {
            $this->usestudentdrawing = array('use');
            if (!$pdfannotator->use_studentdrawing) {
                $this->pdfannotatorpenvisibility = 'teachersonly';
            } else {
                $this->pdfannotatorpenvisibility = '';
            }
        }

        $this->useprint = array();
        $studentsmayprint = pdfannotator_instance::useprint($pdfannotator->id);
        if ($studentsmayprint || $isallowedforuser) {
            $this->useprint = array('use');
            if (!$studentsmayprint) {
                $this->pdfannotatorprintvisibility = 'teachersonly';
            } else {
                $this->pdfannotatorprintvisibility = '';
            }
        }

        $contextid = $file->get_contextid();
        $component = $file->get_component();
        $filearea = $file->get_filearea();
        $itemid = $file->get_itemid();
        $filename = $file->get_filename();

        $this->printurl = "$CFG->wwwroot/pluginfile.php/$contextid/$component/$filearea/$itemid/$filename?forcedownload=1";

    }

    public function export_for_template(renderer_base $output) {
        global $OUTPUT, $PAGE;
        $url = $PAGE->url;
        $data = new stdClass();
        $data->usestudenttextbox = $this->usestudenttextbox;
        $data->usestudentdrawing = $this->usestudentdrawing;
        $data->pixhide = $OUTPUT->image_url('/e/accessibility_checker');
        $data->pixopenbook = $OUTPUT->image_url('openbook', 'mod_pdfannotator');
        $data->pixsinglefile = $OUTPUT->image_url('/e/new_document');
        $data->useprint = $this->useprint;
        $data->printlink = $this->printurl;
        $data->pixprintdoc = $OUTPUT->image_url('download', 'mod_pdfannotator');
        $data->pixprintcomments = $OUTPUT->image_url('print_comments', 'mod_pdfannotator');
        $data->pdfannotatorprintvisibility = $this->pdfannotatorprintvisibility;
        $data->pdfannotatortextboxvisibility = $this->pdfannotatortextboxvisibility;
        $data->pdfannotatorpenvisibility = $this->pdfannotatorpenvisibility;

        return $data;
    }
}
