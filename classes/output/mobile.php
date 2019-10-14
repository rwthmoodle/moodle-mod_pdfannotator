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
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle. If not, see http://www.gnu.org/licenses/.

// Bug while using h5p in moodlemobile-App to issue 18 https://github.com/rwthmoodle/moodle-mod_pdfannotator/issues/18

namespace mod_pdfannotator\output;

defined('MOODLE_INTERNAL') || die();

use context_module;
use mod_pdfannotator;

class mobile {

public static function mobile_course_view($args) {
    return array(
        'templates'  => array(
            array(
                'id'   => 'main',
                'html' => $OUTPUT->render_from_template('mod_pdfannotator/mobile_view_page', $data),
            ),
        ),
    );
}
