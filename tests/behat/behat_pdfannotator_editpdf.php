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
 * Behat pdfannotator-related steps definitions.
 *
 * @package    pdfannotator
 * @category   test
 * @copyright  2021 Luca Bösch <luca.boesch@bfh.ch>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// NOTE: no MOODLE_INTERNAL test here, this file may be required by behat before including /config.php.

require_once(__DIR__ . '/../../../../lib/behat/behat_base.php');

/**
 * Steps definitions related with the pdfannotator.
 *
 * @package    pdfannotator
 * @category   test
 * @copyright  2021 Luca Bösch <luca.boesch@bfh.ch>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class behat_pdfannotator_editpdf extends behat_base {

    /**
     * Point at the pdfannotator pdf.
     *
     * @When /^I point at the pdfannotator canvas$/
     */
    public function i_point_at_the_pdfannotator_canvas() {
        $node = $this->find('xpath', '//div[@id=\'pageContainer1\']');
        $this->execute('behat_general::i_click_on', [$node, 'NodeElement']);
    }

    /**
     * Point at the pdfannotator pdf.
     *
     * @When /^I click the pdfannotator public comment dropdown menu button$/
     */
    public function i_click_the_pdfannotator_public_comment_dropdown_menu_button() {
        $node = $this->find('xpath', '//a[@id=\'dropdownMenuButton\']');
        $this->execute('behat_general::i_click_on', [$node, 'NodeElement']);
    }
}
