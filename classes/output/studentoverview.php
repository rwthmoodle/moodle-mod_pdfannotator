<?php

defined('MOODLE_INTERNAL') || die();

/**
 * The purpose of this script is to collect the output data for the studentoverview template and
 * make it available to the renderer. The data is collected via the pdfannotator model and then processed.
 * Therefore, class studentoverview can be seen as a view controller.
 * 
 * @package   mod_pdfannotator
 * @copyright 2018 RWTH Aachen, Anna Heynkes (see README.md)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class studentoverview implements renderable, templatable {
    
    private $openannotator;
    private $annotators_with_answers;
    private $annotators_with_questions;
    private $annotators_with_posts_by_this_user = [];
    private $annotators_with_hiddenentries = [];
    
    
    public function __construct($courseid, $thisannotator, $newsspan = 3) {
    
        $this->openannotator = $thisannotator;
        $this->newsspan = $newsspan;
        
        global $USER;
        
        // 0. Access/create the model
        $annotator_list = pdfannotator_instance::get_pdfannotator_instances($courseid, $thisannotator);
        
        foreach($annotator_list as $annotator) {
            
            // 1. Model is told to retrieve its data from db
            $annotator->set_myQuestions();
            $cmid = $annotator->get_coursemoduleid();

            // 2. Select and organize the model's data for display

            // 2.1 Collect all answers to the user's questions in this course
            // Note that students cannot delete answers to their questions. They can however mark an answer as seen. These are collected separately.
            $questioninfolist = $annotator->get_myquestions();
            $answers = [];
            $hiddenanswers = [];
            if(!empty($questioninfolist)) {

                foreach($questioninfolist as $questioninfo) {

                    if(!empty($questioninfo->answers)) {

                        foreach($questioninfo->answers as $answer) {

                            if ($answer->userid != $USER->id) {
                                
                                if($answer->isdeleted == 1) {
                                    $answer->content = "<em>".get_string('deletedComment', 'pdfannotator')."</em>";
                                }                              
                                
                                if ($answer->seen != 1) {
                                    $answers[] = array('answeredquestion' => $questioninfo->questioncontent, 'answer' => $answer->content, 'answerid' => $answer->id, 'annoid' => $questioninfo->questionid, 'link' => $questioninfo->questionlink.'&commid='.$answer->id);
                                } else {
                                    
                                    $link = new moodle_url('/mod/pdfannotator/view.php', array('id' => $cmid, 'page' => $questioninfo->page, 'annoid' => $questioninfo->questionid, 'commid' => $answer->id));
                                    $hiddenanswers[] = array('hiddenentrysubjectline' => $questioninfo->questioncontent, 'hiddenentry' => $answer->content, 'hiddenentrysid' => $answer->id, 'annoid' => $questioninfo->questionid, 'link' => $link);
                                
                                }
                                
                            }

                        }

                    }
                    
                }
            }

            // most recent entries should come first
            $answers = array_reverse($answers);
            $hiddenanswers = array_reverse($hiddenanswers);
            
            if (count($answers)>=1) {                   
                $this->annotators_with_answers[] = array('annotatorid' => $annotator->get_id(), 'annotatorname' => $annotator->get_name(), 'answers' => $answers, 'answercount' => count($answers));
            }
            
            if (count($hiddenanswers)>=1) {                   
                $this->annotators_with_hiddenentries[] = array('annotatorid' => $annotator->get_id(), 'annotatorname' => $annotator->get_name(), 'hiddenentries' => $hiddenanswers, 'hiddenentrycount' => count($hiddenanswers));
                
            }

            // 2.2 Collect all new questions
            $annotator->set_latest_questions($newsspan);
            $questionlist = $annotator->get_latest_questions();
            $questions = [];

            if(!empty($questionlist)) { // ist immer der Fall -> Hintergrund ändern

                foreach($questionlist as $questionarray) {
                    foreach($questionarray as $question) {
                        $question->link = new moodle_url('/mod/pdfannotator/view.php', array('id' => $cmid, 'page' => $question->page, 'annoid' => $question->annotationid, 'commid' => $question->commentid));
                        $questions[] = $question;
                    }

                }
                // most recent entries should come first
                $questions = array_reverse($questions);
                if (count($questions)>=1) {
                    $this->annotators_with_questions[] = array('annotatorid' => $annotator->get_id(), 'annotatorname' => $annotator->get_name(), 'questions' => $questions, 'questioncount'=> count($questions));
                }
            }
            
            // 2.3 Collect all questions/comments posted by this user in this course
            $userposts = $annotator->get_posts_by_user($USER->id);

            $posts = [];
            
            if (!empty($userposts)) {
                
                foreach ($userposts as $userpost) {
                    
                    $userpost->link = new moodle_url('/mod/pdfannotator/view.php', array('id' => $cmid, 'page' => $userpost->page, 'annoid' => $userpost->annotationid, 'commid' => $userpost->commid));
                    $posts[] = array('content' => $userpost->content, 'link' => $userpost->link);
                           
                }
                // most recent entries should come first
                $posts = array_reverse($posts);
                if (count($posts) > 0) {
                    $this->annotators_with_posts_by_this_user[] = array('annotatorid' => $annotator->get_id(), 'annotatorname' => $annotator->get_name(), 'posts' => $posts, 'postcount' => count($posts));
                }
                
            }
                        
        } 
        
    }
    /**
     * This function is required by any renderer to retrieve the data structure
     * passed into the template.
     * 
     * @param \renderer_base $output
     */
    public function export_for_template(\renderer_base $output) {
        
        global $CFG;
        global $USER;
        global $OUTPUT;
        
        $data = [];
            
            $data['openannotator'] = $this->openannotator;
        
            $data['annotators_with_answers'] = $this->annotators_with_answers;
            if (empty($this->annotators_with_answers)) {
                $data['noanswers'] = get_string('noanswers', 'pdfannotator');
            }
            $data['annotators_with_questions'] = $this->annotators_with_questions;
            if (empty($this->annotators_with_questions)) {
                $data['noquestions'] = get_string('noquestions_overview', 'pdfannotator');
            }
            $data['annotators_with_posts_by_this_user'] = $this->annotators_with_posts_by_this_user;
            if (empty($this->annotators_with_posts_by_this_user)) {
                $data['nomyposts'] = get_string('nomyposts', 'pdfannotator');
            }
            $data['annotators_with_hiddenentries'] = $this->annotators_with_hiddenentries;
            if (empty($this->annotators_with_hiddenentries)) {
                $data['nohiddenentries'] = get_string('nohiddenentries_student', 'pdfannotator');
            }
               
            // icons
            $data['pixcollapsed'] = $OUTPUT->image_url("/t/collapsed"); // moodle icon ('moodle/pix/t/collapsed.png')
            $data['pixgotox'] = $OUTPUT->image_url('link_klein', 'mod_pdfannotator'); // plugin-specific icon, not part of a theme ('/moodle/mod/pdfannotator/pix/link_klein.png')
            $data['pixhide'] = $OUTPUT->image_url('/e/accessibility_checker');
            $data['pixdisplay'] = $OUTPUT->image_url('/i/hide'); // '/moodle/pix/i/hide.png'
            
            $data['linktosettingspage'] = new moodle_url('/message/notificationpreferences.php', array('userid' => $USER->id));
            
            $data['timespan'] = $this->newsspan;
            
        return $data;
            
    }

}


////////////////////////////////////////////////////////////////////////////////

class studentoverviewUpdateAnswers implements \renderable, \templatable {

    private $openannotator;
    private $annotators_with_answers = [];

    /**
     * Konstruktor (not necessary)
     * @param type $pdfannotators
     */
    public function __construct($courseid, $thisannotator) {

        $this->openannotator = $thisannotator;
        
        global $USER;
        
        // 0. Access/create the model
        $annotator_list = pdfannotator_instance::get_pdfannotator_instances($courseid, $thisannotator);
          
        foreach ($annotator_list as $annotator) {
            
            // 1. Model is told to retrieve its data from db
            $annotator->set_myQuestions();
            $cmid = $annotator->get_coursemoduleid();

            // 2. Select and organize the model's data for display
            
            // 2.1 Collect all answers to the user's questions in this course
            // Note that students cannot delete answers to their questions. They can however mark an answer as seen. These are collected separately.
            $questioninfolist = $annotator->get_myquestions();
            $answers = [];
            if(!empty($questioninfolist)) {

                foreach($questioninfolist as $questioninfo) {

                    if(!empty($questioninfo->answers)) {

                        foreach($questioninfo->answers as $answer) {

                            if ($answer->userid != $USER->id) {
                                
                                if($answer->isdeleted == 1) {
                                    $answer->content = "<em>".get_string('deletedComment', 'pdfannotator')."</em>";
                                }                              
                                
                                if ($answer->seen != 1) {
                                    $answers[] = array('answeredquestion' => $questioninfo->questioncontent, 'answer' => $answer->content, 'answerid' => $answer->id, 'annoid' => $questioninfo->questionid, 'link' => $questioninfo->questionlink.'&commid='.$answer->id);
                                }
                                                               
                            }

                        }

                    }
                    
                }
            }
            // most recent entries should come first
            $answers = array_reverse($answers);
            if (count($answers)>=1) {                   
                $this->annotators_with_answers[] = array('annotatorid' => $annotator->get_id(), 'annotatorname' => $annotator->get_name(), 'answers' => $answers, 'answercount' => count($answers));
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
        
            $data['annotators_with_answers'] = $this->annotators_with_answers;
            if (empty($this->annotators_with_answers)) {
                $data['noanswers'] = get_string('noanswers', 'pdfannotator');
            }
            
        // icons
//            $data['pixcollapsed'] = $OUTPUT->image_url("/t/collapsed"); // moodle icon  'moodle/pix/t/collapsed.png';
//            $data['pixgotox'] = $OUTPUT->image_url('link_klein', 'mod_pdfannotator'); // plugin-specific icon, not part of a theme '/moodle/mod/pdfannotator/pix/link_klein.png'
//            $data['pixhide'] = $OUTPUT->image_url('/e/accessibility_checker');
//            $data['pixdisplay'] = $OUTPUT->image_url('/i/hide'); // '/moodle/pix/i/hide.png'    

        return $data;
    }

}
///////////////////////////////////////////////////////////////////////////////////////////////////////


class studentoverviewUpdateHiddenEntries implements \renderable, \templatable {

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
        $annotator_list = pdfannotator_instance::get_pdfannotator_instances($courseid, $thisannotator);
          
        foreach ($annotator_list as $annotator) {
            
            // 1. Model is told to retrieve its data from db
            $annotator->set_myQuestions();
            $cmid = $annotator->get_coursemoduleid();

            // 2. Select and organize the model's data for display
            
            // 2.1 Collect all answers to the user's questions in this course
            // Note that students cannot delete answers to their questions. They can however mark an answer as seen. These are collected separately.
            $questioninfolist = $annotator->get_myquestions();
            $hiddenanswers = [];
            if(!empty($questioninfolist)) {

                foreach($questioninfolist as $questioninfo) {

                    if(!empty($questioninfo->answers)) {

                        foreach($questioninfo->answers as $answer) {

                            if ($answer->userid != $USER->id) {
                                
                                if($answer->isdeleted == 1) {
                                    $answer->content = "<em>".get_string('deletedComment', 'pdfannotator')."</em>";
                                }                              
                                
                                if ($answer->seen != 0 && $answer->seen != -1) {
//                                    $link = new moodle_url('/mod/pdfannotator/view.php', array('id' => $cmid, 'page' => $questioninfo->page, 'annoid' => $questioninfo->questionid, 'commid' => $answer->id));
                                    $link = $questioninfo->questionlink.'&commid='.$answer->id;
                                    $hiddenanswers[] = array('hiddenentrysubjectline' => $questioninfo->questioncontent, 'hiddenentry' => $answer->content, 'hiddenentrysid' => $answer->id, 'annoid' => $questioninfo->questionid, 'link' => $link);
                                
                                }
                                
                            }

                        }

                    }
                    
                }
            }
            // most recent entries should come first
            $hiddenanswers = array_reverse($hiddenanswers);
            if (count($hiddenanswers)>=1) {                   
                $this->annotators_with_hiddenentries[] = array('annotatorid' => $annotator->get_id(), 'annotatorname' => $annotator->get_name(), 'hiddenentries' => $hiddenanswers, 'hiddenentrycount' => count($hiddenanswers));
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

        global $OUTPUT;

        $data = [];
        
        $data['openannotator'] = $this->openannotator;
               
        $data['annotators_with_hiddenentries'] = $this->annotators_with_hiddenentries;
            if (empty($this->annotators_with_hiddenentries)) {
                $data['noanswers'] = get_string('noanswers', 'pdfannotator');
            }
        
        // 5. icons
//        $data['pixcollapsed'] = $OUTPUT->image_url("/t/collapsed"); // moodle icon  'moodle/pix/t/collapsed.png';
//        $data['pixgotox'] = $OUTPUT->image_url('link_klein', 'mod_pdfannotator'); // plugin-specific icon, not part of a theme '/moodle/mod/pdfannotator/pix/link_klein.png'
//        $data['pixhide'] = $OUTPUT->image_url('/e/accessibility_checker');
//        $data['pixdisplay'] = $OUTPUT->image_url('/i/hide'); // '/moodle/pix/i/hide.png'
//        $data['pixdelete'] = $OUTPUT->image_url('/t/delete');

        return $data;
    }

}
