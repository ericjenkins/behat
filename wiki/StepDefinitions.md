# Behat Step Definitions

The following is a list of all available step definitions that can be utilized
for writing new Behat feature tests. This list can also be produced by running:
`vendor/bin/behat -p chrome -di`

```gherkin
default | [When|*] /^(?:|I )wait (\d+) seconds?$/
        | Wait a specified number of seconds.
        | Example: When I wait 1 second
        | Example: And wait 2 seconds
        | at `FeatureContext::waitSeconds()`

default | [Then|*] /^(?:|I )debug$/
        | Prints last response to console.
        | at `FeatureContext::debug()`

default | [Given|*] /^(?:|I )(enable|disable|uninstall)(?: the|) module "(?P<module>[^"]*)"$/
        | Enable/Disable/Uninstall a drupal module with drush.
        | Example: Given I enable the module "update"
        | Example: And I disable module "update"
        | at `FeatureContext::alterDrupalModule()`

default | [Given|*] I am logged in as :name
        | at `DrupalCustomContext::assertLoggedInByName()`

default | [Given|*] I am an anonymous user
        | at `DrupalCustomContext::assertAnonymousUser()`

default | [Given|*] I am not logged in
        | at `DrupalCustomContext::assertAnonymousUser()`

default | [Given|*] I am logged in as a user with the :role role(s)
        | Creates and authenticates a user with the given role(s).
        | at `DrupalCustomContext::assertAuthenticatedByRole()`

default | [Given|*] I am logged in as a/an :role
        | Creates and authenticates a user with the given role(s).
        | at `DrupalCustomContext::assertAuthenticatedByRole()`

default | [Given|*] I am logged in as a user with the :role role(s) and I have the following fields:
        | Creates and authenticates a user with the given role(s) and given fields.
        | | field_user_name     | John  |
        | | field_user_surname  | Smith |
        | | ...                 | ...   |
        | at `DrupalCustomContext::assertAuthenticatedByRoleWithGivenFields()`

default | [Given|*] I am logged in as a user with the :permissions permission(s)
        | at `DrupalCustomContext::assertLoggedInWithPermissions()`

default | [Then|*] I should see (the text ):text in the :rowText row
        | Find text in a table row containing given text.
        | at `DrupalCustomContext::assertTextInTableRow()`

default | [Then|*] I should not see (the text ):text in the :rowText row
        | Asset text not in a table row containing given text.
        | at `DrupalCustomContext::assertTextNotInTableRow()`

default | [Given|*] I click :link in the :rowText row
        | Attempts to find a link in a table row containing giving text. This is for
        | administrative pages such as the administer content types screen found at
        | `admin/structure/types`.
        | at `DrupalCustomContext::assertClickInTableRow()`

default | [Then|*] I (should )see the :link in the :rowText row
        | Attempts to find a link in a table row containing giving text. This is for
        | administrative pages such as the administer content types screen found at
        | `admin/structure/types`.
        | at `DrupalCustomContext::assertClickInTableRow()`

default | [Given|*] the cache has been cleared
        | at `DrupalCustomContext::assertCacheClear()`

default | [Given|*] I run cron
        | at `DrupalCustomContext::assertCron()`

default | [Given|*] I am viewing a/an :type (content )with the title :title
        | Creates content of the given type.
        | at `DrupalCustomContext::createNode()`

default | [Given|*] a/an :type (content )with the title :title
        | Creates content of the given type.
        | at `DrupalCustomContext::createNode()`

default | [Given|*] I am viewing my :type (content )with the title :title
        | Creates content authored by the current user.
        | at `DrupalCustomContext::createMyNode()`

default | [Given|*] :type content:
        | Creates content of a given type provided in the form:
        | | title    | author     | status | created           |
        | | My title | Joe Editor | 1      | 2014-10-17 8:00am |
        | | ...      | ...        | ...    | ...               |
        | at `DrupalCustomContext::createNodes()`

default | [Given|*] I am viewing a/an :type( content):
        | Creates content of the given type, provided in the form:
        | | title     | My node        |
        | | Field One | My field value |
        | | author    | Joe Editor     |
        | | status    | 1              |
        | | ...       | ...            |
        | at `DrupalCustomContext::assertViewingNode()`

default | [Then|*] I should be able to edit a/an :type( content)
        | Asserts that a given content type is editable.
        | at `DrupalCustomContext::assertEditNodeOfType()`

default | [Given|*] I am viewing a/an :vocabulary term with the name :name
        | Creates a term on an existing vocabulary.
        | at `DrupalCustomContext::createTerm()`

default | [Given|*] a/an :vocabulary term with the name :name
        | Creates a term on an existing vocabulary.
        | at `DrupalCustomContext::createTerm()`

default | [Given|*] users:
        | Creates multiple users.
        | Provide user data in the following format:
        | | name     | mail         | roles        |
        | | user foo | foo@bar.com  | role1, role2 |
        | at `DrupalCustomContext::createUsers()`

default | [Given|*] :vocabulary terms:
        | Creates one or more terms on an existing vocabulary.
        | Provide term data in the following format:
        | | name  | parent | description | weight | taxonomy_field_image |
        | | Snook | Fish   | Marine fish | 10     | snook-123.jpg        |
        | | ...   | ...    | ...         | ...    | ...                  |
        | Only the 'name' field is required.
        | at `DrupalCustomContext::createTerms()`

default | [Given|*] the/these (following )languages are available:
        | Creates one or more languages.
        | 
        | Provide language data in the following format:
        | | langcode |
        | | en       |
        | | fr       |
        | 
        |   The table listing languages by their ISO code.
        | at `DrupalCustomContext::createLanguages()`

default | [Then|*] (I )break
        | Pauses the scenario until the user presses a key. Useful when debugging a scenario.
        | at `DrupalCustomContext::iPutABreakpoint()`

default | [Given|*] /^(?:|I )set (?:|the )browser window size to (\d+)(?:| )x(?:| )(\d+)$/
        | Step definition for setting browser window size.
        | Example: Given I set the browser window size to 800 x 600
        | Example: And set the browser window size to 1280x720
        | Adapted from: http://www.devengineering.com/node/17
        | at `DmoreChromeDriverContext::setBrowserWindowSizeToWxH()`

default | [When|*] /^(?:|I )scroll (?:|the )(?<selector>[^"]*) "(?<locator>[^"]*)" into view$/
        | Scroll to a specific (link|button|field|element).
        | Example: Wen I scroll element "#swap-div" into view .
        | Adapted from: https://gist.github.com/MKorostoff/c94824a467ffa53f4fa9
        | at `DmoreChromeDriverContext::scrollElemIntoView()`

default | [When|*] /^(?:|I )scroll to the top$/
        | Scroll to top of page.
        | Adapted from: https://stackoverflow.com/questions/36647785
        | at `DmoreChromeDriverContext::scrollToTop()`

default | [When|*] /^(?:|I )scroll to the bottom$/
        | Scroll to bottom of page.
        | Adapted from: https://stackoverflow.com/questions/42982950
        | at `DmoreChromeDriverContext::scrollToBottom()`

default | [When|*] /^(?:|I )hover over(?: the|) (?<selector>[^"]*) "(?<locator>[^"]*)"$/
        | Hover the mouse over (link|button|field|element).
        | Example: When I hover over the link "Conditions"
        | Example: And I hover over button "submit"
        | Adapted from: https://stackoverflow.com/questions/18499851
        | at `DmoreChromeDriverContext::hoverOver()`

default | [Then|*] /^(?:the |)(?<selector>[^"]*) "(?<locator>[^"]*)" should be visible$/
        | Checks, that (link|button|field|element) is visible in the DOM.
        | Note: "element" refers to a css element.
        | Example: Then the link "Chronic Pain" should be visible
        | Example: And element "#Illi" should be visible .
        | Adapted from: https://stackoverflow.com/questions/19669786
        | at `DmoreChromeDriverContext::assertVisibleElement()`

default | [Then|*] /^(?:the |)(?<selector>[^"]*) "(?<locator>[^"]*)" should be hidden$/
        | Checks, that (link|button|field|element) is not visible in the DOM.
        | Note: "element" refers to a css element.
        | Example: Then the link "Chronic Pain" should be hidden
        | Example: And element "#Illi" should be hidden .
        | at `DmoreChromeDriverContext::assertHiddenElement()`

default | [Then|*] /^(?:|the )viewport should contain(?: the|) (?<selector>[^"]*) "(?<locator>[^"]*)"$/
        | Checks, that (link|button|field|element) is rendered in browser viewport.
        | Example: Then the viewport should contain the element "#Illi"
        | Example: And the viewport should contain button "Submit"
        | Adapted from:
        |   - https://stackoverflow.com/questions/123999
        |   - https://alfrednutile.info/posts/37
        |   - https://stackoverflow.com/questions/25494456
        | at `DmoreChromeDriverContext::assertViewportContainsElement()`

default | [Then|*] /^(?:|the )viewport should not contain(?: the|) (?<selector>[^"]*) "(?<locator>[^"]*)"$/
        | Checks, that (link|button|field|element) is not rendered in browser viewport.
        | Example: Then the viewport should not contain the element "#Illi"
        | Example: And the viewport should not contain button "Submit"
        | at `DmoreChromeDriverContext::assertViewportNotContainsElement()`

default | [Then|*] /^take a screenshot$/
        | Take a screenshot.
        | at `DmoreChromeDriverContext::assertScreenshot()`

default | [Then|*] /^I (?:|should )see (\d+) advertisement(?:|s) in the "(?<locator>[^"]*)"(?:| region)$/
        | Assert a number of advertisements are in a region.
        | Example: Then I should see 1 advertisement in the "right sidebar" region
        | Example: And I see 2 advertisements in the "right sidebar"
        | at `DmoreChromeDriverContext::assertGoogleAds()`

default | [Then|*] /^I (?:|should )see (\d+) advertisement(?:|s) of (width|height) "(\d+)(?:|px)" in the "(?<locator>[^"]*)"(?:| region)$/
        | Assert a number of advertisements are in a region at a specific size.
        | Example: Then I should see 1 advertisement of height "200" in the "right sidebar" region
        | Example: And I see 2 advertisements of width "300px" in the "right sidebar"
        | at `DmoreChromeDriverContext::assertDimensionedGoogleAds()`

default | [Then|*] /^(?:|the )JS property "(?<property>[^"]*)" should contain (?:|")(?<value>[^"]*)(?:|")$/
        | Checks that JS object with specified property contains specified substring.
        | Example: Then JS property "pbjs.adUnits[0].code" should contain "1006215"
        | at `DmoreChromeDriverContext::assertJsPropertyContainsSubstring()`

default | [Then|*] /^(?:|the )JS property "(?<property>[^"]*)" should (?:be set to|equal) (?:|")(?<value>[^"]*)(?:|")$/
        | Checks, that JS object with specified property equals specified value.
        | Example: Then the JS property "googletag.apiReady" should be set to "true"
        | Example: And JS property "PREBID_TIMEOUT" should equal "700"
        | at `DmoreChromeDriverContext::assertJsPropertyEqualsValue()`

default | [Then|*] /^(?:|the )JS property "(?<property>[^"]*)" should not (?:be set to|equal) (?:|")(?<value>[^"]*)(?:|")$/
        | Checks, that JS property does not equal specified value.
        | Example: Then JS property "googletag.apiReady" should not be set to "true"
        | Example: And the JS property "PREBID_TIMEOUT" should not equal 700
        | at `DmoreChromeDriverContext::assertJsPropertyNotEqualsValue()`

default | [Then|*] /^(?:|the )JS property "(?<property>[^"]*)" should be (greater|less) than (?:|")(?<value>[^"]*)(?:|")$/
        | Checks, that JS object with specified property greater/less than value.
        | Example: Then the JS property "PREBID_TIMEOUT" should be greater than 600
        | Example: And JS property "PREBID_TIMEOUT" should be less than "800"
        | at `DmoreChromeDriverContext::assertJsPropertyGreaterOrLessThanValue()`

default | [When|*] /^(?:|I )follow "(?P<link>(?:[^"]|\\")*)"$/
        | Clicks link with specified id|title|alt|text
        | Example: When I follow "Log In"
        | Example: And I follow "Log In"
        | at `DmoreChromeDriverContext::clickLink()`

default | [When|*] /^(?:|I )click the (\d+)(?:st|nd|rd|th) occurrence of "(?<link>[^"]*)"(?:| in the "(?<region>[^"]*)" region)$/
        | Clicks the nth matching link, optionally filtered by a region.
        | Example: When I click the 2nd occurrence of "Submit" in the "content" region
        | Example: And click the 4th occurrence of "click here"
        | 
        | 
        |   If region or text-based link within it cannot be found.
        | at `DmoreChromeDriverContext::assertNthLinkFollow()`

default | [Then|*] /^(?:|the )(?:|(\d+)(?:st|nd|rd|th) occurrence of )link "(?<link>[^"]*)"(?:| in the "(?<region>[^"]*)" region) should point to "(?<path>[^"]*)"$/
        | Checks, that nth matching link points to a path, optionally filtered by region.
        | Example: Then the link "Zimmer Biomet" should point to "cervicaldisc.com"
        | Example: Then link "Herniated Disc" in the "content" region should point to "conditions/herniated-disc"
        | Example: And the 2nd occurrence of link "Zimmer Biomet" should point to "https://www.cervicaldisc.com"
        | 
        | 
        |   If region or text-based link within it cannot be found.
        | at `DmoreChromeDriverContext::assertNthLinkPointsToPath()`

default | [Then|*] /^the (?:|(\d+)(?:st|nd|rd|th) occurrence of )link "(?<link>[^"]*)"(?:| in the "(?<region>[^"]*)" region) should have tag "(?<tag>[^"]*)" set to "(?<value>[^"]*)"$/
        | Checks, that nth matching link has html tag with specified value, optionally filtered by region.
        | Example: Then the link "Zimmer Biomet" should have tag "rel" set to "nofollow"
        | Example: And the 1st occurrence of link "Zimmer Biomet" should have tag "rel" set to "nofollow"
        | Example: And the 2nd occurrence of link "Zimmer Biomet" in the "content" region should have tag "rel" set to "nofollow"
        | 
        | 
        |   If region or text-based link within it cannot be found.
        | at `DmoreChromeDriverContext::assertNthLinkHasTagValue()`

default | [Given|*] I am at :path
        | Visit a given path, and additionally check for HTTP response code 200.
        | at `DmoreChromeDriverContext::assertAtPath()`

default | [When|*] I visit :path
        | Visit a given path, and additionally check for HTTP response code 200.
        | at `DmoreChromeDriverContext::assertAtPath()`

default | [When|*] I click :link
        | at `DmoreChromeDriverContext::assertClick()`

default | [Given|*] for :field I enter :value
        | at `DmoreChromeDriverContext::assertEnterField()`

default | [Given|*] I enter :value for :field
        | at `DmoreChromeDriverContext::assertEnterField()`

default | [Given|*] I wait for AJAX to finish
        | Wait for AJAX to finish.
        | at `DmoreChromeDriverContext::iWaitForAjaxToFinish()`

default | [When|*] /^(?:|I )press "(?P<button>(?:[^"]|\\")*)"$/
        | Presses button with specified id|name|title|alt|value
        | Example: When I press "Log In"
        | Example: And I press "Log In"
        | at `DmoreChromeDriverContext::pressButton()`

default | [When|*] I press the :button button
        | Presses button with specified id|name|title|alt|value.
        | at `DmoreChromeDriverContext::pressButton()`

default | [Given|*] I press the :char key in the :field field
        | at `DmoreChromeDriverContext::pressKey()`

default | [Then|*] I should see the link :link
        | at `DmoreChromeDriverContext::assertLinkVisible()`

default | [Then|*] I should not see the link :link
        | Links are not loaded on the page.
        | at `DmoreChromeDriverContext::assertNotLinkVisible()`

default | [Then|*] I should not visibly see the link :link
        | Links are loaded but not visually visible (e.g they have display: hidden applied).
        | at `DmoreChromeDriverContext::assertNotLinkVisuallyVisible()`

default | [Then|*] I (should )see the heading :heading
        | at `DmoreChromeDriverContext::assertHeading()`

default | [Then|*] I (should )not see the heading :heading
        | at `DmoreChromeDriverContext::assertNotHeading()`

default | [Then|*] I (should ) see the button :button
        | at `DmoreChromeDriverContext::assertButton()`

default | [Then|*] I (should ) see the :button button
        | at `DmoreChromeDriverContext::assertButton()`

default | [Then|*] I should not see the button :button
        | at `DmoreChromeDriverContext::assertNotButton()`

default | [Then|*] I should not see the :button button
        | at `DmoreChromeDriverContext::assertNotButton()`

default | [When|*] I follow/click :link in the :region( region)
        |   If region or link within it cannot be found.
        | at `DmoreChromeDriverContext::assertRegionLinkFollow()`

default | [Given|*] I press :button in the :region( region)
        | Checks, if a button with id|name|title|alt|value exists or not and pressess the same
        | 
        | 
        |   string The id|name|title|alt|value of the button to be pressed
        | 
        |   string The region in which the button should be pressed
        | 
        |   If region or button within it cannot be found.
        | at `DmoreChromeDriverContext::assertRegionPressButton()`

default | [Given|*] I fill in :value for :field in the :region( region)
        | Fills in a form field with id|name|title|alt|value in the specified region.
        | 
        | 
        | 
        |   If region cannot be found.
        | at `DmoreChromeDriverContext::regionFillField()`

default | [Given|*] I fill in :field with :value in the :region( region)
        | Fills in a form field with id|name|title|alt|value in the specified region.
        | 
        | 
        | 
        |   If region cannot be found.
        | at `DmoreChromeDriverContext::regionFillField()`

default | [Then|*] I should see the heading :heading in the :region( region)
        | Find a heading in a specific region.
        | 
        | 
        | 
        |   If region or header within it cannot be found.
        | at `DmoreChromeDriverContext::assertRegionHeading()`

default | [Then|*] I should see the :heading heading in the :region( region)
        | Find a heading in a specific region.
        | 
        | 
        | 
        |   If region or header within it cannot be found.
        | at `DmoreChromeDriverContext::assertRegionHeading()`

default | [Then|*] I should see the link :link in the :region( region)
        |   If region or link within it cannot be found.
        | at `DmoreChromeDriverContext::assertLinkRegion()`

default | [Then|*] I should not see the link :link in the :region( region)
        |   If region or link within it cannot be found.
        | at `DmoreChromeDriverContext::assertNotLinkRegion()`

default | [Then|*] I should see( the text) :text in the :region( region)
        |   If region or text within it cannot be found.
        | at `DmoreChromeDriverContext::assertRegionText()`

default | [Then|*] I should not see( the text) :text in the :region( region)
        |   If region or text within it cannot be found.
        | at `DmoreChromeDriverContext::assertNotRegionText()`

default | [Then|*] I (should )see the text :text
        | at `DmoreChromeDriverContext::assertTextVisible()`

default | [Then|*] I should not see the text :text
        | at `DmoreChromeDriverContext::assertNotTextVisible()`

default | [Then|*] I should get a :code HTTP response
        | at `DmoreChromeDriverContext::assertHttpResponse()`

default | [Then|*] I should not get a :code HTTP response
        | at `DmoreChromeDriverContext::assertNotHttpResponse()`

default | [Given|*] I check the box :checkbox
        | at `DmoreChromeDriverContext::assertCheckBox()`

default | [Given|*] I uncheck the box :checkbox
        | at `DmoreChromeDriverContext::assertUncheckBox()`

default | [When|*] I select the radio button :label with the id :id
        | at `DmoreChromeDriverContext::assertSelectRadioById()`

default | [When|*] I select the radio button :label
        | at `DmoreChromeDriverContext::assertSelectRadioById()`

default | [Given|*] /^(?:|I )am on (?:|the )homepage$/
        | Opens homepage
        | Example: Given I am on "/"
        | Example: When I go to "/"
        | Example: And I go to "/"
        | at `DmoreChromeDriverContext::iAmOnHomepage()`

default | [When|*] /^(?:|I )go to (?:|the )homepage$/
        | Opens homepage
        | Example: Given I am on "/"
        | Example: When I go to "/"
        | Example: And I go to "/"
        | at `DmoreChromeDriverContext::iAmOnHomepage()`

default | [Given|*] /^(?:|I )am on "(?P<page>[^"]+)"$/
        | Opens specified page
        | Example: Given I am on "http://batman.com"
        | Example: And I am on "/articles/isBatmanBruceWayne"
        | Example: When I go to "/articles/isBatmanBruceWayne"
        | at `DmoreChromeDriverContext::visit()`

default | [When|*] /^(?:|I )go to "(?P<page>[^"]+)"$/
        | Opens specified page
        | Example: Given I am on "http://batman.com"
        | Example: And I am on "/articles/isBatmanBruceWayne"
        | Example: When I go to "/articles/isBatmanBruceWayne"
        | at `DmoreChromeDriverContext::visit()`

default | [When|*] /^(?:|I )reload the page$/
        | Reloads current page
        | Example: When I reload the page
        | Example: And I reload the page
        | at `DmoreChromeDriverContext::reload()`

default | [When|*] /^(?:|I )move backward one page$/
        | Moves backward one page in history
        | Example: When I move backward one page
        | at `DmoreChromeDriverContext::back()`

default | [When|*] /^(?:|I )move forward one page$/
        | Moves forward one page in history
        | Example: And I move forward one page
        | at `DmoreChromeDriverContext::forward()`

default | [When|*] /^(?:|I )fill in "(?P<field>(?:[^"]|\\")*)" with "(?P<value>(?:[^"]|\\")*)"$/
        | Fills in form field with specified id|name|label|value
        | Example: When I fill in "username" with: "bwayne"
        | Example: And I fill in "bwayne" for "username"
        | at `DmoreChromeDriverContext::fillField()`

default | [When|*] /^(?:|I )fill in "(?P<field>(?:[^"]|\\")*)" with:$/
        | Fills in form field with specified id|name|label|value
        | Example: When I fill in "username" with: "bwayne"
        | Example: And I fill in "bwayne" for "username"
        | at `DmoreChromeDriverContext::fillField()`

default | [When|*] /^(?:|I )fill in "(?P<value>(?:[^"]|\\")*)" for "(?P<field>(?:[^"]|\\")*)"$/
        | Fills in form field with specified id|name|label|value
        | Example: When I fill in "username" with: "bwayne"
        | Example: And I fill in "bwayne" for "username"
        | at `DmoreChromeDriverContext::fillField()`

default | [When|*] /^(?:|I )fill in the following:$/
        | Fills in form fields with provided table
        | Example: When I fill in the following"
        |              | username | bruceWayne |
        |              | password | iLoveBats123 |
        | Example: And I fill in the following"
        |              | username | bruceWayne |
        |              | password | iLoveBats123 |
        | at `DmoreChromeDriverContext::fillFields()`

default | [When|*] /^(?:|I )select "(?P<option>(?:[^"]|\\")*)" from "(?P<select>(?:[^"]|\\")*)"$/
        | Selects option in select field with specified id|name|label|value
        | Example: When I select "Bats" from "user_fears"
        | Example: And I select "Bats" from "user_fears"
        | at `DmoreChromeDriverContext::selectOption()`

default | [When|*] /^(?:|I )additionally select "(?P<option>(?:[^"]|\\")*)" from "(?P<select>(?:[^"]|\\")*)"$/
        | Selects additional option in select field with specified id|name|label|value
        | Example: When I additionally select "Deceased" from "parents_alive_status"
        | Example: And I additionally select "Deceased" from "parents_alive_status"
        | at `DmoreChromeDriverContext::additionallySelectOption()`

default | [When|*] /^(?:|I )check "(?P<option>(?:[^"]|\\")*)"$/
        | Checks checkbox with specified id|name|label|value
        | Example: When I check "Pearl Necklace"
        | Example: And I check "Pearl Necklace"
        | at `DmoreChromeDriverContext::checkOption()`

default | [When|*] /^(?:|I )uncheck "(?P<option>(?:[^"]|\\")*)"$/
        | Unchecks checkbox with specified id|name|label|value
        | Example: When I uncheck "Broadway Plays"
        | Example: And I uncheck "Broadway Plays"
        | at `DmoreChromeDriverContext::uncheckOption()`

default | [When|*] /^(?:|I )attach the file "(?P<path>[^"]*)" to "(?P<field>(?:[^"]|\\")*)"$/
        | Attaches file to field with specified id|name|label|value
        | Example: When I attach "bwayne_profile.png" to "profileImageUpload"
        | Example: And I attach "bwayne_profile.png" to "profileImageUpload"
        | at `DmoreChromeDriverContext::attachFileToField()`

default | [Then|*] /^(?:|I )should be on "(?P<page>[^"]+)"$/
        | Checks, that current page PATH is equal to specified
        | Example: Then I should be on "/"
        | Example: And I should be on "/bats"
        | Example: And I should be on "http://google.com"
        | at `DmoreChromeDriverContext::assertPageAddress()`

default | [Then|*] /^(?:|I )should be on (?:|the )homepage$/
        | Checks, that current page is the homepage
        | Example: Then I should be on the homepage
        | Example: And I should be on the homepage
        | at `DmoreChromeDriverContext::assertHomepage()`

default | [Then|*] /^the (?i)url(?-i) should match (?P<pattern>"(?:[^"]|\\")*")$/
        | Checks, that current page PATH matches regular expression
        | Example: Then the url should match "superman is dead"
        | Example: Then the uri should match "log in"
        | Example: And the url should match "log in"
        | at `DmoreChromeDriverContext::assertUrlRegExp()`

default | [Then|*] /^the response status code should be (?P<code>\d+)$/
        | Checks, that current page response status is equal to specified
        | Example: Then the response status code should be 200
        | Example: And the response status code should be 400
        | at `DmoreChromeDriverContext::assertResponseStatus()`

default | [Then|*] /^the response status code should not be (?P<code>\d+)$/
        | Checks, that current page response status is not equal to specified
        | Example: Then the response status code should not be 501
        | Example: And the response status code should not be 404
        | at `DmoreChromeDriverContext::assertResponseStatusIsNot()`

default | [Then|*] /^(?:|I )should see "(?P<text>(?:[^"]|\\")*)"$/
        | Checks, that page contains specified text
        | Example: Then I should see "Who is the Batman?"
        | Example: And I should see "Who is the Batman?"
        | at `DmoreChromeDriverContext::assertPageContainsText()`

default | [Then|*] /^(?:|I )should not see "(?P<text>(?:[^"]|\\")*)"$/
        | Checks, that page doesn't contain specified text
        | Example: Then I should not see "Batman is Bruce Wayne"
        | Example: And I should not see "Batman is Bruce Wayne"
        | at `DmoreChromeDriverContext::assertPageNotContainsText()`

default | [Then|*] /^(?:|I )should see text matching (?P<pattern>"(?:[^"]|\\")*")$/
        | Checks, that page contains text matching specified pattern
        | Example: Then I should see text matching "Batman, the vigilante"
        | Example: And I should not see "Batman, the vigilante"
        | at `DmoreChromeDriverContext::assertPageMatchesText()`

default | [Then|*] /^(?:|I )should not see text matching (?P<pattern>"(?:[^"]|\\")*")$/
        | Checks, that page doesn't contain text matching specified pattern
        | Example: Then I should see text matching "Bruce Wayne, the vigilante"
        | Example: And I should not see "Bruce Wayne, the vigilante"
        | at `DmoreChromeDriverContext::assertPageNotMatchesText()`

default | [Then|*] /^the response should contain "(?P<text>(?:[^"]|\\")*)"$/
        | Checks, that HTML response contains specified string
        | Example: Then the response should contain "Batman is the hero Gotham deserves."
        | Example: And the response should contain "Batman is the hero Gotham deserves."
        | at `DmoreChromeDriverContext::assertResponseContains()`

default | [Then|*] /^the response should not contain "(?P<text>(?:[^"]|\\")*)"$/
        | Checks, that HTML response doesn't contain specified string
        | Example: Then the response should not contain "Bruce Wayne is a billionaire, play-boy, vigilante."
        | Example: And the response should not contain "Bruce Wayne is a billionaire, play-boy, vigilante."
        | at `DmoreChromeDriverContext::assertResponseNotContains()`

default | [Then|*] /^(?:|I )should see "(?P<text>(?:[^"]|\\")*)" in the "(?P<element>[^"]*)" element$/
        | Checks, that element with specified CSS contains specified text
        | Example: Then I should see "Batman" in the "heroes_list" element
        | Example: And I should see "Batman" in the "heroes_list" element
        | at `DmoreChromeDriverContext::assertElementContainsText()`

default | [Then|*] /^(?:|I )should not see "(?P<text>(?:[^"]|\\")*)" in the "(?P<element>[^"]*)" element$/
        | Checks, that element with specified CSS doesn't contain specified text
        | Example: Then I should not see "Bruce Wayne" in the "heroes_alter_egos" element
        | Example: And I should not see "Bruce Wayne" in the "heroes_alter_egos" element
        | at `DmoreChromeDriverContext::assertElementNotContainsText()`

default | [Then|*] /^the "(?P<element>[^"]*)" element should contain "(?P<value>(?:[^"]|\\")*)"$/
        | Checks, that element with specified CSS contains specified HTML
        | Example: Then the "body" element should contain "style=\"color:black;\""
        | Example: And the "body" element should contain "style=\"color:black;\""
        | at `DmoreChromeDriverContext::assertElementContains()`

default | [Then|*] /^the "(?P<element>[^"]*)" element should not contain "(?P<value>(?:[^"]|\\")*)"$/
        | Checks, that element with specified CSS doesn't contain specified HTML
        | Example: Then the "body" element should not contain "style=\"color:black;\""
        | Example: And the "body" element should not contain "style=\"color:black;\""
        | at `DmoreChromeDriverContext::assertElementNotContains()`

default | [Then|*] /^(?:|I )should see an? "(?P<element>[^"]*)" element$/
        | Checks, that element with specified CSS exists on page
        | Example: Then I should see a "body" element
        | Example: And I should see a "body" element
        | at `DmoreChromeDriverContext::assertElementOnPage()`

default | [Then|*] /^(?:|I )should not see an? "(?P<element>[^"]*)" element$/
        | Checks, that element with specified CSS doesn't exist on page
        | Example: Then I should not see a "canvas" element
        | Example: And I should not see a "canvas" element
        | at `DmoreChromeDriverContext::assertElementNotOnPage()`

default | [Then|*] /^the "(?P<field>(?:[^"]|\\")*)" field should contain "(?P<value>(?:[^"]|\\")*)"$/
        | Checks, that form field with specified id|name|label|value has specified value
        | Example: Then the "username" field should contain "bwayne"
        | Example: And the "username" field should contain "bwayne"
        | at `DmoreChromeDriverContext::assertFieldContains()`

default | [Then|*] /^the "(?P<field>(?:[^"]|\\")*)" field should not contain "(?P<value>(?:[^"]|\\")*)"$/
        | Checks, that form field with specified id|name|label|value doesn't have specified value
        | Example: Then the "username" field should not contain "batman"
        | Example: And the "username" field should not contain "batman"
        | at `DmoreChromeDriverContext::assertFieldNotContains()`

default | [Then|*] /^(?:|I )should see (?P<num>\d+) "(?P<element>[^"]*)" elements?$/
        | Checks, that (?P<num>\d+) CSS elements exist on the page
        | Example: Then I should see 5 "div" elements
        | Example: And I should see 5 "div" elements
        | at `DmoreChromeDriverContext::assertNumElements()`

default | [Then|*] /^the "(?P<checkbox>(?:[^"]|\\")*)" checkbox should be checked$/
        | Checks, that checkbox with specified id|name|label|value is checked
        | Example: Then the "remember_me" checkbox should be checked
        | Example: And the "remember_me" checkbox is checked
        | at `DmoreChromeDriverContext::assertCheckboxChecked()`

default | [Then|*] /^the "(?P<checkbox>(?:[^"]|\\")*)" checkbox is checked$/
        | Checks, that checkbox with specified id|name|label|value is checked
        | Example: Then the "remember_me" checkbox should be checked
        | Example: And the "remember_me" checkbox is checked
        | at `DmoreChromeDriverContext::assertCheckboxChecked()`

default | [Then|*] /^the checkbox "(?P<checkbox>(?:[^"]|\\")*)" (?:is|should be) checked$/
        | Checks, that checkbox with specified id|name|label|value is checked
        | Example: Then the "remember_me" checkbox should be checked
        | Example: And the "remember_me" checkbox is checked
        | at `DmoreChromeDriverContext::assertCheckboxChecked()`

default | [Then|*] /^the "(?P<checkbox>(?:[^"]|\\")*)" checkbox should (?:be unchecked|not be checked)$/
        | Checks, that checkbox with specified id|name|label|value is unchecked
        | Example: Then the "newsletter" checkbox should be unchecked
        | Example: Then the "newsletter" checkbox should not be checked
        | Example: And the "newsletter" checkbox is unchecked
        | at `DmoreChromeDriverContext::assertCheckboxNotChecked()`

default | [Then|*] /^the "(?P<checkbox>(?:[^"]|\\")*)" checkbox is (?:unchecked|not checked)$/
        | Checks, that checkbox with specified id|name|label|value is unchecked
        | Example: Then the "newsletter" checkbox should be unchecked
        | Example: Then the "newsletter" checkbox should not be checked
        | Example: And the "newsletter" checkbox is unchecked
        | at `DmoreChromeDriverContext::assertCheckboxNotChecked()`

default | [Then|*] /^the checkbox "(?P<checkbox>(?:[^"]|\\")*)" should (?:be unchecked|not be checked)$/
        | Checks, that checkbox with specified id|name|label|value is unchecked
        | Example: Then the "newsletter" checkbox should be unchecked
        | Example: Then the "newsletter" checkbox should not be checked
        | Example: And the "newsletter" checkbox is unchecked
        | at `DmoreChromeDriverContext::assertCheckboxNotChecked()`

default | [Then|*] /^the checkbox "(?P<checkbox>(?:[^"]|\\")*)" is (?:unchecked|not checked)$/
        | Checks, that checkbox with specified id|name|label|value is unchecked
        | Example: Then the "newsletter" checkbox should be unchecked
        | Example: Then the "newsletter" checkbox should not be checked
        | Example: And the "newsletter" checkbox is unchecked
        | at `DmoreChromeDriverContext::assertCheckboxNotChecked()`

default | [Then|*] /^print current URL$/
        | Prints current URL to console.
        | Example: Then print current URL
        | Example: And print current URL
        | at `DmoreChromeDriverContext::printCurrentUrl()`

default | [Then|*] /^print last response$/
        | Prints last response to console
        | Example: Then print last response
        | Example: And print last response
        | at `DmoreChromeDriverContext::printLastResponse()`

default | [Then|*] /^show last response$/
        | Opens last response content in browser
        | Example: Then show last response
        | Example: And show last response
        | at `DmoreChromeDriverContext::showLastResponse()`

default | [Then|*] I should see the error message( containing) :message
        | Checks if the current page contains the given error message
        | 
        |   string The text to be checked
        | at `Drupal\DrupalExtension\Context\MessageContext::assertErrorVisible()`

default | [Then|*] I should see the following error message(s):
        | Checks if the current page contains the given set of error messages
        | 
        |   array An array of texts to be checked
        | at `Drupal\DrupalExtension\Context\MessageContext::assertMultipleErrors()`

default | [Given|*] I should not see the error message( containing) :message
        | Checks if the current page does not contain the given error message
        | 
        |   string The text to be checked
        | at `Drupal\DrupalExtension\Context\MessageContext::assertNotErrorVisible()`

default | [Then|*] I should not see the following error messages:
        | Checks if the current page does not contain the given set error messages
        | 
        |   array An array of texts to be checked
        | at `Drupal\DrupalExtension\Context\MessageContext::assertNotMultipleErrors()`

default | [Then|*] I should see the success message( containing) :message
        | Checks if the current page contains the given success message
        | 
        |   string The text to be checked
        | at `Drupal\DrupalExtension\Context\MessageContext::assertSuccessMessage()`

default | [Then|*] I should see the following success messages:
        | Checks if the current page contains the given set of success messages
        | 
        |   array An array of texts to be checked
        | at `Drupal\DrupalExtension\Context\MessageContext::assertMultipleSuccessMessage()`

default | [Given|*] I should not see the success message( containing) :message
        | Checks if the current page does not contain the given set of success message
        | 
        |   string The text to be checked
        | at `Drupal\DrupalExtension\Context\MessageContext::assertNotSuccessMessage()`

default | [Then|*] I should not see the following success messages:
        | Checks if the current page does not contain the given set of success messages
        | 
        |   array An array of texts to be checked
        | at `Drupal\DrupalExtension\Context\MessageContext::assertNotMultipleSuccessMessage()`

default | [Then|*] I should see the warning message( containing) :message
        | Checks if the current page contains the given warning message
        | 
        |   string The text to be checked
        | at `Drupal\DrupalExtension\Context\MessageContext::assertWarningMessage()`

default | [Then|*] I should see the following warning messages:
        | Checks if the current page contains the given set of warning messages
        | 
        |   array An array of texts to be checked
        | at `Drupal\DrupalExtension\Context\MessageContext::assertMultipleWarningMessage()`

default | [Given|*] I should not see the warning message( containing) :message
        | Checks if the current page does not contain the given set of warning message
        | 
        |   string The text to be checked
        | at `Drupal\DrupalExtension\Context\MessageContext::assertNotWarningMessage()`

default | [Then|*] I should not see the following warning messages:
        | Checks if the current page does not contain the given set of warning messages
        | 
        |   array An array of texts to be checked
        | at `Drupal\DrupalExtension\Context\MessageContext::assertNotMultipleWarningMessage()`

default | [Then|*] I should see the message( containing) :message
        | Checks if the current page contain the given message
        | 
        |   string The message to be checked
        | at `Drupal\DrupalExtension\Context\MessageContext::assertMessage()`

default | [Then|*] I should not see the message( containing) :message
        | Checks if the current page does not contain the given message
        | 
        |   string The message to be checked
        | at `Drupal\DrupalExtension\Context\MessageContext::assertNotMessage()`

default | [Then|*] I should see the button :button in the :region( region)
        | Checks if a button with id|name|title|alt|value exists in a region
        | 
        | 
        | 
        |   string The id|name|title|alt|value of the button
        | 
        |   string The region in which the button should be found
        | 
        |   If region or button within it cannot be found.
        | at `Drupal\DrupalExtension\Context\MarkupContext::assertRegionButton()`

default | [Then|*] I should see the :button button in the :region( region)
        | Checks if a button with id|name|title|alt|value exists in a region
        | 
        | 
        | 
        |   string The id|name|title|alt|value of the button
        | 
        |   string The region in which the button should be found
        | 
        |   If region or button within it cannot be found.
        | at `Drupal\DrupalExtension\Context\MarkupContext::assertRegionButton()`

default | [Then|*] I( should) see the :tag element in the :region( region)
        | at `Drupal\DrupalExtension\Context\MarkupContext::assertRegionElement()`

default | [Then|*] I( should) not see the :tag element in the :region( region)
        | at `Drupal\DrupalExtension\Context\MarkupContext::assertNotRegionElement()`

default | [Then|*] I( should) not see :text in the :tag element in the :region( region)
        | at `Drupal\DrupalExtension\Context\MarkupContext::assertNotRegionElementText()`

default | [Then|*] I( should) see the :tag element with the :attribute attribute set to :value in the :region( region)
        | at `Drupal\DrupalExtension\Context\MarkupContext::assertRegionElementAttribute()`

default | [Then|*] I( should) see :text in the :tag element with the :attribute attribute set to :value in the :region( region)
        | at `Drupal\DrupalExtension\Context\MarkupContext::assertRegionElementTextAttribute()`

default | [Then|*] I( should) see :text in the :tag element with the :property CSS property set to :value in the :region( region)
        | at `Drupal\DrupalExtension\Context\MarkupContext::assertRegionElementTextCss()`
```
