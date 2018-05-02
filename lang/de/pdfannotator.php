<?php
/**
 * @package   mod_pdfannotator
 * @copyright 2018 RWTH Aachen, Rabea de Groot and Anna Heynkes(see README.md)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * 
 */
$string['pdfannotator:administrateuserinput'] = 'Administrieren der Kommentare';
$string['pdfannotator:recievenewquestionnotifications'] = 'Empfangen von Benachrichtigungen über neue Fragen';
$string['global_setting_anonymous'] = 'Anonymes Posten erlauben?';
$string['global_setting_anonymous_desc'] = 'Mit dieser Einstellung erlauben Sie allen Benutzern das Posten unter anonymem Namen';
$string['modulename'] = 'PDF-Annotation';
$string['modulename_help'] = 'Diese Plugin ermöglicht das private und kollaborative Markieren von PDF Dokumenten. Die Nutzer/innen haben die Möglichkeit bestimmte Stellen in einem PDF hervorzuheben und sich mit anderen Nutzer/innen über markierte
Abschnitte auszutauschen.';
$string['modulename_link'] = 'mod/pdfannotator/view';
$string['modulenameplural'] = 'PDF-Annotation';
$string['pdfannotatorname'] = 'PDF-Annotation Tool';
$string['pluginadministration'] = 'PDF-Annotation Administration';
$string['pluginname'] = 'PDF-Annotation';
$string['setting_alternative_name'] = 'Name';
$string['setting_alternative_name_desc'] = 'Ermöglicht den Namen des zu listenden PDFs zu ändern. Falls leer, wird der Dateiname als Listname genommen';
$string['setting_col_annotating_enabled'] = 'Kollaboratives Markieren auf dem PDF erlauben aktivieren';
$string['setting_anonymous'] = 'Anonymes Posten erlauben?';
$string['setting_fileupload'] = 'Bitte wählen Sie eine PDF-Datei aus.';
$string['setting_usevotes'] = "Abstimmung für Kommentare ermöglichen?";
$string['setting_usevotes_help'] = "Ist diese Option ausgewählt, so kann ein Nutzer jeden Beitrag bis zu einmal 'liken'. Eigene Beiträge sind hiervon ausgenommen. ";
$string['setting_choosetimespanfornews'] = "Wie lange soll ein Kommentar als neu angezeigt werden?";


$string['public'] = 'öffentlich';
$string['anonymous'] = 'anonym';
$string['private'] = 'privat';
$string['sendAnonymous'] = 'anonym posten';
$string['pleaseSelectReportReason'] = '--- Bitte wählen Sie den Grund für Ihre Meldung aus. ---';
$string['reportreason'] = 'Grund der Meldung';
$string['reason'] = 'Erläuterung';
$string['titleforreportcommentform'] = 'Kommentar melden';
$string['subtitleforreportcommentform'] = 'Ihre Nachricht an den Kurs-Manager';
$string['inaccurate'] = 'sachlich falsch';
$string['inappropriate'] = 'unangemessen';
$string['other_reason'] = 'anderer Grund';
$string['location'] = 'PDF';
$string['slotdatetimelabel'] = 'Tag und Zeit';
$string['comment'] = 'Kommentar';
$string['comments'] = 'Kommentare';
$string['author'] = 'Verfasser';
$string['guestscantdoanything'] = 'Gäste können hier nichts tun.';
//$string['titleforoverviewpage'] = 'Neuigkeiten';

$string['newquestions'] = 'Neu gestellte Fragen in diesem Kurs';
$string['read'] = 'Gelesen';
$string['page'] = 'Seite';
$string['location'] = 'Ort';
$string['comment'] = 'Kommentar';
$string['view'] = 'Dokument';
$string['overview'] = 'Übersicht';
$string['statistic'] = 'Statistik';
$string['report'] = 'Meldung';
$string['toreport'] = 'Melden';
$string['reportForbidden'] = 'Melden nicht erlaubt';

$string['newstitle'] = 'Neu gestellte Fragen';
// When displaying your message types in a user's messaging preferences it will use a string from your component's language file called "messageprovider:messagename".
$string['messageprovider:newanswer'] = 'Benachrichtigung, wenn auf eine Ihrer '.$string['modulename'].'-Fragen geantwortet wurde';
$string['messageprovider:newreport'] = 'Benachrichtigung über neu gemeldete '.$string['modulename'].'-Kommentare';
$string['messageprovider:newquestion'] = 'Benachrichtigung über eine neue '.$string['modulename'].'-Frage';
$string['notificationsubject:newreport'] = 'Neue Meldung eines Kommentars in {$a}';
$string['notificationsubject:newanswer'] = 'Neue Antwort auf Ihre Frage in {$a}';
$string['notificationsubject:newquestion'] = 'Neue Frage in {$a}';
$string['reportwassentoff'] = 'Ihre Meldung wurde erfolgreich versandt.';

$string['question'] = 'Meine Frage';
$string['answers'] = 'Antworten';
$string['datetime'] = 'Datum';
$string['answerButton'] = 'Antworten';
$string['cancelButton'] = 'Abbrechen';
$string['yesButton'] = 'Ja';

$string['loading'] = 'Lädt!';
$string['answerButton'] = 'Antworten';

$string['rectangle'] = 'Mit dem Rahmen-Werkzeug wird ein Rahmen gesetzt und ein Kommentar hinzugefügt. Zum Aufspannen des Rahmens kann mit der Maus auf einen beliebigen Punkt auf dem Dokument geklickt und gezogen werden.';
$string['highlight'] = 'Mit dem Textmarker-Werkzeug wird Text markiert und ein Kommentar hinzugefügt.';
$string['strikeout'] = 'Mit dem Streichen-Werkzeug wird Text durchgestrichen und ein Kommentar hinzugefügt.';
$string['text'] = 'Mit dem Text-Werkzeug kann ein Text auf dem PDF hinzugefügt werden.';
$string['drawing'] = 'Mit dem Stift-Werkzeug können beliebige Formen gezeichnet werden';
$string['point'] = 'Mit dem Pin-Werkzeug wird ein Pin gesetzt und ein Kommentar hinzugefügt.';
$string['hideAnnotations'] = 'Annotationen ausblenden';
$string['showAnnotations'] = 'Annotationen einblenden';
$string['fullscreen'] = 'Vollbild-Modus';
$string['fullscreenBack'] = 'Vollbild-Modus beenden';

$string['currentPage'] = 'aktulle Seitenzahl';
$string['sumPages'] = 'Anzahl der Seiten';

$string['addAComment'] = 'Kommentar hinzufügen';
$string['createAnnotation'] = 'Annotation erstellen';

$string['activities'] = 'Aktivitäten';

$string['editNotAllowed'] = 'Verschieben nicht erlaubt!';

/***************************** Delete a comment *********************************/

// confirmation prompts
$string['deletingCommentTitle'] = 'Wirklich löschen?';
$string['deletingComment_manager'] = 'Der Kommentar wird endgültig gelöscht und - falls er bereits beantwortet wurde - in der Diskussion als gelöscht angezeigt.';
$string['deletingComment_student'] = 'Der Kommentare wird endgültig gelöscht.';
$string['deletingQuestion_student'] = 'Der Kommentar und die dazugehörige Annotation werden endgültig gelöscht.';
$string['deletingQuestion_manager'] = 'Die Annotation wird mit allen zugehörigen Kommentaren gelöscht.';

// sucess or failure notifications
$string['commentDeleted'] = 'Kommentar wurde gelöscht';

/****************************************************************/

$string['deletingAnnotation_manager'] = 'Die Annotation wird mit allen zugehörigen Kommentaren gelöscht.';
$string['deletingAnnotation_student'] = 'Hinweis: Eigene Annotationen können gelöscht werden, solange sie noch nicht von anderen Nutzern kommentiert wurden.';

$string['startDiscussion'] = 'Beginnen Sie eine Diskussion.';
$string['continueDiscussion'] = 'Fügen Sie einen Kommentar hinzu.';


$string['missingAnnotation'] = 'Die zugehörige Annotation konnte nicht gefunden werden.'; 

$string['error'] = 'Fehler!';
$string['error:openingPDF'] = 'Beim Öffnen der PDF-Datei ist ein Fehler aufgetreten';
$string['error:addAnnotation'] = 'Beim Hinzufügen einer Annotation ist ein Fehler aufgetreten.';
$string['error:editAnnotation'] = 'Beim Ändern einer Annotation ist ein Fehler aufgetreten.';
$string['error:deleteAnnotation'] = 'Beim Löschen der Annotation ist ein Fehler aufgetreten.';
$string['error:getAnnotation'] = 'Beim Auslesen der Annotation ist ein Fehler aufgetreten.';
$string['error:getAnnotations'] = 'Beim Aulesen der Annotationen ist ein Fehler aufgetreten.';
$string['error:addComment'] = 'Beim Hinzufügen des Kommentars ist ein Fehler aufgetreten.';
$string['error:getComments'] = 'Beim Auslesen der Kommentare ist ein Fehler aufgetreten.';
$string['error:renderPage'] = 'Beim Anzeigen der Seite ist ein Fehler aufgetreten.';
$string['error:getQuestions'] = 'Beim Auslesen der Fragen ist ein Fehler aufgetreten.';
$string['error:voteComment'] = 'Beim Speichern des Votes ist ein Fehler aufgetreten.';

$string['document'] = 'Dokument';

$string['pdfannotator:addinstance'] = 'Einen neuen Pdf-Annotator anlegen';
$string['pdfannotator:submit'] = 'Unangemessene Kommentare dem Kursmanager melden';
$string['pdfannotator:view'] = 'Pdf-Annotation ansehen';

$string['unknownuser'] = 'unbekannter Nutzer';
$string['deletedComment'] = 'gelöschter Kommentar';
$string['delete'] = 'Löschen';

$string['deletionForbidden'] = 'Löschen nicht erlaubt';
$string['onlyDeleteOwnAnnotations'] = ', da die Annotation von einem anderen Nutzer stammt';
$string['onlyDeleteUncommentedPosts'] = ', da Ihre Annotation bzw. Ihr Kommentar bereits kommentiert wurde';
$string['onlyDeleteOwnComments'] = ', da Kommentare anderer Nutzer können nicht gelöscht werden.';

$string['me'] = 'ich';

$string['decision'] = 'Wählen Sie aus:';
$string['decision:overlappingAnnotation'] = 'Sie haben einen Bereich angeklickt, in dem sich mehrere Annotationen befinden. Bitte wählen Sie aus, welche Sie anklicken möchten.';

$string['eventreport_added'] = 'Ein Kommentar wurde gemeldet.';

$string['reportaddedhtml'] = '{$a->reportinguser} hat einen Kommentar gemeldet mit der Nachricht: <br /> <br /> "{$a->introduction}"<br /><br />
Die Meldung ist <a href="{$a->urltoreport}">hier</a> verfügbar.';

$string['reportaddedtext'] = '{$a->reportinguser} hat einen Kommentar gemeldet mit der Nachricht: 
    
    "{$a->introduction}"
    
Die Meldung ist verfügbar unter: {$a->urltoreport}';

$string['questionstitle'] = 'Fragen auf dieser Seite';
$string['noquestions'] = 'Keine Fragen auf dieser Seite!';

$string['newanswerhtml'] = 'Ihre Frage "{$a->question}" wurde von {$a->answeruser} beantwortet mit dem Kommentar: <br /> <br /> "{$a->content}"<br /><br />
Der Kommentar ist <a href="{$a->urltoanswer}">hier</a> einzusehen.';

$string['newanswertext'] = 'Ihre Frage "{$a->question}" wurde von {$a->answeruser} beantwortet mit dem Kommentar:
    
    "{$a->content}"
    
Der Kommentar ist verfügbar unter: {$a->urltoanswer}';

$string['newquestionhtml'] = 'Es wurde eine neue Frage von {$a->answeruser} eingestellt mit dem Inhalt: <br /> <br /> "{$a->content}"<br /><br />
Die Frage ist <a href="{$a->urltoanswer}">hier</a> einzusehen.';

$string['newquestiontext'] = 'Es wurde eine neue Frage von {$a->answeruser} eingestellt mit dem Inhalt:
    
    "{$a->content}"
    
Die Frage ist verfügbar unter: {$a->urltoanswer}';

$string['unsubscribe_notification'] = 'Um keine Benachrichtigung mehr zu bekommen, klicken Sie bitte <a href="{$a}">hier</a>.';

$string['gotocomment'] = 'zum Kommentar';
$string['gotoquestion'] = 'zur Frage';


$string['showmore'] = 'mehr';
$string['showless'] = 'weniger';

// *********************************** teacheroverview page ***********************************

$string['hideforever'] = 'Meldung ausblenden';
$string['displayagain'] = 'Eintrag wieder einblenden';
$string['deletereport'] = 'Meldung endgültig löschen';

$string['didyouknow'] = 'Tipp';

$string['chooseyoursettings'] = 'Möchten Sie in Zukunft über neue Aktivitäten in diesem Modul benachrichtigt werden?';

$string['reportinfotitle'] = 'Gemeldete Kommentare';
$string['newquestionstitle'] = 'Neue Fragen';

$string['entity_helptitle'] = 'Hilfe für ';

$string['reportinfotitle_help'] = 'Hier finden Sie alle Meldungen in diesem Kurs. Der gemeldete Kommentar ist jeweils fettgedruckt. Unter ihm steht die Meldung.';
$string['newquestionstitle_help1'] = 'Hier finden Sie alle Fragen, die während der letzten ';
$string['newquestionstitle_help2'] = ' Tage in diesem Kurs gestellt wurden.';

$string['mypoststitle'] = 'Meine Beiträge';
$string['mypoststitle_help'] = "Hier sehen Sie alle Beiträge, die Sie in einem Annotator dieses Kurses verfasst haben. Auch anonym verfasste Beiträge werden Ihnen hier angezeigt, jedoch keine gelöschten.";

$string['hiddenentriestitle'] = 'Einträge verwalten';
$string['hiddenentriestitle_help'] = "Hier können Sie ausgeblendete Einträge wieder einblenden oder endgültig löschen."; // muss auch im Englischen angepasst werden
$string['nohiddenentries_student'] = "Sie haben in diesem Kurs zurzeit keine Antwort ausgeblendet.";
$string['nohiddenentries_manager'] = "Sie haben in diesem Kurs zurzeit keine Meldung ausgeblendet.";

$string['noreports'] = 'Derzeit gibt es keine Meldungen in diesem Kurs.';
$string['noanswers'] = 'Derzeit gibt es keine neuen Antworten in diesem Kurs.';
$string['noquestions_overview'] = 'Derzeit gibt es keine neuen Fragen in diesem Kurs.';
$string['nomyposts'] = 'Sie haben in diesem Kurs noch keine Beiträge verfasst.';


$string['tosettingspage'] = 'zu den Einstellungen';

// *********************************** studentoverview page (additional strings apart from those above) ***********************************

$string['newanswersavailable'] = 'Antworten';
$string['newanswersavailable_helptitle'] = 'Hilfe für Antworten';
$string['newanswersavailable_help'] = 'Hier finden Sie alle Antworten, die auf Ihre Fragen in diesem Kurs gegeben wurden. Die Frage ist jeweils fettgedruckt. Darunter steht die Antwort.';

$string['min2Chars'] = 'Eine Frage oder Kommentar mit weniger als zwei Zeichen ist nicht erlaubt.';

// *********************************** statistics-tab ***********************************

$string['questions'] = 'Fragen';
$string['myquestions'] = 'eigene Fragen';
$string['answers'] = 'Antworten';
$string['myanswers'] = 'eigene Antworten';
$string['answers_myquestions'] = 'Antworten auf meine Fragen';
$string['reports'] = 'gemeldete Kommentare';
$string['count']= 'Anzahl';
$string['in_document']= 'in diesem Dokument';
$string['in_course']= 'in diesem Kurs';
$string['by_other_users']='von anderen Usern';
$string['own']='eigene';
$string['average']='Durchschnitt';
$string['average_help']='In die Berechnung des Durchschnitts (arithmetisches Mittel) werden nur User mit einbezogen, die mind. einen Kommentar verfasst haben';
$string['total']='Gesamt';


$string['reportsendbutton'] = 'Meldung versenden';

$string['colorPicker'] = 'Farbauswahl';
$string['chart_title']='Fragen und Antworten in den Annotatoren im Kurs';

$string['noCommentsupported'] = 'Dieser Annotationstyp unterstützt keine Kommentare';

$string['enterText'] = 'Text eingeben';

$string['recievenewquestionnotifications'] = 'Benachrichtigung, wenn eine neue Frage gestellt wurde';

$string['deletereport'] = 'Meldung endgültig löschen';

// *********************************** vote ***********************************
$string['likeQuestion'] = 'interessiert mich auch';
$string['likeAnswer'] = 'finde ich hilfreich';
$string['likeAnswerForbidden'] = 'bereits als hilfreich markiert';
$string['likeQuestionForbidden'] = 'bereits als interessant markiert';
$string['likeOwnComment'] = 'eigener Kommentar';
$string['like'] = 'Like';
$string['likeForbidden'] = 'Liken nicht erlaubt';
$string['likeCountQuestion'] = 'Personen interessiert diese Frage auch';
$string['likeCountAnswer'] = 'Personen finden diese Antwort hilfreich';
