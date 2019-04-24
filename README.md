# PHP-based automated testing with Behat, Mink, and similar tools

## Feature Tests with Behat & Mink

*What do Behat and Mink Do?*

Behat and Mink allow you to describe the behavior of a web site in plain, but
stylized language, and then turn that description into an automated test that
will visit the site and perform each step you describe.

*What does the Drupal Extension add?*

The Drupal Extension to Behat and Mink assists in the performance of these
common Drupal testing tasks:
* Set up test data with Drush or the Drupal API
* Define theme regions and test data appears within them
* Clear the cache, log out, and other useful steps
* Detect and discover steps provided by contributed modules and themes

### Behat Drivers

The following drivers have been tested to work with behat feature tests for this
project:
- Blackbox (php-based headless browser)
- Drush
- Javascript

Refer to [BehatDrivers.md][] for an overview of drivers that are available
for writing and running automated Behat tests.

#### Using Chrome to drive JS Tests

Google Chrome provides built-in commandline switches which allow for remote
automated JS-based behavioral testing. Chrome can also be run in a *headless*
state, meaning it can run in the background.

##### Windows

Running Chrome with remote-debugging enabled in Windows:

```
chrome --no-first-run --no-default-browser-check --user-data-dir=remote-profile --remote-debugging-address=0.0.0.0 --remote-debugging-port=9222 --disable-gpu --window-size="1280,720" --disable-extensions
```

Running Chrome with remote-debugging enabled in Ubuntu:

```sh
google-chrome --no-first-run --no-default-browser-check --user-data-dir=remote-profile --remote-debugging-address=0.0.0.0 --remote-debugging-port=9222 --disable-gpu --window-size="1280,720" --disable-extensions
```

Running behat:

```sh
# Run all desktop tests:
vendor\bin\behat -p chrome
# Run all mobile device tests:
vendor\bin\behat -p android
```

#### Using PhantomJS to drive JS Tests

PhantomJS is a *headless* browser, which can be used in place of Chrome for
testing on cloud servers that do not otherwise have a graphical interface.

Running PhantomJS in Windows:

```
phantomjs --webdriver= --ignore-ssl-errors=true
```

### Getting Started

Refer to [InitialSetup.md][] for instructions on setting up your automated
testing environment.

#### Running Behat from inside Vagrant

Behat can be run from your local command line if PHP is installed.
Alternatively, Behat can be run from inside the Vagrant box.

```sh
vagrant ssh -- -R 9222:localhost:9222
cd [path to testing-suite probject]
```

To run a specific feature test:
* `vendor/bin/behat features/javascriptDriver.feature`

To run a suite of tests simulating a specified web browser:
* `vendor/bin/behat  # No profile selected will default to Blackbox PHP tests`
* `vendor/bin/behat -p desktop  # Run JS tests simulating Chrome desktop browser`
* `vendor/bin/behat -p mobile  # Run JS tests simulating Android mobile device`


### Writing New Behat Tests

Behat tests are located in this repo under `/features`.

To see a list of all possible step definitions, refer to
[StepDefinitions.md][], or run:

```sh
vendor/bin/behat -dl  # Just list definition expressions.
vendor/bin/behat -di  # Show definitions with extended info.
```

#### Behat Limitations

* Following external links causes Behat to crash, because it cannot correctly
  open new windows/tabs.
    * Instead of following a link, check for the presence of the link and verify
      that its html points to the correct path. Example:

        ```gherkin
        Given I am at "education-centers"
        Then the 2nd occurrence of link "Zimmer Biomet" in the "content" region should point to "https://www.cervicaldisc.com"
        And the 2nd occurrence of link "Zimmer Biomet" should have tag "rel" set to "nofollow"
        ```

## Further Reading

Many thanks to @YesCT for the source code that helped me create some of these custom tools!

* [The Drupal Extension to Behat and Mink documentation][1]

[1]: https://behat-drupal-extension.readthedocs.io
[BehatDrivers.md]: wiki/BehatDrivers.md
[InitialSetup.md]: wiki/InitialSetup.md
[StepDefinitions.md]: wiki/StepDefinitions.md
