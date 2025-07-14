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
 * Description of Annotation
 * Methods:
 * create_annotation
 * delete
 * read (abstract)
 *
 * @package   mod_pdfannotator
 * @copyright 2018 RWTH Aachen (see README.md)
 * @author    Rabea de Groot, Anna Heynkes and Friederike Schwager
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 */

/**
 * Class pdfannotator_annotation
 */
class pdfannotator_annotation {

    /**
     * This method creates a new record in the database table named mdl_pdfannotator_annotations and returns its id
     *
     * @param int $documentid specifies the pdf file to which this annotation belongs
     * @param int $pageid specifies the page within that pdf file
     * @param string $type child class (highlight, strikeout, area, textbox, drawing, comment or point)
     * @param int $itemid identifies the record in the respective child class table, e.g. highlights
     * @return int (or boolean false)
     */
    public static function create_annotation($documentid, $pageid, $type, $itemid) {

        global $DB;
        global $USER;
        $datarecord = new stdClass();
        $datarecord->userid = $USER->id;
        $datarecord->documentid = $documentid;
        $datarecord->pageid = $pageid;
        $datarecord->type = $type;
        $datarecord->itemid = $itemid;
        $annotationid = $DB->insert_record('pdfannotator_annotations', $datarecord, $returnid = true);
        return $annotationid;
    }

    /**
     * Method updates data attribute (consisting of width, color and lines)
     * in mdl_pdfannotator_drawings after a drawing was shifted in position
     *
     * @param int $annotationid
     * @param array $newdata
     * @return int 1 for success
     */
    public static function update($annotationid, $newdata) {
        global $DB, $USER;

        $annotation = $DB->get_record('pdfannotator_annotations', ['id' => $annotationid]);
        if ($annotation) {
            $annotation->data = json_encode($newdata);
            $annotation->timemodified = time();
            $annotation->modifiedby = $USER->id;
            $time = pdfannotator_get_user_datetime($annotation->timemodified);
            $success = $DB->update_record('pdfannotator_annotations', $annotation);
        } else {
            $success = false;
        }

        if ($success) {
            $result = ['status' => 'success', 'timemoved' => $time];
            if ($annotation->userid != $USER->id) {
                $result['movedby'] = pdfannotator_get_username($USER->id);
            }
            return $result;
        } else {
            return ['status' => 'error'];
        }
    }

    /**
     * Method deletes the specified annotation and all comments attached to it,
     * if the user is allowed to do so.
     * Teachers are allowed to delete any comment, students may only delete their own comments.
     *
     * @param int $annotationid
     * @param id $cmid
     * @param bool $deleteanyway Delete annotation in any case. F.e. if right to be forgotten was invoked or
     *  a user without the capability to delete the annotation deletes it implicitly by deleting the last comment of the annotation
     * @return boolean
     */
    public static function delete($annotationid, $cmid = null, $deleteanyway = null) {

        global $DB;
        $annotation = $DB->get_record('pdfannotator_annotations', ['id' => $annotationid], '*', $strictness = IGNORE_MISSING);
        if (!$annotation) {
            return false;
        }

        // Check user rights to delete this annotation and all its attached comments.
        $deletionallowed = self::deletion_allowed($annotation, $cmid);

        // Delete annotation.
        if ($deletionallowed[0] === true || $deleteanyway === true) {

            // Delete all comments of this annotation.
            $comments = $DB->get_records('pdfannotator_comments', ["annotationid" => $annotationid]);
            foreach ($comments as $commentdata) {
                $DB->delete_records('pdfannotator_votes', ["commentid" => $commentdata->id]);
            }
            $success = $DB->delete_records('pdfannotator_comments', ["annotationid" => $annotationid]);

            // Delete subscriptions to the question.
            $DB->delete_records('pdfannotator_subscriptions', ['annotationid' => $annotationid]);

            // Delete the annotation itself.
            $success = $DB->delete_records('pdfannotator_annotations', ["id" => $annotationid]);

            if ($deleteanyway) {
                return;
            }

            if (!$success) {
                return false;
            }

            return true;
        } else {
            return $deletionallowed[1];
        }
    }

    /**
     * Method checks whether the annotation as well as possible comments attached to it
     * belong to the current user.
     *
     * @param stdClass $annotation
     * @param int $cmid
     * @return array
     */
    public static function deletion_allowed($annotation, $cmid) {

        global $DB, $USER;
        $userid = $USER->id;
        $author = $annotation->userid;

        $result = [];

        // If user has admin rights with regard to annotations/comments: Allow deletion.
        $cm = get_coursemodule_from_id('pdfannotator', $cmid, 0, false, MUST_EXIST);
        $context = context_module::instance($cm->id);
        $deleteany = has_capability('mod/pdfannotator:deleteany', $context);
        $deleteown = has_capability('mod/pdfannotator:deleteown', $context);
        if ($deleteany) {
            $result[] = true;
        } else if (($author !== $userid) || !$deleteown) {
            $result[] = false;
            $result[] = get_string('onlyDeleteOwnAnnotations', 'pdfannotator');
        } else if ($DB->record_exists_select('pdfannotator_comments', "annotationid = ? AND userid != ?",
            [$annotation->id, $userid])) { // Check whether other people have commented this annotation.
            $result[] = false;
            $result[] = get_string('onlyDeleteUncommentedPosts', 'pdfannotator');
        } else {
            $result[] = true;
        }

        return $result;
    }

    /**
     * Method checks whether the annotation in question may be shifted in position.
     * It returns true if the annotation was made by the user who is trying to shift it and no other person has answered
     * or if that user is an admin.
     *
     * @param int $annotationid
     * @param context $context
     * @return boolean
     */
    public static function shifting_allowed($annotationid, $context) {
        global $DB;
        global $USER;
        $editanypost = has_capability('mod/pdfannotator:editanypost', $context);
        $editownpost = has_capability('mod/pdfannotator:edit', $context);
        if ($editanypost) {
            return true;
        }
        if (!$editownpost || $USER->id != self::get_author($annotationid)) {
            return false;
        } else if ($DB->record_exists_select('pdfannotator_comments', "annotationid = ? AND userid != ?",
            [$annotationid, $USER->id])) {
            // Annotation was answered by other users.
            return false;
        }
        return true;
    }

    /**
     * Return information for the dummy-comment of a textbox or drawing
     * @param int $annotationid
     */
    public static function get_information($annotationid) {
        global $DB;
        $annotation = $DB->get_record('pdfannotator_annotations', ['id' => $annotationid]);
        $annotationtype = $DB->get_field('pdfannotator_annotationtypes', 'name', ['id' => $annotation->annotationtypeid]);

        if ($annotationtype === "textbox" || $annotationtype === "drawing") {
            $comment = new stdClass();
            $comment->type = $annotationtype;
            $comment->content = '';
            $comment->displaycontent = get_string('noCommentsupported', 'pdfannotator');
            $comment->userid = $annotation->userid;
            $comment->username = pdfannotator_get_username($annotation->userid);
            $comment->visibility = 'public';
            $comment->timecreated = pdfannotator_get_user_datetime($annotation->timecreated);
            if (!empty($annotation->timemodified) && $annotation->timemodified != $comment->timecreated) {
                $comment->repositioned = true;
                $comment->timemoved = pdfannotator_get_user_datetime($annotation->timemodified);
                if (!empty($annotation->modifiedby) && $annotation->modifiedby != $annotation->userid) {
                    $comment->movedby = $annotation->modifiedby;
                } else {
                    $comment->movedby = null;
                }
            }
            $comment->usevotes = 0;
            // phpcs:disable moodle.Commenting.TodoComment
            $comment->uuid = -1; // TODO.
            $comment->annotation = $annotationid;
            $comment->isdeleted = 0;
            $comment->isquestion = 1;
            $comment->solved = 0;

            return $comment;
        } else {
            return false;
        }
    }

    /**
     * Method takes an annotation's id and returns the user id of its author
     *
     * @param int $annotationid
     * @return array
     */
    public static function get_author($annotationid) {

        global $DB;
        return $DB->get_field('pdfannotator_annotations', 'userid', ['id' => $annotationid], $strictness = MUST_EXIST);
    }

    /**
     * Method takes an annotation's id and returns the page it was made on
     *
     * @param int $annotationid
     * @return array
     */
    public static function get_pageid($annotationid) {
        global $DB;
        return $DB->get_field('pdfannotator_annotations', 'page', ['id' => $annotationid], $strictness = IGNORE_MISSING);
    }

    /**
     * Method takes an annotation's id and returns the content of the underlying question comment
     *
     * @param int $annotationid
     * @return string
     */
    public static function get_question($annotationid) {
        global $DB;
        $question = $DB->get_record('pdfannotator_comments', ['annotationid' => $annotationid, 'isquestion' => 1], 'content');
        return $question->content;
    }

}
