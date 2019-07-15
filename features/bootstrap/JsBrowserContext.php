<?php

/**
 * @file
 * Screen-based web driver context.
 */

use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use Behat\Behat\Hook\Scope\AfterStepScope;
use Behat\Mink\Exception\DriverException;
use Behat\Mink\Exception\ExpectationException;
use Behat\Mink\Exception\ElementNotFoundException;
use Behat\Mink\Element\NodeElement;
use Behat\Mink\Driver\Selenium2Driver;
use DMore\ChromeDriver\ChromeDriver;

/**
 * Define a Mink Subcontext.
 */
class JsBrowserContext extends PhpBrowserContext implements SnippetAcceptingContext {

  /**
   * Ghost driver pid.
   *
   * @var int
   */
  protected static $webDriverPid;

  /**
   * Initializes context.
   */
  public function __construct() {
  }

  /**
   * Stops the web driver.
   *
   * @AfterSuite
   */
  public static function stopWebDriver() {
    self::stopProcess(self::$webDriverPid);
  }

  /**
   * Set the default window size for web browsers.
   *
   * @BeforeScenario
   */
  public function prepareBrowser(BeforeScenarioScope $scope) {
    $suite = $scope->getEnvironment()->getSuite();
    $width = $suite->getSetting('browser_width');
    $height = $suite->getSetting('browser_height');
    $this->setBrowserWindowSizeToWxH($width, $height);
  }

  /**
   * Step definition for setting browser window size.
   *
   * Example: Given I set the browser window size to 800 x 600
   * Example: And set the browser window size to 1280x720
   *
   * Adapted from: http://www.devengineering.com/node/17
   *
   * @Given /^(?:|I )set (?:|the )browser window size to (\d+)(?:| )x(?:| )(\d+)$/
   */
  public function setBrowserWindowSizeToWxH($width, $height) {
    $session = $this->getSession();
    if ($session->GetDriver() instanceof Selenium2Driver) {
      $session->resizeWindow((int) $width, (int) $height);
    }
    else {
      echo "Unsupported driver. Skipping browser resize.\n";
    }
  }

  /**
   * Scroll to a specific (link|button|field|element).
   *
   * Example: Wen I scroll element "#swap-div" into view .
   *
   * Adapted from: https://gist.github.com/MKorostoff/c94824a467ffa53f4fa9
   *
   * @When /^(?:|I )scroll (?:|the )(?<selector>[^"]*) "(?<locator>[^"]*)" into view$/
   */
  public function scrollElemIntoView($selector, $locator) {
    $element = $this->findElem($selector, $locator);
    $xpath = str_replace(["\n", '"'], [" ", "'"], $element->getXpath());
    $script = <<<JS
(function(){
  var el = document.evaluate("$xpath", document, null, XPathResult.FIRST_ORDERED_NODE_TYPE, null).singleNodeValue;
  el.scrollIntoView(false);
})()
JS;
    try {
      $this->getSession()->getDriver()->executeScript($script);
    }
    catch (Exception $e) {
      throw new DriverException("ScrollIntoView failed");
    }
  }

  /**
   * Scroll to top of page.
   *
   * Adapted from: https://stackoverflow.com/questions/36647785
   *
   * @When /^(?:|I )scroll to the top$/
   */
  public function scrollToTop() {
    $function = <<<JS
(function(){
  window.scrollTo(0, 0);
})()
JS;
    try {
      $this->getSession()->executeScript($function);
    }
    catch (Exception $e) {
      throw new DriverException("ScrollToTop failed");
    }
  }

  /**
   * Scroll to bottom of page.
   *
   * Adapted from: https://stackoverflow.com/questions/42982950
   *
   * @When /^(?:|I )scroll to the bottom$/
   */
  public function scrollToBottom() {
    $function = <<<JS
(function(){
  window.scrollTo(0, document.body.scrollHeight);
})()
JS;
    try {
      $this->getSession()->executeScript($function);
    }
    catch (Exception $e) {
      throw new DriverException("ScrollToBottom failed");
    }
  }

  /**
   * Hover the mouse over (link|button|field|element).
   *
   * Example: When I hover over the link "Conditions"
   * Example: And I hover over button "submit"
   *
   * Adapted from: https://stackoverflow.com/questions/18499851
   *
   * @When /^(?:|I )hover over(?: the|) (?<selector>[^"]*) "(?<locator>[^"]*)"$/
   */
  public function hoverOver($selector, $locator) {
    $element = $this->findElem($selector, $locator);
    $element->mouseOver();
  }

  /**
   * Checks, that (link|button|field|element) is visible in the DOM.
   *
   * Note: "element" refers to a css element.
   * Example: Then the link "Chronic Pain" should be visible
   * Example: And element "#Illi" should be visible .
   *
   * Adapted from: https://stackoverflow.com/questions/19669786
   *
   * @Then /^(?:the |)(?<selector>[^"]*) "(?<locator>[^"]*)" should be visible$/
   */
  public function assertVisibleElement($selector, $locator) {
    return $this->findElem($selector, $locator)->isVisible();

    $message = sprintf(
      'The %s "%s" is hidden in the DOM.',
      $selector,
      $locator
    );

    throw new ExpectationException($message, $this->getSession()->getDriver());
  }

  /**
   * Checks, that (link|button|field|element) is not visible in the DOM.
   *
   * Note: "element" refers to a css element.
   * Example: Then the link "Chronic Pain" should be hidden
   * Example: And element "#Illi" should be hidden .
   *
   * @Then /^(?:the |)(?<selector>[^"]*) "(?<locator>[^"]*)" should be hidden$/
   */
  public function assertHiddenElement($selector, $locator) {
    return !$this->findElem($selector, $locator)->isVisible();

    $message = sprintf(
      'The %s "%s" is visible in the DOM.',
      $selector,
      $locator
    );

    throw new ExpectationException($message, $this->getSession()->getDriver());
  }

  /**
   * Checks, that (link|button|field|element) is rendered in browser viewport.
   *
   * Example: Then the viewport should contain the element "#Illi"
   * Example: And the viewport should contain button "Submit"
   *
   * Adapted from:
   *   - https://stackoverflow.com/questions/123999
   *   - https://alfrednutile.info/posts/37
   *   - https://stackoverflow.com/questions/25494456
   *
   * @Then /^(?:|the )viewport should contain(?: the|) (?<selector>[^"]*) "(?<locator>[^"]*)"$/
   */
  public function assertViewportContainsElement($selector, $locator) {
    $element = $this->findElem($selector, $locator);
    if ($this->elemIsInViewport($element)) {
      return;
    }

    $message = sprintf(
      'The %s "%s" was not found in the browser viewport.',
      $selector,
      $locator
    );

    throw new ExpectationException($message, $this->getSession()->getDriver());
  }

  /**
   * Checks, that (link|button|field|element) is not rendered in browser viewport.
   *
   * Example: Then the viewport should not contain the element "#Illi"
   * Example: And the viewport should not contain button "Submit"
   *
   * @Then /^(?:|the )viewport should not contain(?: the|) (?<selector>[^"]*) "(?<locator>[^"]*)"$/
   */
  public function assertViewportNotContainsElement($selector, $locator) {
    $element = $this->findElem($selector, $locator);
    if (!$this->elemIsInViewport($element)) {
      return;
    }

    $message = sprintf(
      'The %s "%s" was found in the browser viewport, but it should not.',
      $selector,
      $locator
    );

    throw new ExpectationException($message, $this->getSession()->getDriver());
  }

  /**
   * Helper function to determine if element at given xpath is in viewport.
   *
   * @param Behat\Mink\Element\NodeElement $element
   *   Element object to check for visibility.
   *
   * @return bool
   *   Return TRUE if the element is in the browser viewport.
   */
  protected function elemIsInViewport(NodeElement $element) {
    $xpath = str_replace(["\n", '"'], [" ", "'"], $element->getXpath());
    $script = <<<JS
(function(){
  var el = document.evaluate("$xpath", document, null, XPathResult.FIRST_ORDERED_NODE_TYPE, null).singleNodeValue;

  var rect     = el.getBoundingClientRect();
  var vWidth   = window.innerWidth || doc.documentElement.clientWidth;
  var vHeight  = window.innerHeight || doc.documentElement.clientHeight;
  var efp      = function (x, y) { return document.elementFromPoint(x, y) };

  // Return false if it's not in the viewport
  if (rect.right < 0 || rect.bottom < 0
          || rect.left > vWidth || rect.top > vHeight)
      return false;

  // Return true if any of its four corners are visible
  return (
        el.contains(efp(rect.left,  rect.top))
    ||  el.contains(efp(rect.right, rect.top))
    ||  el.contains(efp(rect.right, rect.bottom))
    ||  el.contains(efp(rect.left,  rect.bottom))
  );
})()
JS;
    return $this->getSession()->evaluateScript($script);
  }

  /**
   * Check if the web driver's user agent is a mobile device.
   */
  public function isMobile() {
    $session = $this->getSession();
    $driver = $session->GetDriver();
    $user_agent = $driver->evaluateScript('return navigator.userAgent');
    return preg_match("/(android|avantgo|blackberry|bolt|boost|cricket|docomo|fone|hiptop|mini|mobi|palm|phone|pie|tablet|up\.browser|up\.link|webos|wos)/i", $user_agent);
  }

  /**
   * Take browser screenshot and dump markup to file.
   *
   * Overrides PhpBrowserContext::assertScreenshot() to add in a png snapshot.
   */
  public function assertScreenshot() {
    $fileName = $this->fileName();
    $this->outputMarkupToFile($fileName);
    $this->screenshot($fileName);
  }

  /**
   * Take screenshot on failure.
   *
   * Adapted from:
   *  - https://gist.github.com/michalochman/3175175/
   *  - https://gitlab.com/DMore/chrome-mink-driver/
   *  - https://github.com/MidCamp/midcamp/blob/master/features/bootstrap/
   *  - https://blogs.library.ucsf.edu/ckm/2014/05/14/
   *
   * Overrides PhpBrowserContext::takeScreenShotAfterFailedStep() to add in a
   * png snapshot.
   */
  public function takeScreenshotAfterFailedStep(afterStepScope $scope) {
    if (99 !== $scope->getTestResult()->getResultCode()) {
      return;
    }
    $fileName = $this->fileName($scope);
    $this->outputMarkupToFile($fileName);
    $this->screenshot($fileName);
  }

  /**
   * Save a screen shot from the failed step.
   */
  protected function screenshot($fileName) {
    $fileName .= '.png';
    $session = $this->getSession();

    file_put_contents($fileName, $session->getScreenshot());
    echo sprintf("Screen shot available at: %s\n", $fileName);
  }

  /**
   * Assert a number of advertisements are in a region.
   *
   * Example: Then I should see 1 advertisement in the "right sidebar" region
   * Example: And I see 2 advertisements in the "right sidebar"
   *
   * @Then /^I (?:|should )see (\d+) advertisement(?:|s) in the "(?<locator>[^"]*)"(?:| region)$/
   */
  public function assertGoogleAds($num, $locator) {
    $regionObj = $this->getRegion($locator);
    // VH uses ".block-unicorn" class to identify ad block divs.
    $selector = '.block-unicorn .block__content > div > div';
    $adIframeElems = $regionObj->findAll('css', $selector);
    if (!count($adIframeElems)) {
      throw new ElementNotFoundException($this->getSession()->getDriver(), $selector);
    }

    $adCount = 0;
    foreach ($adIframeElems as $delta => $elem) {
      if ($elem->isVisible()) {
        $adCount++;
      }
    }

    if ($adCount != $num) {
      $message = sprintf(
        'Found %s advertisement%s, but expected %s.',
        $adCount,
        $adCount == 1 ? '' : 's',
        $num
      );
      throw new ExpectationException($message, $this->getSession()->getDriver());
    }
  }

  /**
   * Assert a number of advertisements are in a region at a specific size.
   *
   * Example: Then I should see 1 advertisement of height "200" in the "right sidebar" region
   * Example: And I see 2 advertisements of width "300px" in the "right sidebar"
   *
   * @Then /^I (?:|should )see (\d+) advertisement(?:|s) of (width|height) "(\d+)(?:|px)" in the "(?<locator>[^"]*)"(?:| region)$/
   */
  public function assertDimensionedGoogleAds($num, $dimension, $value, $locator) {
    $regionObj = $this->getRegion($locator);
    // VH uses ".block-unicorn" class to identify ad block divs.
    $adIframeElems = $regionObj->findAll('css', '.block-unicorn iframe');
    if (!count($adIframeElems)) {
      throw new ElementNotFoundException($this->getSession()->getDriver(), 'element', 'css', '.block-unicorn iframe');
    }
    elseif (count($adIframeElems) != $num) {
      $message = sprintf(
        'Found %s advertisement%s, but expected %s.',
        count($adIframeElems),
        count($adIframeElems) == 1 ? '' : 's',
        $num
      );
      throw new ExpectationException($message, $this->getSession()->getDriver());
    }

    foreach ($adIframeElems as $elem) {
      $xpath = str_replace(["\n", '"'], [" ", "'"], $elem->getXpath());
      $iframePropValue = $elem->getAttribute($dimension);
      if ($iframePropValue != $value) {
        $message = sprintf(
          'Advertisement found in the "%s" region with %s of %spx.',
          $locator,
          $dimension,
          $iframePropValue
        );
        throw new ExpectationException($message, $this->getSession()->getDriver());
      }
    }
  }

  /**
   * Checks that JS object with specified property contains specified substring.
   *
   * Example: Then JS property "pbjs.adUnits[0].code" should contain "1006215"
   *
   * @Then /^(?:|the )JS property "(?<property>[^"]*)" should contain (?:|")(?<value>[^"]*)(?:|")$/
   */
  public function assertJsPropertyContainsSubstring($property, $substring) {
    $computed_val = $this->getSession()->evaluateScript($property);
    if (strpos($computed_val, $substring) !== FALSE) {
      return;
    }

    $message = sprintf(
      'Substring "%s" not found in the computed value "%s".',
      $substring,
      $computed_val
    );
    throw new ExpectationException($message, $this->getSession()->getDriver());
  }

  /**
   * Checks, that JS object with specified property equals specified value.
   *
   * Example: Then the JS property "googletag.apiReady" should be set to "true"
   * Example: And JS property "PREBID_TIMEOUT" should equal "700"
   *
   * @Then /^(?:|the )JS property "(?<property>[^"]*)" should (?:be set to|equal) (?:|")(?<value>[^"]*)(?:|")$/
   */
  public function assertJsPropertyEqualsValue($property, $value) {
    $computed_val = $this->getSession()->evaluateScript($property);
    if ($value == $computed_val) {
      return;
    }

    $message = sprintf(
      'The computed value of property "%s" is "%s".',
      $property,
      $computed_val
    );
    throw new ExpectationException($message, $this->getSession()->getDriver());
  }

  /**
   * Checks, that JS property does not equal specified value.
   *
   * Example: Then JS property "googletag.apiReady" should not be set to "true"
   * Example: And the JS property "PREBID_TIMEOUT" should not equal 700
   *
   * @Then /^(?:|the )JS property "(?<property>[^"]*)" should not (?:be set to|equal) (?:|")(?<value>[^"]*)(?:|")$/
   */
  public function assertJsPropertyNotEqualsValue($property, $value) {
    if ($value != $this->getSession()->evaluateScript($property)) {
      return;
    }

    $message = sprintf(
      'The computed value of property "%s" is equal to "%s", but it should not.',
      $property,
      $value
    );
    throw new ExpectationException($message, $this->getSession()->getDriver());
  }

  /**
   * Checks, that JS object with specified property greater/less than value.
   *
   * Example: Then the JS property "PREBID_TIMEOUT" should be greater than 600
   * Example: And JS property "PREBID_TIMEOUT" should be less than "800"
   *
   * @Then /^(?:|the )JS property "(?<property>[^"]*)" should be (greater|less) than (?:|")(?<value>[^"]*)(?:|")$/
   */
  public function assertJsPropertyGreaterOrLessThanValue($property, $operator, $value) {
    $computed_val = $this->getSession()->evaluateScript($property);
    switch ($operator) {
      case 'greater':
        if ($computed_val > $value) {
          return;
        }
        break;

      default:
        if ($computed_val < $value) {
          return;
        }
    }

    $message = sprintf(
      'The computed value of property "%s" is %s, which is not %s than %s.',
      $property,
      $computed_val,
      $operator,
      $value
    );
    throw new ExpectationException($message, $this->getSession()->getDriver());
  }

  /**
   * Helper function to get the computed css property of a specified element.
   *
   * Reference: https://www.w3schools.com/jsref/jsref_getcomputedstyle.asp/
   *
   * @param Behat\Mink\Element\NodeElement $element
   *   The element to check for computed css properties.
   * @param string $property
   *   The property to compute.
   *
   * @return string
   *   Return the computed value of the given css property.
   */
  protected function getComputedStyle(NodeElement $element, $property) {
    $xpath = str_replace(["\n", '"'], [" ", "'"], $element->getXpath());
    $script = <<<JS
(function(){
var el = document.evaluate("$xpath", document, null, XPathResult.FIRST_ORDERED_NODE_TYPE, null).singleNodeValue;
if (!el.offsetHeight && !el.offsetWidth) { return false; }
return getComputedStyle(el).$property;
})()
JS;
    return $this->getSession()->evaluateScript($script);
  }

  /**
   * Checks whether a server is running.
   *
   * @param string $host
   *   Hostname.
   * @param string $port
   *   Port number.
   *
   * @return bool
   *   True if server is up, false otherwise.
   */
  protected static function serverIsUp($host, $port) {
    set_error_handler(function () {
      return TRUE;
    });
    $fp = fsockopen($host, $port);
    restore_error_handler();
    if ($fp) {
      fclose($fp);
      return TRUE;
    }
    else {
      return FALSE;
    }
  }

  /**
   * Stops a system process.
   *
   * @param int $pid
   *   Process ID.
   */
  protected static function stopProcess($pid) {
    if ($pid) {
      trim(shell_exec("kill " . $pid . " 2>&1"));
    }
  }

}
