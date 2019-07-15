# Behat Drivers

These drivers have been tested to work on Eric's Ubuntu laptop. Each driver has
its own limitations.

## Blackbox (default)

The default driver, Blackbox, is the simplest driver for feature tests. It
assumes no privileged access to the site and it does not use a graphic web
browser. Blackbox tests cannot interpret JavaScript and so cannot fully render
what a page is meant to look like in a typical web browser. However, it is great
for asserting that the correct PHP and HTML code is delivered from the server
to the client. Example of a simple Blackbox feature test:

```gherkin
Scenario: Test the ability to find a heading in a region
  Given I am on the homepage
  When I click "Conditions"
  Then I should see the heading "Pain Conditions Health Center" in the "content" region
```

## JavaScript

Javascript tests provide a mechanism for actually testing what a page will look
like in a graphic web browser, including anything JavaScript might do to
manipulate the page's contents.

JavaScript tests require a web browser driver running either on your local
computer or as a connectable service on a remote server.

Example of a Javascript-based feature test:

```gherkin
@javascript @desktop
Scenario: Test the ability to find a heading in a region
  Given I am on the homepage
  When I click "Conditions"
  And wait 2 seconds
  And take a screenshot
  Then I should see the heading "Pain Conditions Health Center" in the "content" region
```

* The `@desktop` tag is configured by this project's `behat.yml` file to act as
  a filter which will only run when the `destkop` profile is specified as a
  commandline directive. Available JS browser tags include:
  * `@desktop` - PhantomJS-based desktop browser
  * `@mobile` - PhantomJS-based mobile browser
  * `@chrome` - ChromeDriver-based desktop browser
  * `@android` - ChromeDriver-based mobile browser

When ChromeDriver is used to test Javascript-based features, Behat will
additionally check for and report any JS console warnings and errors.
* To ignore JS warnings, add the `@nojswarnings` tag.
* To ignore all JS messages, add the `@nojserrors` tag.

## Drush

The Drush driver provides a non-graphical way to create temporary content and to
log into the site via ssh, bypassing the CAPTCHA prompt. This Drush driver can
operate in tandem with either the non-graphical Blackbox driver or with the
graphical Javascript driver.

To use the Drush driver, add the `@api` tag to the Feature or Scenario
defintion. In addition to activating Drush-based testing, the `@api` tag also
activates Watchdog log monitoring.
- To ignore Watchdog warnings and notices monitoring, add the `@nowarnings` tag.
- To ignore all Watchdog messages, add the `@noerrors` tag.

Example of a Drush-based feature test:

```gherkin
@api @nowarnings  # Activate Drush API but ignore Watchdog warnings & notices.
Scenario: Create and log in as a sub-admin
  Given I am logged in as a user with the "sub-admin" role
  When I am at "user"
  Then I should see the heading "History"
```
