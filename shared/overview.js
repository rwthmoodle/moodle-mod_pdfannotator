/*
 * This file is a collection of JavaScript functions that control the behaviour
 * of the overview pages / templates for both student and teacher
 *
 * @package   mod_pdfannotator
 * @copyright 2018 RWTH Aachen, Rabea de Groot and Anna Heynkes (see README.md)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 *
 * @param {type} Y
 * @param {type} __annotatorid
 * @param {type} __role
 * @return {undefined}
 */
function startOverview(Y, __annotatorid) { // Wrapper function that is called by teacher-/studentview controller.

    require(['jquery', 'core/templates', 'core/notification'], function ($, templates, notification) {

        /************************** 1. Call initialising functions **************************/

        markCurrent();

        loadLocalStorageSettings();
        makeCurrentViewNewStandard();
        setTimespanForDisplayingNewQuestions();

        toggleShowAllShowNone();
        toggleOpenAndClose();

        addEventListenerForHidingReports();
        addEventListenerForDisplayingReports();
        addEventListenerForDeletingReports();

        addEventListenerForHidingAnswers();
        addEventListenerForDisplayingAnswers();
        addEventListenerForUnsubscribing();

        shortenTextOverview();
        renderMathJax();

        /************************** 2. Function definitions **************************/

        /**
         * Helper function for setting icon paths after asynchronous partial page reload
         *
         * @param {type} data
         * @return {undefined}
         */
        function setPicturePaths(data) {
            data.pixcollapsed = M.util.image_url('/t/collapsed');
            data.pixgotox = M.util.image_url('link_small', 'mod_pdfannotator');
            data.pixhide = M.util.image_url('/e/accessibility_checker');
            data.pixdisplay = M.util.image_url('/i/hide');
            data.pixdelete = M.util.image_url('/t/delete');
        }

        /**
         * This function adds the CSS class 'mycurrent' to the current annotator
         * so that it is highlighted by a different background color from the
         * other annotators in the course.
         *
         * @return {undefined}
         */
        function markCurrent() {
            let headings = document.getElementsByClassName('panel-default');
            let panel;
            let id1a = 'answerpanel_' + __annotatorid;
            let id1b = 'reportpanel_' + __annotatorid;
            let id2 = 'questionpanel_' + __annotatorid;
            let id3 = 'postpanel_' + __annotatorid;
            let id4 = 'hiddenentrypanel_' + __annotatorid;
            let id5 = 'hiddenreportpanel_' + __annotatorid;

            for (i = 0; i < headings.length; i++) {
                (function (innerI) {
                    panel = headings[innerI];
                    if (panel.id === id1a || panel.id === id1b || panel.id === id2 || panel.id === id3 || panel.id === id4 || panel.id === id5) {
                        panel.classList.add('mycurrent');
                    }
                })(i);
            }
        }
        /**
         * Function opens or collapses all categories and sets the timespan for
         * displaying "new" questions
         *
         * (according to the user's local storage settings)
         *
         * @return {undefined}
         */
        function loadLocalStorageSettings() {
            if (localStorage !== "undefined") {

                for (let i = 0; i <= 5; i++) {
                    (function (innerI) {

                        let id = 'accordion' + innerI;

                        let container = document.getElementById(id);

                        if (container) {
                            let item = "collapseCategory" + innerI;

                            if (localStorage.getItem(item) === 'true') {
                                if (!container.classList.contains('collapse')) {
                                    container.classList.add('collapse');
                                }
                                if (container.classList.contains('in')) {
                                    container.classList.remove('in');
                                }
                                if (container.classList.contains('show')) {
                                    container.classList.remove('show');
                                }
                            }

                            if (localStorage.getItem(item) === 'false') {
                                if (container.classList.contains('collapse') && !(container.classList.contains('in') && !(container.classList.contains('show')))) {
                                    container.classList.remove('collapse');
                                }
                            }
                        }
                    })(i);
                }
                // Load user preferences concerning new questions time period.
                var usernewsspan = localStorage.getItem('pdfannotatornewsspan');
                if (usernewsspan != null) {
                    var id = 'setNewsspan' + usernewsspan;
                    document.getElementById(id).selected = "true";

                    if (usernewsspan != 3) { // 3 is selected per default.
                        return $.ajax({
                            type: "POST",
                            url: "action.php",
                            data: {"documentId": __annotatorid, "newsspan": usernewsspan, "action": 'setNewsspan', sesskey: M.cfg.sesskey}
                        }).then(function (data) {
                            data = JSON.parse(data);
                            if (data.status === "success") {
                                (function (templates, data) {
                                    setPicturePaths(data);
                                    templates.render('mod_pdfannotator/overview_new_questions', data)
                                            .then(function (html, js) {
                                                templates.replaceNodeContents('#accordion2', html, js);
                                                toggleOpenAndClose();
                                                markCurrent();
                                                document.getElementById('count2').innerHTML = '(' + (data.count2) + ')';
                                                shortenTextOverview();
                                                renderMathJax();
                                            }); // Add a catch.
                                })(templates, data.newdata);
                            }
                        });
                    }
                }
            }
        }
        function setTimespanForDisplayingNewQuestions() {
            require(['core/notification'], function (notification) {
                let time = document.getElementById('setNewsspan');
                time.addEventListener("change", function () {
                    var t = time.value;
                    // 1. Show change (rerender partial template "overview_new_questions").
                    return $.ajax({
                        type: "POST",
                        url: "action.php",
                        data: {"documentId": __annotatorid, "newsspan": t, "action": 'setNewsspan', sesskey: M.cfg.sesskey}
                    }).then(function (data) {
                        data = JSON.parse(data);

                        if (data.status === "success") {
                            (function (templates, data) {
                                setPicturePaths(data);
                                templates.render('mod_pdfannotator/overview_new_questions', data)
                                        .then(function (html, js) {
                                            templates.replaceNodeContents('#accordion2', html, js);
                                            toggleOpenAndClose();
                                            markCurrent(); // Former parameters: null, data.openannotator
                                            document.getElementById('count2').innerHTML = '(' + (data.count2) + ')';
                                            shortenTextOverview();
                                                renderMathJax();
                                        }); // Add a catch.
                            })(templates, data.newdata);
                        } else {
                            console.error(M.util.get_string('timecouldnotbeset', 'pdfannotator'));
                        }

                        // 2. Save change.
                        if (localStorage !== "undefined") {
                            localStorage.setItem('pdfannotatornewsspan', t);
                            notification.addNotification({
                                message: M.util.get_string('timewasset', 'pdfannotator'),
                                type: "success"
                            });
                        } else {
                            notification.addNotification({
                                message: M.util.get_string('timecouldnotbeset', 'pdfannotator'),
                                type: "error"
                            });
                        }
                        setTimeout(function () {
                            let notificationpanel = document.getElementById("user-notifications");
                            while (notificationpanel.hasChildNodes()) {
                                notificationpanel.removeChild(notificationpanel.firstChild);
                            }
                        }, 5000);
                    });
                });
            });
        }
        /**
         * Function adds the event listener to the 'saveOverviewConfig' button.
         * Onclick, the current configuration of opened and closed categories is
         * stored in the browser's local storage
         *
         * @return {undefined}
         */
        function makeCurrentViewNewStandard() {
            require(['core/notification'], function (notification) {
                let button = document.getElementById('saveConfig');
                button.addEventListener("click", function () {
                    if (localStorage !== "undefined") {
                        for (let i = 0; i <= 5; i++) {
                            (function (innerI) {
                                let id = 'accordion' + innerI;
                                let container = document.getElementById(id);
                                if (container !== null) {
                                    let item = "collapseCategory" + innerI;
                                    if (container.classList.contains('collapse') && (!container.classList.contains('in')) && (!container.classList.contains('show'))) {
                                        localStorage.setItem(item, 'true');
                                    } else {
                                        localStorage.setItem(item, 'false');
                                    }
                                }

                            })(i);
                        }
                        notification.addNotification({
                            message: M.util.get_string('OverviewConfigSaved', 'pdfannotator'),
                            type: "success"
                        });

                    } else {
                        notification.addNotification({
                            message: M.util.get_string('OverviewConfigCouldNotBeSaved', 'pdfannotator'),
                            type: "error"
                        });
                    }
                    setTimeout(function () {
                        let notificationpanel = document.getElementById("user-notifications");
                        while (notificationpanel.hasChildNodes()) {
                            notificationpanel.removeChild(notificationpanel.firstChild);
                        }
                    }, 3000);
                });
            });
        }
        /**
         * Function initialises "show/collapse all"-button with a click-event-listener
         * for toggling between collapsing and decollapsing all categories on the overview page
         *
         * @return {undefined}
         */
        function toggleShowAllShowNone() {
            let button = document.getElementById('openAll');
            button.addEventListener("click", function (e) {

                let doclose = false;
                if (e.target.innerHTML === M.util.get_string('closeAll', 'pdfannotator')) {
                    doclose = true;
                }

                for (let i = 0; i <= 5; i++) {
                    (function (innerI) {
                        let id = 'accordion' + innerI;
                        let container = document.getElementById(id);
                        if (container !== null) {
                            if (doclose === false && container.classList.contains('collapse') && (!container.classList.contains('in')) && (!container.classList.contains('show'))) {
                                container.classList.remove('collapse');
                                container.style.removeProperty("height");
                                // if (localStorage !== "undefined") { // Check for browser support.
                                     // localStorage.setItem('collapseoverview', 'false');
                                // }
                                e.target.innerHTML = M.util.get_string('closeAll', 'pdfannotator');
                            }
                            if (doclose === true && (!container.classList.contains('collapse') || container.classList.contains('in') || container.classList.contains('show'))) {
                                if (!container.classList.contains('collapse')) {
                                    container.classList.add('collapse');
                                }
                                if (container.classList.contains('in')) {
                                    container.classList.remove('in');
                                }
                                if (container.classList.contains('show')) {
                                    container.classList.remove('show');
                                }
                                // if (localStorage !== "undefined") {
                                  // localStorage.setItem('collapseoverview', 'true');
                                // }
                                e.target.innerHTML = M.util.get_string('openAll', 'pdfannotator');
                            }
                        }

                    })(i);
                }
            });
        }
        /**
         * Function enables the accordion behaviour
         *
         * @return {undefined}
         */
        function toggleOpenAndClose() {

            $(document).ready(function () {

                $('.panel-default').each(function (index, elem) {
                    if (elem.classList.contains('open')) {
                        elem.classList.remove('open');
                    }
                });

                $('.annotator').click(function (e) {

                    // 1. Get the sourrunding panel.

                    let panel_id = -1;
                    let annoid = -1;
                    let dropdown = -1;

                    // 1.1 a Category: Answers for this user in this course.

                    if (e.target.classList.contains('answerlink')) {
                        dropdown = 1;
                        annoid = e.target.id.split('_')[1];
                        panel_id = 'answerpanel_' + annoid;

                    } else if (e.target.parentNode.classList.contains('answerlink')) {
                        dropdown = 1;
                        annoid = e.target.parentNode.id.split('_')[1];
                        panel_id = 'answerpanel_' + annoid;
                    }

                    // 1.1 b Category: Reported comments in this course.

                    if (e.target.classList.contains('reportlink')) {
                        dropdown = 0; // Has been 1.
                        annoid = e.target.id.slice(12);
                        panel_id = 'reportpanel_' + annoid;

                    } else if (e.target.parentNode.classList.contains('reportlink')) {
                        dropdown = 0; // Has been 1.
                        annoid = e.target.parentNode.id.slice(12);
                        panel_id = 'reportpanel_' + annoid;
                    }

                    // 1.2 Category: New questions.

                    if (e.target.classList.contains('questionlink')) {
                        dropdown = 2;
                        annoid = e.target.id.slice(13);
                        panel_id = 'questionpanel_' + annoid;

                    } else if (e.target.parentNode.classList.contains('questionlink')) {
                        dropdown = 2;
                        annoid = e.target.parentNode.id.slice(13);
                        panel_id = 'questionpanel_' + annoid;
                    }

                    // 1.3 Category: My posts.

                    if (e.target.classList.contains('postlink')) {
                        dropdown = 3;
                        annoid = e.target.id.slice(9);
                        panel_id = 'postpanel_' + annoid;

                    } else if (e.target.parentNode.classList.contains('postlink')) {
                        dropdown = 3;
                        annoid = e.target.parentNode.id.slice(9);
                        panel_id = 'postpanel_' + annoid;
                    }

                    // 1.4 Category: Administrate hidden entries/answers (display again or delete permanently).

                    if (e.target.classList.contains('hiddenentrylink')) {
                        dropdown = 4;
                        annoid = e.target.id.slice(16);
                        panel_id = 'hiddenentrypanel_' + annoid;

                    } else if (e.target.parentNode.classList.contains('hiddenentrylink')) {
                        dropdown = 4;
                        annoid = e.target.parentNode.id.slice(16);
                        panel_id = 'hiddenentrypanel_' + annoid;
                    }

                    // 1.5 Category: Administrate hidden reports (display again or delete permanently).

                    if (e.target.classList.contains('hiddenreportlink')) {
                        dropdown = 5;
                        annoid = e.target.id.slice(17);
                        panel_id = 'hiddenreportpanel_' + annoid;

                    } else if (e.target.parentNode.classList.contains('hiddenreportlink')) {
                        dropdown = 5;
                        annoid = e.target.parentNode.id.slice(17);
                        panel_id = 'hiddenreportpanel_' + annoid;
                    }

                    // 1. For all:

                    let panel = document.getElementById(panel_id);
                    let iconid = 'dropdown' + dropdown + '_' + annoid;
                    let icon = document.getElementById(iconid);

                    // 2. Add or remove the CSS class 'open' and toggle the dropdown icon.

                    if ($(panel).hasClass('open')) {
                        panel.classList.remove('open');
                        icon.src = M.util.image_url('/t/collapsed'); // '/moodle/pix/t/collapsed.png';

                    } else {

                        // Mark any open elements as closed.
                        if ($('.panel-default.open')) {
                            $('.panel-default.open').each(function (index, elem) {
                                elem.classList.remove('open');
                            });
                        }
                        // Toggle the dropdown icon back to collapsed...
                        if ($('dropdown_image')) {
                            $('.dropdown_image').each(function (index, elem) {
                                elem.src = M.util.image_url('/t/collapsed'); // '/moodle/pix/t/collapsed.png';
                            });

                        }
                        // And then mark this element as open.
                        panel.classList.add('open');
                        icon.src = M.util.image_url('/t/dropdown'); // '/moodle/pix/t/dropdown.png';
                    }

                });

            });
        }
        /**
         * Mark a report as seen and hide it henceforth when user clicks hide icon (eye)
         *
         * @return {undefined}
         */
        function addEventListenerForHidingReports() {

            var hide = document.getElementsByClassName("hidereport");

            for (i = 0; i < hide.length; i++) {
                (function (innerI) {
                    hide[innerI].addEventListener("click", function () {
                        
                        var annotatorid = this.getAttribute("data-annotator"); // this = event.target.parentNode
                        var openannotator = __annotatorid;

                        return $.ajax({
                            type: "POST",
                            url: "action.php",
                            data: {"documentId": annotatorid, "reportid": this.id, "openannotator": openannotator, "action": 'markReportAsSeen', sesskey: M.cfg.sesskey}
                        }).then(function (data) {

                            data = JSON.parse(data);

                            if (data.status === "success") {

                                // 1. Make the report disappear in the 'reported comments' category.
                                var id = 'report' + data.reportid;
                                var report = document.getElementById(id);
                                if (report !== null) {
                                    report.parentNode.removeChild(report);
                                    // report.style.setProperty('display', 'none', 'important');
                                }

                                id = 'reportcount' + data.pdfannotatorid;
                                var reportcount = document.getElementById(id).innerHTML;
                                reportcount = reportcount.slice(1, reportcount.length - 1);
                                var newreportcount = reportcount - 1;
                                if (newreportcount !== 0) { // Is there at least 1 'unseen'/not hidden report left in this annotator?
                                    document.getElementById(id).innerHTML = '(' + (newreportcount) + ')';

                                } else { // No longer show the annotator.
                                    id = 'reportpanel_' + data.pdfannotatorid;
                                    var reportpanel = document.getElementById(id);
                                    if (reportpanel !== null) {
                                        reportpanel.style.setProperty('display', 'none', 'important');
                                    }
                                }
                                // Adjust global report count.
                                var globalReportCount = document.getElementById('count0').innerHTML;
                                globalReportCount = globalReportCount.slice(1, globalReportCount.length - 1);
                                globalReportCount--;
                                document.getElementById('count0').innerHTML = '(' + (globalReportCount) + ')';

                                // Adjust global administration count.
                                var globalAdminCount = document.getElementById('count5').innerHTML;
                                globalAdminCount = globalAdminCount.slice(1, globalAdminCount.length - 1);
                                globalAdminCount++;
                                document.getElementById('count5').innerHTML = '(' + (globalAdminCount) + ')';

                                // 2. Make the same report appear in the 'administrate' category by rerendering the corresponding template.

                                (function (templates, data) {
                                    setPicturePaths(data);
                                    templates.render('mod_pdfannotator/overview_hidden_reports', data)
                                            .then(function (html, js) {
                                                templates.replaceNodeContents('#accordion5', html, js);
                                                addEventListenerForDeletingReports(2);
                                                addEventListenerForDisplayingReports();
                                                toggleOpenAndClose();
                                                markCurrent(); // Former parameters: null, data.openannotator
                                                shortenTextOverview();
                                                renderMathJax();
                                            }); // Add a catch.
                                })(templates, data.newdata);

                            } else {

                                console.error(M.util.get_string('error:hide', 'pdfannotator'));
                            }

                        });

                    });
                })(i);
            }

        }
        /**
         * Mark an answer as seen and hide it henceforth when student user clicks hide icon (eye)
         *
         * @return {undefined}
         */
        function addEventListenerForHidingAnswers() {

            var hide = document.getElementsByClassName("hideanswer");

            for (i = 0; i < hide.length; i++) {
                (function (innerI) {
                    hide[innerI].addEventListener("click", function () {
                        var annotatorid = this.getAttribute("data-annotator");
                        var openannotator = __annotatorid;

                        return $.ajax({
                            type: "POST",
                            url: "action.php",
                            data: {"documentId": annotatorid, "answerid": hide[innerI].id, "openannotator": openannotator, "action": 'markAnswerAsSeen', sesskey: M.cfg.sesskey}
                        }).then(function (data) {

                            data = JSON.parse(data);

                            if (data.status === "success") {

                                // 1. Make the answer disappear in the 'answer' category.
                                var id = 'answer' + data.answerid;
                                var answer = document.getElementById(id);
                                if (answer !== null) {
                                    answer.style.setProperty('display', 'none', 'important');
                                }

                                id = 'answercount' + data.pdfannotatorid; // TODO!
                                var answercount = document.getElementById(id).innerHTML;
                                answercount = answercount.slice(1, answercount.length - 1);
                                var newanswercount = answercount - 1;

                                if (newanswercount !== 0) { // Is there at least 1 'unseen'/not hidden report left in this annotator?
                                    document.getElementById(id).innerHTML = '(' + (newanswercount) + ')';

                                } else { // No longer show the annotator.
                                    id = 'answerpanel_' + data.pdfannotatorid;
                                    var answerpanel = document.getElementById(id);
                                    if (answerpanel !== null) {
                                        answerpanel.style.setProperty('display', 'none', 'important');
                                    }
                                }

                                // 2. Make the same answer appear in the 'administrate' category by rerendering the corresponding template.
                                (function (templates, data) {
                                    setPicturePaths(data);
                                    templates.render('mod_pdfannotator/overview_administrate_entries', data)
                                            .then(function (html, js) {
                                                templates.replaceNodeContents('#accordion4', html, js);
                                                // addEventListenerForDelete();
                                                addEventListenerForDisplayingAnswers();
                                                toggleOpenAndClose();
                                                markCurrent(); // Former parameters: null, data.openannotator
                                                shortenTextOverview();
                                                renderMathJax();
                                            }); // Add a catch!
                                })(templates, data.newdata);

                                // Adjust global answer count.
                                var globalAnswerCount = document.getElementById('count1').innerHTML;
                                globalAnswerCount = globalAnswerCount.slice(1, globalAnswerCount.length - 1);
                                globalAnswerCount--;
                                document.getElementById('count1').innerHTML = '(' + (globalAnswerCount) + ')';

                                // Adjust global administration count.
                                var globalAdminCount = document.getElementById('count4').innerHTML;
                                globalAdminCount = globalAdminCount.slice(1, globalAdminCount.length - 1);
                                globalAdminCount++;
                                document.getElementById('count4').innerHTML = '(' + (globalAdminCount) + ')';

                            } else {
                                console.error(M.util.get_string('error:hide', 'pdfannotator'));
                            }

                        });

                    });
                })(i);
            }

        }
        /**
         * Mark a hidden report as unseen again and display it henceforth when teacher user clicks display icon (eye).
         *
         * @return {undefined}
         */
        function addEventListenerForDisplayingReports() {

            var hide = document.getElementsByClassName("reportseinblenden");

            for (i = 0; i < hide.length; i++) {
                (function (innerI) {
                    hide[innerI].addEventListener("click", function () {
                        var annotatorid = this.getAttribute("data-annotator");
                        var openannotator = __annotatorid;

                        return $.ajax({
                            type: "POST",
                            url: "action.php",
                            data: {"documentId": annotatorid, "reportid": hide[innerI].id, "openannotator": openannotator, "action": 'markReportAsUnseen', sesskey: M.cfg.sesskey}
                        }).then(function (data) {

                            data = JSON.parse(data);

                            if (data.status === "success") {

                                // 1.1 No longer display the report in 'administrate entries' category.
                                var id = 'hiddenreport' + data.reportid;
                                var target = document.getElementById(id);
                                if (target !== null) {
                                    target.style.setProperty('display', 'none', 'important');
                                }

                                // 1.2 Instead, display it once more in the regular 'reports' category by rerendering the corresponding template.
                                (function (templates, data) {
                                    setPicturePaths(data);
                                    templates.render('mod_pdfannotator/overview_reports', data)
                                            .then(function (html, js) {
                                                templates.replaceNodeContents('#accordion0', html, js);
                                                addEventListenerForDeletingReports(1);
                                                addEventListenerForHidingReports();
                                                toggleOpenAndClose();
                                                markCurrent(null, data.openannotator);
                                                shortenTextOverview();
                                                renderMathJax();
                                            }); // Add a catch.
                                })(templates, data.newdata);

                                // 2.1 a) Adjust the pdfannotator's count of hidden reports (i.e. substract 1).
                                id = 'hiddenreportcount' + data.pdfannotatorid;

                                var hiddenentrycount = document.getElementById(id).innerHTML;

                                hiddenentrycount = hiddenentrycount.slice(1, hiddenentrycount.length - 1); // Slice of the brackets () around the count.

                                var newhiddenentrycount = hiddenentrycount - 1;

                                if (newhiddenentrycount !== 0) { // Is there at least 1 'unseen'/not hidden report left in this annotator?
                                    target = document.getElementById(id);
                                    if (target !== null) {
                                        target.innerHTML = '(' + (newhiddenentrycount) + ')';
                                    }

                                } else {

                                    // 2.1 b) If it was the last hidden entry in this annotator, then no longer show the annotator.
                                    id = 'hiddenreportpanel_' + data.pdfannotatorid;
                                    var target = document.getElementById(id);
                                    if (target !== null) {
                                        target.style.setProperty('display', 'none', 'important');
                                    }

                                }

                                // Adjust global report count.
                                var globalReportCount = document.getElementById('count0').innerHTML;

                                globalReportCount = globalReportCount.slice(1, globalReportCount.length - 1);

                                globalReportCount++;

                                document.getElementById('count0').innerHTML = '(' + (globalReportCount) + ')';

                                // Adjust global administration count.
                                var globalAdminCount = document.getElementById('count5').innerHTML;

                                globalAdminCount = globalAdminCount.slice(1, globalAdminCount.length - 1);

                                globalAdminCount--;

                                document.getElementById('count5').innerHTML = '(' + (globalAdminCount) + ')';

                            } else {
                                console.error(M.util.get_string('error:show', 'pdfannotator'));
                            }

                        });

                    });
                })(i);
            }

        }
        /**
         * Mark a hidden answer as unseen again and display it henceforth when student user clicks display icon (eye)
         *
         * @return {undefined}
         */
        function addEventListenerForDisplayingAnswers() {

            var hide = document.getElementsByClassName("einblenden");

            for (i = 0; i < hide.length; i++) {
                (function (innerI) {
                    hide[innerI].addEventListener("click", function () {
                        var annotatorid = this.getAttribute("data-annotator");
                        var openannotator = __annotatorid;

                        return $.ajax({
                            type: "POST",
                            url: "action.php",
                            data: {"documentId": annotatorid, "answerid": hide[innerI].id, "openannotator": openannotator, "action": 'markAnswerAsUnseen', sesskey: M.cfg.sesskey}
                        }).then(function (data) {

                            data = JSON.parse(data);

                            if (data.status === "success") {

                                // 1.1 No longer display the answer in 'administrate entries' category.
                                var id = 'hiddenentry' + data.answerid;
                                var target = document.getElementById(id);
                                if (target !== null) {
                                    target.style.setProperty('display', 'none', 'important');
                                }
                                // 1.2 Instead, display it once more in the regular 'answers' category by rerendering the corresponding template.
                                (function (templates, data) {
                                    setPicturePaths(data);
                                    templates.render('mod_pdfannotator/overview_answers', data)
                                            .then(function (html, js) {
                                                templates.replaceNodeContents('#accordion1', html, js);
                                                // addEventListenerForDelete();
                                                addEventListenerForHidingAnswers();
                                                toggleOpenAndClose();
                                                markCurrent(); // Former parameters: null, data.openannotator
                                                shortenTextOverview();
                                                renderMathJax();
                                            }); // Add a catch.
                                })(templates, data.newdata);
                                // 2.1 a) Adjust the pdfannotator's count of hidden entries (i.e. substract 1).
                                id = 'hiddenentrycount' + data.pdfannotatorid;
                                var hiddenentrycount = document.getElementById(id).innerHTML;
                                hiddenentrycount = hiddenentrycount.slice(1, hiddenentrycount.length - 1); // Slice of the brackets () around the count.
                                var newhiddenentrycount = hiddenentrycount - 1;

                                if (newhiddenentrycount !== 0) { // Is there at least 1 'unseen'/not hidden report left in this annotator?
                                    target = document.getElementById(id);
                                    if (target !== null) {
                                        target.innerHTML = '(' + (newhiddenentrycount) + ')';
                                    }

                                } else {

                                    // 2.1 b) If it was the last hidden entry in this annotator, then no longer show the annotator.
                                    id = 'hiddenentrypanel_' + data.pdfannotatorid;
                                    var target = document.getElementById(id);
                                    if (target !== null) {
                                        target.style.setProperty('display', 'none', 'important');
                                    }

                                }

                                // Adjust global answer count.
                                var globalAnswerCount = document.getElementById('count1').innerHTML;
                                globalAnswerCount = globalAnswerCount.slice(1, globalAnswerCount.length - 1);
                                globalAnswerCount++;
                                document.getElementById('count1').innerHTML = '(' + (globalAnswerCount) + ')';

                                // Adjust global administration count.
                                var globalAdminCount = document.getElementById('count4').innerHTML;
                                globalAdminCount = globalAdminCount.slice(1, globalAdminCount.length - 1);
                                globalAdminCount--;
                                document.getElementById('count4').innerHTML = '(' + globalAdminCount + ')';

                            } else {
                                 console.error(M.util.get_string('error:show', 'pdfannotator'));
                            }

                        });

                    });
                })(i);
            }

        }
        /**
         * Permanently delete a report when teacher user clicks delete icon
         *
         * @return {undefined}
         */
        function addEventListenerForDeletingReports(category = null) {

            var deletebuttons;

            if (!category) {
                deletebuttons = document.getElementsByClassName("delete");
            } else if (category === 1) {
                deletebuttons = document.getElementsByClassName("catone");
            } else { // category === 2
                deletebuttons = document.getElementsByClassName("cattwo");
            }

            for (i = 0; i < deletebuttons.length; i++) {
                (function (innerI) {
                    deletebuttons[innerI].addEventListener("click", function () {

                        // XXX Would be nice to ask for confirmation before deleting a report
                        //
                        // var reallyDelete = false;
                        //
                        // confirmDelete = M.util.get_string('deletingComment_manager', 'pdfannotator');
                        //
                        // function dialogCallbackForDeleteCancel(){
                            // return false;
                        // }
                        // function dialogCallbackForDelete() {
                            // return true;
                        // }
                        //
                        // notification.confirm(M.util.get_string('deletingCommentTitle', 'pdfannotator'), confirmDelete, M.util.get_string('yesButton', 'pdfannotator'), M.util.get_string('cancelButton', 'pdfannotator'), dialogCallbackForDelete, dialogCallbackForDeleteCancel);

                        var reportid = this.id;
                        reportid = reportid.slice(6);

                        return $.ajax({
                            type: "POST",
                            url: "action.php",
                            data: {"documentId": __annotatorid, "reportid": reportid, "action": 'deleteReport', sesskey: M.cfg.sesskey} // documentID is a dummy passed on pro forma.
                        }).then(function (data) {
                            data = JSON.parse(data);

                            if (data.status === "success") {

                                var id = 'report' + data.reportid;
                                var target = document.getElementById(id);

                                if (target !== null) {

                                    // 1.1 No longer display the report in the reports category.
                                    target.style.setProperty('display', 'none', 'important');
                                    // target.parentNode.removeChild(target);

                                    // 1.2 Adjust the annotator's report count to -= 1
                                    id = 'reportcount' + data.pdfannotatorid;

                                    var reportcount = document.getElementById(id).innerHTML;
                                    reportcount = reportcount.slice(1, reportcount.length - 1);

                                    var newreportcount = reportcount - 1;

                                    if (newreportcount !== 0) { // Is there at least 1 'unseen'/not hidden report left in this annotator?
                                        document.getElementById(id).innerHTML = '(' + (newreportcount) + ')';

                                    } else { // 1.3 If it was the last report in this annotator, no longer display the latter.

                                        id = 'reportpanel_' + data.pdfannotatorid;
                                        var target = document.getElementById(id);
                                        if (target !== null) {
                                            target.style.setProperty('display', 'none', 'important');
                                        }

                                    }
                                    // Adjust global report count.
                                    var globalReportCount = document.getElementById('count0').innerHTML;
                                    globalReportCount = globalReportCount.slice(1, globalReportCount.length - 1);
                                    globalReportCount--;
                                    document.getElementById('count0').innerHTML = '(' + (globalReportCount) + ')';

                                }
                                id = 'hiddenreport' + data.reportid;
                                target = document.getElementById(id);
                                if (target !== null) {

                                    // 2.1 No longer display the report in the hidden reports category.
                                    target.style.setProperty('display', 'none', 'important');

                                    // 2.2 Adjust the annotator's hidden report count to -= 1
                                    id = 'hiddenreportcount' + data.pdfannotatorid;
                                    var hiddenentrycount = document.getElementById(id).innerHTML;
                                    hiddenentrycount = hiddenentrycount.slice(1, hiddenentrycount.length - 1);
                                    var newhiddenentrycount = hiddenentrycount - 1;

                                    if (newhiddenentrycount !== 0) { // Is there at least 1 'unseen'/not hidden report left in this annotator.
                                        document.getElementById(id).innerHTML = '(' + (newhiddenentrycount) + ')';

                                    } else { // 2.3 If it was the last hidden report in this annotator, no longer display the latter.

                                        id = 'hiddenreportpanel_' + data.pdfannotatorid;
                                        var target = document.getElementById(id);
                                        if (target !== null) {
                                            target.style.setProperty('display', 'none', 'important');
                                        }
                                    }
                                    // Adjust global administration count.
                                    var globalAdminCount = document.getElementById('count5').innerHTML;
                                    globalAdminCount = globalAdminCount.slice(1, globalAdminCount.length - 1);
                                    globalAdminCount--;
                                    document.getElementById('count5').innerHTML = '(' + (globalAdminCount) + ')';
                                }

                            } else {
                                 console.error(M.util.get_string('error:hide', 'pdfannotator'));
                            }

                        });

                    });
                })(i);
            }

        }
        /**
         * Students can unsubscribe from a question via the overview page by clicking on the bell icon
         *
         * @return {undefined}
         */
        function addEventListenerForUnsubscribing() {

            var unsubscribeButtons = document.getElementsByClassName("unsubscribe");

            for (i = 0; i < unsubscribeButtons.length; i++) {
                (function (innerI) {
                    unsubscribeButtons[innerI].addEventListener("click", function () {

                        var annotationid = unsubscribeButtons[innerI].id;
                        annotationid = annotationid.slice(11);

                        return $.ajax({
                            type: "POST",
                            url: "action.php",
                            data: {"documentId": __annotatorid, "annotationid": annotationid, "action": 'unsubscribeQuestion', sesskey: M.cfg.sesskey} // documentID is a dummy passed on pro forma.
                        }).then(function (data) {
                            data = JSON.parse(data);

                            if (data.status === "success") {
                                notification.addNotification({
                                    message: M.util.get_string('successfullyUnsubscribed', 'pdfannotator'),
                                    type: "success"
                                });

                            } else if (data.status === 'error') {
                                notification.addNotification({
                                    message: M.util.get_string('error:unsubscribe', 'pdfannotator'),
                                    type: "error"
                                });
                                console.error(M.util.get_string('error:unsubscribe', 'pdfannotator'));
                            }
                            setTimeout(function () {
                                let notificationpanel = document.getElementById("user-notifications");
                                while (notificationpanel.hasChildNodes()) {
                                    notificationpanel.removeChild(notificationpanel.firstChild);
                                }
                            }, 3000);

                        });

                    });
                })(i);
            }

        }

    });
    /**
     * Shorten display of any report or question to a maximum of 120 characters and display
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
    function shortenTextOverview() {
        require(['jquery'], function ($) {
            var showChar = 120;
            var ellipsestext = "...";
            var moretext = M.util.get_string('showmore', 'pdfannotator');
            var lesstext = M.util.get_string('showless', 'pdfannotator');
            $('.more').each(function () {
                var content = this.innerText;
                var widthParent = document.querySelector('.panel-title').offsetWidth;
                if (widthParent === 0) {
                    widthParent = 917; // Minimum width.
                }
                showChar = widthParent / 10;
                if (content.length > (showChar + ellipsestext.length)) {

                    let x = 0;
                    let i1, i2, i3;
                    i1 = i2 = i3 = 0;
                    // If content contains MathJax, don't cut it in a formula.
                    while (i1 !== -1 || i2 !== -1 || i3 !== -1) {
                        i1 = content.indexOf('\(', x);
                        if (i1 > showChar) {
                            i1 = -1;
                        }
                        if (i1 > -1) {
                            x = content.indexOf('\)', x) + 4;
                            showChar = Math.max(showChar, x);
                        }
                        i2 = content.indexOf('\['.x);
                        if (i2 > showChar) {
                            i2 = -1;
                        }
                        if (i2 > -1 && i2 < showChar) {
                            x = content.indexOf('\]', x) + 4;
                            showChar = Math.max(showChar, x);
                        }
                        i3 = content.indexOf('$$', x);
                        if (i3 > showChar) {
                            i3 = -1;
                        }
                        if (i3 > -1 && i3 < showChar) {
                            x = content.indexOf('$$', i3 + 1) + 4
                            showChar = Math.max(showChar, x);
                        }
                        if (showChar === content.length) {
                            return;
                        }
                    }
                    var c = content.substr(0, showChar); // First part of the string.
                    var h = content.slice(showChar); // Second part of the string.

                    var html = c + '<span class="moreellipses">' + ellipsestext + '&nbsp;</span><span class="morecontent"><span>' + h + '</span>&nbsp;&nbsp;<a href="" class="morelink">' + moretext + '</a></span>';

                    $(this).html(html);
                }

            });

            $(".morelink").click(function () {
                if ($(this).hasClass("less")) {
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
    }

    function renderMathJax() {
        var counter = 0;
        let mathjax = function () {
            if (typeof (MathJax) !== "undefined") {
                MathJax.Hub.Queue(['Typeset', MathJax.Hub]);
            } else if (counter < 100) {
                counter++;
                setTimeout(mathjax, 100);
            } else {
            }
        };
        mathjax();
    }

}
