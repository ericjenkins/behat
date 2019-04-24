@javascript @mobile
Feature: Test javascript
  In order to prove javascript context for mobile devices is working properly
  As a developer
  I need to use the step definitions of this context

  Scenario: Verify that the mobile site loads
    Given I am at "conditions/arthritis"
    # MinkContext::assertAtPath()
    And I wait 2 seconds
    # FeatureContext::waitSeconds()
    And take a screenshot
    # JsBrowserContext::assertScreenShot()
    Then I should see 2 advertisements in the "right sidebar"
    # JsBrowserContext::assertGoogleAds()
    And the "#block-vh-dfp-dfp-ad-r1m" element should contain "advertisement"
    # MinkContext::assertElementContains()

  @nophantomjs
  Scenario: Test viewport size and scrolling on mobile
    Given I am on the homepage
      # MinkContext::iAmOnHomepage()
    When I click "vh-off-canvas-show"
      # MinkContext::assertClick()
    And I click "Conditions"
      # MinkContext::assertClick()
    And I click "Upper Back Pain"
      # MinkContext::assertClick()
    And set browser window size to 400 x 300
      # JsBrowserContext::setBrowserWindowSizeToX()
    And scroll to the bottom
      # JsBrowserContext::scrollToBottom()
    And wait 1 second
      # FeatureContext::waitSeconds()
    And take a screenshot
      # JsBrowserContext::assertScreenShot()
    Then the viewport should contain the element "p.vh-version"
    # JsBrowserContext::assertViewportContainsElement()
    And the viewport should not contain link "vh-off-canvas-show"
    # JsBrowserContext::assertViewportNotContainsElement()
