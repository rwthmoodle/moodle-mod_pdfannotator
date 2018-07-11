<?php
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
    public static function make_from_comment(pdfannotator_comment $comment) { // Klasse umbenennen in pdfannotator_comment
            
        // determine author (possibly anonymous)
        if ($comment->visibility === 'public') {
              
            $authorID = pdfannotator_comment::get_authorid($comment->id); 
            $author = get_username($authorID);
            
        } else {
            $author = get_string('anonymous', 'pdfannotator');
        }
        
        // create info object
        $info = new pdfannotator_comment_info();
        $timestamp = pdfannotator_comment::get_timestamp($comment->id);
        $info->datetime = get_user_date_time($timestamp);
        $info->author = $author;
        $info->content = $comment->content;

        return $info;
    }
    
}
