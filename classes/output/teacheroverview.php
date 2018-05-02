<?php
/**
 * The purpose of this script is to collect the output data for the teacheroverview template
 * and make it available to the renderer. The data is collected via the pdfannotator model
 * and then processed. Therefore, class teacheroverview can be seen as a view controller.
 *
 * @package   mod_pdfannotator
 * @copyright 2018 RWTH Aachen, Anna Heynkes (see README.md)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * 
 */
defined('MOODLE_INTERNAL') || die();

class teacheroverview implements \renderable, \templatable {

    private $openannotator;
    private $newsspan;
    private $annotators_with_reports = [];
    private $annotators_with_questions = [];
    private $annotators_with_posts_by_this_user = [];
    private $annotators_with_hiddenentries = [];

    /**
     * 
     * @global type $USER
     * @param int $courseid
     * @param int $thisannotator id of the currently opened annotator
     * @param int $newsspan number of days that a new comment is to be displayed as new on the overview page
     */
    public function __construct($courseid, $thisannotator, $newsspan = 3) {

        $this->openannotator = $thisannotator;
        $this->newsspan = $newsspan;
        
        global $USER;
        
        // 0. Access/create the model
        $annotator_list = pdfannotator_instance::get_pdfannotator_instances($courseid, $thisannotator);
        
        foreach ($annotator_list as $annotator) {
             
            // 1. Model is told to retrieve its data from db
            $annotator->set_reports($courseid);
            $annotator->set_latest_questions($newsspan);
                        
            $cmid = $annotator->get_coursemoduleid();

            // 2. Select and organize the model's data for display
            
            // 2.1. Collect all reports of inappropriate comments
            $reportlist = $annotator->get_reports();
            $reports = [];

            if (!empty($reportlist)) {

                foreach ($reportlist as $report) {
                    if ($report->seen != 1) {
                        $reports[] = array('reportedcomment' => $report->comment, 'report' => $report->report, 'reportid' => $report->reportid, 'link' => $report->link);
                    }
                }
                // most recent entries should come first
                $reports = array_reverse($reports);
                if (count($reports) >= 1) {
                    $this->annotators_with_reports[] = array('annotatorid' => $annotator->get_id(), 'annotatorname' => $annotator->get_name(), 'reportcount' => count($reports), 'reports' => $reports);
                }
            }

            // 2.2 Collect all new questions
            $questionlist = $annotator->get_latest_questions();
            $questions = [];
            if (!empty($questionlist)) {

                foreach ($questionlist as $questionarray) {
                    foreach ($questionarray as $question) {
                        $question->link = new moodle_url('/mod/pdfannotator/view.php', array('id' => $cmid, 'page' => $question->page, 'annoid' => $question->annotationid, 'commid' => $question->commentid));
                        $questions[] = $question;
                    }
                }
                // most recent entries should come first
                $questions = array_reverse($questions);
                if (count($questions) > 0) {
                    $this->annotators_with_questions[] = array('annotatorid' => $annotator->get_id(), 'annotatorname' => $annotator->get_name(), 'questions' => $questions, 'questioncount' => count($questions));
                }
            }
            
            // 2.3 Collect all questions/comments posted by this user in this course
            $userposts = $annotator->get_posts_by_user($USER->id);

            $posts = [];
            
            if (!empty($userposts)) {
                
                foreach ($userposts as $userpost) {
                    
                    $params = array('id' => $cmid, 'page' => $userpost->page, 'annoid' => $userpost->annotationid, 'commid' => $userpost->commid);
                    $link = new moodle_url('/mod/pdfannotator/view.php', $params);
                    
                    $posts[] = array('content' => $userpost->content, 'link' => $link);
                           
                }
                // most recent entries should come first
                $posts = array_reverse($posts);
                if (count($posts) > 0) {
                    $this->annotators_with_posts_by_this_user[] = array('annotatorid' => $annotator->get_id(), 'annotatorname' => $annotator->get_name(), 'posts' => $posts, 'postcount' => count($posts));
                }
                
            }
            
            // 2.4 Collect all hidden reports in this course
            $hiddenreports = [];
            $link = '';
            
            $hiddenentries = $annotator->get_hidden_reports();
            
            if( !empty($hiddenentries) ) {
                
                foreach ($hiddenentries as $report) {
                    $link = new moodle_url('/mod/pdfannotator/view.php', array('id' => $cmid, 'page' => $report->page, 'annoid' => $report->annotationid, 'commid' => $report->commentid));
                    $hiddenreports[] = array('hiddenentrysubjectline' => $report->commentcontent, 'hiddenentry' => $report->message, 'hiddenentrysid' => $report->id, 'link' => $link);
                }
                // most recent entries should come first
                $hiddenreports = array_reverse($hiddenreports);
                if (count($hiddenreports) > 0) {
                    $this->annotators_with_hiddenentries[] = array('annotatorid' => $annotator->get_id(), 'annotatorname' => $annotator->get_name(), 'hiddenentrycount' => count($hiddenreports), 'hiddenentries' => $hiddenreports);
                }
            }
            
        } // foreach annotator
    }
    /**
     * 
     * @global type $USER
     * @param \renderer_base $output
     * @return type
     */
    public function export_for_template(\renderer_base $output) {

        global $USER; global $OUTPUT;

        $data = [];
        $data['openannotator'] = $this->openannotator;
        
        // 1. reports of inappropriate comments in this course
        $data['annotators_with_reports'] = $this->annotators_with_reports;
        if (empty($this->annotators_with_reports)) {
            $data['noreports'] = get_string('noreports', 'pdfannotator');
        }
        
        // 2. new questions in this course
        $data['annotators_with_questions'] = $this->annotators_with_questions;
        if (empty($this->annotators_with_questions)) {
            $data['noquestions'] = get_string('noquestions_overview', 'pdfannotator');
        }
        
        // 3. questions/comments posted by this user in this course
        $data['annotators_with_posts_by_this_user'] = $this->annotators_with_posts_by_this_user;
        if (empty($this->annotators_with_posts_by_this_user)) {
            $data['nomyposts'] = get_string('nomyposts', 'pdfannotator');
        }
        
        // 4. hidden reports in this course
        $data['annotators_with_hiddenentries'] = $this->annotators_with_hiddenentries;
        if (empty($this->annotators_with_hiddenentries)) {
            $data['nohiddenentries'] = get_string('nohiddenentries_manager', 'pdfannotator');
        }
        
        // 5. icons
        $data['pixcollapsed'] = $OUTPUT->image_url("/t/collapsed"); // moodle icon  'moodle/pix/t/collapsed.png';
        $data['pixgotox'] = $OUTPUT->image_url('link_klein', 'mod_pdfannotator'); // plugin-specific icon, not part of a theme '/moodle/mod/pdfannotator/pix/link_klein.png'
        $data['pixhide'] = $OUTPUT->image_url('/e/accessibility_checker');
        $data['pixdisplay'] = $OUTPUT->image_url('/i/hide'); // '/moodle/pix/i/hide.png'
        $data['pixdelete'] = $OUTPUT->image_url('/t/delete');
        
        // 6. link to individual settings page
        $data['linktosettingspage'] = new moodle_url('/message/notificationpreferences.php', array('userid' => $USER->id));
        //"moodle/message/notificationpreferences.php?userid=$USER->id";

        $data['timespan'] = $this->newsspan;
        
        return $data;
    }

}

/*****************************************************************************************************************************/

class teacheroverviewUpdateReports implements \renderable, \templatable {

    private $openannotator;
    private $annotators_with_reports = [];
    /**
     * @param type $courseid
     */
    public function __construct($courseid, $thisannotator) {

        $this->openannotator = $thisannotator;
        
        global $USER;
        
        // 0. Access/create the model
        $annotator_list = pdfannotator_instance::get_pdfannotator_instances($courseid);
          
        foreach ($annotator_list as $annotator) {
            
            // 1. Model is told to retrieve its data from db
            $annotator->set_reports($courseid);
            $cmid = $annotator->get_coursemoduleid();

            // 2. Select and organize the model's data for display
            
            // 2.1. Collect all reports of inappropriate comments
            $reportlist = $annotator->get_reports();
            $reports = [];

            if (!empty($reportlist)) {

                foreach ($reportlist as $report) {
                    if ($report->seen != 1) {
                        $reports[] = array('reportedcomment' => $report->comment, 'report' => $report->report, 'reportid' => $report->reportid, 'link' => $report->newlink);
                    }
                }
                // most recent entries should come first
                $reports = array_reverse($reports);
                if (count($reports) >= 1) {
                    $this->annotators_with_reports[] = array('annotatorid' => $annotator->get_id(), 'annotatorname' => $annotator->get_name(), 'reportcount' => count($reports), 'reports' => $reports);
                }
            }

        } // foreach annotator
    }
    /**
     * 
     * @global type $USER
     * @param \renderer_base $output
     * @return type
     */
    public function export_for_template(\renderer_base $output) {

        global $USER; global $OUTPUT;

        $data = [];
        $data['openannotator'] = $this->openannotator;
        
        // reports of inappropriate comments in this course
        $data['annotators_with_reports'] = $this->annotators_with_reports;
        if (empty($this->annotators_with_reports)) {
            $data['noreports'] = get_string('noreports', 'pdfannotator');
        }

        return $data;
    }

}
/*****************************************************************************************************************************/

class teacheroverviewUpdateHiddenEntries implements \renderable, \templatable {

    private $openannotator;
    private $annotators_with_hiddenentries = [];

    /**
     * Konstruktor (not necessary)
     * @param type $pdfannotators
     */
    public function __construct($courseid, $thisannotator) {
        
        $this->openannotator = $thisannotator;

        global $USER;
        
        // 0. Access/create the model
        $annotator_list = pdfannotator_instance::get_pdfannotator_instances($courseid);
          
        foreach ($annotator_list as $annotator) {
            
            // 1. Model is told to retrieve its data from db
            $annotator->set_reports($courseid);
                        
            $cmid = $annotator->get_coursemoduleid();

            // 2. Select and organize the model's data for display
        
            // 2.2 Collect all hidden reports in this course
            $hiddenreports = [];
            $link = '';
            
            $hiddenentries = $annotator->get_hidden_reports();
            
            if( !empty($hiddenentries) ) {
                
                foreach ($hiddenentries as $report) {
                    $link = "view.php?id=$cmid&page=$report->page&annoid=$report->annotationid&commid=$report->commentid";
                    $hiddenreports[] = array('hiddenentrysubjectline' => $report->commentcontent, 'hiddenentry' => $report->message, 'hiddenentrysid' => $report->id, 'link' => $link);
                }
                // most recent entries should come first
                $hiddenreports = array_reverse($hiddenreports);
                if (count($hiddenreports) > 0) {
                    $this->annotators_with_hiddenentries[] = array('annotatorid' => $annotator->get_id(), 'annotatorname' => $annotator->get_name(), 'hiddenentrycount' => count($hiddenreports), 'hiddenentries' => $hiddenreports);
                }
            }

        } // foreach annotator
    }
    /**
     * 
     * @global type $USER
     * @param \renderer_base $output
     * @return type
     */
    public function export_for_template(\renderer_base $output) {

        global $USER;
        global $OUTPUT;

        $data = [];
        $data['openannotator'] = $this->openannotator;
        // hidden reports in this course
        $data['annotators_with_hiddenentries'] = $this->annotators_with_hiddenentries;
        if (empty($this->annotators_with_hiddenentries)) {
            $data['nohiddenentries_manager'] = get_string('nohiddenentries_manager', 'pdfannotator');
        }

        return $data;
    }

}
