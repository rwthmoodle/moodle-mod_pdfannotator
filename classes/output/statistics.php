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
 * The purpose of this script is to collect the output data for the template and
 * make it available to the renderer.
 */
class statistics implements \renderable, \templatable {

    private $isteacher;
    private $tabledata1;
    private $tabledata2;

    public function __construct($annotatorid, $courseid, $isteacher) {
        global $USER, $PAGE;
        $userid = $USER->id;
        $this->isteacher = $isteacher;

        $statistics = new pdfannotator_statistics($courseid, $annotatorid, $userid, $isteacher);

        $this->tabledata1 = $statistics->get_tabledata_1();
        if (!$isteacher) {
            $this->tabledata2 = $statistics->get_tabledata_2();
        }

        $params = $statistics->get_chartdata();
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
        $data['tabledata_1'] = $this->tabledata1;
        if (!$this->isteacher) {
            $data['tabledata_2'] = $this->tabledata2;
        }

        return $data;
    }

}
