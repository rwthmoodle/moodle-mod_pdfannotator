<?php
/**
 * Define all the backup steps that will be used by the backup_pdfannotator_activity_task
 * 
 * Moodle creates backups of courses or their parts by executing a so called backup plan.
 * The backup plan consists of a set of backup tasks and finally each backup task consists of one or more backup steps. 
 * This file provides all the backup steps classes.
 * 
 * See https://docs.moodle.org/dev/Backup_API and https://docs.moodle.org/dev/Backup_2.0_for_developers for more information.
 *
 * @package   mod_pdfannotator
 * @category  backup
 * @copyright 2018 RWTH Aachen, Anna Heynkes (see README.md)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die;

/**
 * Define the complete pdfannotator structure for backup, with file and id annotations
 */
class backup_pdfannotator_activity_structure_step extends backup_activity_structure_step {

    /**
     * There are three main things that the method must do:
     * 1. Create a set of backup_nested_element instances that describe the required data of your plugin
     * 2. Connect these instances into a hierarchy using their add_child() method
     * 3. Set data sources for the elements, using their methods like set_source_table() or set_source_sql()
     * The method must return the root backup_nested_element instance processed by the prepare_activity_structure()
     * method (which just wraps your structures with a common envelope).
     * 
     * TODO Adjust after final db structure has been determined
     * 
     */
    protected function define_structure() {

        // 1. To know if we are including userinfo
        $userinfo = $this->get_setting_value('userinfo'); // is 0

        // 2. Define each element separately
        $pdfannotator = new backup_nested_element('pdfannotator', array('id'), array(
            'name', 'intro', 'introformat', 'timecreated', 'timemodified'));

            $annotations = new backup_nested_element('annotations');
            $annotation = new backup_nested_element('annotation', array('id'), array('page', 'userid', 'annotationtypeid', 'data', 'timecreated', 'timemodified'));

                $comments = new backup_nested_element('comments');
                $comment = new backup_nested_element('comment', array('id'), array('userid', 'content', 'timecreated', 'timemodified', 'visibility', 'isquestion', 'isdeleted', 'seen'));

                    $reports = new backup_nested_element('reports');
                    $report = new backup_nested_element('report', array('id'), array('courseid', 'pdfannotatorid', 'page', 'message', 'userid', 'timecreated', 'seen'));
            
//                $comments_archiv = new backup_nested_element('comments_archiv');
//                $comment_archiv = new backup_nested_element('comment_archiv', array('id'), array('userid', 'content', 'timecreated', 'timemodified', 'visibility', 'isquestion', 'isdeleted', 'seen'));
                
        // 3. Build the tree (mind the right order!)
        $pdfannotator->add_child($annotations);        
            $annotations->add_child($annotation);
       
                $annotation->add_child($comments);         
                    $comments->add_child($comment);
            
                        $comment->add_child($reports);
                            $reports->add_child($report);
        
//                $annotation->add_child($comments_archiv);
//                    $comments_archiv->add_child($comment_archiv);
        
        // 4. Define db sources
        $pdfannotator->set_source_table('pdfannotator', array('id' => backup::VAR_ACTIVITYID)); // backup::VAR_ACTIVITYID is the 'course module id'.

//        if ($userinfo) {
            
            // add all annotations specific to this annotator instance
            $annotation->set_source_table('pdfannotator_annotationsneu', array('pdfannotatorid' => backup::VAR_PARENTID));
            
                // add any comments of this annotation 
                $comment->set_source_table('pdfannotator_comments', array('annotationid' => backup::VAR_PARENTID));
                    
                    // add any reports of this comment
                    $report->set_source_table('pdfannotator_reports', array('commentid' => backup::VAR_PARENTID));
            
//             $comment_archiv->set_source_table('pdfannotator_comments_archiv', array('annotationid' => backup::VAR_PARENTID));     
//        }   
                    
        // 5. Define id annotations (some attributes are foreign keys)
        $annotation->annotate_ids('user', 'userid');
        $comment->annotate_ids('user', 'userid');
        $report->annotate_ids('user', 'userid');
        $report->annotate_ids('pdfannotator', 'pdfannotatorid');
//        $comment_archiv->annotate_ids('user', 'userid');

        // 6. Define file annotations (vgl. resource activity)
        $pdfannotator->annotate_files('mod_pdfannotator', 'intro', null); // This file area does not have an itemid
        $pdfannotator->annotate_files('mod_pdfannotator', 'content', null); // see above

        // 7. Return the root element (pdfannotator), wrapped into standard activity structure
        return $this->prepare_activity_structure($pdfannotator);
    }
}
