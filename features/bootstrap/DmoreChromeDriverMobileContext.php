<?php

/**
 * @file
 * PhantomJS web driver context.
 */

use Behat\Behat\Context\SnippetAcceptingContext;
use DMore\ChromeDriver\ChromeDriver;

/**
 * Define a Mink Subcontext.
 */
class DmoreChromeDriverMobileContext extends DmoreChromeDriverContext implements SnippetAcceptingContext {
  /**
   * Check if the web driver's user agent is a mobile device.
   *
   * Overrides JsBrowserContext::isMobile() because ChromeDriver doesn't
   * accurately return result header UserAgent information.
   */
  public function isMobile() {
    return true;
  }
}
