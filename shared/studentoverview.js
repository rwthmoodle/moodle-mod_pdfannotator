/**
 * This file is a collection of JavaScript functions that control the behaviour
 * of the studentoverview page / template
 * 
 * @package   mod_pdfannotator
 * @copyright 2018 RWTH Aachen, Rabea de Groot and Anna Heynkes (see README.md)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * 
 */
    /**
     * Function makes the currently opened annotator instance appear with lilac
     * background and white font color
     * 
     * @param {type} Y
     * @param {type} annotatorid
     * @return {undefined}
     */
    function markCurrent(Y, annotatorid) {
          let headings = document.getElementsByClassName('panel-default');
          let panel;
          let id1 = 'answerpanel_' + annotatorid;
          let id2 = 'questionpanel_' + annotatorid;
          let id3 = 'postpanel_' + annotatorid;
          let id4 = 'hiddenentrypanel_' + annotatorid;
          for (i = 0; i < headings.length; i++) {
            (function(innerI) {
                panel = headings[innerI];
                if (panel.id === id1 || panel.id == id2 || panel.id == id3 || panel.id == id4) {
                    panel.classList.add('mycurrent');
                }        
            })(i);
          }
    }
    
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////


require(['jquery', 'core/templates'], function($, templates) { // bei Aufruf

        /************************** 1. Call initialising functions **************************/

        addEventListenerForDelete();
        addEventListenerForHiding();
        addEventListenerForDisplaying();
        
        toggleOpenAndClose();
        
        /************************** 2. Function definitions **************************/
        
        /**
         * Helper function for setting icon paths after asynchronous partial page reload
         * 
         * @param {type} data
         * @return {undefined}
         */
        function setPicturePaths(data) {
            data.pixcollapsed = M.util.image_url('/t/collapsed');
            data.pixgotox = M.util.image_url('link_klein', 'mod_pdfannotator');
            data.pixhide = M.util.image_url('/e/accessibility_checker');
            data.pixdisplay = M.util.image_url('/i/hide');
            data.pixdelete = M.util.image_url('/t/delete');
        }
        
        /**
         * This function dynamically adds and removes a panel's CSS class 'open'
         * as the collapsable list within it is opened or closed.
         * 
         * @return {undefined}
         */
        function toggleOpenAndClose() {
            
            $(document).ready(function () {
                
                $('.panel-default').each(function(index,elem) {
                    if(elem.classList.contains('open')) {
                        elem.classList.remove('open');
                    }
                });

                $('.annotator').click(function(e) {

                    // 1. Get the sourrunding panel 

                    let panel_id = -1;
                    let annoid = -1;
                    let dropdown = -1;

                    // 1.1 Category: Reported comments in this course

                    if (e.target.classList.contains('answerlink')) {
                        dropdown = 1;
                        annoid = e.target.id.split('_')[1];
                        panel_id = 'answerpanel_' + annoid;

                    } else if (e.target.parentNode.classList.contains('answerlink')) {
                        dropdown = 1;
                        annoid = e.target.parentNode.id.split('_')[1];
                        panel_id = 'answerpanel_' + annoid;
                    }

                    // 1.2 Category: New questions

                    if (e.target.classList.contains('questionlink')) { 
                        dropdown = 2;
                        annoid = e.target.id.slice(13);
                        panel_id = 'questionpanel_' + annoid;

                    } else if (e.target.parentNode.classList.contains('questionlink')) {
                        dropdown = 2;
                        annoid = e.target.parentNode.id.slice(13);
                        panel_id = 'questionpanel_' + annoid;
                    }

                    // 1.3 Category: My posts

                    if (e.target.classList.contains('postlink')) {
                        dropdown = 3;
                        annoid = e.target.id.slice(9);
                        panel_id = 'postpanel_' + annoid;

                    } else if (e.target.parentNode.classList.contains('postlink')) {
                        dropdown = 3;
                        annoid = e.target.parentNode.id.slice(9);
                        panel_id = 'postpanel_' + annoid;
                    }

                    // 1.4 Category: Administrate hidden entries (display again or delete permanently)

                    if (e.target.classList.contains('hiddenentrylink')) {
                        dropdown = 4;
                        annoid = e.target.id.slice(16);
                        panel_id = 'hiddenentrypanel_' + annoid;

                    } else if (e.target.parentNode.classList.contains('hiddenentrylink')) {
                        dropdown = 4;
                        annoid = e.target.parentNode.id.slice(16);
                        panel_id = 'hiddenentrypanel_' + annoid;
                    }

                    // 1.4 Category: Administrate hidden entries (display again or delete permanently)

                    if (e.target.classList.contains('hiddenentrylink')) {
                        dropdown = 4;
                        annoid = e.target.id.slice(16);
                        panel_id = 'hiddenentrypanel_' + annoid;

                    } else if (e.target.parentNode.classList.contains('hiddenentrylink')) {
                        dropdown = 4;
                        annoid = e.target.parentNode.id.slice(16);
                        panel_id = 'hiddenentrypanel_' + annoid;
                    }

                    // 1. For all:

                    let panel = document.getElementById(panel_id);

                    let iconid = 'dropdown' + dropdown + '_' + annoid;
                    let icon = document.getElementById(iconid);

                    // 2. Add or remove the CSS class 'open' and toggle the dropdown icon

                    if($(panel).hasClass('open')) {      
                            panel.classList.remove('open');
                            icon.src = M.util.image_url('/t/collapsed'); // '/moodle/pix/t/collapsed.png';

                    } else {

                            // Mark any open elements as closed...
                            if($('.panel-default.open')) {
                               $('.panel-default.open').each(function(index, elem){
                                   elem.classList.remove('open');  
                               });
                            }
                            // and toggle the dropdown icon back to collapsed
                            if($('dropdown_image')) {
                                $('.dropdown_image').each(function(index, elem){
                                    elem.src = M.util.image_url('/t/collapsed'); // '/moodle/pix/t/collapsed.png';
                                });

                            }
                            // ...and then mark this element as open
                            panel.classList.add('open');
                            icon.src = M.util.image_url('/t/dropdown'); // '/moodle/pix/t/dropdown.png';                      
                    }

                });

            });
        }    
        /**
         * Permanently delete a report when user clicks delete icon
         * 
         * @param {type} $
         * @returns {undefined}
         */
        function addEventListenerForDelete() {
        
            var deletebuttons = document.getElementsByClassName("delete");

            for (i = 0; i < deletebuttons.length; i++) {
                (function (innerI){
                    deletebuttons[innerI].addEventListener("click", function(){
                        
                        var annotatorid = deletebuttons[innerI].getAttribute("data-annotator");
                        
//                        var reallyDelete = false;
//
//                        confirmDelete = M.util.get_string('deletingComment_manager', 'pdfannotator');
//                        
//                        function dialogCallbackForDeleteCancel(){
//                            return false;
//                        }
//                        function dialogCallbackForDelete() {
//                            return true;
//                        }
//                        
//                        
//                        notification.confirm(M.util.get_string('deletingCommentTitle', 'pdfannotator'), confirmDelete, M.util.get_string('yesButton', 'pdfannotator'), M.util.get_string('cancelButton', 'pdfannotator'), dialogCallbackForDelete, dialogCallbackForDeleteCancel);

                        var reportid = deletebuttons[innerI].id;
                        reportid = reportid.slice(6);

                        return $.ajax({
                            type: "POST",
                            url: "action.php",
                            data: { "documentId": annotatorid, "reportid": reportid, "action": 'deleteReport'} // documentID is a dummy passed on pro forma
                        }).then(function(data){                     
                            data = JSON.parse(data);

                            if (data.status === "success") {

                                var id = 'report' + data.reportid;
                                var target = document.getElementById(id);

                                if (target !== null) {

                                    // 1.1 No longer display the report in the reports category
                                    target.style.setProperty('display', 'none', 'important');

                                    // 1.2 Adjust the annotator's report count to -= 1
                                    id = 'reportcount' + data.pdfannotatorid;
                                    var reportcount = document.getElementById(id).innerHTML;
                                    reportcount = reportcount.slice(1,reportcount.length-1);
                                    var newreportcount = reportcount - 1;
                                    if (newreportcount !== 0) { // is there at least 1 'unseen'/not hidden report left in this annotator
                                        document.getElementById(id).innerHTML = '(' + (newreportcount) + ')';

                                    } else { // 1.3 If it was the last report in this annotator, no longer display the latter 

                                        id = 'reportpanel_' + data.pdfannotatorid;
                                        var target = document.getElementById(id);
                                        if (target !== null) {
                                            target.style.setProperty('display', 'none', 'important');
                                        }

                                    }     

                                } 
                                id = 'hiddenentry' + data.reportid;
                                target = document.getElementById(id);
                                if (target !== null) {

                                    // 2.1 No longer display the report in the hidden reports category
                                    target.style.setProperty('display', 'none', 'important');

                                    // 2.2 Adjust the annotator's hidden report count to -= 1
                                    id = 'hiddenentrycount' + data.pdfannotatorid;
                                    var hiddenentrycount = document.getElementById(id).innerHTML;
                                    hiddenentrycount = hiddenentrycount.slice(1,hiddenentrycount.length-1);
                                    var newhiddenentrycount = hiddenentrycount - 1;

                                    if (newhiddenentrycount !== 0) { // is there at least 1 'unseen'/not hidden report left in this annotator
                                        document.getElementById(id).innerHTML = '(' + (newhiddenentrycount) + ')';

                                    } else { // 2.3 If it was the last hidden report in this annotator, no longer display the latter

                                        id = 'hiddenentrypanel_' + data.pdfannotatorid;
                                        var target = document.getElementById(id);
                                        if (target !== null) {
                                            target.style.setProperty('display', 'none', 'important');
                                        }
                                    } 

                                }


                            } else {
                                console.log("Error: Element konnte nicht ausgeblendet werden.");
                            }

                        });

                    });
                })(i);
            }
        }  
        /**
         * Mark a report as seen and hide it henceforth when user clicks hide icon (eye)
         * 
         * @return {undefined}
         */
        function addEventListenerForHiding() {
            
            var hide = document.getElementsByClassName("ausblenden");
            
            for (i = 0; i < hide.length; i++) {
                (function (innerI){
                    hide[innerI].addEventListener("click", function(){
                        
                        var annotatorid = hide[innerI].getAttribute("data-annotator");
                        
                        var openannotator = document.getElementsByClassName("mycurrent")[0];
                        if(openannotator !== null) {
                            openannotator = openannotator.id.split("_")[1];
                        }
                                            
                        //document.getElementById("openannotator").innerHTML;
                        
                        return $.ajax({
                            type: "POST",
                            url: "action.php",
                            data: { "documentId": annotatorid, "answerid": hide[innerI].id, "openannotator": openannotator, "action": 'markAnswerAsSeen'}
                        }).then(function(data){
                            
                            data = JSON.parse(data);

                            if (data.status === "success") {

                                // 1. Make the answer disappear in the 'answer' category
                                var id = 'answer' + data.answerid;
                                var answer = document.getElementById(id);
                                if (answer !== null) {
                                    answer.style.setProperty('display', 'none', 'important');
                                }
                                
                                id = 'answercount' + data.pdfannotatorid; // TODO
                                var answercount = document.getElementById(id).innerHTML;
                                answercount = answercount.slice(1,answercount.length-1);
                                var newanswercount = answercount - 1;
                                
                                if (newanswercount !== 0) { // is there at least 1 'unseen'/not hidden report left in this annotator?
                                    document.getElementById(id).innerHTML = '(' + (newanswercount) + ')';

                                } else { // no longer show the annotator 
                                    id = 'answerpanel_' + data.pdfannotatorid;
                                    var answerpanel = document.getElementById(id);                             
                                    if (answerpanel !== null) {
                                        answerpanel.style.setProperty('display', 'none', 'important');
                                    }
                                }

                                // 2. Make the same answer appear in the 'administrate' category by rerendering the corresponding template
                                (function(templates, data) {
                                setPicturePaths(data);
                                templates.render('mod_pdfannotator/overview_administrate_entries', data)
                                    .then(function(html,js){
                                        templates.replaceNodeContents('#accordion4', html, js);
                                        addEventListenerForDelete();
                                        addEventListenerForDisplaying();
                                        toggleOpenAndClose();
                                        markCurrent(null, data.openannotator);
                                        }); // add a catch
                                })(templates, data.newdata);


                            } else {
                                console.log("Error: Element konnte nicht ausgeblendet werden.");
                            }

                        });

                    });
                })(i);
            }

        }        
        /**
         * Mark a hidden report as unseen again and display it henceforth when user clicks display icon (eye)
         * @return {undefined}
         */
        function addEventListenerForDisplaying() {
            
            var hide = document.getElementsByClassName("einblenden");

            for (i = 0; i < hide.length; i++) {
                (function (innerI){
                    hide[innerI].addEventListener("click", function(){
                        
                        var annotatorid = hide[innerI].getAttribute("data-annotator");
                        
                        var openannotator = document.getElementsByClassName("mycurrent")[0];
                            if(openannotator !== null) {
                                openannotator = openannotator.id.split("_")[1];
                        }

                        return $.ajax({
                            type: "POST",
                            url: "action.php",
                            data: { "documentId": annotatorid, "answerid": hide[innerI].id, "openannotator": openannotator, "action": 'markAnswerAsUnseen'}
                        }).then(function(data){
                            
                            data = JSON.parse(data);
                            
                            if (data.status === "success") {
                                
                                // 1.1 No longer display the answer in 'administrate entries' category
                                var id = 'hiddenentry' + data.answerid;
                                var target = document.getElementById(id);    
                                if (target !== null) {
                                    target.style.setProperty('display', 'none', 'important');
                                }

                                // 1.2 Instead, display it once more in the regular 'answers' category by rerendering the corresponding template                            
                                (function(templates, data) {
                                    setPicturePaths(data);
                                    templates.render('mod_pdfannotator/studentoverview_answers', data)
                                        .then(function(html,js){
                                            templates.replaceNodeContents('#accordion1', html, js);
                                            addEventListenerForDelete();
                                            addEventListenerForHiding();
                                            toggleOpenAndClose();
                                            markCurrent(null, data.openannotator);
                                            }); // add a catch
                                })(templates, data.newdata);
                                   
                                // 2.1 a) Adjust the pdfannotator's count of hidden entries (i.e. substract 1)
                                id = 'hiddenentrycount' + data.pdfannotatorid;
                                var hiddenentrycount = document.getElementById(id).innerHTML;
                                hiddenentrycount = hiddenentrycount.slice(1,hiddenentrycount.length-1); // slice of the brackets () around the count                           
                                var newhiddenentrycount = hiddenentrycount - 1;
                                
                                if (newhiddenentrycount !== 0) { // is there at least 1 'unseen'/not hidden report left in this annotator
                                    target = document.getElementById(id);    
                                    if (target !== null) {
                                        target.innerHTML = '(' + (newhiddenentrycount) + ')';              
                                    }

                                } else { 

                                    //  2.1 b) If it was the last hidden entry in this annotator, then no longer show the annotator // :)
                                    id = 'hiddenentrypanel_' + data.pdfannotatorid;
                                    var target = document.getElementById(id);    
                                    if (target !== null) {
                                        target.style.setProperty('display', 'none', 'important');
                                    }

                                }
                                
                           } else {
                                console.log("Error: Element konnte nicht wieder eingeblendet werden.");
                            }

                        });

                    });
                })(i);
            }

            
            
            
            
        }
        
}); // end


    /**
     * Shorten display of any report or question to a maximum of 80 characters and display
     * a 'view more'/'view less' link
     * 
     * Copyright 2013 Viral Patel and other contributors
     * http://viralpatel.net
     * 
     * slightly modified by RWTH Aachen in 2018
     * 
     * Permission is hereby granted, free of charge, to any person obtaining
     * a copy of this software and associated documentation files (the
     * "Software"), to deal in the Software without restriction, including
     * without limitation the rights to use, copy, modify, merge, publish,
     * distribute, sublicense, and/or sell copies of the Software, and to
     * permit persons to whom the Software is furnished to do so, subject to
     * the following conditions:
     * 
     * The above copyright notice and this permission notice shall be
     * included in all copies or substantial portions of the Software.
     * 
     * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
     * EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
     * MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
     * NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE
     * LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION
     * OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION
     * WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
     * 
     * @param {type} $
     * @returns {undefined}
     */
    require(['jquery'], function($) { 
            var showChar = 100;
            var ellipsestext = "...";
            var moretext = M.util.get_string('showmore', 'pdfannotator');
            var lesstext = M.util.get_string('showless', 'pdfannotator');
            $('.more').each(function() {
                    var content = $(this).html();
                    var widthParent = document.querySelector('.panel-title').offsetWidth;
                    showChar = widthParent/10;
                    if(content.length > (showChar + ellipsestext.length)) {

                            var c = content.substr(0, showChar); // erste String-Hälfte
                            var h = content.slice(showChar); // zweite String-Hälfte
                            
                            var html = c + '<span class="moreellipses">' + ellipsestext+ '&nbsp;</span><span class="morecontent"><span>' + h + '</span>&nbsp;&nbsp;<a href="" class="morelink">' + moretext + '</a></span>';

                            $(this).html(html);
                    }

            });

            $(".morelink").click(function(){
                    if($(this).hasClass("less")) {
                            $(this).removeClass("less");
                            $(this).html(moretext);
                    } else {
                            $(this).addClass("less");
                            $(this).html(lesstext);
                    }
                    $(this).parent().prev().toggle();
                    $(this).prev().toggle();
                    return false;
            });
    });   
    /**
     * This function dynamically adds and removes a panel's CSS class 'open'
     * as the collapsable list within it is opened or closed.
     * 
     * @param {type} $
     * @returns {undefined}
     */
    require(['jquery'], function($) {
    
        $(document).ready(function () {
        
            $('.panel-default').each(function(index,elem) {
                if(elem.classList.contains('open')) {
                    elem.classList.remove('open'); 
                }
            });
            
            $('.annotator').click(function(e) {
                    
                // 1. Get the sourrunding panel   
                    let panel_id = -1;
                    let annoid = -1;
                    let dropdown = -1;
                    
                // Category: Answers to your questions
                    
                    // Did you click on the annotator's name?
                    if (e.target.classList.contains('answerlink')) {
                        annoid = e.target.id.slice(11);
                        panel_id = 'answerpanel_' + annoid;
                        dropdown = 1;
                    }
                    // Did you click on the count or triangle icon?
                    if (e.target.parentNode.classList.contains('answerlink')) {
                        annoid = e.target.parentNode.id.slice(11);
                        panel_id = 'answerpanel_' + annoid;
                        dropdown = 1;
                    }
                
                // Category: New questions
                
                    // Did you click on the annotator's name?
                    if (e.target.classList.contains('questionlink')) {
                        annoid = e.target.id.slice(13);
                        panel_id = 'questionpanel_' + annoid;
                        dropdown = 2;
                    }
                    // Did you click on the count or triangle icon?
                    if (e.target.parentNode.classList.contains('questionlink')) {
                        annoid = e.target.parentNode.id.slice(13);
                        panel_id = 'questionpanel_' + annoid
                        dropdown = 2;
                    }
                    
                    // Category: My posts
                
                    if (e.target.classList.contains('postlink')) {
                        dropdown = 3;
                        annoid = e.target.id.slice(9);
                        panel_id = 'postpanel_' + annoid;

                    } else if (e.target.parentNode.classList.contains('postlink')) {
                        dropdown = 3;
                        annoid = e.target.parentNode.id.slice(9);
                        panel_id = 'postpanel_' + annoid;
                    }
                    
                    
                    let panel = document.getElementById(panel_id);                           

                    let iconid = 'dropdown' + dropdown + '_' + annoid;
                    let icon = document.getElementById(iconid);

                // 2. Add or remove the CSS class 'open'
                    
                    if($(panel).hasClass('open')) {                       
                        panel.classList.remove('open');
                        icon.src = M.util.image_url('/t/collapsed');

                    } else {

                        // Mark any open elements as closed...
                        if($('.panel-default.open')) {
                           $('.panel-default.open').each(function(index, elem){
                               elem.classList.remove('open');
                           });
                        }
                        // and toggle the dropdown icon back to collapsed
                        if($('dropdown_image')) {
                            $('.dropdown_image').each(function(index, elem){
                                elem.src = M.util.image_url('/t/collapsed');
                            });
                            
                            
                        }
                        // ...and then mark this element as open
                        panel.classList.add('open');
                        icon.src = M.util.image_url('/t/dropdown');

                    }

            }); // click function
        }); // document.ready function              
    });   
    /**
     * Function causes zebra stripes effect on all list items
     * 
     * @param {type} $
     * @returns {undefined}
     */
    require(['jquery'], function($) {

        $('.zebra li:even').addClass('stripe-even');
        $('.zebra li:odd').addClass('stripe-odd');
        
    });
    
