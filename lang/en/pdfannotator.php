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
 * @package   mod_pdfannotator
 * @copyright 2018 RWTH Aachen (see README.md)
 * @authors   Rabea de Groot, Anna Heynkes, Friederike Schwager
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 */

$string['pdfannotator'] = 'Document';

$string['strftimedatetime'] = '%d %b %Y, %I:%M %p';

/* ******************************* capabilities *******************************/
$string['pdfannotator:view'] = 'View PDF Annotation';
$string['pdfannotator:addinstance'] = 'add instance';
$string['pdfannotator:administrateuserinput'] = 'Administrate comments';

$string['pdfannotator:create'] = 'Create annotations and comments';
$string['pdfannotator:deleteown'] = 'Delete your own annotations and comments';
$string['pdfannotator:deleteany'] = 'Delete any annotation and comment';
$string['pdfannotator:edit'] = 'Edit your own annotations and comments';
$string['pdfannotator:editanypost'] = 'Edit any annotation and comment';
$string['pdfannotator:report'] = 'Report inappropriate comments to the course manager';
$string['pdfannotator:vote'] = "Vote for an interesting question or helpful answer";
$string['pdfannotator:subscribe'] = 'Subscribe to a question';
$string['pdfannotator:closequestion'] = 'Close own questions';
$string['pdfannotator:closeanyquestion'] = 'Close any question';
$string['pdfannotator:markcorrectanswer'] = 'Mark answers as correct';
$string['pdfannotator:usetextbox'] = 'Use textbox (even if the option is disabled for a PDF-Annotator)';
$string['pdfannotator:usedrawing'] = 'Use drawing (even if the option is disabled for a PDF-Annotator)';
$string['pdfannotator:printdocument'] = 'Download the document';
$string['pdfannotator:printcomments'] = 'Download the comments';

$string['pdfannotator:hidecomments'] = 'Hide comments for participants';
$string['pdfannotator:seehiddencomments'] = 'See hidden comments';

$string['pdfannotator:viewreports'] = 'View reported comments (overview page)';
$string['pdfannotator:viewanswers'] = 'View answers to subscribed questions (overview page)';
$string['pdfannotator:viewquestions'] = 'View open questions (overview page)';
$string['pdfannotator:viewposts'] = 'View own comments (overview page)';

$string['pdfannotator:viewstatistics'] = 'View statistics page';
$string['pdfannotator:viewteacherstatistics'] = 'See additional information on statistics page';

$string['pdfannotator:recievenewquestionnotifications'] = 'Recieve notifications about new questions';

/* ******************************* settings in mod form *******************************/

$string['global_setting_anonymous'] = 'Allow anonymous posting?';
$string['global_setting_anonymous_desc'] = 'With this option you allow your user to post comments anonymously. This option activates anonymous posting globally';
$string['global_setting_usevotes'] = 'Allow liking of comments?';
$string['global_setting_usevotes_desc'] = 'With this option users can like / vote for posts other than their own.';
$string['global_setting_use_studentdrawing'] = 'Allow drawings for participants?';
$string['global_setting_use_studentdrawing_desc'] = 'Please note that drawings are anonymous and can neither be commented nor reported.';
$string['global_setting_use_studenttextbox'] = 'Allow textboxes for participants?';
$string['global_setting_use_studenttextbox_desc'] = "Please note that textbox annotations are anonymous and can neither be commented nor reported.";
$string['global_setting_useprint'] = 'Allow save and print?';
$string['global_setting_useprint_desc'] = 'Allow participants to save and print the pdf document and its comments';
$string['global_setting_useprint_document'] = 'Allow saving/printing document?';
$string['global_setting_useprint_document_desc'] = 'Allow participants to save and print the pdf document';

$string['global_setting_useprint_comments'] = 'Allow saving/printing comments?';
$string['global_setting_useprint_comments_desc'] = 'Allow participants to save and print the annotations and comments';

$string['modulename'] = 'PDF Annotation';
$string['modulename_help'] = 'This Tool enables collaborative markup on PDF Documents. The users are able to annotate specific parts of an PDF and discuss them with other users.';
$string['modulename_link'] = 'mod/pdfannotator/view';
$string['modulenameplural'] = 'PDF Annotations';
$string['pdfannotatorname'] = 'PDF Annotation Tool';
$string['pluginadministration'] = 'PDF Annotation administration';
$string['pluginname'] = 'PDF Annotation';
$string['setting_alternative_name'] = 'Name';
$string['setting_alternative_name_help'] = "If the name is more than 20 characters long, the remaining characters will be replaced with '...' in the annotator's internal tab navigation.";
$string['setting_alternative_name_desc'] = 'Provide an alternative name for the PDF. If empty, the name of the pdf will be taken as represantative name';
$string['setting_anonymous'] = 'Allow anonymous posting?';
$string['setting_col_annotating_enabled'] = 'Enable collaborative annotation on PDF';
$string['setting_fileupload'] = 'Select a pdf-file';
$string['setting_fileupload_help'] = "You can only change the selected file until the annotator has been created by a click on 'Save'.";
$string['setting_usevotes'] = "Votes/Likes";
$string['setting_usevotes_help'] = "With this option enabled, users can like / vote for posts other than their own.";
$string['setting_use_studenttextbox'] = "Textbox";
$string['setting_use_studenttextbox_help'] = "Please note that textbox annotations are not anonymous and can neither be commented nor reported.";
$string['setting_use_studentdrawing'] = "Drawing";
$string['setting_use_studentdrawing_help'] = "Allow participants to save and print the pdf document without annotations or comments";


$string['setting_useprint'] = "save and print";
$string['setting_useprint_help'] = "Please note that drawings are not anonymous and can neither be commented nor reported.";
$string['setting_useprint_document'] = 'save and print pdf document';
$string['setting_useprint_document_help'] = 'Allow participants to save and print the pdf document';
$string['setting_useprint_comments'] = 'save and print comments';
$string['setting_useprint_comments_help'] = 'Allow participants to save and print the annotations and comments';

$string['setting_choosetimespanfornews'] = "For how long should a comment be marked as new?";
$string['usevotes'] = "Allow users to like comments.";
$string['use_studenttextbox'] = "Enable textbox tool for participants?";
$string['use_studentdrawing'] = "Enable drawing for participants?";
$string['useprint'] = "Give participants access to the PDF?";
$string['useprint_document'] = "Give participants access to the PDF?";
$string['useprint_comments'] = "Give participants access to the PDF and its comments?";

$string['allquestionsimgtitle'] = "Show all questions in this document";
$string['questionsimgtitle'] = "Show all questions on this page";
$string['search'] = "Search";

$string['legacyfiles'] = 'Migration of old course file';
$string['legacyfilesactive'] = 'Active';
$string['legacyfilesdone'] = 'Finished';
$string['clicktoopen2'] = 'Click {$a} link to view the file.';
$string['dnduploadpdfannotator'] = 'Create file for PDF Annotation';
$string['pdfannotatorcontent'] = 'Files and subfolders';
$string['filenotfound'] = 'File not found, sorry.';
$string['public'] = 'public';
$string['anonymous'] = 'Anonymous';
$string['sendAnonymous'] = 'post anonymous';
$string['pleaseSelectReportReason'] = '--- Please indicate your reason for reporting this comment. ---';
$string['reportreason'] = 'Reason for reporting this comment';
$string['reason'] = 'Explanation';
$string['titleforreportcommentform'] = 'Report comment';
$string['subtitleforreportcommentform'] = 'Your message for the course manager';
$string['inaccurate'] = 'inaccurate';
$string['inappropriate'] = 'inappropriate';
$string['other_reason'] = 'other reason';
$string['location'] = 'Pdf';
$string['slotdatetimelabel'] = 'Date and time';
$string['comment'] = 'Comment';
$string['reportedcomment'] = 'Reported comment';
$string['reportedby'] = 'by / on';
$string['writtenby'] = 'von / am';
$string['lastanswered'] = 'Last Answer';
$string['correct'] = 'correct';
$string['comments'] = 'Comments';
$string['author'] = 'Author';
$string['guestscantdoanything'] = 'Guests can\'t do anything here.';
$string['newanswersavailable'] = 'Recently answered';
$string['newquestions'] = 'Recently asked';
$string['read'] = 'Read';
$string['page'] = 'page';
$string['location'] = 'Location';
$string['comment'] = 'Comment';
$string['view'] = 'Document';
$string['overview'] = 'Overview';
$string['pdfannotatorcolumn'] = 'Document';
$string['itemsperpage'] = 'Items per page';
$string['show'] = 'Show';
$string['all'] = 'all';

$string['openquestions'] = 'unsolved';
$string['closedquestions'] = 'solved';
$string['allquestions'] = 'all';

$string['allanswers'] = 'all';
$string['subscribedanswers'] = 'to my subscribed questions';
$string['allreports'] = 'all reports';
$string['unseenreports'] = 'unread only';
$string['seenreports'] = 'read only';
$string['questionstab'] = 'Unsolved questions';
$string['questionstabicon'] = 'Unsolved questions';
$string['questionstabicon_help'] = 'This page displays all unsolved questions that were asked in this course. You can also choose to see all or all solved questions in this course.';
$string['answerstab'] = 'Answers';
$string['answerstabicon'] = 'Answers';
$string['answerstabicon_help'] = "This page can show you all answers or only answers to questions you subscribed* to. The list covers all annotators in this course.<br>*When you post a question yourself, you are automatically subscribed to it as long as you don't unsubscribe.";
$string['ownpoststab'] = 'My posts';
$string['ownpoststabicon'] = 'My posts';
$string['ownpoststabicon_help'] = 'This page displays all comments that you posted in this course.';
$string['reportstab'] = 'Reported comments';
$string['reportstabicon'] = 'Reported comments';
$string['reportstabicon_help'] = 'This page displays comments that were reported as inappropriate in this course. You can choose to see only unread/read* reports or all reports.<br>* Any manager of this course can mark a report as read.';
$string['voteshelpicon'] = 'Likes';
$string['voteshelpicon_help'] = 'This column tells you how many other people take an interest in the question.';
$string['voteshelpicontwo'] = 'Likes';
$string['voteshelpicontwo_help'] = 'This column tells you how often your posts were <em>liked</em>.';
$string['answercounthelpicon'] = 'Number of answers';
$string['answercounthelpicon_help'] = 'This column tells you how many answers a question has received.';
$string['iscorrecthelpicon'] = 'Correct';
$string['iscorrecthelpicon_help'] = 'When a teacher or manager has marked an answer as correct, a green check mark appears next to it.';
$string['recyclebintab'] = 'Read';
$string['recyclebintabicon'] = 'Read';
$string['recyclebintabicon_help'] = "This page displays all reports marked as <em>seen</em> in this course";

$string['markasread'] = 'Mark as read';
$string['markasunread'] = 'Mark as unread';
$string['successfullymarkedasread'] = 'The report was marked as read.';
$string['successfullymarkedasreadandnolongerdisplayed'] = 'The report was marked as read and removed from the table.';
$string['successfullymarkedasunread'] = 'he report was marked as unread.';
$string['successfullymarkedasunreadandnolongerdisplayed'] = 'The report was marked as unread and removed from the table.';

$string['putinrecyclebin'] = 'Mark as read';
$string['putinrecyclebin_report'] = 'Mark as read';
$string['overviewactioncolumn'] = "Manage";
$string['actiondropdown'] = "Options";
$string['statistic'] = 'Statistics';
$string['report'] = 'Report';
$string['toreport'] = 'Report';
$string['reportForbidden'] = 'Not allowed to report';
$string['introquestions'] = 'All unsolved questions in this course are displayed.';

$string['newstitle'] = 'Just asked';
// When displaying your message types in a user's messaging preferences it will use a string from your component's language file called "messageprovider:messagename".
$string['messageprovider:newanswer'] = 'When a question you subscribed to was answered';
$string['messageprovider:newreport'] = 'When a comment was reported';
$string['messageprovider:newquestion'] = 'When a new question was asked';
$string['notificationsubject:newreport'] = 'A comment was reported in {$a}';
$string['notificationsubject:newanswer'] = 'New answer to subscribed question in {$a}';
$string['notificationsubject:newquestion'] = 'New question in {$a}';
$string['reportwassentoff'] = 'The comment has been reported.';

$string['myquestion'] = 'Question';
$string['mypost'] = 'My post';
$string['question'] = 'question';
$string['askedby'] = 'By / on';
$string['answeredby'] = 'By / on';
$string['by'] = 'by';
$string['on'] = 'on';
$string['votes'] = 'Likes';
$string['answers'] = 'Answers';
$string['datetime'] = 'Date';
$string['post'] = 'Post';

$string['loading'] = 'Loading!';
$string['answerButton'] = 'Answer';
$string['editButton'] = 'Save';
$string['cancelButton'] = 'Cancel';
$string['printButton'] = 'Download';
$string['yesButton'] = 'Yes';
$string['rectangle'] = 'Add a Rectangle in the document and write a comment.';
$string['highlight'] = 'Highlight text and add a comment.';
$string['strikeout'] = 'Strikeout text and add a comment.';
$string['text'] = 'Add a text in the document.';
$string['drawing'] = 'Draw in the document with the pen.';
$string['point'] = 'Add a pin in the document and write a comment.';
$string['hideAnnotations'] = 'Hide Annotations';
$string['showAnnotations'] = 'Show Annotations';
$string['fullscreen'] = 'Fullscreen';
$string['fullscreenBack'] = 'Exit Fullscreen';
$string['print'] = 'download document';
$string['printwithannotations'] = 'download comments';
$string['infonocomments'] = "This document contains no comments at present.";
$string['emptypdf'] = 'There are no comemnts in this annotator at present.';

$string['zoom'] = 'zoom';
$string['zoomout'] = 'zoom out';
$string['zoomin'] = 'zoom in';

$string['currentPage'] = 'current page number';
$string['sumPages'] = 'Number of pages';
$string['nextPage'] = 'Next page';
$string['prevPage'] = 'Previous page';

$string['addAComment'] = 'Add a comment';
$string['createAnnotation'] = 'Create Annotation';
$string['editedComment'] = 'last edited ';
$string['modifiedby'] = ' by ';
$string['activities'] = 'Activities';
$string['editNotAllowed'] = 'Panning not allowed!';

/***************************** Delete a comment *********************************/

// Confirmation prompts.
$string['deletingCommentTitle'] = 'Are you sure?';

$string['printwhat'] = 'What would you like to open?';
$string['printwhatTitle'] = 'Open PDF in Acrobat Reader';
$string['pdfButton'] = 'Document';
$string['annotationsButton'] = 'Comments';
$string['deletingComment'] = 'The comment will be deleted. It will be displayed as deleted unless it is the last comment in its thread.';
$string['deletingQuestion_student'] = 'The question will be deleted.<br>If it is not answered, the annotation will be deleted too, otherwise the question will be displayed as deleted';
$string['deletingQuestion_manager'] = 'The comment will be deleted.<br>Hint: If you want to delete all answers as well, delete the annotation in the document.';
$string['deletingAnnotation_manager'] = 'The annotation and all corresponding comments will be deleted.';
$string['deletingAnnotation_student'] = "The annotation and all corresponding comments will be deleted.<br>You may delete your own annotations as long as they haven't been commented by other users.";

$string['editAnnotationTitle'] = 'Are you sure?';
$string['editAnnotation'] = 'The annotation will be moved. <br>This might change the context of the question.';

$string['deletedQuestion'] = 'deleted question';
$string['deletedComment'] = 'deleted comment';
$string['hiddenComment'] = 'hidden comment';
$string['deleteComment'] = 'Delete comment';
$string['deleteAndArchiveComment'] = 'Archive and delete comment';
$string['annotationDeleted'] = 'Annotation has been deleted';
$string['commentDeleted'] = 'Comment has been deleted';

$string['delete'] = 'Delete';
$string['edit'] = 'Edit';
$string['deletionForbidden'] = 'Deletion not allowed';
$string['onlyDeleteOwnAnnotations'] = ", because it belongs to another user.";
$string['onlyDeleteOwnComments'] = ", because You are not allowed to delete other users' comments.";
$string['onlyDeleteUncommentedPosts'] = ", because the other users comments would be deleted as well.";

$string['startDiscussion'] = 'Start a discussion';
$string['continueDiscussion'] = 'Add a comment';

$string['missingAnnotation'] = 'The corresponding annotation could not be found!';

$string['error'] = 'Error!';
$string['error:openingPDF'] = 'An error occurred while opening the PDF file.';
$string['error:addAnnotation'] = 'An error has occurred while adding an annotation.';
$string['error:editAnnotation'] = 'An error has occurred while editing an annotation.';
$string['error:deleteAnnotation'] = 'An error has occured while deleting an annotation.';
$string['error:getAnnotation'] = 'An error has occured while getting the annotation.';
$string['error:getAnnotations'] = 'An error has occured while getting all annotations.';
$string['error:addComment'] = 'An error has occured while adding the comment.';
$string['error:getComments'] = 'An error has occured while getting the comments.';
$string['error:renderPage'] = 'An error has occured while rendering the page.';
$string['error:getQuestions'] = 'An error has occured while getting the questions for this page.';
$string['error:getAllQuestions'] = 'An error has occured while getting the questions of this document.';
$string['error:voteComment'] = 'An error has occured while saving the vote.';
$string['error:reportComment'] = 'An error has occured while saving the report.';
$string['error:subscribe'] = 'An error has occured while subscribing to the question.';
$string['error:unsubscribe'] = 'An error has occured while unsubscribing to the question.';
$string['error:openprintview'] = 'An error has occured while trying to open the pdf in Acrobat Reader.';
$string['error:printcomments'] = 'An error has occured while trying to open the comments in a pdf.';
$string['error:editcomment'] = 'An error has occured while trying to edit a comment.';
$string['error:hideComment'] = "An error has occured while trying to hide the comment from participants' view.";
$string['error:redihideCommentsplayComment'] = 'Beim Wiedereinblenden des Kommentars für Teilnehmer ist ein Fehler aufgetreten.';
$string['error:closequestion'] = 'An error has occured while closing/openingthe question.';
$string['error:markcorrectanswer'] = 'An error has occured while marking the answer as correct.';
$string['error:settimespan'] = "An error has occured while trying to set the timespan for new questions.";
$string['error:hide'] = 'An error has occured while hiding the element.';
$string['error:show'] = 'An error has occured while showing the element.';
$string['error:putinrecyclebin'] = 'The item could not be placed in the recycle bin.';
$string['error:markasread'] = 'The item could not be marked as read.';
$string['error:markasunread'] = 'The item could not be marked as unread.';

$string['document'] = 'Document';

$string['unknownuser'] = 'unknown user';
$string['me'] = 'me';


$string['decision'] = 'Make a decision';
$string['decision:overlappingAnnotation'] = 'You clicked an area, in which is more than one annotation. Decide which one you wanted to click.';

$string['eventreport_added'] = 'A comment was reported';

$string['reportaddedhtml'] = '{$a->reportinguser} has reportet a comment with the message: <br /><br /> "{$a->introduction}"<br /><br />
It is <a href="{$a->urltoreport}">available on the web site</a>.';

$string['reportaddedtext'] = '{$a->reportinguser} has reportet a comment with the message:

    "{$a->introduction}"

It is available under: {$a->urltoreport}';

$string['allquestionstitle'] = 'All questions in ';
$string['questionstitle'] = 'Questions on page ';
$string['noquestions'] = 'No questions on this page!';
$string['searchresults'] = 'Search results';
$string['nosearchresults'] = 'No search results found.';

$string['newanswerhtml'] = 'Your subscribed question "{$a->question}" was ansered by {$a->answeruser} with the comment: <br /> <br /> "{$a->content}"<br /><br />
The answer is <a href="{$a->urltoanswer}">here</a> available.';

$string['newanswertext'] = 'Your subscribed question "{$a->question}" was ansered by {$a->answeruser} with the comment:

    "{$a->content}"

The answer is available under: {$a->urltoanswer}';

$string['newquestionhtml'] = 'A new Questions was added by {$a->answeruser} with the content: <br /> <br /> "{$a->content}"<br /><br />
The question is <a href="{$a->urltoanswer}">hier</a> available.';

$string['newquestiontext'] = 'A new Questions was added by {$a->answeruser} with the content:

    "{$a->content}"

The question is available under: {$a->urltoanswer}';

$string['unsubscribe_notification'] = 'To unsubscribe from notification, please click <a href="{$a}">here</a>.';

$string['gotocomment'] = 'Open the comment.';
$string['gotoquestion'] = 'Open the question.';


$string['showmore'] = 'more';
$string['showless'] = 'less';

$string['noquestions_view'] = 'There are no questions in this document at present.';

// Overview page.

$string['recyclebin_overview_studentsintro'] = 'This page displays all hidden answer notifications for questions that you asked or subscribed to in this course.';
$string['recyclebin_overview_managerintro'] = 'This page displays all hidden report notifications for this course.';

$string['openAll'] = 'Show all';
$string['closeAll'] = 'Collapse all';
$string['saveOverviewConfig'] = 'Remember this view';
$string['OverviewConfigSaved'] = 'The current view was saved.';
$string['OverviewConfigCouldNotBeSaved'] = 'The current view could not be saved, because your browser does not support this function.';
$string['successfullyPutInRecycleBin'] = 'The item was put in the recycle bin.';
$string['successfullyRedisplayedReport'] = 'The item was moved to the "Reports" category once more.';
$string['successfullyRedisplayedAnswer'] = 'The item was moved to the "Answers" category once more..';
$string['seeabove'] = '';
$string['day'] = 'day';
$string['days'] = 'days';
$string['week'] = 'week';
$string['weeks'] = 'weeks';
$string['register'] = 'apply';
$string['timewasset'] = 'Your selection has been saved.';
$string['timecouldnotbeset'] = 'An error has occured.';

$string['hideforever'] = 'Hide permanently';
$string['hideanswerforever'] = 'Hide permanently';
$string['displayagain'] = 'Display this report once more';
$string['displayagain'] = 'Display once more';
$string['displayreportagain'] = 'Display report';
$string['deletereport'] = 'Delete this report permanently';

$string['didyouknow'] = 'Did you know?';

$string['reportinfotitle'] = 'Reported comments';
$string['unsolvedquestionstitle'] = 'Unsolved Questions';
$string['interestedpeople'] = 'other people interested in the question';

$string['entity_helptitle'] = 'Help for ';

$string['reportinfotitle_help'] = 'All reports in this course are listed.';
$string['unsolvedquestionstitle_help'] = 'All unsolved questions in this course are listed.';
$string['labelforsettingnewsspan'] = 'Select a period of time';

$string['mypoststitle'] = 'My posts';
$string['mypoststitle_help'] = "Here's a list of all your posts in this course, including anonymous ones but excluding deleted ones.";

$string['hiddenreportstitle'] = 'Recycle bin (reports)';
$string['hiddenreportstitle_help'] = "You can display hidden reports once more or delete them permanently.";

$string['nohiddenanswernotifications'] = "There are no hidden answers in this course at present.";
$string['nohiddenreports'] = "There are no hidden reports in this course at present.";

$string['choice_viewanswers'] = 'Display answers';
$string['choice_viewreports'] = 'Display reports';

$string['chooseyoursettings'] = 'Would you like to be notified about new activities in this module?';
$string['tosettingspage'] = 'got to settings page';

$string['viewAllActivitiesInThisCourse'] = 'Would you like to see all modules with new activities in this course?';
$string['tooverview'] = 'to overview';

// Studentoverview page.

$string['newanswersavailable'] = 'Recently answered';

$string['newanswersavailable_helptitle'] = 'Help for Recently answered';

$string['newanswersavailable_help'] = 'See all answers to questions that you asked or subscribed to in this course.';

$string['min2Chars'] = 'A question or comment with less than two characters is not allowed.';

$string['successfullySubscribed'] = 'Your subscription to the question was registered.';
$string['successfullyUnsubscribedSingular'] = 'Your subscribtion to the question was cancelled and the only answer removed from this table.';
$string['successfullyUnsubscribedTwo'] = 'Your subscribtion was cancelled. Both answers to the question were removed from this table.';
$string['successfullyUnsubscribedPlural'] = 'Your subscribtion was cancelled. All {$a} answers to the question were removed from this table.';
$string['successfullyUnsubscribed'] = 'Your subscribtion was cancelled.';
$string['successfullySubscribed'] = 'Subscribed to question.';
$string['successfullyEdited'] = 'Changes saved';
$string['successfullyHidden'] = 'Participants now see this comment as hidden.';
$string['successfullyRedisplayed'] = 'The comment is visible to participants once more';
$string['unsubscribingDidNotWork'] = 'The subscription could not be cancelled.';

/*********************************** statistics-tab ***********************************/
$string['questions'] = 'questions';
$string['myquestions'] = 'my questions';
$string['answers'] = 'answers';
$string['myanswers'] = 'my answers';
$string['answers_myquestions'] = 'answers to my questions';
$string['reports'] = 'reported comments';
$string['count'] = 'count';
$string['in_document'] = 'in this document';
$string['in_course'] = 'in this course';
$string['by_other_users'] = 'by other users';
$string['own'] = 'own';
$string['average'] = 'average';
$string['average_questions'] = 'average questions';
$string['average_answers'] = 'average answers';
$string['average_help'] = 'Only users who wrote at least one comment are included in the calculation of the average (arithmetic mean)';
$string['total'] = 'total';

$string['reportsendbutton'] = 'Send';

$string['noreports'] = 'There are no reports in this course at present.';
$string['nounreadreports'] = 'There are no unread reports in this course at present.';
$string['noreadreports'] = 'There are no read reports in this course at present.';
$string['noanswers'] = 'There are no answers in this course at present.';
$string['noanswerssubscribed'] = 'There are no answers to subscribed questions in this course at present.';
$string['noquestionsopen_overview'] = 'There are no open questions in this course at present.';
$string['noquestionsclosed_overview'] = 'There are no closed questions in this course at present.';
$string['noquestions_overview'] = 'There are no questions in this course at present.';
$string['nomyposts'] = 'You have posted no question or answer in this course yet.';


$string['colorPicker'] = 'Pick a color';
$string['chart_title'] = 'Questions and answers in the annotators in this course';

$string['noCommentsupported'] = 'This kind of annotation does not support comments.';

$string['enterText'] = 'Enter text';

$string['recievenewquestionnotifications'] = 'Notify about new questions';

$string['deletereport'] = 'Permanently delete this report';

/*********************************** Vote ***********************************/
$string['likeQuestion'] = 'interesting question';
$string['likeAnswer'] = 'helpful';
$string['likeAnswerForbidden'] = 'already marked as helpful';
$string['likeQuestionForbidden'] = 'already marked as helpful';
$string['likeOwnComment'] = 'own comment';
$string['like'] = 'like';
$string['likeForbidden'] = 'You are not allowed to like this comment';
$string['likeCountQuestion'] = 'persons are also interested in this question';
$string['likeCountAnswer'] = 'persons think this answer is helpful';

$string['subscribeQuestion'] = 'Subscribe';
$string['unsubscribeQuestion'] = 'Unsubscribe';
$string['markSolved'] = 'Close question';
$string['markUnsolved'] = 'Reopen question';
$string['questionSolved'] = 'Questions is closed. However, you can still create new comments.';
$string['markCorrect'] = 'Mark as correct';
$string['removeCorrect'] = 'Remove marking as correct';
$string['markhidden'] = 'Hide';
$string['removehidden'] = 'Show';
$string['answerSolved'] = 'This answer was marked as correct by the manager.';
$string['hiddenforparticipants'] = 'Hidden from students';

/************************************** Privacy ***********************************/

$string['privacy:metadata:core_files'] = 'The Pdfannotator stores files which have been uploaded by the user as a basis for annotation and discussion.';

// pdfannotator_annotations table
$string['privacy:metadata:pdfannotator_annotations'] = "Information about the annotations a user made. This includes the type of annotation (e.g. highlight or drawing), its position within a specific file, as well as the time of creation.";
$string['privacy:metadata:pdfannotator_annotations:userid'] = 'The ID of the user who made this annotation.';
$string['privacy:metadata:pdfannotator_annotations:annotationid'] = 'The ID of the annotation that was made. It refers to the data listed above.';

// pdfannotator_comments table
$string['privacy:metadata:pdfannotator_comments'] = "Information about a user's comments. This includes the content and time of creation of the comment, as well as the underlying annotation.";
$string['privacy:metadata:pdfannotator_comments:userid'] = "The ID of the comment's author.";
$string['privacy:metadata:pdfannotator_comments:annotationid'] = 'The ID of the underlying annotation.';
$string['privacy:metadata:pdfannotator_comments:content'] = 'The literal comment.';

// pdfannotator_commentsarchive table
$string['privacy:metadata:pdfannotator_commentsarchive'] = "Comments which were reported and subsequently deleted are archived here. The information stored is the same as for other comments.";

// pdfannotator_reports table
$string['privacy:metadata:pdfannotator_reports'] = "Users can report other users' comments as inappropriate. These reports stored. This includes the ID of the reported comment as well as the author, content and time of the report.";
$string['privacy:metadata:pdfannotator_reports:commentid'] = 'The ID of the reported comment.';
$string['privacy:metadata:pdfannotator_reports:message'] = 'The text content of the report.';
$string['privacy:metadata:pdfannotator_reports:userid'] = 'The author of the report.';

// pdfannotator_subscriptions table
$string['privacy:metadata:pdfannotator_subscriptions'] = "Information about the subscriptions to individual questions/discussions.";
$string['privacy:metadata:pdfannotator_subscriptions:annotationid'] = 'The ID of the question/discussion that was subscribed to.';
$string['privacy:metadata:pdfannotator_subscriptions:userid'] = 'The ID of the user with this subscription.';

// pdfannotator_votes table
$string['privacy:metadata:pdfannotator_votes'] = "Information about questions and comments that were marked as interesting or helpful.";
$string['privacy:metadata:pdfannotator_votes:commentid'] = "The ID of the comment.";
$string['privacy:metadata:pdfannotator_votes:userid'] = "The ID of the user who marked the comment as interesting or helpful. It is saved in order to prevent users from voting for the same comment repeatedly.";

/****************************recent activity************/
$string['bynameondate'] = 'by {$a->name} - {$a->date}';

/**********************index.php**********************/
$string['subscribe'] = 'Subscribe to this Annotations';
$string['unsubscribe'] = 'Unsubscribe from this Annotations';
$string['subscribed'] = 'Subscribed';

$string['studentdrawingforbidden'] = 'This annotator does not support drawings for your user role.';
$string['studenttextboxforbidden'] = 'This annotator does not support textboxes for your user role.';

/********************** printview **********************/

$string['answer'] = "Answer";
$string['printviewtitle'] = "Comments";

$string['lastedited'] = 'last edited';

/* * ****************************************** Time ******************************************** */

$string['ago'] = '{$a} ago';
$string['second'] = 'second';
$string['minute'] = 'minute';
$string['hour'] = 'hour';
$string['day'] = 'day';
$string['month'] = 'month';
$string['year'] = 'year';
$string['seconds'] = 'seconds';
$string['minutes'] = 'minutes';
$string['hours'] = 'hours';
$string['days'] = 'days';
$string['months'] = 'months';
$string['years'] = 'years';
$string['justnow'] = 'just now';
