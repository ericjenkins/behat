@api
Feature: Drush driver
  In order to show functionality added by the Drush driver 
  As a developer
  I need to use the step definitions it supports

  Scenario: Verify Drush ULI functionality
    Given I am logged in as an "administrator"
      # DrupalContext::assertAuthenticatedByRole()
    Then I should not see "404 Not Found"
      # MinkContext::assertPageNotContainsText()

  Scenario: Create and log in as a sub-admin
    Given I am logged in as a user with the "sub-admin" role
      # DrupalContext::assertAuthenticatedByRole()
    When I am at "user"
      # MinkContext::assertAtPath()
    Then I should see the heading "History"
      # MinkContext::assertHeading()

  Scenario: Target links within table rows
    Given I am logged in as a user with the "administrator" role
      # DrupalContext::assertAuthenticatedByRole()
    When I am at "admin/structure/types"
      # MinkContext::assertAtPath()
    And I click "manage fields" in the "Article" row
      # DrupalContext::assertClickInTableRow()
    Then I should be on "admin/structure/types/manage/article/fields"
      # MinkContext::assertPageAddress()
    And I should see text matching "Add new field"
      # MinkContext::assertPageMatchesText()

  Scenario: Clear cache
    Given the cache has been cleared
      # DrupalContext::assertCacheClear()
    When I am on the homepage
      # MinkContext::iAmOnHomepage()
    Then I should get a "200" HTTP response
      # MinkContext::assertHttpResponse()
