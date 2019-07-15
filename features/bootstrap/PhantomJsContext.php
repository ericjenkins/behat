<?php

/**
 * @file
 * PhantomJS web driver context.
 */

use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\Testwork\Hook\Scope\BeforeSuiteScope;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use Behat\Mink\Exception\ExpectationException;
use Behat\Mink\Element\NodeElement;

/**
 * Define a Mink Subcontext.
 */
class PhantomJsContext extends JsBrowserContext implements SnippetAcceptingContext {

  /**
   * Starts the phantomjs GhostDriver for UI tests.
   *
   * Adapted from: https://github.com/bnowack/backbone-php/tree/master/test/behat/
   *
   * @BeforeSuite
   */
  public static function startWebDriver(BeforeSuiteScope $scope) {
    if ($scope->getSuite()->hasSetting('phantomjs_path')) {
      $phantomjs_path = $scope->getSuite()->getSetting('phantomjs_path');
      $port = 8910;
      $host = 'localhost';

      // Launch if not already up.
      if (!self::serverIsUp($host, $port)) {
        $command = "{$phantomjs_path} --webdriver={$port} --ignore-ssl-errors=true >/dev/null 2>&1 & echo \$!";
        $output = trim(shell_exec($command));
        self::$webDriverPid = is_numeric($output) ? intval($output) : NULL;
      }
      // Check that the server is running, wait up to 5 seconds.
      $attempts = 0;
      do {
        $up = self::serverIsUp($host, $port);
        $attempts++;
        usleep(100000);
        // 0.1 sec.
      } while (!$up && $attempts < 50);
      if (!$up) {
        self::stopProcess(self::$webDriverPid);
        // Just in case it *did* start but did not respond in time.
        throw new \RuntimeException("Could not start PhantomJS driver at {$host}:{$port}");
      }
    }
  }

  /**
   * Save a screen shot from the failed step.
   *
   * Overrides JsBrowserContext::screenshot() to change background color from
   * transparent to white.
   */
  protected function screenshot($fileName) {
    $script = "document.body.style.backgroundColor = 'white';";
    $this->getSession()->executeScript($script);
    parent::screenshot($fileName);
  }

  /**
   * Checks, that (link|button|field|element) is rendered in browser viewport.
   *
   * Overrides JsBrowserContext::elemIsInViewport because PhantomJs does not
   * suport this feature. PhantomJS has no viewport.
   */
  protected function elemIsInViewport(NodeElement $element) {
    if ($element->isVisible()) {
      echo "Note: The element is visible in the DOM, but PhantomJS does not use a viewport.";
      return TRUE;
    }

    $message = sprintf('The element is hidden in the DOM.');

    throw new ExpectationException($message, $this->getSession()->getDriver());

  }

  /**
   * Checks, that (link|button|field|element) is not rendered in browser viewport.
   *
   * Overrides JsBrowserContext::assertViewportNotContainsElement() because
   * PhantomJS has no viewport. If the element is visible, print a warning
   * message that this test is not to be trusted.
   */
  public function assertViewportNotContainsElement($selector, $locator) {
    if ($this->findElem($selector, $locator)->isVisible()) {
      echo "Note: The $selector \"$locator\" is visible in the DOM, but ";
      echo "PhantomJS does not use a viewport.";
    }
    return;
  }

}
