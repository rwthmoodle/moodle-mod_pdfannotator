<?php
/**
 * @package   mod_pdfannotator
 * @copyright 2018 RWTH Aachen, Rabea de Groot and Anna Heynkes (see README.md)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * 
 */
defined('MOODLE_INTERNAL') || die();

require_once('../../config.php');
require_once($CFG->dirroot . '/mod/assign/locallib.php');

class mod_pdfannotator_renderer extends plugin_renderer_base{
    /**
     * 
     * @param \templatable $statistic
     * @return type
     */
    public function render_statistic(\templatable $statistic) {
        $data = $statistic->export_for_template($this);
        return $this->render_from_template('mod_pdfannotator/statistic', $data); // 1. param specifies the template, 2. param the data to pass into it
    }
    /**
     * 
     * @param \templatable $teacheroverview renderable
     * @return type
     */
    public function render_teacheroverview(\templatable $teacheroverview) {
        $data = $teacheroverview->export_for_template($this);
        return $this->render_from_template('mod_pdfannotator/teacheroverview', $data); // 1. param specifies the template, 2. param the data to pass into it
    } 
    /**
     * 
     * @param \templatable $studentoverview
     * @return type
     */
    public function render_studentoverview(\templatable $studentoverview) {
        $data = $studentoverview->export_for_template($this);
        return $this->render_from_template('mod_pdfannotator/studentoverview', $data); // 1. param specifies the template, 2. param the data to pass into it
    } 
    /**
     * 
     * @param type $index
     * @return type
     */
    public function render_index( $index){
        return $this->render_from_template('pdfannotator/index', $index->export_for_template($this));
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
    /**
     * 
     * 
     * @param type $infolist
     * @return type
     */
    public function render_pdfannotator_report_info($pdfannotator_list) { // Vorbild: render_cilscheduler_slot_booker
        
        $table = new html_table();
        $table->head  = array('Nr.', get_string('location', 'pdfannotator'), get_string('comment', 'pdfannotator'), get_string('author', 'pdfannotator'), get_string('datetime', 'pdfannotator'), get_string('report', 'pdfannotator'), get_string('author', 'pdfannotator'), get_string('datetime', 'pdfannotator'));
        $table->align = array ('left', 'left', 'left', 'left', 'left', 'left');
        $table->id = 'reportinfotable';//'slotbookertable'; // XXX
        $table->data = array();

        $previousdate = '';
        $previoustime = '';
        $previousendtime = '';
        $canappoint = false;

        $count = 1;
        
        foreach($pdfannotator_list as $annotator_instance) { // for each annotator instance
                
            $infolist = $annotator_instance->get_reports();
        
            foreach ($infolist as $report) {

                $rowdata = array();

                $rowdata[] =  $count;
                $count++;

                $commentlocation = $report->pdfannotatorname . ', ' . get_string('page', 'pdfannotator') . ' ' . $report->page;
                $rowdata[] =  $commentlocation;
                $rowdata[] = $report->commentcontent;
                $rowdata[] = $report->commentauthor;
                $rowdata[] = $report->userdatetime;
                $rowdata[] = $report->reportcontent;
                $rowdata[] = $report->reportinguser;
                $rowdata[] = $report->timereported;

                $table->data[] = $rowdata;

            }
        }
        return html_writer::table($table);
            
            
    }
    
    
    public function render_pdfannotator_question_info($pdfannotator_list) {
        
        $table = new html_table();
        $table->head  = array('Nr.', get_string('location', 'pdfannotator'), get_string('question', 'pdfannotator'), get_string('answers', 'pdfannotator'), get_string('read', 'pdfannotator'));
        $table->align = array ('left', 'left', 'left', 'left', 'left', 'left');
        $table->id = 'reportinfotable';
        $table->data = array();

        $previousdate = '';
        $previoustime = '';
        $previousendtime = '';
        $canappoint = false;

        $count = 1;
        
        foreach($pdfannotator_list as $annotator_instance) { // for each annotator instance
                
            $infolist = $annotator_instance->get_myquestions();
            
            if( !empty($infolist) ) {
                
                foreach ($infolist as $questioninfo) {// for each question (questioninfo object)

                    $answercount = 1;
                    foreach($questioninfo->answers as $answerinfo) { // for each answer to this question (answer info object)

                        $rowdata = array();
                            $rowdata[] =  $count;
                        
                        // 1. Add info about the question (location, content) (only) for the first answer
                        if ($answercount == 1) { // 
                            $rowdata[] = $questioninfo->pdfannotatorname . ',<br>' . get_string('page', 'pdfannotator') . ' ' . $questioninfo->page;
                            $rowdata[] = $questioninfo->questioncontent;
                        } else {
                            $rowdata[] = '';
                            $rowdata[] = '';
                        }
                        // 2. Then add the answer
                        $rowdata[] = $answerinfo->content; //get_answer_content();
                        $rowdata[] = '';

                        $table->data[] = $rowdata;
                        
                        $answercount++;
                        $count++;
                    }
                }  
            }
        }     
        return html_writer::table($table);         
    }
    
    
    public function createSeenLink($cm) {
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
    private function teacherview_tab(moodle_url $baseurl, $namekey, $action, $nameargs = null) {
        $taburl = new moodle_url($baseurl, array('action' => $action));
        $tabname = get_string($namekey, 'pdfannotator', $nameargs);
        $id = $action;
        $tab = new tabobject($id, $taburl, $tabname);
        return $tab;
    }
    
    private function teacherview_tab_doc(moodle_url $baseurl, $namekey, $action, $nameargs = null) {
        $taburl = new moodle_url($baseurl, array('action' => $action));
        $tabname = $namekey;
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
    public function render_pdfannotator_teacherview_tabs(moodle_url $baseurl, $selected = null, /*$pdfannotatorname,*/ $inactive = null) {

        $level1 = array(
            $this->teacherview_tab($baseurl, 'overview', 'overview'),
            $this->teacherview_tab($baseurl, 'document', 'view'),
            $this->teacherview_tab($baseurl, 'statistic', 'statistic'),
        );
        return $this->tabtree($level1, $selected, $inactive);
    }
      
} // renderer class

class index implements renderable, templatable { // should be placed elsewhere
    public function export_for_template(renderer_base $output) {
        global $OUTPUT,$PAGE;
        $url = $PAGE->url;
        $data = new stdClass();
        $data->highlightimage = $OUTPUT->image_url('e/text_highlight_picker','core');
        $data->pixhide = $OUTPUT->image_url('/e/accessibility_checker');
        
        return $data;
    }
}