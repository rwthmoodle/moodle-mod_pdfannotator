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
 * @package   mod_pdfannotator
 * @copyright 2018 RWTH Aachen, Rabea de Groot and Anna Heynkes (see README.md)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 */
defined('MOODLE_INTERNAL') || die();

require_once('../../config.php');

class mod_pdfannotator_renderer extends plugin_renderer_base {

    /**
     *
     * @param type $index
     * @return type
     */
    public function render_index($index) {
        return $this->render_from_template('pdfannotator/index', $index->export_for_template($this));
    }

    /**
     *
     * @param \templatable $overview
     * @return type
     */
    public function render_overview_page(\templatable $overview) {
        $data = $overview->export_for_template($this);
        // 1. Param specifies the template, 2. param the data to pass into it.
        return $this->render_from_template('mod_pdfannotator/overview', $data);
    }

    // TODO Obsolete testfunction?
    public function render_printview(\templatable $printview) {
        $data = $printview->export_for_template($this);
        // 1. Param specifies the template, 2. param the data to pass into it.
        return $this->render_from_template('mod_pdfannotator/printview', $data);
    }

    /**
     *
     * @param \templatable $teacheroverview renderable
     * @return type
     */
    public function render_teacheroverview(\templatable $teacheroverview) {
        $data = $teacheroverview->export_for_template($this);
        return $this->render_from_template('mod_pdfannotator/teacheroverview', $data);
    }

    /**
     *
     * @param \templatable $studentoverview
     * @return type
     */
    public function render_studentoverview(\templatable $studentoverview) {
        $data = $studentoverview->export_for_template($this);
        return $this->render_from_template('mod_pdfannotator/studentoverview', $data);
    }

    /**
     *
     * @param \templatable $statistic
     * @return type
     */
    public function render_statistic(\templatable $statistic) {
        $data = $statistic->export_for_template($this);
        return $this->render_from_template('mod_pdfannotator/statistic', $data);
    }

    /**
     * Render a table containing information about a comment the user wants to report
     *
     * @param pdfannotator_comment_info $info a renderable
     * @return string
     */
    public function render_pdfannotator_comment_info(pdfannotator_comment_info $info) {
        $o = '';
        $o .= $this->output->container_start('appointmentinfotable');
        $o .= $this->output->box_start('boxaligncenter appointmentinfotable');

        $t = new html_table();

        $row = new html_table_row();
        $cell1 = new html_table_cell(get_string('slotdatetimelabel', 'pdfannotator'));
        $cell2 = $info->datetime;
        $row->cells = array($cell1, $cell2);
        $t->data[] = $row;

        $row = new html_table_row();
        $cell1 = new html_table_cell(get_string('author', 'pdfannotator'));
        $cell2 = new html_table_cell($info->author);
        $row->cells = array($cell1, $cell2);
        $t->data[] = $row;

        $row = new html_table_row();
        $cell1 = new html_table_cell(get_string('comment', 'pdfannotator'));
        $cell2 = new html_table_cell($info->content);
        $row->cells = array($cell1, $cell2);
        $t->data[] = $row;

        $o .= html_writer::table($t);
        $o .= $this->output->box_end();
        $o .= $this->output->container_end();
        return $o;
    }

    public function create_seen_link($cm) {
        $link = "<a href='/mod/cilscheduler/overview.php?id=>$cm->id>"."Link</a>";
        return $link;
    }

    /**
     * Construct a tab header in the teacher view.
     *
     * @param moodle_url $baseurl
     * @param string $namekey
     * @param string $what
     * @param string $subpage
     * @param string $nameargs
     * @return tabobject
     */
    private function pdfannotator_create_tab(moodle_url $baseurl, $namekey = null, $action, $pdfannotatorname = null, $nameargs = null) {
        $taburl = new moodle_url($baseurl, array('action' => $action));
        $tabname = get_string($namekey, 'pdfannotator', $nameargs);
        if ($pdfannotatorname) {
            strlen($pdfannotatorname) > 20 ? $tabname = substr($pdfannotatorname, 0, 21) . "..." : $tabname = $pdfannotatorname;
        }
        $id = $action;
        $tab = new tabobject($id, $taburl, $tabname);
        return $tab;
    }

    /**
     * Render the tab header hierarchy in the teacher view.
     *
     * @param cilscheduler_instance $cilscheduler the cilscheduler in question
     * @param moodle_url $baseurl base URL for the tab addresses
     * @param string $selected the selected tab
     * @param array $inactive any inactive tabs
     * @return string rendered tab tree
     */
    public function pdfannotator_render_tabs(moodle_url $baseurl, $selected = null, $pdfannotatorname, $inactive = null) {

        $level1 = array(
            $this->pdfannotator_create_tab($baseurl, 'overview', 'overview'),
            $this->pdfannotator_create_tab($baseurl, 'document', 'view', $pdfannotatorname),
            $this->pdfannotator_create_tab($baseurl, 'statistic', 'statistic'),
        );
        return $this->tabtree($level1, $selected, $inactive);
    }


    public function render_pdfannotator_conversation_info(pdfannotator_conversation_info $info) {
        $o = '';
        $o .= $this->output->container_start('conversationinfotable');
        $o .= $this->output->box_start('boxaligncenter conversationinfotable');

        $t = new html_table();

        $row = new html_table_row();
        $cell1 = new html_table_cell('Seite'); // get_string('slotdatetimelabel', 'pdfannotator')
        $cell2 = new html_table_cell('Frage'); // $info->datetime;
        $cell3 = new html_table_cell('Antworten');
        $cell4 = new html_table_cell('Autor');
        $row->cells = array($cell1, $cell2);
        $t->data[] = $row;

        $row = new html_table_row();
        $cell1 = new html_table_cell(get_string('author', 'pdfannotator'));
        $cell2 = new html_table_cell($info->author);
        $row->cells = array($cell1, $cell2);
        $t->data[] = $row;

        $row = new html_table_row();
        $cell1 = new html_table_cell(get_string('comment', 'pdfannotator'));
        $cell2 = new html_table_cell($info->content);
        $row->cells = array($cell1, $cell2);
        $t->data[] = $row;

        $o .= html_writer::table($t);
        $o .= $this->output->box_end();
        $o .= $this->output->container_end();
        return $o;
    }

}
