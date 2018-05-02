<?php

namespace mod_pdfannotator\event;

defined('MOODLE_INTERNAL') || die();


class course_module_viewed extends \core\event\course_module_viewed {

    /**
     * Init method.
     *
     * @return void
     */
    protected function init() {
        $this->data['objecttable'] = 'pdfannotator';
        $this->data['crud'] = 'r';
        $this->data['edulevel'] = self::LEVEL_PARTICIPATING;
    }

    public static function get_objectid_mapping() {
        return array('db' => 'pdfannotator', 'restore' => 'pdfannotator');
    }
}
