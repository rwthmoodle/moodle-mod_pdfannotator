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
 * The purpose of this script is to collect the output data for the overview template
 * and make it available to the renderer. The data is collected via the pdfannotator model
 * and then processed. Therefore, class overview can be seen as a view controller.
 *
 * There are also templatables for rerendering partials of the overview template.
 *
 * @package   mod_pdfannotator
 * @copyright 2018 RWTH Aachen, Anna Heynkes (see README.md)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 */
defined('MOODLE_INTERNAL') || die();

class overview implements \renderable, \templatable {

    private $courseid;
    private $openannotator;
    private $newsspan;
    private $viewreports;
    private $viewanswers;
    private $viewquestions;
    private $viewposts;
    private $annotatorswithreports = [];
    private $annotatorswithanswers = [];
    private $annotatorswithquestions = [];
    private $annotatorswithpostsbythisuser = [];
    private $annotatorswithhiddenentries = [];
    private $annotatorswithhiddenreports = [];
    private $count0 = 0;
    private $count1 = 0;
    private $count2 = 0;
    private $count3 = 0;
    private $count4 = 0;
    private $count5 = 0;

    /**
     *
     * @global type $USER
     * @param int $courseid
     * @param int $thisannotator id of the currently opened annotator
     * @param int $newsspan number of days that a new comment is to be displayed as new on the overview page
     * @param user capability $viewreports
     * @param user capability $viewanswers
     * @param user capability $viewquestions
     * @param user capability $viewposts
     */
    public function __construct($thisannotator, $courseid, $newsspan = 3, $viewreports = false, $viewanswers = false, $viewquestions = false, $viewposts = false) {

        $this->openannotator = $thisannotator;
        $this->courseid = $courseid; //$course->id;
        // $this->newsspan = $newsspan;

        $this->viewreports = $viewreports;
        $this->viewanswers = $viewanswers;
        $this->viewquestions = $viewquestions;
        $this->viewposts = $viewposts;

        global $USER;

        // 0. Access/create the model.
        $annotatorlist = pdfannotator_instance::get_pdfannotator_instances($courseid, $thisannotator);

        foreach ($annotatorlist as $annotator) {

            // 1. Model is told to retrieve certain data from db, depending on the user's capabilities.
            if ($viewreports) {
                $annotator->set_reports($courseid);
                $annotator->set_hidden_reports();
            }
            if ($viewanswers) {
                $annotator->set_answers();
                $annotator->set_hidden_answers();
            }
            if ($viewquestions) {
                $annotator->set_latest_questions($newsspan);
            }
            if ($viewposts) {
                $annotator->set_posts_by_user($USER->id);
            }
            // 2. Select and organize the model's data for display, depending on the user's capabilities.

            if ($viewreports) {
                // 2.0.1 Collect all reports of inappropriate comments.
                $reports = array_reverse($annotator->get_reports()); // Most recent entries come first.
                $this->count0 += count($reports);
                if (count($reports) > 0) {
                    $ar = array('annotatorid' => $annotator->get_id(), 'annotatorname' => $annotator->get_name(), 'reportcount' => count($reports), 'reports' => $reports);
                    $this->annotatorswithreports[] = $ar;
                }
                // 2.0.2 Collect all hidden reports in this course // XXX administrate category is to be separated.
                $hiddenentries = array_reverse($annotator->get_hidden_reports());
                $this->count5 += count($hiddenentries);
                if (count($hiddenentries) > 0) {
                    $ar = [];
                    $ar['annotatorid'] = $annotator->get_id();
                    $ar['annotatorname'] = $annotator->get_name();
                    $ar['hiddenreportcount'] = count($hiddenentries);
                    $ar['hiddenreports'] = $hiddenentries;
                    $this->annotatorswithhiddenreports[] = $ar;
                }
            }
            if ($viewanswers) {
                // 2.1.1 Collect new answers to the user's questions.
                $answers = $annotator->get_answers_for_me();
                $this->count1 += count($answers);
                if (count($answers) > 0) {
                    $answers = array_reverse($answers);
                    $ar = [];
                    $ar['annotatorid'] = $annotator->get_id();
                    $ar['annotatorname'] = $annotator->get_name();
                    $ar['answers'] = $answers;
                    $ar['answercount'] = count($answers);
                    $this->annotatorswithanswers[] = $ar;
                }
                // 2.1.2 Collect all hidden entries/answers.
                $hiddenanswers = $annotator->get_hidden_answers();
                $this->count4 += count($hiddenanswers);
                if (count($hiddenanswers) > 0) {
                    $hiddenanswers = array_reverse($hiddenanswers);
                    $ar = [];
                    $ar['annotatorid'] = $annotator->get_id();
                    $ar['annotatorname'] = $annotator->get_name();
                    $ar['hiddenentries'] = $hiddenanswers;
                    $ar['hiddenentrycount'] = count($hiddenanswers);
                    $this->annotatorswithhiddenentries[] = $ar;
                }
            }
            // 2.2 Collect all new questions.
            if ($viewquestions) {
                $questions = array_reverse($annotator->get_latest_questions()); // Most recent entries come first.
                $this->count2 += count($questions);
                if (count($questions) > 0) {
                    $ar = array('annotatorid' => $annotator->get_id(), 'annotatorname' => $annotator->get_name(), 'questions' => $questions, 'questioncount' => count($questions));
                    $this->annotatorswithquestions[] = $ar;
                }
            }
            // 2.3 Collect all questions/comments posted by this user in this course.
            if ($viewposts) {
                $userposts = $annotator->get_posts_by_user();
                $posts = array_reverse($userposts); // Most recent entries  come first.
                $this->count3 += count($posts);
                if (count($posts) > 0) {
                    $ar = array('annotatorid' => $annotator->get_id(), 'annotatorname' => $annotator->get_name(), 'posts' => $posts, 'postcount' => count($posts));
                    $this->annotatorswithpostsbythisuser[] = $ar;
                }
            }
        }
    }
    /**
     *
     * @global type $USER
     * @param \renderer_base $output
     * @return type
     */
    public function export_for_template(\renderer_base $output) {

        global $USER, $OUTPUT;

        $data = [];
        $data['openannotator'] = $this->openannotator;

        // 0. reports of inappropriate comments in this course.
        if ($this->viewreports) {
            $data['annotators_with_reports'] = $this->annotatorswithreports;
            if (empty($this->annotatorswithreports)) {
                $data['noreports'] = get_string('noreports', 'pdfannotator');
            }
            // 5. hidden reports in this course.
            $data['annotators_with_hidden_reports'] = $this->annotatorswithhiddenreports;
            if (empty($this->annotatorswithhiddenreports)) {
                $data['nohiddenentries'] = get_string('nohiddenreports', 'pdfannotator');
            }
            $data['count0'] = $this->count0;
            $data['count5'] = $this->count5;
            $data['viewreports'] = true;
        }
        // 1. annotators with answers to questions this user wrote or subscribed to.
        if ($this->viewanswers) {
            $data['annotators_with_answers'] = $this->annotatorswithanswers;
            if (empty($this->annotatorswithanswers)) {
                $data['noanswers'] = get_string('noanswers', 'pdfannotator');
            }
            // 4. hidden answer notifications in this course.
            $data['annotators_with_hiddenentries'] = $this->annotatorswithhiddenentries;
            if (empty($this->annotatorswithhiddenentries)) {
                $data['nohiddenentries'] = get_string('nohiddenanswernotifications', 'pdfannotator');
            }
            $data['count1'] = $this->count1;
            $data['count4'] = $this->count4;
            $data['viewanswers'] = true;
        }
        // 2. new questions in this course.
        if ($this->viewquestions) {
            $data['annotators_with_questions'] = $this->annotatorswithquestions;
            if (empty($this->annotatorswithquestions)) {
                $data['noquestions'] = get_string('noquestions_overview', 'pdfannotator');
            }
            $data['count2'] = $this->count2;
        }
        // 3. questions/comments posted by this user in this course.
        if ($this->viewposts) {
            $data['annotators_with_posts_by_this_user'] = $this->annotatorswithpostsbythisuser;
            if (empty($this->annotatorswithpostsbythisuser)) {
                $data['nomyposts'] = get_string('nomyposts', 'pdfannotator');
            }
            $data['count3'] = $this->count3;
        }

        // 5. icons.
        $data['pixunsubscribe'] = $OUTPUT->image_url("/i/notifications");
        $data['pixcollapsed'] = $OUTPUT->image_url("/t/collapsed"); // Moodle icon  'moodle/pix/t/collapsed.png';
        $data['pixgotox'] = $OUTPUT->image_url('link_small', 'mod_pdfannotator'); // Plugin-specific, not part of a theme '/mod/pdfannotator/pix/link_small.png';
        $data['pixhide'] = $OUTPUT->image_url('/e/accessibility_checker');
        $data['pixdisplay'] = $OUTPUT->image_url('/i/hide'); // Moodle icon '/moodle/pix/i/hide.png';
        $data['pixdelete'] = $OUTPUT->image_url('/t/delete');

        // 6. link to individual settings page.
        $systemcontext = context_system::instance();
        if (has_capability('moodle/user:editownmessageprofile', $systemcontext) || has_capability('moodle/user:editmessageprofile', $systemcontext)) {
            $data['masterofsettings'] = [];
            $data['linktosettingspage'] = new moodle_url('/message/notificationpreferences.php', array('userid' => $USER->id));
        }

        $data['linktooverview'] = new moodle_url('/course/recent.php', array('id' => $this->courseid));
        // "moodle/message/notificationpreferences.php?userid=$USER->id";

        // $data['timespan'] = $this->newsspan;

        return $data;
    }

}

/**************** Templatable for (re)rendering the new questions category *****************/

class overviewUpdateNewQuestions implements \renderable, \templatable {

    private $openannotator;
    private $annotatorswithquestions = [];
    private $count2 = 0;

    /**
     * @param type $courseid
     */
    public function __construct($courseid, $thisannotator, $newsspan) {

        $this->openannotator = $thisannotator;

        // 0. Access/create the model.
        $annotatorlist = pdfannotator_instance::get_pdfannotator_instances($courseid);

        foreach ($annotatorlist as $annotator) {

            // 1. Model is told to retrieve its data from db.
            $annotator->set_latest_questions($newsspan);

            // 2. Select and organize the model's data for display.
            // 2.1. Collect all new questions.
            $questions = array_reverse($annotator->get_latest_questions()); // Most recent entries come first.
            $this->count2 += count($questions);
            if (count($questions) > 0) {
                $a = array('annotatorid' => $annotator->get_id(), 'annotatorname' => $annotator->get_name(), 'questions' => $questions, 'questioncount' => count($questions));
                $this->annotatorswithquestions[] = $a;
            }
        }
    }
    /**
     *
     * @global type $USER
     * @param \renderer_base $output
     * @return type
     */
    public function export_for_template(\renderer_base $output) {

        $data['openannotator'] = $this->openannotator;
        $data['annotators_with_questions'] = $this->annotatorswithquestions;
        if (empty($this->annotatorswithquestions)) {
            $data['noquestions'] = get_string('noquestions_overview', 'pdfannotator');
        }
        $data['count2'] = $this->count2;

        return $data;
    }

}

/**************** Templatable for (re)rendering the reports category *****************/

class overviewUpdateReports implements \renderable, \templatable {

    private $openannotator;
    private $annotatorswithreports = [];

    /**
     * @param type $courseid
     */
    public function __construct($courseid, $thisannotator) {

        $this->openannotator = $thisannotator;

        // 0. Access/create the model.
        $annotatorlist = pdfannotator_instance::get_pdfannotator_instances($courseid);

        foreach ($annotatorlist as $annotator) {

            // 1. Model is told to retrieve its data from db.
            $annotator->set_reports($courseid);

            // 2. Select and organize the model's data for display.

            // 2.1. Collect all reports of inappropriate comments.
            $reports = array_reverse($annotator->get_reports()); // Most recent entries come first.
            if (count($reports) > 0) {
                $ar = [];
                $ar['annotatorid'] = $annotator->get_id();
                $ar['annotatorname'] = $annotator->get_name();
                $ar['reportcount'] = count($reports);
                $ar['reports'] = $reports;
                $this->annotatorswithreports[] = $ar;
            }
        }
    }
    /**
     *
     * @global type $USER
     * @param \renderer_base $output
     * @return type
     */
    public function export_for_template(\renderer_base $output) {

        $data = array('openannotator' => $this->openannotator, 'annotators_with_reports' => $this->annotatorswithreports);
        if (empty($this->annotatorswithreports)) {
            $data['noreports'] = get_string('noreports', 'pdfannotator');
        }
        $data['viewreports'] = true;

        return $data;
    }

}

/**************** Templatable for (re)rendering the hidden reports category *****************/

class overviewUpdateHiddenReports implements \renderable, \templatable {

    private $openannotator;
    private $annotatorswithhiddenreports = [];

    /**
     * Konstruktor (not necessary)
     * @param type $pdfannotators
     */
    public function __construct($courseid, $thisannotator) {

        $this->openannotator = $thisannotator;

        // 1. Create the model.
        $annotatorlist = pdfannotator_instance::get_pdfannotator_instances($courseid);

        foreach ($annotatorlist as $annotator) {

            // 2. Model is told to retrieve its data from db.
            $annotator->set_hidden_reports(true);

            // 3. Collect all hidden reports in this course.
            $hiddenentries = array_reverse($annotator->get_hidden_reports());
            if (count($hiddenentries) > 0) {
                $ar = [];
                $ar['annotatorid'] = $annotator->get_id();
                $ar['annotatorname'] = $annotator->get_name();
                $ar['hiddenreportcount'] = count($hiddenentries);
                $ar['hiddenreports'] = $hiddenentries;
                $this->annotatorswithhiddenreports[] = $ar;
            }
        }

    }
    /**
     *
     * @global type $USER
     * @param \renderer_base $output
     * @return type
     */
    public function export_for_template(\renderer_base $output) {

        $data = [];
        $data['openannotator'] = $this->openannotator;
        // Hidden reports in this course.
        $data['annotators_with_hidden_reports'] = $this->annotatorswithhiddenreports;
        if (empty($this->annotatorswithhiddenreports)) {
            $data['nohiddenentries_manager'] = get_string('nohiddenreports', 'pdfannotator');
        }
        return $data;
    }
}

/**************** Templatable for (re)rendering the answers category *****************/

class overviewUpdateAnswers implements \renderable, \templatable {

    private $openannotator;
    private $annotatorswithanswers = [];

    /**
     * Konstruktor (not necessary)
     * @param type $pdfannotators
     */
    public function __construct($courseid, $thisannotator) {

        $this->openannotator = $thisannotator;

        global $USER;

        // 0. Access/create the model.
        $annotatorlist = pdfannotator_instance::get_pdfannotator_instances($courseid, $thisannotator);

        foreach ($annotatorlist as $annotator) {

            // 1. Model is told to retrieve its data from db.
            $annotator->set_answers();

            // 2.1 Collect new answers to the user's questions.
            $answers = $annotator->get_answers_for_me();

            if (count($answers) > 0) {
                $answers = array_reverse($answers);
                $ar = [];
                $ar['annotatorid'] = $annotator->get_id();
                $ar['annotatorname'] = $annotator->get_name();
                $ar['answers'] = $answers;
                $ar['answercount'] = count($answers);
                $this->annotatorswithanswers[] = $ar;
            }

        }

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

        $data['annotators_with_answers'] = $this->annotatorswithanswers;
        if (empty($this->annotatorswithanswers)) {
            $data['noanswers'] = get_string('noanswers', 'pdfannotator');
        }
        $data['viewanswers'] = true;
        // Icons.
        $data['pixunsubscribe'] = $OUTPUT->image_url("/i/notifications");
        $data['pixcollapsed'] = $OUTPUT->image_url("/t/collapsed");
        $data['pixgotox'] = $OUTPUT->image_url('link_small', 'mod_pdfannotator');
        $data['pixhide'] = $OUTPUT->image_url('/e/accessibility_checker');
        $data['pixdisplay'] = $OUTPUT->image_url('/i/hide');

        return $data;
    }

}

/**************** Templatable for (re)rendering the hidden answers category *****************/

class overviewUpdateHiddenAnswers implements \renderable, \templatable {

    private $openannotator;
    private $annotatorswithhiddenentries = [];

    /**
     * Constructor (not necessary)
     * @param type $pdfannotators
     */
    public function __construct($courseid, $thisannotator) {

        $this->openannotator = $thisannotator;

        global $USER;

        // 0. Access/create the model.
        $annotatorlist = pdfannotator_instance::get_pdfannotator_instances($courseid, $thisannotator);

        foreach ($annotatorlist as $annotator) {

            // 1. Model is told to retrieve its data from db.
            $annotator->set_hidden_answers();

            // 2. Select and organize the model's data for display: Collect all hidden entries/answers.
            $hiddenanswers = $annotator->get_hidden_answers();

            if (count($hiddenanswers) > 0) {
                $hiddenanswers = array_reverse($hiddenanswers);
                $ar = [];
                $ar['annotatorid'] = $annotator->get_id();
                $ar['annotatorname'] = $annotator->get_name();
                $ar['hiddenentries'] = $hiddenanswers;
                $ar['hiddenentrycount'] = count($hiddenanswers);
                $this->annotatorswithhiddenentries[] = $ar;

            }

        }

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

        $data['annotators_with_hiddenentries'] = $this->annotatorswithhiddenentries;
        if (empty($this->annotatorswithhiddenentries)) {
            $data['noanswers'] = get_string('noanswers', 'pdfannotator');
        }

        // 5. icons.
        $data['pixcollapsed'] = $OUTPUT->image_url("/t/collapsed");
        $data['pixgotox'] = $OUTPUT->image_url('link_small', 'mod_pdfannotator');
        $data['pixhide'] = $OUTPUT->image_url('/e/accessibility_checker');
        $data['pixdisplay'] = $OUTPUT->image_url('/i/hide');
        $data['pixdelete'] = $OUTPUT->image_url('/t/delete');

        return $data;
    }

}
