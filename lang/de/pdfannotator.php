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

$string['pdfannotator'] = 'Ort';

$string['strftimedatetime'] = '%d. %b %Y, %H:%M';

/* ******************************* capabilities *******************************/
$string['pdfannotator:view'] = 'Pdf-Annotation ansehen';
$string['pdfannotator:addinstance'] = 'Einen neuen Pdf-Annotator anlegen';
$string['pdfannotator:administrateuserinput'] = 'Administrieren der Kommentare';

$string['pdfannotator:create'] = 'Annotationen und Kommentare erstellen';
$string['pdfannotator:deleteown'] = 'Eigene Annotationen und Kommentare löschen';
$string['pdfannotator:deleteany'] = 'Alle Annotationen und Kommentare löschen (sowohl eigene als auch fremde)';
$string['pdfannotator:edit'] = 'Eigene Annotationen und Kommentare editieren';
$string['pdfannotator:editanypost'] = 'Alle Annotationen und Kommentare editieren';
$string['pdfannotator:report'] = 'Unangemessene Kommentare dem Kursmanager melden';
$string['pdfannotator:vote'] = "Interessante Fragen / hilfreiche Kommentare \'liken\'";
$string['pdfannotator:subscribe'] = 'Fragen abonnieren';
$string['pdfannotator:closequestion'] = 'Eigene Fragen schließen';
$string['pdfannotator:closeanyquestion'] = 'Alle Fragen schließen';
$string['pdfannotator:markcorrectanswer'] = 'Antworten als richtig markieren';
$string['pdfannotator:usetextbox'] = 'Textbox verwenden (auch wenn es in den Einstellungen des Annotators nicht erlaubt wurde)';
$string['pdfannotator:usedrawing'] = 'Drawing verwenden (auch wenn es in den Einstellungen des Annotators nicht erlaubt wurde)';
$string['pdfannotator:printdocument'] = 'Dokument herunterladen';
$string['pdfannotator:printcomments'] = 'Kommentare herunterladen';

$string['pdfannotator:hidecomments'] = 'Kommentare für Teilnehmer/innen verbergen';
$string['pdfannotator:seehiddencomments'] = 'Verborgene Kommentare sehen';
        
$string['pdfannotator:viewreports'] = 'Kommentar-Meldungen sehen (Übersichtsseite)';
$string['pdfannotator:viewanswers'] = 'Antworten auf abonnierte Fragen sehen (Übersichtsseite)';
$string['pdfannotator:viewquestions'] = 'Offene Fragen sehen (Übersichtsseite)';
$string['pdfannotator:viewposts'] = 'Eigene Beiträge sehen (Übersichtsseite)';

$string['pdfannotator:viewstatistics'] = 'Statistikseite sehen';
$string['pdfannotator:viewteacherstatistics'] = 'Zusätzliche Informationen auf der Statistikseite sehen';

$string['pdfannotator:recievenewquestionnotifications'] = 'Empfangen von Benachrichtigungen über neue Fragen';

/* ******************************* settings in mod form *******************************/

$string['global_setting_anonymous'] = 'Anonymes Posten erlauben?';
$string['global_setting_anonymous_desc'] = 'Mit dieser Einstellung erlauben Sie allen Benutzern das Posten unter anonymem Namen';
$string['global_setting_usevotes'] = '"Liken" von Beiträgen erlauben?';
$string['global_setting_usevotes_desc'] = 'Mit dieser Einstellung kann ein Nutzer jeden Beitrag bis zu einmal "liken". Eigene Beiträge sind hiervon ausgenommen.';
$string['global_setting_use_studentdrawing'] = 'Teilnehmer/innen Freihandzeichung erlauben?';
$string['global_setting_use_studentdrawing_desc'] = 'Bitte beachten Sie, dass Freihandzeichnungen ohne Verfasser angezeigt werden und weder kommentiert noch gemeldet wedern können.';
$string['global_setting_use_studenttextbox'] = 'Teilnehmer/innen Textbox erlauben?';
$string['global_setting_use_studenttextbox_desc'] = 'Bitte beachten Sie, dass mit der Textbox erstellte Annotationen ohne Verfasser angezeigt werden und weder kommentiert noch gemeldet wedern können.';
$string['global_setting_useprint'] = 'Speichern/Drucken erlauben?';
$string['global_setting_useprint_desc'] = 'Sollen Teilnehmer/innen das PDF-Dokument und alle Kommentare herunterladen dürfen?';
$string['global_setting_useprint_document'] = 'Speichern/Drucken des Dokuments erlauben?';
$string['global_setting_useprint_document_desc'] = 'Sollen Teilnehmer/innen das PDF-Dokument herunterladen dürfen?';
$string['global_setting_useprint_comments'] = 'Speichern/Drucken der Kommentare erlauben?';
$string['global_setting_useprint_comments_desc'] = 'Sollen Teilnehmer/innen die Annotationen und Kommentaren herunterladen dürfen?';

$string['modulename'] = 'PDF-Annotation';
$string['modulename_help'] = 'Diese Plugin ermöglicht das kollaborative Markieren von PDF Dokumenten. Die Nutzer/innen haben die Möglichkeit bestimmte Stellen in einem PDF hervorzuheben und sich mit anderen Nutzer/innen über markierte
Abschnitte auszutauschen.';
$string['modulename_link'] = 'mod/pdfannotator/view';
$string['modulenameplural'] = 'PDF-Annotation';
$string['pdfannotatorname'] = 'PDF-Annotation Tool';
$string['pluginadministration'] = 'PDF-Annotation Administration';
$string['pluginname'] = 'PDF-Annotation';
$string['setting_alternative_name'] = 'Name';
$string['setting_alternative_name_help'] = "Die ersten 20 Zeichen des Dokumentnamens werden in der Navigation des Annotators angezeigt. Der Rest - falls vorhanden - wird mit ... abekürzt.";
$string['setting_alternative_name_desc'] = 'Ermöglicht den Namen des zu listenden PDFs zu ändern. Falls leer, wird der Dateiname als Listname genommen';
$string['setting_col_annotating_enabled'] = 'Kollaboratives Markieren auf dem PDF erlauben aktivieren';
$string['setting_anonymous'] = 'Anonymes Posten erlauben?';
$string['setting_fileupload'] = 'Bitte wählen Sie eine PDF-Datei aus.';
$string['setting_fileupload_help'] = "Sie können die ausgewählte Datei nur solange ändern, bis Sie den Annotator mit Klick auf 'Speichern' erstellt haben.";
$string['setting_usevotes'] = "Votes/Likes";
$string['setting_usevotes_help'] = "Ist diese Option ausgewählt, so kann ein Nutzer jeden Beitrag bis zu einmal 'liken'. Eigene Beiträge sind hiervon ausgenommen. ";
$string['setting_use_studenttextbox'] = "Textbox";
$string['setting_use_studenttextbox_help'] = "Bitte beachten Sie, dass mit der Textbox erstellte Annotationen immer mit Verfasser und Erstellungsdatum angezeigt werden. Diese Annotationen können weder kommentiert noch gemeldet wedern.";
$string['setting_use_studentdrawing'] = "Freihandzeichnung";
$string['setting_use_studentdrawing_help'] = "Bitte beachten Sie, dass Freihandzeichnungen immer mit Verfasser und Erstellungsdatum angezeigt werden. Diese Annotationen können weder kommentiert noch gemeldet wedern.";

$string['setting_useprint'] = "Speichern/Drucken";
$string['setting_useprint_help'] = "Sollen Teilnehmer/innen das PDF-Dokument ohne Annotationen und Kommentare herunterladen dürfen?";
$string['setting_useprint_document'] = 'Dokument Speichern/Drucken';
$string['setting_useprint_document_help'] = 'Sollen Teilnehmer/innen das PDF-Dokument herunterladen dürfen?';
$string['setting_useprint_comments'] = 'Kommentare Speichern/Drucken';
$string['setting_useprint_comments_help'] = 'Sollen Teilnehmer/innen die Annotationen und Kommentaren herunterladen dürfen?';

$string['setting_choosetimespanfornews'] = "Wie lange soll ein Kommentar als neu angezeigt werden?";


$string['usevotes'] = "Abstimmung für Kommentare ermöglichen?";
$string['use_studenttextbox'] = "Textbox für Teilnehmer/innen freigeben?";
$string['use_studentdrawing'] = "Freihandzeichnung für Teilnehmer/innen freigeben?";
$string['useprint'] = "PDF für Teilnehmer/innen freigeben?";
$string['useprint_document'] = "PDF für Teilnehmer/innen freigeben?";
$string['useprint_comments'] = "Kommentare für Teilnehmer/innen freigeben?";
$string['allquestionsimgtitle'] = "alle Fragen in diesem Dokument";
$string['questionsimgtitle'] = "alle Fragen auf dieser Seite";
$string['search'] = "Suchen";

$string['public'] = 'öffentlich';
$string['anonymous'] = 'Anonym';
$string['sendAnonymous'] = 'anonym posten';
$string['pleaseSelectReportReason'] = '--- Bitte wählen Sie den Grund für Ihre Meldung aus. ---';
$string['reportreason'] = 'Grund der Meldung';
$string['reason'] = 'Grund der Meldung';
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

// When displaying your message types in a user's messaging preferences it will use a string from your component's language file called "messageprovider:messagename".
$string['messageprovider:newanswer'] = 'Mitteilung bei neuer Antwort auf eine abonnierte Frage';
$string['messageprovider:newreport'] = 'Mitteilung, wenn ein Kommentar gemeldet wurde';
$string['messageprovider:newquestion'] = 'Mitteilung, wenn eine neue Frage gestellt wurde';
$string['notificationsubject:newreport'] = 'Neue Meldung eines Kommentars in {$a}';
$string['notificationsubject:newanswer'] = 'Neue Antwort auf von Ihnen abonnierte Frage in {$a}';
$string['notificationsubject:newquestion'] = 'Neue Frage in {$a}';
$string['reportwassentoff'] = 'Ihre Meldung wurde erfolgreich versandt.';

$string['myquestion'] = 'Frage'; // 'zugehörige Frage' / 'Frage' / 'Abonnement'
$string['mypost'] = 'Mein Beitrag';
$string['question'] = 'Frage';
$string['askedby'] = 'Gestellt von / am';
$string['answeredby'] = 'von / am';
$string['by'] = 'von, ';
$string['on'] = 'am';
$string['votes'] = 'Likes';
$string['answers'] = 'Antworten';
$string['datetime'] = 'Datum';
$string['answerButton'] = 'Antworten';
$string['cancelButton'] = 'Abbrechen';
$string['yesButton'] = 'Ja';
$string['post'] = 'Beitrag';

$string['loading'] = 'Lädt!';
$string['answerButton'] = 'Antworten';
$string['editButton'] = 'speichern';
$string['printButton'] = 'Download';

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
$string['print'] = 'Dokument herunterladen';
$string['printwithannotations'] = 'Kommentare herunterladen';
$string['emptypdf'] = 'Dieser Annotator enthält zurzeit keine Kommentare.';

$string['zoom'] = 'zoomen';
$string['zoomout'] = 'verkleinern';
$string['zoomin'] = 'vergrößern';

$string['currentPage'] = 'aktuelle Seitenzahl';
$string['sumPages'] = 'Anzahl der Seiten';
$string['nextPage'] = 'Nächste Seite';
$string['prevPage'] = 'Vorherige Seite';

$string['addAComment'] = 'Kommentar hinzufügen';
$string['createAnnotation'] = 'Annotation erstellen';
$string['editedComment'] = 'zuletzt bearbeitet ';// 'zuletzt bearbeitet am ';
$string['modifiedby'] = ' von ';

$string['activities'] = 'Aktivitäten';

$string['editNotAllowed'] = 'Verschieben nicht erlaubt!';

/* * *************************** Delete a comment ******************************** */

// Confirmation prompts.
$string['deletingCommentTitle'] = 'Wirklich löschen?';

$string['printwhat'] = 'Was möchten Sie öffnen?';
$string['printwhatTitle'] = 'PDF im Acrobat Reader öffnen';
$string['pdfButton'] = 'Dokument';
$string['annotationsButton'] = 'Kommentare';
$string['deletingComment'] = 'Der Kommentar wird endgültig gelöscht und - falls er bereits beantwortet wurde - in der Diskussion als gelöscht angezeigt.';
$string['deletingQuestion_student'] = 'Die Frage wird endgültig gelöscht.<br>Wenn sie noch nicht beantwortet wurde, wird die Annotation gelöscht, andernfalls wird die Frage in der Diskussion als gelöscht angezeigt';
$string['deletingQuestion_manager'] = 'Der Frage wird endgültig gelöscht.<br>Tipp: Wenn Sie auch alle Antworten löschen möchten, klicken Sie auf das Löschkreuz der Annotation im Dokument.';

$string['editAnnotationTitle'] = 'Wirklich verschieben?';
$string['editAnnotation'] = 'Die Annotation wird verschoben. <br>Dadurch könnte der Kontext der Frage verändert werden.';

// Sucess or failure notifications.
$string['annotationDeleted'] = 'Annotation wurde gelöscht';
$string['commentDeleted'] = 'Kommentar wurde gelöscht';

/* * ************************************************************* */

$string['deletingAnnotation_manager'] = 'Die Annotation wird mit allen zugehörigen Kommentaren gelöscht.';
$string['deletingAnnotation_student'] = 'Die Annotation wird mit allen zugehörigen Kommentaren gelöscht.<br>Hinweis: Eigene Annotationen können nur gelöscht werden, solange sie noch nicht von anderen Nutzern kommentiert wurden.';

$string['startDiscussion'] = 'Beginnen Sie eine Diskussion.';
$string['continueDiscussion'] = 'Fügen Sie einen Kommentar hinzu.';


$string['missingAnnotation'] = 'Die zugehörige Annotation konnte nicht gefunden werden.';

$string['error'] = 'Fehler!';
$string['error:openingPDF'] = 'Beim Öffnen der PDF-Datei ist ein Fehler aufgetreten';
$string['error:addAnnotation'] = 'Beim Hinzufügen einer Annotation ist ein Fehler aufgetreten.';
$string['error:editAnnotation'] = 'Beim Ändern einer Annotation ist ein Fehler aufgetreten.';
$string['error:deleteAnnotation'] = 'Beim Löschen der Annotation ist ein Fehler aufgetreten.';
$string['error:getAnnotation'] = 'Beim Auslesen der Annotation ist ein Fehler aufgetreten.';
$string['error:getAnnotations'] = 'Beim Auslesen der Annotationen ist ein Fehler aufgetreten.';
$string['error:addComment'] = 'Beim Hinzufügen des Kommentars ist ein Fehler aufgetreten.';
$string['error:getComments'] = 'Beim Auslesen der Kommentare ist ein Fehler aufgetreten.';
$string['error:renderPage'] = 'Beim Anzeigen der Seite ist ein Fehler aufgetreten.';
$string['error:getQuestions'] = 'Beim Auslesen der Fragen dieser Seite ist ein Fehler aufgetreten.';
$string['error:getAllQuestions'] = 'Beim Auslesen der Fragen dieses Dokumentes ist ein Fehler aufgetreten.';
$string['error:voteComment'] = 'Beim Speichern des Votes ist ein Fehler aufgetreten.';
$string['error:reportComment'] = 'Beim Speichern Ihrer Meldung ist ein Fehler aufgetreten.';
$string['error:subscribe'] = 'Beim Abonnieren der Frage ist ein Fehler aufgetreten.';
$string['error:unsubscribe'] = 'Beim Kündigen des Abonnements ist ein Fehler aufgetreten.';
$string['error:openprintview'] = 'Beim Öffnen des PDFs ist ein Fehler aufgetreten.';
$string['error:printcomments'] = 'Beim Öffnen der Kommentare ist ein Fehler aufgetreten.';
$string['error:editcomment'] = 'Beim Editieren des Kommentars ist ein Fehler aufgetreten.';
$string['error:hideComment'] = 'Beim Ausblenden des Kommentars ist ein Fehler aufgetreten.';
$string['error:redisplayComment'] = 'Beim Wiedereinblenden des Kommentars ist ein Fehler aufgetreten.';
$string['error:closequestion'] = 'Beim Schließen/Öffnen der Frage ist ein Fehler aufgetreten.';
$string['error:markcorrectanswer'] = 'Beim Markieren der Antwort als richtig ist ein Fehler aufgetreten';
$string['error:settimespan'] = "Zeitauswahl konnte nicht übernommen werden.";
$string['error:hide'] = 'Beim Ausblenden des Elements ist ein Fehler aufgetreten.';
$string['error:show'] = 'Beim Einblenden des Elements ist ein Fehler aufgetreten.';
$string['error:putinrecyclebin'] = 'Das Element konnte nicht in den Papierkorb verschoben werden.';
$string['error:markasread'] = 'Das Element konnte nicht als gelesen markiert werden.';
$string['error:markasunread'] = 'Das Element konnte nicht als ungelesen markiert werden.';

$string['document'] = 'Dokument';

$string['unknownuser'] = 'unbekannter Nutzer';
$string['deletedQuestion'] = 'gelöschte Frage';
$string['deletedComment'] = 'gelöschter Kommentar';
$string['hiddenComment'] = 'verborgener Beitrag';
$string['deleteComment'] = 'Kommentar löschen';
$string['deleteAndArchiveComment'] = 'Kommentar archivieren und löschen';
$string['delete'] = 'Löschen';
$string['edit'] = 'Bearbeiten';
$string['editButton'] = 'Speichern';

$string['deletionForbidden'] = 'Löschen nicht erlaubt';
$string['onlyDeleteOwnAnnotations'] = ', da die Annotation von einem anderen Nutzer stammt';
$string['onlyDeleteUncommentedPosts'] = ', da mit Ihrer Annotation auch Kommentare von anderen Nutzern gelöscht werden würden.';
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

$string['questionstitle'] = 'Fragen auf Seite ';
$string['noquestions'] = 'Keine Fragen auf dieser Seite!';
$string['allquestionstitle'] = 'Alle Fragen in ';
$string['searchresults'] = 'Suchergebnisse ';
$string['nosearchresults'] = 'Keine Suchergebnisse gefunden.';

$string['newanswerhtml'] = 'Die von Ihnen abonnierte Frage "{$a->question}" wurde von {$a->answeruser} beantwortet mit dem Kommentar: <br /> <br /> "{$a->content}"<br /><br />
Der Kommentar ist <a href="{$a->urltoanswer}">hier</a> einzusehen.';

$string['newanswertext'] = 'Die von Ihnen abonnierte Frage "{$a->question}" wurde von {$a->answeruser} beantwortet mit dem Kommentar:

    "{$a->content}"

Der Kommentar ist verfügbar unter: {$a->urltoanswer}';

$string['newquestionhtml'] = 'Es wurde eine neue Frage von {$a->answeruser} eingestellt mit dem Inhalt: <br /> <br /> "{$a->content}"<br /><br />
Die Frage ist <a href="{$a->urltoanswer}">hier</a> einzusehen.';

$string['newquestiontext'] = 'Es wurde eine neue Frage von {$a->answeruser} eingestellt mit dem Inhalt:

    "{$a->content}"

Die Frage ist verfügbar unter: {$a->urltoanswer}';

$string['unsubscribe_notification'] = 'Um keine Benachrichtigung mehr zu erhalten, klicken Sie bitte <a href="{$a}">hier</a>.';

$string['gotocomment'] = 'zum Kommentar';
$string['gotoquestion'] = 'zur Frage';

$string['showmore'] = 'mehr';
$string['showless'] = 'weniger';

$string['noquestions_view'] = 'Derzeit gibt es keine Fragen in diesem Dokument.';

/* *********************************** overview page ***********************************/

$string['newquestions'] = 'Neu gestellte Fragen in diesem Kurs';
$string['read'] = 'Gelesen';
$string['page'] = 'Seite';
$string['location'] = 'Ort';
$string['comment'] = 'Kommentar';
$string['reportedcomment'] = 'Gemeldeter Kommentar';
$string['view'] = 'Dokument';
$string['overview'] = 'Übersicht';
$string['pdfannotatorcolumn'] = 'Dokument';
$string['itemsperpage'] = 'Einträge pro Seite';
$string['show'] = 'Zeige';

$string['all'] = 'alle';

$string['openquestions'] = 'offene';
$string['closedquestions'] = 'geschlossene';
$string['allquestions'] = 'alle';

$string['allanswers'] = 'alle';
$string['subscribedanswers'] = 'auf abonnierte Fragen';
$string['allreports'] = 'alle Meldungen';
$string['unseenreports'] = 'nur ungelesene';
$string['seenreports'] = 'nur gelesene';

$string['reportedby'] = 'von / am';
$string['writtenby'] = 'von / am';
$string['lastanswered'] = 'Letzte Antwort';
$string['correct'] = 'richtig';

$string['questionstab'] = 'Fragen';
$string['questionstabicon'] = 'Fragen'; // Hover on help icon.
$string['questionstabicon_help'] = 'Hier werden alle noch offenen Fragen angezeigt. Wahlweise sehen Sie auch alle Fragen oder alle geschlossenen Fragen. Die Übersicht umfasst alle PDF-Annotatoren des aktuellen Kurses.';

$string['answerstab'] = 'Antworten';
$string['answerstabicon'] = 'Antworten';
$string['answerstabicon_help'] = 'Auf dieser Seite sehen Sie wahlweise alle Antworten oder alle Antworten auf von Ihnen abonnierte* Fragen. Die Übersicht umfasst alle PDF-Annotatoren des aktuellen Kurses.<br>* Ihre eigenen Fragen sind automatisch abonniert, solange Sie sie nicht aktiv entabonnieren.';

$string['ownpoststab'] = 'Meine Beiträge';
$string['ownpoststabicon'] = 'Meine Beiträge';
$string['ownpoststabicon_help'] = 'Hier werden alle von Ihnen verfassten Beiträge angezeigt. Die Übersicht umfasst alle PDF-Annotatoren des aktuellen Kurses.';

$string['reportstab'] = 'Meldungen';
$string['reportstabicon'] = 'Meldungen';
$string['reportstabicon_help'] = 'Diese Seite zeigt Beiträge an, die im aktuellen Kurs gemeldet wurden. Sie sehen wahlweise nur ungelese, nur gelesene* oder alle Meldungen.<br>* Eine Meldung gilt als gelesen, sobald sie von einem Kursmanager als gelesen markiert wurde.';

$string['recyclebintab'] = 'Gelesen';
$string['recyclebintabicon'] = 'Gelesen';
$string['recyclebintabicon_help'] = "Hier werden alle Meldungen angezeigt, die aus diesem Kurs stammen und als <em>gelesen</em> markiert wurden.";

$string['voteshelpicon'] = 'Likes';
$string['voteshelpicon_help'] = 'Hier sehen Sie, wieviele weitere Personen sich für die Frage interessieren.';

$string['voteshelpicontwo'] = 'Likes';
$string['voteshelpicontwo_help'] = 'Hier sehen Sie, wieviele <em>Likes</em> Ihre Beiträge erhalten haben.';

$string['answercounthelpicon'] = 'Zahl der Antworten';
$string['answercounthelpicon_help'] = 'Hier sehen Sie, wieviele Antworten eine Frage erhalten hat.';

$string['iscorrecthelpicon'] = 'Richtig';
$string['iscorrecthelpicon_help'] = 'Neben Antworten, die als richtig markiert wurden, erscheint ein grünes Häkchen.';

$string['markasread'] = 'Als gelesen markieren';
$string['markasunread'] = 'Als ungelesen markieren';
$string['successfullymarkedasread'] = 'Die Meldung wurde als gelesen markiert.';
$string['successfullymarkedasreadandnolongerdisplayed'] = 'Die Meldung wurde als gelesen markiert und aus der Tabelle entfernt.';
$string['successfullymarkedasunread'] = 'Die Meldung wurde als ungelesen markiert.';
$string['successfullymarkedasunreadandnolongerdisplayed'] = 'Die Meldung wurde als ungelesen markiert und aus der Tabelle entfernt.';
$string['successfullymarkedasread'] = 'Die Meldung wurde als gelesen markiert.';

$string['putinrecyclebin'] = 'Als gelesen markieren';
$string['putinrecyclebin_report'] = 'Als gelesen markieren';
$string['overviewactioncolumn'] = "Verwalten";
$string['actiondropdown'] = "Optionen";
$string['statistic'] = 'Statistik';
$string['report'] = 'Meldung';
$string['toreport'] = 'Melden';
$string['reportForbidden'] = 'Melden nicht erlaubt';
$string['introquestions'] = 'Es werden alle Fragen in diesem Kurs angezeigt, die noch nicht als beantwortet markiert wurden.';

$string['newstitle'] = 'Neu gestellte Fragen';
$string['actiondropdown'] = 'Optionen';

$string['recyclebin_overview_studentsintro'] = 'Hier können Sie ausgeblendete Antworten auf von Ihnen gestellte oder abonnierte Fragen wieder einblenden. Die Übersicht umfasst alle PDF-Annotatoren des aktuellen Kurses.';
$string['recyclebin_overview_managerintro'] = 'Hier können Sie ausgeblendete Kommentarmeldungen wieder einblenden. Die Übersicht umfasst alle PDF-Annotatoren des aktuellen Kurses.';

$string['openAll'] = 'Alle aufklappen';
$string['closeAll'] = 'Alle zuklappen';
$string['saveOverviewConfig'] = 'Aktuelle Ansicht merken';
$string['OverviewConfigSaved'] = 'Ihre Einstellung wurde gespeichert.';
$string['OverviewConfigCouldNotBeSaved'] = 'Ihr Browser unterstützt die Funktion zum Speichern der Einstellung nicht.';
$string['successfullyPutInRecycleBin'] = 'Der Eintrag wurde in den Papierkorb verschoben.';
$string['successfullyRedisplayedReport'] = 'Der Eintrag ist nun wieder unter "Meldungen" zu sehen.';
$string['successfullyRedisplayedAnswer'] = 'Der Eintrag ist nun wieder unter "Antworten" zu sehen.';

$string['day'] = 'Tag';
$string['days'] = 'Tage';
$string['week'] = 'Woche';
$string['weeks'] = 'Wochen';
$string['register'] = 'Übernehmen';
$string['timewasset'] = 'Ihre Änderung wurde übernommen.';
$string['timecouldnotbeset'] = 'Beim Versuch, den Zeitraum für die Anzeige neuer Fragen zu ändern ist ein Fehler aufgetreten.';

$string['hideforever'] = 'Meldung ausblenden';
$string['hideanswerforever'] = 'Antwort ausblenden';
$string['displayagain'] = 'wieder einblenden';
$string['displayreportagain'] = 'Meldung wieder einblenden';
$string['deletereport'] = 'Meldung endgültig löschen';

$string['didyouknow'] = 'Tipp';

$string['reportinfotitle'] = 'Gemeldete Kommentare';
$string['unsolvedquestionstitle'] = 'Offene Fragen';
$string['interestedpeople'] = 'weitere Interessenten';

$string['entity_helptitle'] = 'Hilfe für ';

$string['reportinfotitle_help'] = 'Hier finden Sie alle Meldungen in diesem Kurs. Der gemeldete Kommentar ist jeweils fettgedruckt. Unter ihm steht die Meldung.';
$string['unsolvedquestionstitle_help'] = 'Hier finden Sie alle offenen Fragen in diesem Kurs.';
$string['labelforsettingnewsspan'] = 'Zeitraum auswählen';

$string['mypoststitle'] = 'Meine Beiträge';
$string['mypoststitle_help'] = "Hier sehen Sie alle Beiträge, die Sie in einem Annotator dieses Kurses verfasst haben. Auch anonym verfasste Beiträge werden Ihnen hier angezeigt, jedoch keine gelöschten.";

$string['hiddenreportstitle'] = 'Papierkorb (Meldungen)';
$string['hiddenreportstitle_help'] = "Hier können Sie ausgeblendete Meldungen wieder einblenden oder endgültig löschen.";

$string['nohiddenanswernotifications'] = "Sie haben in diesem Kurs zurzeit keine Antwort ausgeblendet.";
$string['nohiddenreports'] = "Sie haben in diesem Kurs zurzeit keine Meldung ausgeblendet.";

$string['choice_viewanswers'] = 'Antworten anzeigen';
$string['choice_viewreports'] = 'Meldungen anzeigen';

$string['noreports'] = 'Derzeit gibt es keine Meldungen in diesem Kurs.';
$string['nounreadreports'] = 'Derzeit gibt es keine ungelesenen Meldungen in diesem Kurs.';
$string['noreadreports'] = 'Derzeit gibt es keine gelesenen Meldungen in diesem Kurs.';
$string['noanswers'] = 'Derzeit gibt es keine Antworten in diesem Kurs.';
$string['noanswerssubscribed'] = 'Derzeit gibt es keine Antworten auf ihre abonnierten Fragen in diesem Kurs.';
$string['noquestionsopen_overview'] = 'Derzeit gibt es keine offenen Fragen in diesem Kurs.';
$string['noquestionsclosed_overview'] = 'Derzeit gibt es keine geschlossenen Fragen in diesem Kurs.';
$string['noquestions_overview'] = 'Derzeit gibt es keine Fragen in diesem Kurs.';
$string['nomyposts'] = 'Sie haben in diesem Kurs noch keine Beiträge verfasst.';

$string['chooseyoursettings'] = 'Möchten Sie in Zukunft über neue Aktivitäten in diesem Modul benachrichtigt werden?';
$string['tosettingspage'] = 'zu den Einstellungen';

$string['viewAllActivitiesInThisCourse'] = 'Möchten Sie alle Module mit neuen Aktivitäten in diesem Kurs ansehen?';
$string['tooverview'] = 'zur Übersicht';

/* *********************************** studentoverview page (additional strings apart from those above) ***********************************/

$string['newanswersavailable'] = 'Antworten';
$string['newanswersavailable_helptitle'] = 'Hilfe für Antworten';
$string['newanswersavailable_help'] = 'Hier finden Sie alle Antworten auf Fragen, die Sie in diesem Kurs gestellt oder abonniert haben. Die Frage ist jeweils fettgedruckt. Darunter steht die Antwort.';

$string['min2Chars'] = 'Eine Frage oder Kommentar mit weniger als zwei Zeichen ist nicht erlaubt.';

$string['successfullySubscribed'] = 'Die Frage wurde abonniert.';
$string['successfullyUnsubscribedSingular'] = 'Die Frage wurde entabonniert und die einzige Antwort aus der Tabelle entfernt.';
$string['successfullyUnsubscribedTwo'] = 'Die Frage wurde entabonniert. Beide Antworten wurden aus der Tabelle entfernt.';
$string['successfullyUnsubscribedPlural'] = 'Die Frage wurde entabonniert. Alle {$a} Antworten wurden aus der Tabelle entfernt.';
$string['successfullyUnsubscribed'] = 'Das Abonnement wurde gekündigt.';
$string['successfullySubscribed'] = 'Die Frage wurde abonniert.';
$string['successfullyEdited'] = 'Ihre Änderungen wurden übernommen.';
$string['successfullyHidden'] = 'Der Kommentar erscheint Teilnehmern als verborgen.';
$string['successfullyRedisplayed'] = 'Der Kommentar ist für Teilnehmer wieder sichtbar.';

$string['unsubscribingDidNotWork'] = 'Bei der Kündigung des Abonnements ist ein Fehler aufgetreten.';

$string['seeabove'] = ''; // 's.o.';

/* *********************************** statistics-tab ***********************************/

$string['questions'] = 'Fragen';
$string['myquestions'] = 'eigene Fragen';
$string['answers'] = 'Antworten';
$string['myanswers'] = 'eigene Antworten';
$string['answers_myquestions'] = 'Antworten auf meine Fragen';
$string['reports'] = 'gemeldete Kommentare';
$string['count'] = 'Anzahl';
$string['in_document'] = 'in diesem Dokument';
$string['in_course'] = 'in diesem Kurs';
$string['by_other_users'] = 'von anderen Usern';
$string['own'] = 'eigene';
$string['average'] = 'Durchschnitt';
$string['average_questions'] = 'Durchschnitt Fragen';
$string['average_answers'] = 'Durchschnitt Antworten';
$string['average_help'] = 'In die Berechnung des Durchschnitts (arithmetisches Mittel) werden nur User mit einbezogen, die mind. einen Kommentar verfasst haben';
$string['total'] = 'Gesamt';

$string['reportsendbutton'] = 'Meldung versenden';

$string['colorPicker'] = 'Farbauswahl';
$string['chart_title'] = 'Fragen und Antworten in den Annotatoren im Kurs';

$string['noCommentsupported'] = 'Dieser Annotationstyp unterstützt keine Kommentare.';

$string['enterText'] = 'Text eingeben';

$string['recievenewquestionnotifications'] = 'Benachrichtigung, wenn eine neue Frage gestellt wurde';

$string['deletereport'] = 'Meldung endgültig löschen';

/* *********************************** vote ***********************************/
$string['likeQuestion'] = 'interessiert mich auch';
$string['likeAnswer'] = 'finde ich hilfreich';
$string['likeAnswerForbidden'] = 'bereits als hilfreich markiert';
$string['likeQuestionForbidden'] = 'bereits als interessant markiert';
$string['likeOwnComment'] = 'eigener Kommentar';
$string['like'] = 'Like';
$string['likeForbidden'] = 'Liken nicht erlaubt';
$string['likeCountQuestion'] = 'Personen interessiert diese Frage auch';
$string['likeCountAnswer'] = 'Personen finden diese Antwort hilfreich';

$string['subscribeQuestion'] = 'Abonnieren';
$string['unsubscribeQuestion'] = 'Abo kündigen';
$string['markSolved'] = 'Frage schließen';
$string['markUnsolved'] = 'Frage öffnen';
$string['questionSolved'] = 'Frage geschlossen. Sie können jedoch weiterhin Kommentare erstellen.';
$string['markCorrect'] = 'Als richtig markieren';
$string['removeCorrect'] = 'Markierung als richtig entfernen';
$string['markhidden'] = 'Verbergen';
$string['removehidden'] = 'Anzeigen';
$string['answerSolved'] = 'Diese Antwort wurde vom Manager als richtig markiert.';
$string['hiddenforparticipants'] = 'Für Teilnehmer/innen verborgen';

/* * ************************************ privacy ********************************** */

$string['privacy:metadata:core_files'] = 'Der Pdfannotator speichert Dateien, die ein Benutzer als Annotations- und Diskussionsgrundlage hochgeladen hat.';

// pdfannotator_annotations-table
$string['privacy:metadata:pdfannotator_annotations'] = "Informationen über die Annotationen eines Nutzers. Dies beinhaltet den Annotationstypen (z.B. Textmarkierung oder Freihandzeichnung), die Position innerhalb einer bestimmten Pdf-Datei sowie den Erstellungszeitpunkt.";
$string['privacy:metadata:pdfannotator_annotations:userid'] = 'Die ID des Nutzers, der diese Annotation angelegt hat.';
$string['privacy:metadata:pdfannotator_annotations:annotationid'] = 'Die ID der vom Nutzer angelegten Annotation. Unter dieser ID sind die oben genannten Daten gespeichert.';

// pdfannotator_comments-table
$string['privacy:metadata:pdfannotator_comments'] = "Informationen über die Kommentare eines Nutzers. Dies beinhaltet den Inhalt und Erstellungszeitpunkt des Kommentars sowie die Annotation, auf die sich der Kommentar bezieht.";
$string['privacy:metadata:pdfannotator_comments:userid'] = 'Die ID des Nutzers, der diesen Kommentar verfasst hat.';
$string['privacy:metadata:pdfannotator_comments:annotationid'] = 'Die ID der zugrunde liegenden Annotation.';
$string['privacy:metadata:pdfannotator_comments:content'] = 'Der Wortlaut des Kommentars.';

// pdfannotator_commentsarchive-table
$string['privacy:metadata:pdfannotator_commentsarchive'] = "Kommentare die gemeldet und anschließend gelöscht wurde, werden hier archiviert. Dabei werden dieselben Daten wie bei anderen Kommentaren gespeichert.";

// pdfannotator_reports-table
$string['privacy:metadata:pdfannotator_reports'] = "Nutzer können die Kommentare anderer Nutzer als unangemessen melden. Diese Meldung werden gespeichert. Folgende Informationen über Meldung werden gespeichert: Die ID des gemeldeten Kommentars sowie der Verfasser, Wortlaut und Zeitpunkt der Meldung.";
$string['privacy:metadata:pdfannotator_reports:commentid'] = 'Die ID des gemeldeten Kommentars.';
$string['privacy:metadata:pdfannotator_reports:message'] = 'Der Inhalt der Meldung.';
$string['privacy:metadata:pdfannotator_reports:userid'] = 'Der Verfasser der Meldung.';

// pdfannotator_subscriptions-table
$string['privacy:metadata:pdfannotator_subscriptions'] = "Informationen über abonnierte Fragen bzw. Diskussionen.";
$string['privacy:metadata:pdfannotator_subscriptions:annotationid'] = 'Die ID der abonnierten Frage bzw. Diskussion.';
$string['privacy:metadata:pdfannotator_subscriptions:userid'] = 'Die ID des Nutzers, der diese Frage bzw. Diskussion abonniert hat.';

// pdfannotator_votes-table
$string['privacy:metadata:pdfannotator_votes'] = "Informationen über Fragen und Kommentare, die als interessant bzw. hilfreich markiert wurden.";
$string['privacy:metadata:pdfannotator_votes:commentid'] = "Die ID des Kommentars.";
$string['privacy:metadata:pdfannotator_votes:userid'] = "Die ID des Nutzers, der den Kommentar markiert hat. Diese wird gespeichert, damit ein Nutzer nicht mehrfach für denselben Kommentar stimmen kann.";

/* * **************************recent activity*********** */
$string['bynameondate'] = 'von {$a->name} - {$a->date}';

/* * ********************index.php********************* */
$string['subscribe'] = 'Annotationen abonniere';
$string['unsubscribe'] = 'Abonierte Annotationen kündigen';
$string['subscribed'] = 'Aboniert';

$string['studentdrawingforbidden'] = 'Dieser Annotator unterstützt keine Freihandzeichnungen für Ihre Nutzerrolle.';
$string['studenttextboxforbidden'] = 'Dieser Annotator unterstützt keine Textfelder für Ihre Nutzerrolle.';

/* * ******************** printview ********************* */

$string['answer'] = "Antwort";
$string['printviewtitle'] = "Kommentare";

$string['infonocomments'] = "Dieses Dokument enthält zurzeit keine Kommentare.";

$string['lastedited'] = 'zuletzt bearbeitet';

/* * ****************************************** time ******************************************** */
$string['ago'] = 'vor {$a}';
$string['second'] = 'Sekunde';
$string['minute'] = 'Minute';
$string['hour'] = 'Stunde';
$string['day'] = 'Tag';
$string['month'] = 'Monat';
$string['year'] = 'Jahre';
$string['seconds'] = 'Sekunden';
$string['minutes'] = 'Minuten';
$string['hours'] = 'Stunden';
$string['days'] = 'Tagen';
$string['months'] = 'Monaten';
$string['years'] = 'Jahren';
$string['justnow'] = 'vor einem Moment';
