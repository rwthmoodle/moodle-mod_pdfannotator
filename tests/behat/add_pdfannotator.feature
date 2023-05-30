@mod @mod_pdfannotator @_file_upload
Feature: Add a pdfannotator activity
  In order to let the users use the pdfannotator in a course
  As a teacher
  I need to add a pdfannotator to a moodle course

  @javascript
  Scenario: Add a pdfannotator to a course
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
    And I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on
    And I add a "PDF Annotation" to section "1" and I fill the form with:
      | Name              | Test pdf annotation                                             |
      | Description       | Test pdf annotation description                                 |
      | Select a pdf-file | mod/pdfannotator/tests/fixtures/submission.pdf |
    And I am on "Course 1" course homepage with editing mode on
    And I log out
    And I log in as "student1"
    And I am on "Course 1" course homepage
    And I follow "Test pdf annotation"
    Then I should see "Test pdf annotation"
