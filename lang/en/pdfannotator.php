<?php
/**
 * @package   mod_pdfannotator
 * @copyright 2018 RWTH Aachen, Rabea de Groot and Anna Heynkes(see README.md)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * 
 */
$string['pdfannotator:administrateuserinput'] = 'Administrate comments';
$string['pdfannotator:recievenewquestionnotifications'] = 'Recieve notifications about new questions';
$string['global_setting_anonymous'] = 'Allow anonymous posting?';
$string['global_setting_anonymous_desc'] = 'With this option you allow your user to post comments anonymously. This option activates anonymous posting globally';
$string['modulename'] = 'PDF Annotation';
$string['modulename_help'] = 'This Tool enables private and collaborative markup on PDF Documents. The users are able to annotate specific parts of an PDF and discuss them with other users.';
$string['modulename_link'] = 'mod/pdfannotator/view';
$string['modulenameplural'] = 'PDF Annotations';
$string['pdfannotatorname'] = 'PDF Annotation Tool';
$string['pluginadministration'] = 'PDF Annotation administration';
$string['pluginname'] = 'PDF Annotation';
$string['setting_alternative_name'] = 'Name';
$string['setting_alternative_name_desc'] = 'Provide an alternative name for the PDF. If empty, the name of the pdf will be taken as represantative name';
$string['setting_anonymous'] = 'Allow anonymous posting?';
$string['setting_col_annotating_enabled'] = 'Enable collaborative annotation on PDF';
$string['setting_fileupload'] = 'Select a pdf-file';
$string['setting_usevotes'] = "Allow users to like comments.";
$string['setting_usevotes_help'] = "With this option enabled, users can like / vote for posts other than their own.";
$string['setting_choosetimespanfornews'] = "For how long should a comment be marked as new?";

$string['legacyfiles'] = 'Migration of old course file';
$string['legacyfilesactive'] = 'Active';
$string['legacyfilesdone'] = 'Finished';
$string['clicktoopen2'] = 'Click {$a} link to view the file.';
$string['dnduploadpdfannotator'] = 'Create file for PDF Annotation';
$string['pdfannotatorcontent'] = 'Files and subfolders';
$string['pdfannotator:view'] = 'View PDF Annotation';
$string['pdfannotator:addinstance'] = 'add instance';
$string['filenotfound'] = 'File not found, sorry.';
$string['public'] = 'public';
$string['anonymous'] = 'anonymous';
$string['private'] = 'private';
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
$string['statistic'] = 'Statistics';
$string['report'] = 'Report';
$string['toreport'] = 'Report';
$string['reportForbidden'] = 'Not allowed to report';

$string['newstitle'] = 'Just asked';
// When displaying your message types in a user's messaging preferences it will use a string from your component's language file called "messageprovider:messagename".
$string['messageprovider:newanswer'] = 'Notification of an answer to one of your '.$string['modulename'].' questions';
$string['messageprovider:newreport'] = 'Notification whenever a comment is reported in '.$string['modulename'];
$string['messageprovider:newquestion'] = 'Notification of a new '.$string['modulename'].' question';
$string['notificationsubject:newreport'] = 'A comment was reported in {$a}';
$string['notificationsubject:newanswer'] = 'New answer to your question in {$a}';
$string['notificationsubject:newquestion'] = 'New question in {$a}';
$string['reportwassentoff'] = 'The comment has been reported.';

$string['question'] = 'My question';
$string['answers'] = 'Answers';
$string['datetime'] = 'Date';

$string['loading'] = 'Loading!';
$string['answerButton'] = 'Answer';
$string['cancelButton'] = 'Cancel';
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

$string['currentPage'] = 'current page number';
$string['sumPages'] = 'Number of pages';

$string['addAComment'] = 'Add a comment';
$string['createAnnotation'] = 'Create Annotation';

$string['activities'] = 'Activities';

$string['editNotAllowed'] = 'Panning not allowed!';


/***************************** Delete a comment *********************************/

// confirmation prompts
$string['deletingCommentTitle'] = 'Are you sure?';
$string['deletingComment_manager'] = 'The comment will be deleted. It will be displayed as deleted unless it is the last comment in its thread.';
$string['deletingComment_student'] = 'The comment will be deleted.';
$string['deletingQuestion_student'] = 'The comment and its corresponding annotation will be deleted.';
$string['deletingQuestion_manager'] = 'The annotation and all of its corresponding comments will be deleted.';

$string['deletingAnnotation_manager'] = 'The annotation and all corresponding comments will be deleted.';
$string['deletingAnnotation_student'] = "You may delete your own annotations as long as they haven't been commented by other users.";

$string['deletedComment'] = 'deleted comment';
$string['commentDeleted'] = 'Comment has been deleted';

$string['delete'] = 'Delete!!!!!';
$string['deletionForbidden'] = 'Deletion not allowed';
$string['onlyDeleteOwnAnnotations'] = ", because it belongs to another user.";
$string['onlyDeleteOwnComments'] = ", because You are not allowed to delete other users' comments.";
$string['onlyDeleteUncommentedPosts'] = ", because it has already been commented by other users.";

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
$string['error:getQuestions'] = 'An error has occured while getting the questions.';
$string['error:voteComment'] = 'An error has occured while saving the vote.';

$string['document'] = 'Document';

$string['pdfannotator:submit'] = 'Report inappropriate comments to the course manager';

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

$string['questionstitle'] = 'Questions on this page';
$string['noquestions'] = 'No questions on this page!';

$string['newanswerhtml'] = 'Your question "{$a->question}" was ansered by {$a->answeruser} with the comment: <br /> <br /> "{$a->content}"<br /><br />
The answer is <a href="{$a->urltoanswer}">here</a> available.';

$string['newanswertext'] = 'Your question "{$a->question}" was ansered by {$a->answeruser} with the comment:
    
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

// Teacher overview page

$string['hideforever'] = 'Hide permanently';
$string['displayagain'] = 'Display this report once more';
$string['deletereport'] = 'Delete this report permanently';

$string['didyouknow'] = 'Did you know?';

$string['chooseyoursettings'] = 'Would you like to be notified about new activities in this module?';

$string['reportinfotitle'] = 'Reported comments';
$string['newquestionstitle'] = 'New Questions';

$string['entity_helptitle'] = 'Help for ';

$string['reportinfotitle_help'] = 'All reports in this course are listed.';
$string['newquestionstitle_help1'] = "Here's a list of all questions that were asked in this course during the past ";
$string['newquestionstitle_help2'] = " days.";

$string['mypoststitle'] = 'My posts';
$string['mypoststitle_help'] = "Here's a list of all your posts in this course, including anonymous ones but excluding deleted ones.";

$string['hiddenentriestitle'] = 'Administration';
$string['hiddenentriestitle_help'] = "Here you can display hidden entries once more or delete them permanently.";

$string['nohiddenentries_student'] = "There are no hidden answers in this course at present.";
$string['nohiddenentries_manager'] = "There are no hidden reports in this course at present.";

$string['tosettingspage'] = 'got to settings page';

// studentoverview page

$string['newanswersavailable'] = 'Recently answered';

$string['newanswersavailable_helptitle'] = 'Help for Recently answered';

$string['newanswersavailable_help'] = 'See all answers to your questions in this course.';

$string['min2Chars'] = 'A question or comment with less than two characters is not allowed.';

/*********************************** statistics-tab ***********************************/
$string['questions'] = 'questions';
$string['myquestions'] = 'my questions';
$string['answers'] = 'answers';
$string['myanswers'] = 'my answers';
$string['answers_myquestions'] = 'answers to my questions';
$string['reports'] = 'reported comments';
$string['count']= 'count';
$string['in_document']= 'in this document';
$string['in_course']= 'in this course';
$string['by_other_users']='by other users';
$string['own']='own';
$string['average']='Average';
$string['average_help']='Only users who wrote at least one comment are included in the calculation of the average (arithmetic mean)';
$string['total']='total';

$string['reportsendbutton'] = 'Send';

$string['noreports'] = 'There are no reports in this course at present.';
$string['noanswers'] = 'There are no new answers in this course at present.';
$string['noquestions_overview'] = 'There are no new questions in this course at present.';
$string['nomyposts'] = 'You have posted no question or answer in this course yet.';


$string['colorPicker'] = 'Pick a color';
$string['chart_title']='Questions and answers in the annotators in this course';

$string['noCommentsupported'] = 'This kind of annotation does not support comments';

$string['enterText'] = 'Enter text';

$string['recievenewquestionnotifications'] = 'Notify about new questions';

$string['deletereport'] = 'Permanently delete this report';

/*********************************** vote ***********************************/
$string['likeQuestion'] = 'interesting question';
$string['likeAnswer'] = 'helpful';
$string['likeAnswerForbidden'] = 'already marked as helpful';
$string['likeQuestionForbidden'] = 'already marked as helpful';
$string['likeOwnComment'] = 'own comment';
$string['like'] = 'like';
$string['likeForbidden'] = 'You are not allowed to like this comment';
$string['likeCountQuestion'] = 'persons are also interested in this question';
$string['likeCountAnswer'] = 'persons think this answer is helpful';
