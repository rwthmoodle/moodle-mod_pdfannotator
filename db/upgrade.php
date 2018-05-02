<?php

/**
 * @package   mod_pdfannotator
 * @copyright 2018 RWTH Aachen, Rabea de Groot and Anna Heynkes(see README.md)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * 
 */
defined('MOODLE_INTERNAL') || die;

function xmldb_pdfannotator_upgrade($oldversion) {

    global $CFG, $DB;
    $dbman = $DB->get_manager();
    
    if ($oldversion < 2018032600) {

        // Define table pdfannotator_votes to be created.
        $table = new xmldb_table('pdfannotator_votes');

        // Adding fields to table pdfannotator_votes.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('commentid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('userid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('vote', XMLDB_TYPE_INTEGER, '4', null, XMLDB_NOTNULL, null, '1');

        // Adding keys to table pdfannotator_votes.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        // Conditionally launch create table for pdfannotator_votes.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Pdfannotator savepoint reached.
        upgrade_mod_savepoint(true, 2018032600, 'pdfannotator');
    }
    
    if ($oldversion < 2018032601) {

        // Define table pdfannotator_comments_archiv to be created.
        $table = new xmldb_table('pdfannotator_comments_archiv');

        // Adding fields to table pdfannotator_comments_archiv.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('annotationid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('userid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('content', XMLDB_TYPE_TEXT, null, null, null, null, null);
        $table->add_field('timecreated', XMLDB_TYPE_INTEGER, '11', null, XMLDB_NOTNULL, null, null);
        $table->add_field('timemodified', XMLDB_TYPE_INTEGER, '11', null, XMLDB_NOTNULL, null, null);
        $table->add_field('visibility', XMLDB_TYPE_CHAR, '45', null, XMLDB_NOTNULL, null, 'public');
        $table->add_field('isquestion', XMLDB_TYPE_INTEGER, '4', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('isdeleted', XMLDB_TYPE_INTEGER, '4', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('seen', XMLDB_TYPE_INTEGER, '4', null, XMLDB_NOTNULL, null, '0');

        // Adding keys to table pdfannotator_comments_archiv.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        // Conditionally launch create table for pdfannotator_comments_archiv.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Pdfannotator savepoint reached.
        upgrade_mod_savepoint(true, 2018032601, 'pdfannotator');
    }

    if ($oldversion < 2018043000) {

        // Define field usevotes to be added to pdfannotator.
        $table = new xmldb_table('pdfannotator');
        $field = new xmldb_field('usevotes', XMLDB_TYPE_INTEGER, '2', null, XMLDB_NOTNULL, null, '0', 'introformat');

        // Conditionally launch add field usevotes.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }
        
         // Define field newsspan to be added to pdfannotator.
        $table = new xmldb_table('pdfannotator');
        $field = new xmldb_field('newsspan', XMLDB_TYPE_INTEGER, '2', null, XMLDB_NOTNULL, null, '3', 'usevotes');

        // Conditionally launch add field newsspan.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }
        
        // Pdfannotator savepoint reached.
        upgrade_mod_savepoint(true, 2018043000, 'pdfannotator');
    }
    
    if ($oldversion < 2018050201) {

        // Define key commentid (foreign) to be added to pdfannotator_votes.
        $table1 = new xmldb_table('pdfannotator_votes');
        $key1 = new xmldb_key('commentid', XMLDB_KEY_FOREIGN, array('commentid'), 'comments', array('id'));

        // Launch add key commentid.
        $dbman->add_key($table1, $key1);
        
        // Define index userid (not unique) to be added to pdfannotator_votes.
        $index1 = new xmldb_index('userid', XMLDB_INDEX_NOTUNIQUE, array('userid'));

        // Conditionally launch add index userid.
        if (!$dbman->index_exists($table1, $index1)) {
            $dbman->add_index($table1, $index1);
        }
        
        // Define key annotationid (foreign) to be added to pdfannotator_comments.
        $table2 = new xmldb_table('pdfannotator_comments');
        $key2 = new xmldb_key('annotationid', XMLDB_KEY_FOREIGN, array('annotationid'), 'annotations', array('id'));

        // Launch add key annotationid.
        $dbman->add_key($table2, $key2);

         // Define index userid (not unique) to be added to pdfannotator_comments.
        $index2 = new xmldb_index('userid', XMLDB_INDEX_NOTUNIQUE, array('userid'));

        // Conditionally launch add index userid.
        if (!$dbman->index_exists($table2, $index2)) {
            $dbman->add_index($table2, $index2);
        }
        
        // Define key commentid (foreign) to be added to pdfannotator_reports.
        $table3 = new xmldb_table('pdfannotator_reports');
        $key3 = new xmldb_key('commentid', XMLDB_KEY_FOREIGN, array('commentid'), 'comments', array('id'));

        // Launch add key commentid.
        $dbman->add_key($table3, $key3);

        // Pdfannotator savepoint reached.
        upgrade_mod_savepoint(true, 2018050201, 'pdfannotator');
    }
    
    if ($oldversion < 2018050202) {

        // Changing type of field isquestion on table pdfannotator_comments to int.
        $table1 = new xmldb_table('pdfannotator_comments');
        $field1 = new xmldb_field('isquestion', XMLDB_TYPE_INTEGER, '2', null, XMLDB_NOTNULL, null, '0', 'visibility');
        // Launch change of type for field isquestion.
        $dbman->change_field_type($table1, $field1);
        
        // Changing type of field isdeleted on table pdfannotator_comments to int.
        $field2 = new xmldb_field('isdeleted', XMLDB_TYPE_INTEGER, '2', null, XMLDB_NOTNULL, null, '0', 'isquestion');
        // Launch change of type for field isdeleted.
        $dbman->change_field_type($table1, $field2);
        
        // Changing type of field seen on table pdfannotator_comments to int.
        $field3 = new xmldb_field('seen', XMLDB_TYPE_INTEGER, '2', null, null, null, '0', 'isdeleted');
        // Launch change of type for field seen.
        $dbman->change_field_type($table1, $field3);
        
        // Changing type of field seen on table pdfannotator_reports to int.
        $table2 = new xmldb_table('pdfannotator_reports');
        $field4 = new xmldb_field('seen', XMLDB_TYPE_INTEGER, '2', null, XMLDB_NOTNULL, null, '0', 'timecreated');
        // Launch change of type for field seen.
        $dbman->change_field_type($table2, $field4);

        // Pdfannotator savepoint reached.
        upgrade_mod_savepoint(true, 2018050202, 'pdfannotator');
    }
    
    
    return true;
}
