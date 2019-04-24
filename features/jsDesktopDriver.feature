@javascript @desktop
Feature: Test javascript
  In order to prove javascript context desktop is working properly
  As a developer
  I need to use the step definitions of this context

  Scenario: Run a Google search for Behat
    Given I am on "https://www.google.com/?complete=0"
      # MinkContext::visit()
    When I fill in "q" with "Behat"
      # MinkContext::fillField()
    And I press the "Google Search" button
      # MinkContext::pressButton()
    And wait 2 seconds
      # FeatureContext::waitSeconds()
    Then I should see "BDD in PHP"
      # MinkContext::assertElementContainsText()

  Scenario: Test the ability to find a heading in a region
    Given I am on the homepage
      # MinkContext::iAmOnHomepage()
    When I click "Conditions"
      # MinkContext::assertClick()
    And set browser window size to 640 x 480
      # ScreenContext::iSetBrowserWindowSizeToX()
    And take a screenshot
      # JsBrowserContext::assertScreenShot()
    Then I should see the heading "Pain Conditions Health Center" in the "title" region
      # MinkContext::assertRegionHeading()

  Scenario: Test the ability to find an element in the browser viewport
    Given I am on the homepage
      # MinkContext::iAmOnHomepage()
    When I scroll element ".directory-sidebar-search-form" into view
      # JsBrowserContext::ScrollIntoView()
    Then the viewport should contain the button "submit"
    # JsBrowserContext::assertViewportContainsElement()
    And the viewport should not contain element "#vhd_top_nav_ul_container"
      # JsBrowserContext::assertViewportNotContainsElement()

  Scenario: Test the ability to assert visible and hidden menu elements
    Given I am at "wellness"
      # MinkContext::assertAtPath()
    When I hover over the link "Conditions"
      # JsBrowserContext::iHoverOver()
    And wait 1 second
      # FeatureContext::waitSeconds()
    Then the link "Chronic Pain" should be visible
      # JsBrowserContext::assertVisibleElement()
    And the link "Physical Therapy" should be hidden
      # JsBrowserContext::assertHiddenElement()
    And the viewport should contain element "#vhd_top_nav_ul_container"
      # JsBrowserContext::assertViewportContainsElement()
