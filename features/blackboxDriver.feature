Feature: Test DrupalContext
  In order to prove the Drupal context using the blackbox driver is working properly
  As a developer
  I need to use the step definitions of this context

  Scenario: Test the ability to find a heading in a region
    Given I am on the homepage
      # MinkContext::iAmOnHomepage()
    When I click "Conditions"
      # MinkContext::assertClick()
    Then I should see the heading "Pain Conditions Health Center" in the "title" region
      # MinkContext::assertRegionHeading()
    And take a screenshot

  Scenario: Clicking content in a region
    Given I am at "treatment"
      # MinkContext::assertAtPath()
    When I click "Alternative Care" in the "content" region
      # MinkContext::assertRegionLinkFollow()
    Then I should see "Alternative Care Topics" in the "left sidebar"
      # MinkContext::assertRegionText()
    And I should see the link "Acupuncture" in the "left sidebar" region
      # MinkContext::assertRegionText()

  Scenario: Viewing content in a region
    Given I am on the homepage
      # MinkContext::iAmOnHomepage()
    Then I should see "All rights reserved" in the "footer"
      # MinkContext::assertRegionText()

  Scenario: Test ability to find text that should not appear in a region
    Given I am on the homepage
      # MinkContext::iAmOnHomepage()
    Then I should not see "Conditions" in the "breadcrumb" region
      # MinkContext::assertNotRegionText()

  Scenario: Submit a form in a region
    Given I am at "doctor"
      # MinkContext::assertAtPath()
    When I fill in "City, State, or Zip" with "60015" in the "content" region
      # MinkContext::regionFillField()
    And I press "submit" in the "content" region
      # MinkContext::assertRegionPressButton()
    Then I should see the text "Results for Physician/Surgeon near Riverwoods, IL 60015, USA" in the "title" region
      # MinkContext::assertRegionText()

  Scenario: Check a link should not exist in a region
    Given I am at "conditions"
      # MinkContext::assertAtPath()
    Then I should not see the link "Conditions" in the "breadcrumb"
      # MinkContext::assertNotLinkRegion()

  Scenario: Find a button
    Given I am on the homepage
      # MinkContext::iAmOnHomepage()
    Then I should see the "submit" button
      # MinkContext::assertButton()

  Scenario: Find a button in a region
    Given I am on the homepage
      # MinkContext::iAmOnHomepage()
    Then I should see the "submit" button in the "right sidebar"
      # MarkupContext::assertRegionButton()

  Scenario: Find an element in a region
    Given I am on the homepage
      # MinkContext::iAmOnHomepage()
    Then I should see the "ul" element in the "right sidebar"

  Scenario: Element not in region
    Given I am on the homepage
      # MinkContext::iAmOnHomepage()
    Then I should not see the "header" element in the "content"
      # MarkupContext::assertRegionElement()

  Scenario: Text not in element in region
    Given I am at "conditions"
      # MinkContext::assertAtPath()
    Then I should not see "Wellness Topics" in the "div" element in the "left sidebar"
      # MarkupContext::assertNotRegionElementText()

  Scenario: Find an element with an attribute in a region
    Given I am at "treatment/alternative-care"
      # MinkContext::assertAtPath()
    Then I should see the "div" element with the "id" attribute set to "block-vh-main-menu-main-menu-articles-related" in the "left sidebar" region
      # MarkupContext::assertRegionElementAttribute()

  Scenario: Find text in an element with an attribute in a region
    Given I am at "conditions/chronic-pain"
      # MinkContext::assertAtPath()
    Then I should see "Treatment" in the "div" element with the "id" attribute set to "vhd_top_nav_level1_1" in the "header" region
      # MarkupContext::assertRegionElementTextAttribute()

  Scenario: Error messages
    Given I am on "/user"
      # MinkContext::visit()
    When I press "Log in"
      # MinkContext::pressButton()
    Then I should see the error message "Password field is required"
      # MessageContext::assertErrorVisible()
    And I should not see the error message "Sorry, unrecognized username or password"
      # Messagecontext::assertNotErrorVisible()
    And I should see the following error messages:
        | error messages                         |
        | Username field is required             |
        | Password field is required             |
        | Enter the code above field is required |
        | We've recently moved our forums        |
      # MessageContext::assertMultipleErrors()
    And I should not see the following error messages:
        | error messages                                                                |
        | Sorry, unrecognized username or password                                      |
        | Unable to send e-mail. Contact the site administrator if the problem persists |
      # MessageContext::assertNotMultipleErrors()

  Scenario: Education Center webform submission
    Given I am on "https://eve.spine-health.com/education-centers/providence/not-for-profit-care/more-information"
      # MinkContext::visit()
    When I fill in the following:
        | First Name   | Test                   |
        | Last Name    | Name                   |
        | Email        | test@veritashealth.com |
        | Phone Number | 8471234567             |
        | Zip Code     | 60015                  |
      # MinkContext::fillFields()
    And I select the radio button "Ask a Question or Discuss My Case before requesting an appointment"
      # MinkContext::assertSelectRadioById()
    And I check "edit-submitted-terms-of-service-1"
      # MinkContext::checkOption()
    And I press "Submit" in the "content" region
      # MinkContext::assertRegionPressButton()
    Then I should see the text "Thank you for your submission" in the "content" region
      # MinkContext::assertRegionText()
    But I should not see the message "Terms of Service field is required"
      # MessageContext::assertNotMessage()

  Scenario: User Registration Page access denied
    Given I am on "/user/register"
      # MinkContext::visit()
    Then I should get a "404" HTTP response
      # MinkContext::assertHttpResponse()

  Scenario: Debug a page and export an html snapshot
    Given I am on "/user/register"
      # MinkContext::visit()
    Then debug
      # FeatureContext::debug()
    And take a screenshot
      # FailureContext::assertScreenShot()
