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

    return true;
}
