<?php

/**
 * @file
 * PhantomJS web driver context.
 */

use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\Testwork\Hook\Scope\BeforeSuiteScope;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use DMore\ChromeDriver\ChromeDriver;

/**
 * Define a Mink Subcontext.
 */
class DmoreChromeDriverContext extends JsBrowserContext implements SnippetAcceptingContext {

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
   * Overrides JsBrowserContext::setBrowserWindowSizeToWxH() because PhantomJS
   * does not support resizing browser viewport.
   */
  public function setBrowserWindowSizeToWxH($width, $height) {
    $this->getSession()->GetDriver()->resizeWindow((int) $width, (int) $height);
  }

}
