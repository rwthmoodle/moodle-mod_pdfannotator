<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of index
 *
 * @author degroot
 */
class index implements renderable, templatable { // should be placed elsewhere
    
    private $use_studenttextbox;
    private $use_studentdrawing;
    
    public function __construct($pdfannotator, $isAllowedForUser) {
        //if the textbox should be allowed for students, the array should have a single value.
        $this->use_studenttextbox = array();
        if($pdfannotator->use_studenttextbox || $isAllowedForUser){
            $this->use_studenttextbox = array('use');
        }
        
        //if the drawing should be allowed for students, the array should have a single value.
        $this->use_studentdrawing = array();
        if($pdfannotator->use_studentdrawing || $isAllowedForUser){
            $this->use_studentdrawing = array('use');
        }
    }
    
    public function export_for_template(renderer_base $output) {
        global $OUTPUT,$PAGE;
        $url = $PAGE->url;
        $data = new stdClass();
        $data->use_studenttextbox = $this->use_studenttextbox;
        $data->use_studentdrawing = $this->use_studentdrawing;
        $data->pixhide = $OUTPUT->image_url('/e/accessibility_checker');
        $data->pixopenbook = $OUTPUT->image_url('openbook', 'mod_pdfannotator');
        $data->pixsinglefile = $OUTPUT->image_url('/e/new_document');
        
        return $data;
    }
}
