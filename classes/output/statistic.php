<?php

/**
 * The purpose of this script is to collect the output data for the statistic template and
 * make it available to the renderer. The data is collected via the statistic model and then processed.
 * Therefore, class statistic can be seen as a view controller.
 * 
 * @package   mod_pdfannotator
 * @copyright 2018 RWTH Aachen, Friederike Schwager (see README.md)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

/**
 * 
 * The purpose of this script is to collect the output data for the template and
 * make it available to the renderer.
 * 
 */
class statistic implements \renderable, \templatable {

    private $isteacher;
    private $tabledata_1;
    private $tabledata_2;

    /**
     * Konstruktor (nicht notwendig)
     * @param type $pdfannotators
     */
    public function __construct($annotatorid, $courseid, $isteacher = false) {
        global $USER, $PAGE;
        $userid = $USER->id;
        $this->isteacher = $isteacher;

        $pdfannotators = pdfannotator_instance::get_pdfannotator_instances($courseid);
        $model = new statisticmodel($courseid, $annotatorid, $userid, $isteacher);

        $this->tabledata_1 = $model->get_tabledata_1();
        if (!$isteacher) {
            $this->tabledata_2 = $model->get_tabledata_2();
        }

        $params = $model->get_chartdata($pdfannotators);
        $PAGE->requires->js_init_call('setCharts', $params, true);
    }

    /**
     * This function is required by any renderer to retrieve the data structure
     * passed into the template.
     * @param \renderer_base $output
     * @return type
     */
    public function export_for_template(\renderer_base $output) {

        $data = [];

        $data['isteacher'] = $this->isteacher;
        $data['tabledata_1'] = $this->tabledata_1;
        if (!$this->isteacher) {
            $data['tabledata_2'] = $this->tabledata_2;
        }

        return $data;
    }

}
