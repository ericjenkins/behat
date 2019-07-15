<?php

/**
 * @file
 * Chrome web driver context.
 */

use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\Testwork\Hook\Scope\BeforeSuiteScope;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use Behat\Testwork\Hook\Scope\AfterSuiteScope;
use Behat\Behat\Hook\Scope\AfterStepScope;
use DMore\ChromeDriver\ChromeDriver;

/**
 * Define a Mink Subcontext.
 */
class DmoreChromeDriverContext extends JsBrowserContext implements SnippetAcceptingContext {

  /**
   * Javascript Warnings counter.
   *
   * @var int
   */
  protected static $jsWarnings = array();

  /**
   * Javascript Errors counter.
   *
   * @var int
   */
  protected static $jsErrors = array();

  /**
   * Starts Chrome in Remote Debug Mode for UI tests.
   *
   * Adapted from: https://github.com/bnowack/backbone-php/tree/master/test/behat/
   *
   * @BeforeSuite
   */
  public static function startWebServer(BeforeSuiteScope $scope) {
    $suite = $scope->getSuite();

    if ($suite->hasSetting('chrome_path')) {
      $chrome_path = $suite->getSetting('chrome_path');
      $chrome_headless = $suite->getSetting('chrome_headless');
      $port = 9222;
      $host = 'localhost';
      $headless = $chrome_headless ? "--headless" : "";

      // Launch if not already up.
      if (!self::serverIsUp($host, $port)) {
        $command = "{$chrome_path} --no-first-run --no-default-browser-check --user-data-dir=/tmp/chrome-remote-profile --remote-debugging-address=0.0.0.0 --remote-debugging-port={$port} --disable-gpu --disable-extensions --window-size='1600,900' {$headless} >/dev/null 2>&1 & echo \$!";
        $output = trim(shell_exec($command));
        self::$webDriverPid = is_numeric($output) ? intval($output) : NULL;
      }
      // Check that the server is running, wait up to 2 seconds.
      $attempts = 0;
      do {
        $up = self::serverIsUp($host, $port);
        $attempts++;
        usleep(100000);
        // 0.1 sec.
      } while (!$up && $attempts < 20);
      if (!$up) {
        self::stopProcess(self::$webDriverPid);
        // Just in case it *did* start but did not respond in time.
        throw new \RuntimeException("Could not start web server at $host:$port");
      }
    }
  }

  /**
   * Sets the user agent and resolution for ChromeDriver by Dmore.
   *
   * Overrides JsBrowserContext::prepareBrowser()
   *
   * ChromeDriver by Dmore has no built-in mechanism to define a mobile device
   * browser, but it does provide a mechanism to alter the user agent.
   * So, this method provides a default user-agent header.
   *
   * @BeforeScenario
   */
  public function prepareBrowser(BeforeScenarioScope $scope) {
    $driver = $this->getSession()->GetDriver();
    $suite = $scope->getSuite();
    $useragent = $suite->getSetting('chrome_useragent');

    $driver->setRequestHeader('user-agent', $useragent);
    parent::prepareBrowser($scope);
  }

  /**
   * Get the console logs.
   *
   * - Use tag @nojswarnings to ignore JS warnings.
   * - Use tag @nojserrors to ignore all JS errors & warnings.
   *
   * @AfterStep
   */
  public function getCapturedConsoleLogs(AfterStepScope $scope) {
    $tags = array_merge($scope->getFeature()->getTags(), $this->getScenario($scope)->getTags());

    // Bypass all JS message checking if the scenario has the @nojserrors tag.
    if (in_array('nojserrors', $tags)) {
      return;
    }

    if ($consoleErrors = $this->getSession()->evaluateScript("window.consoleErrors")) {
      foreach ($consoleErrors as $error) {
        print "JS Error: " . $error;
        self::$jsErrors[] = $scope->getFeature()->getFile() . ":" . $scope->getStep()->getLine();
      }
      // Reset the consoleErrors array to prepare for the next step.
      $this->getSession()->executeScript("window.consoleErrors = [];");
    }


    // If the scenario has the @nojswarnings tag, return now.
    if (in_array('nojswarnings', $tags)) {
      return;
    }

    if ($consoleWarnings = $this->getSession()->evaluateScript("window.consoleWarnings")) {
      foreach ($consoleWarnings as $warning) {
        print "JS Warning: " . $warning;
        self::$jsWarnings[] = $scope->getFeature()->getFile() . ":" . $scope->getStep()->getLine();
      }
      // Reset the consoleWarnings array to prepare for the next step.
      $this->getSession()->executeScript("window.consoleWarnings = [];");
    }
  }

  /**
   * Report any recorded Javascript events.
   *
   * @param Behat\Behat\Hook\Scope\AfterSuiteScope $scope
   *   After Suite hook scope.
   *
   * @AfterSuite
   */
  public static function reportJsEvents(AfterSuiteScope $scope) {
    if (!empty(self::$jsWarnings) && !empty(self::$jsErrors)) {
      echo(sprintf("Javascript warnings & errors thrown during scenarios:\n\n%s\n\n",
        implode("\n", array_unique(array_merge(self::$jsWarnings, self::$jsErrors)))));
    }
    elseif (!empty(self::$jsWarnings)) {
      echo(sprintf("Javascript warnings thrown during scenarios:\n\n%s",
        implode("\n", array_unique(self::$jsWarnings))));
    }
    elseif (!empty(self::$jsErrors)) {
      echo(sprintf("Javascript errors thrown during scenarios:\n\n%s\n\n",
        implode("\n", array_unique(self::$jsErrors))));
    }
  }

  /**
   * Overrides PhpBrowserContext::assertAtPath() to add in console log tracking.
   *
   *  References:
   *  - https://github.com/minkphp/MinkSelenium2Driver/issues/189
   *  - https://gist.github.com/amitaibu/ba6b78e24c315a7f5e3c/
   */
  public function assertAtPath($path) {
    parent::assertAtPath($path);
    $script = <<<JS
  window.consoleErrors = [];
  window.consoleWarnings = [];

  if (window.console && console.error) {
    var old = console.error;
    console.error = function() {
      window.consoleErrors.push("'" + arguments[0] + "'\\nCaller:\\n" + arguments.callee.caller + "\\nTrace:\\n" + arguments.trace);
      old.apply(this, arguments);
    }
  }

  if (window.console && console.warn) {
    var old = console.warn;
    console.warn = function() {
      window.consoleWarnings.push("'" + arguments[0] + "'\\nCaller:\\n" + arguments.callee.caller + "\\nTrace:\\n" + arguments.trace);
      old.apply(this, arguments);
    }
  }
JS;
    $this->getSession()->evaluateScript($script);
  }

  /**
   * Stops the web driver.
   *
   * Overrides JsBrowserContext::stopWebDriver() to delete temporary Chrome
   * profile directory.
   */
  public static function stopWebDriver() {
    parent::stopWebDriver();
    trim(shell_exec("rm -rfv /tmp/chrome-remote-profile 2>&1"));
  }

  /**
   * Step definition for setting browser window size.
   *
   * Overrides JsBrowserContext::setBrowserWindowSizeToWxH() because it only
   * supports Selenium2.
   */
  public function setBrowserWindowSizeToWxH($width, $height) {
    $this->getSession()->GetDriver()->resizeWindow((int) $width, (int) $height);
  }

}
