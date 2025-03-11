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
 * The report_added event.
 *
 * @package    mod_pdfannotator
 * @copyright  2014 CIL
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace mod_pdfannotator\event;
defined('MOODLE_INTERNAL') || die();
/**
 *
 * The mod_pdfannotator report_added event class.
 *
 * @package   mod_pdfannotator
 * @copyright 2018 RWTH Aachen (see README.md)
 * @author    Rabea de Groot, Anna Heynkes, Friederike Schwager, Amrita Deb Dutta
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class report_added extends \core\event\base {
    /**
     * the initialising function
     */
    protected function init() {
        $this->data['crud'] = 'c'; // ... c(reate), r(ead), u(pdate), d(elete).
        $this->data['edulevel'] = self::LEVEL_PARTICIPATING;
        $this->data['objecttable'] = 'pdfannotator_reports';
    }
     /**
      * the get event name function
      */
    public static function get_name() {
        return get_string('eventreport_added', 'pdfannotator');
    }

     /**
      * the event description function
      */
    public function get_description() {
        return "The user with id {$this->userid} created an report with id {$this->objectid}.";
    }

     /**
      * the url fetch function
      */
    public function get_url() {
        return new \moodle_url('/mod/pdfannotator/view.php', array('id' => $this->other['cmid'], 'action' => 'overview'));
    }
}
