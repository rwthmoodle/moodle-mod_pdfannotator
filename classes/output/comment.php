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
 * @copyright 2018 RWTH Aachen, Friederike Schwager (see README.md)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

class comment implements \renderable, \templatable {

    private $comments = [];

    public function __construct($data, $isteacher, $cm, $context) {
        global $USER;

        if (!is_array($data)) {
            $data = [$data];
        }

        foreach ($data as $comment) {

            $comment->isdeleted = boolval($comment->isdeleted);
            $comment->isquestion = boolval($comment->isquestion);

            $owner = ($comment->userid == $USER->id);
            $editanypost = has_capability('mod/pdfannotator:editanypost', $context);

            $comment->wrapperClass = 'chat-message comment-list-item';
            if ($comment->isquestion) {
                $comment->wrapperClass .= ' questioncomment';
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

            if (!isset($comment->type) && $comment->timemodified != $comment->timecreated) {
                $comment->edited = true;
            }

            if (!isset($comment->type)) { // Only set for textbox and drawing.
                if ($comment->isquestion && $comment->issubscribed) {
                    $comment->subscriptionTitle = get_string('unsubscribeQuestion', 'pdfannotator');
                    $comment->subscriptionClass = 'icon fa fa-bell-slash fa-fw';
                } else {
                    $comment->subscriptionTitle = get_string('subscribeQuestion', 'pdfannotator');
                    $comment->subscriptionClass = 'icon fa fa-bell fa-fw';
                }
            }
            if ($comment->isdeleted || isset($comment->type)) {
                $comment->content = '<em>' . $comment->content . '</em>';
            }

            if (!$comment->isdeleted) {
                if ($owner || $isteacher) { // Delete.
                    $comment->delete = true;
                } else if (!isset($comment->type)) { // Report (textbox/drawing can't be reported because of a missing commentid).
                    $comment->report = true;
                    $comment->cm = json_encode($cm);  // Course module object.
                    $comment->cmid = $cm->id;
                }
            }

            if (!isset($comment->type) && ($owner || $editanypost)) {
                $comment->edit = true;
            }
            if ( !empty($comment->modifiedby) && ($comment->modifiedby != $comment->userid) )  {
                $comment->modifiedby = get_string('modifiedby', 'pdfannotator') . pdfannotator_get_username($comment->modifiedby);
            } else {
                $comment->modifiedby = null;
            }
            if (!empty($comment->repositioned) && !empty($comment->movedby) && ($comment->movedby != $comment->userid))  {
                $comment->movedby = get_string('modifiedby', 'pdfannotator') . pdfannotator_get_username($comment->movedby);
            } else {
                $comment->movedby = null;
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
