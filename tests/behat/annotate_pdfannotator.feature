@mod @mod_pdfannotator @_file_upload  @javascript
Feature: Annotate in a pdfannotator activity
  In order to annotate in the pdfannotator in a course
  As a student
  I need to note questions and subscribe or unsubscribe to notificatoins

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher1 | Teacher   | 1        | teacher1@example.com |
      | student1 | Student   | 1        | student1@example.com |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | 0        |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
      | student1 | C1     | student        |
    And the following "user preferences" exist:
      | user     | preference | value |
      | teacher1 | htmleditor | atto  |
      | student1 | htmleditor | atto  |
    And I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on
    And I add a pdfannotator activity to course "Course 1" section "1" and I fill the form with:
      | Name              | Test PDF annotation                            |
      | Description       | Test pdf annotation description                |
      | Subscription mode | Optional subscription                          |
      | Select a pdf-file | mod/pdfannotator/tests/fixtures/submission.pdf |
    And I am on "Course 1" course homepage with editing mode on
    And I log out

  Scenario: Add a question to a pdfannotator with optional subscription
    Given I am on the "Test PDF annotation" "mod_pdfannotator > View" page logged in as "student1"
    And I click on "comment" "button"
    And I wait "1" seconds
    And I point at the pdfannotator canvas
    And I wait "1" seconds
    And I set the field with xpath "//div[@id='id_pdfannotator_contenteditable']" to "This is a smurfing smurf"
    And I click on "Create Annotation" "button"
    And I wait until the page is ready
    And I should see "This is a smurfing smurf"
    And I click the pdfannotator public comment dropdown menu button
    Then I should not see "Unsubscribe"
    And I should see "Subscribe"
    And I log out

  Scenario: Add a question to a pdfannotator with auto subscription
    Given I am on the "Test PDF annotation" "mod_pdfannotator > Edit" page logged in as "teacher1"
    And I set the following fields to these values:
      | Subscription mode | Auto subscription |
    And I press "Save"
    And I log out
    And I am on the "Test PDF annotation" "mod_pdfannotator > View" page logged in as "student1"
    And I click on "comment" "button"
    And I wait "1" seconds
    And I point at the pdfannotator canvas
    And I wait "1" seconds
    And I set the field with xpath "//div[@id='id_pdfannotator_contenteditable']" to "This is a smurfing smurf"
    And I click on "Create Annotation" "button"
    And I wait until the page is ready
    And I should see "This is a smurfing smurf"
    And I click the pdfannotator public comment dropdown menu button
    Then I should not see "Subscribe"
    And I should see "Unsubscribe"
    And I log out

  Scenario: Add a question to a pdfannotator with subscription disabled
    Given I am on the "Test PDF annotation" "mod_pdfannotator > Edit" page logged in as "teacher1"
    And I set the following fields to these values:
      | Subscription mode | Subscription disabled |
    And I press "Save"
    And I log out
    And I log in as "student1"
    And I am on "Course 1" course homepage
    And I follow "Test PDF annotation"
    And I click on "comment" "button"
    And I wait "1" seconds
    And I point at the pdfannotator canvas
    And I wait "1" seconds
    And I set the field with xpath "//div[@id='id_pdfannotator_contenteditable']" to "This is a smurfing smurf"
    And I click on "Create Annotation" "button"
    And I wait until the page is ready
    And I should see "This is a smurfing smurf"
    And I click the pdfannotator public comment dropdown menu button
    Then I should not see "Subscribe"
    And I should not see "Unsubscribe"
    And I log out

  Scenario: Add a question to a pdfannotator with forced subscription
    Given I am on the "Test PDF annotation" "mod_pdfannotator > Edit" page logged in as "teacher1"
    And I set the following fields to these values:
      | Subscription mode | Forced subscription |
    And I press "Save"
    And I log out
    And I log in as "student1"
    And I am on "Course 1" course homepage
    And I follow "Test PDF annotation"
    And I click on "comment" "button"
    And I wait "1" seconds
    And I point at the pdfannotator canvas
    And I wait "1" seconds
    And I set the field with xpath "//div[@id='id_pdfannotator_contenteditable']" to "This is a smurfing smurf"
    And I click on "Create Annotation" "button"
    And I wait until the page is ready
    And I should see "This is a smurfing smurf"
    And I click the pdfannotator public comment dropdown menu button
    Then I should not see "Subscribe"
    And I should not see "Unsubscribe"
    And I log out
