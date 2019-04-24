<?php

/**
 * @file
 * Custom mobile device context for ChromeDriver by Dmore and for PhantomJS.
 */

use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use Behat\Mink\Driver\Selenium2Driver;
use DMore\ChromeDriver\ChromeDriver;

/**
 * Define a Mink Subcontext.
 */
class JsBrowserMobileContext extends JsBrowserContext implements SnippetAcceptingContext {

  /**
   * Sets the user agent and resolution for ChromeDriver by Dmore.
   *
   * Overrides JsBrowserContext::resizeBrowser().
   *
   * ChromeDriver by Dmore has no built-in mechanism to define a mobile device
   * browser, but it does provide a mechanism to alter the user agent.
   * So, this method provides a default user-agent header.
   *
   * @BeforeScenario
   */
  public function prepareBrowser(BeforeScenarioScope $scope) {
    $session = $this->getSession();
    $driver = $session->GetDriver();

    $suite = $scope->getEnvironment()->getSuite();

    $width = $suite->getSetting('browser_width');
    $height = $suite->getSetting('browser_height');
    $driver->resizeWindow((int) $width, (int) $height);

    $useragent = $suite->getSetting('browser_useragent');
    if ($driver instanceof ChromeDriver) {
      $driver->setRequestHeader('user-agent', $useragent);
    }
    elseif ($driver instanceof Selenium2Driver) {
      $capabilities = $driver->getDefaultCapabilities();
      $capabilities['extra_capabilities']['phantomjs.page.settings.userAgent'] = $useragent;
      $driver->setDesiredCapabilities($capabilities);
    }
  }

}
