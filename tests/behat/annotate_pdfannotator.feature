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
      | Select a pdf-file | mod/pdfannotator/tests/fixtures/submission.pdf |
    And I am on "Course 1" course homepage with editing mode on
    And I log out

  Scenario: Add a question to a pdfannotator
    Given I am on the "Test PDF annotation" "mod_pdfannotator > View" page logged in as "student1"
    And I click on "comment" "button"
    And I wait "1" seconds
    And I point at the pdfannotator canvas
    And I wait "1" seconds
    And I set the field with xpath "//div[@class='pdfannotator-comment-list']//textarea" to "This is a smurfing smurf"
    And I click on "Create Annotation" "button"
    And I wait until the page is ready
    Then I should see "This is a smurfing smurf"
    And I log out
