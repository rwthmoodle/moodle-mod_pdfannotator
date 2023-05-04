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

namespace mod_pdfannotator\privacy;

use mod_pdfannotator\privacy\provider;
use core_privacy\local\request\transform;
use core_privacy\local\request\writer;
use core_privacy\local\request\approved_contextlist;
use core_privacy\local\request\approved_userlist;
use stdClass;

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/../fixtures/test_indicator_max.php');
require_once(__DIR__ . '/../fixtures/test_indicator_min.php');
require_once(__DIR__ . '/../fixtures/test_target_site_users.php');
require_once(__DIR__ . '/../fixtures/test_target_course_users.php');

/**
 * Unit tests for privacy.
 *
 * @package   mod_pdfannotator
 * @copyright IT Center RWTH Aachen University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class provider_test extends \core_privacy\tests\provider_testcase {

    protected $course;
    protected $cmcontext;
    protected $user;
    protected $pdffile;
    protected $annotations;
    protected $questions;
    protected $answers;

    public function setUp(): void {
        global $DB;

        $this->resetAfterTest();

        // Create a course.
        $generator = $this->getDataGenerator();
        $this->course = $generator->create_course();

        // Create a pdfannotator module.
        $cm = $generator->create_module('pdfannotator', ['course' => $this->course->id]);
        $this->cmcontext = \context_module::instance($cm->cmid);

        // Create a user.
        $this->user = $generator->create_user();

        // Create a pdf object.
        $pdfobject = new stdClass();
        $pdfobject->course = $this->course->id;
        $pdfobject->name = "PDF_Provider_Test";
        $pdfobject->intro = "";
        $pdfobject->introformat = 1;
        $pdfobject->usevotes = 1;
        $pdfobject->useprint = 1;
        $pdfobject->useprintcomments = 1;
        $pdfobject->use_studenttextbox = 0;
        $pdfobject->use_studentdrawing = 0;
        $pdfobject->useprivatecomments = 0;
        $pdfobject->useprotectedcomments = 0;
        $pdfobject->timecreated = time();
        $pdfobject->timemodified = time();
        $pdfobject->id = $DB->insert_record('pdfannotator', $pdfobject);
        $this->pdffile = $pdfobject;

        // Create an (pin as a test) annotation to the test pdf.
        $annotationobj = new stdClass();
        $annotationobj->pdfannotatorid = $this->pdffile->id;
        $annotationobj->page = 1;
        $annotationobj->userid = $this->user->id;
        $pinannotation = $DB->get_record('pdfannotator_annotationtypes', ['name' => 'pin']);
        $annotationobj->annotationtypeid = $pinannotation->id;
        $annotationobj->data = json_encode(['x' => 365, 'y' => 166]);
        $annotationobj->timecreated = time();
        $annotationobj->timemodified = time();
        $annotationobj->id = $DB->insert_record('pdfannotator_annotations', $annotationobj);
        $this->annotations[] = $annotationobj;

        // Create a question for the pin annotation.
        $question = new stdClass();
        $question->pdfannotatorid = $this->pdffile->id;
        $question->annotationid = $this->annotations[0]->id;
        $question->userid = $this->user->id;
        $question->timecreated = time();
        $question->timemodified = time();
        $question->isquestion = 1;
        $question->isdeleted = 0;
        $question->ishidden = 0;
        $question->solved = 0;
        $question->id = $DB->insert_record('pdfannotator_comments', $question);
        $this->questions[] = $question;

        // Create a comment for the question above.
        $answer = new stdClass();
        $answer->pdfannotatorid = $this->pdffile->id;
        $answer->annotationid = $this->annotations[0]->id;
        $answer->userid = $this->user->id;
        $answer->timecreated = time();
        $answer->timemodified = time();
        $answer->isquestion = 0;
        $answer->isdeleted = 0;
        $answer->ishidden = 0;
        $answer->solved = 0;
        $answer->id = $DB->insert_record('pdfannotator_comments', $answer);
        $this->answers[] = $answer;
    }

    public function test_delete_data_for_users() {
        global $DB;

        $this->resetAfterTest();

        // Create a comment for the question above.
        $answer = new stdClass();
        $answer->pdfannotatorid = $this->pdffile->id;
        $answer->annotationid = $this->annotations[0]->id;
        $answer->userid = $this->user->id;
        $answer->timecreated = time();
        $answer->timemodified = time();
        $answer->isquestion = 0;
        $answer->isdeleted = 0;
        $answer->ishidden = 0;
        $answer->solved = 0;
        $answer->id = $DB->insert_record('pdfannotator_comments', $answer);
        $this->answers[] = $answer;

        // Report the first comment from the setUp().
        $reportobj = new stdClass();
        $reportobj->commentid = $this->answers[0]->id;
        $reportobj->courseid = $this->course->id;
        $reportobj->pdfannotatorid = $this->pdffile->id;
        $reportobj->userid = $this->user->id;
        $reportobj->timecreated = time();
        $reportobj->seen = 0;
        $reportobj->id = $DB->insert_record('pdfannotator_reports', $reportobj);

        // Vote the second comment in the quetions[0].
        $voteobj = new stdClass();
        $voteobj->commentid = $this->answers[1]->id;
        $voteobj->userid = $this->user->id;
        $voteobj->vote = 1;
        $voteobj->id = $DB->insert_record('pdfannotator_votes', $voteobj);

        // Subscribe the questions[0].
        $subscriptionsobj = new stdClass();
        $subscriptionsobj->annotationid = $this->questions[0]->id;
        $subscriptionsobj->userid = $this->user->id;
        $subscriptionsobj->id = $DB->insert_record('pdfannotator_subscriptions', $voteobj);

        // Perform delete_data_for_users.
        $systemcontext = \context_system::instance();
        $component = 'mod_pdfannotator';
        $userlist = new approved_userlist($systemcontext, $component, $this->user->id);
        // Delete using delete_data_for_user.
        provider::delete_data_for_users($userlist);
    }
}