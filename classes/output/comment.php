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
 * The purpose of this script is to collect the output data for the statistic template and
 * make it available to the renderer. The data is collected via the statistic model and then processed.
 * Therefore, class statistic can be seen as a view controller.
 *
 * @package   mod_pdfannotator
 * @copyright 2018 RWTH Aachen (see README.md)
 * @author    Friederike Schwager
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

class comment implements \renderable, \templatable {

    private $comments = [];

    public function __construct($data, $cm, $context) {
        global $USER;

        if (!is_array($data)) {
            $data = [$data];
        }
        $editanypost = has_capability('mod/pdfannotator:editanypost', $context);
        foreach ($data as $comment) {

            $comment->buttons = [];

            $comment->isdeleted = boolval($comment->isdeleted);
            $comment->isquestion = boolval($comment->isquestion);
            $comment->solved = boolval($comment->solved);

            $owner = ($comment->userid == $USER->id);

            $comment->wrapperClass = 'chat-message comment-list-item';
            if ($comment->isquestion) {
                $comment->wrapperClass .= ' questioncomment';
            } else if ($comment->solved) {
                $comment->wrapperClass .= ' correct';
            }
            if ($owner) {
                $comment->wrapperClass .= ' owner';
            }
            if ($comment->usevotes) {
                $comment->wrapperClass .= ' usevotes';
                if (!$comment->isdeleted) {
                    if ($owner) {
                        $comment->voteBtn = get_string('likeOwnComment', 'pdfannotator');
                    } else if ($comment->isvoted) {
                        if ($comment->isquestion) {
                            $comment->voteBtn = get_string('likeQuestionForbidden', 'pdfannotator');
                        } else {
                            $comment->voteBtn = get_string('likeAnswerForbidden', 'pdfannotator');
                        }
                    } else {
                        if ($comment->isquestion) {
                            $comment->voteBtn = get_string('likeQuestion', 'pdfannotator');
                        } else {
                            $comment->voteBtn = get_string('likeAnswer', 'pdfannotator');
                        }
                    }
                }

                if (!$comment->votes) {
                    $comment->votes = "0";
                }
                if ($comment->isquestion) {
                    $comment->voteTitle = $comment->votes . " " . get_string('likeCountQuestion', 'pdfannotator');
                } else {
                    $comment->voteTitle = $comment->votes . " " . get_string('likeCountAnswer', 'pdfannotator');
                }
            }

            if (!empty($comment->ishidden)) {
                if (has_capability('mod/pdfannotator:seehiddencomments', $context)) {
                    $comment->content = $comment->content;
                    $comment->dimmed = 'dimmed_text';
                    $comment->displayhidden = 1;
                    $comment->buttons[] = ["attributes" => ["name" => "id", "value" => "hideButton" . $comment->uuid],
                        "moodleicon" => ["key" => "i/hide", "component" => "core", "title" => get_string('removehidden', 'pdfannotator')],
                        "text" => get_string('removehidden', 'pdfannotator')];
                } else {
                    $comment->visibility = 'anonymous';
                    $comment->content = '<em>' . get_string('hiddenComment', 'pdfannotator') . '</em>';
                }

            } else {
                if (has_capability('mod/pdfannotator:hidecomments', $context)) {
                    $comment->buttons[] = ["attributes" => ["name" => "id", "value" => "hideButton" . $comment->uuid],
                        "moodleicon" => ["key" => "i/show", "component" => "core", "title" => get_string('markhidden', 'pdfannotator')],
                        "text" => get_string('markhidden', 'pdfannotator')];
                }
            }

            if ($comment->isdeleted || isset($comment->type)) {
                $comment->content = '<em>' . $comment->content . '</em>';
            }

            if (!$comment->isdeleted) {
                $deleteany = has_capability('mod/pdfannotator:deleteany', $context);
                $deleteown = has_capability('mod/pdfannotator:deleteown', $context);
                $report = has_capability('mod/pdfannotator:report', $context);
                if ($deleteany || ($deleteown && $owner)) { // Delete.
                    $comment->buttons[] = ["classes" => "comment-delete-a", "text" => get_string('delete', 'pdfannotator'),
                        "moodleicon" => ["key" => "delete", "component" => "pdfannotator", "title" => get_string('delete', 'pdfannotator')]];
                }
                 // Report (textbox/drawing can't be reported because of a missing commentid).
                if ($report && !$owner && !isset($comment->type) ) {
                    $comment->report = true;
                    $comment->cm = json_encode($cm);  // Course module object.
                    $comment->cmid = $cm->id;
                }
                if (!isset($comment->type) && ($owner || $editanypost)) {
                    $comment->buttons[] = ["classes" => "comment-edit-a", "attributes" => ["name" => "id", "value" => "editButton" . $comment->uuid],
                        "moodleicon" => ["key" => "i/edit", "component" => "core", "title" => get_string('edit', 'pdfannotator')],
                        "text" => get_string('edit', 'pdfannotator')];
                }
            }

            if (!empty($comment->modifiedby) && ($comment->modifiedby != $comment->userid) && ($comment->userid != 0)) {
                $comment->modifiedby = get_string('modifiedby', 'pdfannotator') . pdfannotator_get_username($comment->modifiedby);
            } else {
                $comment->modifiedby = null;
            }

            if ($comment->isquestion || !$comment->isdeleted) {
                $comment->dropdown = true;
            }

            if (!isset($comment->type) && $comment->isquestion) { // Only set for textbox and drawing.
                if (!empty($comment->issubscribed)) {
                    $comment->buttons[] = ["classes" => "comment-subscribe-a", "faicon" => ["class" => "fa-bell-slash"],
                        "text" => get_string('unsubscribeQuestion', 'pdfannotator')];
                } else {
                    $comment->buttons[] = ["classes" => "comment-subscribe-a", "faicon" => ["class" => "fa-bell"],
                        "text" => get_string('subscribeQuestion', 'pdfannotator')];
                }
                // Open/Close.
                $closequestion = has_capability('mod/pdfannotator:closequestion', $context);
                $closeanyquestion = has_capability('mod/pdfannotator:closeanyquestion', $context);
                if (($owner && $closequestion) || $closeanyquestion) {
                    if ($comment->solved) {
                        $comment->buttons[] = ["classes" => "comment-solve-a", "faicon" => ["class" => "fa-unlock"],
                            "text" => get_string('markUnsolved', 'pdfannotator')];
                    } else {
                        $comment->buttons[] = ["classes" => "comment-solve-a", "faicon" => ["class" => "fa-lock"],
                            "text" => get_string('markSolved', 'pdfannotator')];
                    }
                }
            }

            $solve = has_capability('mod/pdfannotator:markcorrectanswer', $context);
            if ($solve && !$comment->isquestion && !$comment->isdeleted && !isset($comment->type)) {
                if ($comment->solved) {
                    $comment->buttons[] = ["classes" => "comment-solve-a", "text" => get_string('removeCorrect', 'pdfannotator'),
                        "moodleicon" => ["key" => "i/completion-manual-n", "component" => "core", "title" => get_string('removeCorrect', 'pdfannotator')]];
                } else {
                    $comment->buttons[] = ["classes" => "comment-solve-a", "text" => get_string('markCorrect', 'pdfannotator'),
                        "moodleicon" => ["key" => "i/completion-manual-enabled", "component" => "core", "title" => get_string('markCorrect', 'pdfannotator')]];
                }
            }
            if ($comment->solved) {
                if ($comment->isquestion) {
                    $comment->solvedicon = ["classes" => "icon fa fa-lock fa-fw solvedquestionicon", "title" => get_string('questionSolved', 'pdfannotator')];
                } else if (!$comment->isdeleted) {
                    $comment->solvedicon = ["classes" => "icon fa fa-check fa-fw correctanswericon", "title" => get_string('answerSolved', 'pdfannotator')];
                }
            }
            $this->comments[] = $comment;
        }
        return;
    }

    /**
     * This function is required by any renderer to retrieve the data structure
     * passed into the template.
     * @param \renderer_base $output
     * @return type
     */
    public function export_for_template(\renderer_base $output) {
        $data = [];
        $data['comments'] = $this->comments;
        return $data;
    }

}
