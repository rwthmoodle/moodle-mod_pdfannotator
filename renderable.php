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
 * This file contains the definition of renderable classes in the pdfannotator module.
 * The renderables will be replaced by templatables but are still used by the latter.
 *
 * @package   mod_pdfannotator
 * @copyright 2018 RWTH Aachen, Anna Heynkes (see README.md)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 */
defined('MOODLE_INTERNAL') || die();

require_once('model/comment.class.php');

class pdfannotator_comment_info implements renderable {

    public $pdfname;
    public $page;
    public $datetime;
    public $author;
    public $content;


    /**
     * Method returns an object with info about a comment the user is about to report.
     * This info is displayed above the report form.
     *
     * @param comment $comment
     * @return \pdfannotator_comment_info
     */
    public static function make_from_comment(pdfannotator_comment $comment) {

        // Determine author (possibly anonymous).
        if ($comment->visibility === 'public') {

            $authorid = pdfannotator_comment::get_authorid($comment->id);
            $author = pdfannotator_get_username($authorid);

        } else {
            $author = get_string('anonymous', 'pdfannotator');
        }

        // Create info object.
        $info = new pdfannotator_comment_info();
        $timestamp = pdfannotator_comment::get_timestamp($comment->id);
        $info->datetime = pdfannotator_get_user_datetime($timestamp);
        $info->author = $author;
        $info->content = $comment->content;

        return $info;
    }

}

class pdfannotator_conversation_info implements renderable {

    public function __construct($dbrecord) {

    }

}