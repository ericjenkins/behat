<?php

/**
 * @file
 * Context for all web drivers, including Blackbox.
 */

use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use Behat\Behat\Hook\Scope\AfterStepScope;
use Drupal\DrupalExtension\Context\MinkContext;
use Behat\Mink\Element\NodeElement;
use Behat\Mink\Exception\ExpectationException;
use Behat\Mink\Exception\UnsupportedDriverActionException;

/**
 * Define a Mink Subcontext.
 */
class PhpBrowserContext extends MinkContext implements SnippetAcceptingContext {

  protected $screenshotPath;

  /**
   * Prepare scenario to take screenshot on failed step.
   *
   * @param Behat\Behat\Hook\Scope\BeforeScenarioScope $scope
   *   Before Scenario hook scope.
   *
   * @BeforeScenario
   */
  public function prepare(BeforeScenarioScope $scope) {
    $this->screenshotPath = $scope->getEnvironment()
      ->getSuite()
      ->getSetting('screenshot_path');
  }

  /**
   * Take a screenshot.
   *
   * @Then /^take a screenshot$/
   */
  public function assertScreenshot() {
    $fileName = $this->fileName();
    $this->outputMarkupToFile($fileName);
  }

  /**
   * Dump markup on failure.
   *
   * Adapted from:
   *  - https://gist.github.com/michalochman/3175175/
   *  - https://gitlab.com/DMore/chrome-mink-driver/
   *  - https://github.com/MidCamp/midcamp/blob/master/features/bootstrap/
   *
   * @AfterStep
   */
  public function takeScreenshotAfterFailedStep(afterStepScope $scope) {
    if (99 !== $scope->getTestResult()->getResultCode()) {
      return;
    }
    $fileName = $this->fileName($scope);
    $this->outputMarkupToFile($fileName);
  }

  /**
   * Compute a file name for the output.
   *
   * @param Behat\Behat\Hook\Scope\AfterStepScope $scope
   *   After Step hook scope.
   *
   * @return string
   *   Full path to screenshot filename.
   *   - Format: yyyymmdd-hh.mm.ss-featureName[-lineNumber]
   */
  protected function fileName(AfterStepScope $scope = NULL) {
    $fileName = date('Ymd-H.i.s') . '-';
    if ($scope) {
      $pathInfo = pathinfo($scope->getFeature()->getFile());
      $fileName .= substr($pathInfo['basename'], 0,
        strlen($pathInfo['basename']) - strlen($pathInfo['extension']) - 1);
      $fileName .= '-' . $scope->getStep()->getLine();
    }
    else {
      $fileName .= 'screenshot';
    }
    $fullPath = $this->screenshotPath . '/' . $fileName;
    return $fullPath;
  }

  /**
   * Helper function to output the html markup to a file.
   *
   * @param string $fileName
   *   Base file name string. Exclude the file extension.
   */
  protected function outputMarkupToFile($fileName) {
    $fileName .= '.html';
    $html = $this->getSession()->getPage()->getContent();
    file_put_contents($fileName, $html);
    echo sprintf("HTML available at: %s\n", $fileName);
  }

  /**
   * Helper function to find an element from a (link|button|field|element) value.
   *
   * @param string $selector
   *   Selector (link|button|field|id|element)
   * @param string $locator
   *   Selector locator.
   *
   * @return Behat\Mink\Element\NodeElement
   *   Return NodeElement object of the element, if it is found.
   */
  protected function findElem($selector, $locator) {
    if ($selector == 'element') {
      $element = $this->assertSession()->elementExists('css', $locator);
    }
    else {
      $element = $this->assertSession()->elementExists('named', [$selector, $locator]);
    }

    return $element;
  }

  /**
   * Clicks the nth matching link, optionally filtered by a region.
   *
   * Example: When I click the 2nd occurrence of "Submit" in the "content" region
   * Example: And click the 4th occurrence of "click here"
   *
   * @When /^(?:|I )click the (\d+)(?:st|nd|rd|th) occurrence of "(?<link>[^"]*)"(?:| in the "(?<region>[^"]*)" region)$/
   *
   * @throws \Exception
   *   If region or text-based link within it cannot be found.
   */
  public function assertNthLinkFollow($occurrence, $link, $region) {
    if (empty($region)) {
      $object = $this->getSession()->getPage();
    }
    else {
      $object = $this->getRegion($region);
    }

    // Find all links within the region
    $links = $object->findAll('named', ['link', $link]);

    if (empty($links)) {
      throw new \Exception(sprintf('The link "%s" was not found in the region "%s" on the page %s', $link, $region, $this->getSession()->getCurrentUrl()));
    }
    elseif (count($links) < $occurrence) {
      throw new \Exception(sprintf('Less than %s links were found in the given page/region.', $occurrence));
    }
    // Click the object.
    $links[$occurrence - 1]->click();
  }

  /**
   * Checks, that nth matching link points to a path, optionally filtered by region.
   *
   * Example: Then the link "Zimmer Biomet" should point to "cervicaldisc.com"
   * Example: And the 2nd occurrence of link "Zimmer Biomet" should point to "https://www.cervicaldisc.com"
   *
   * @Then /^the (?:|(\d+)(?:st|nd|rd|th) occurrence of )link "(?<link>[^"]*)"(?:| in the "(?<region>[^"]*)" region) should point to "(?<path>[^"]*)"$/
   *
   * @throws \Exception
   *   If region or text-based link within it cannot be found.
   */
  public function assertNthLinkPointsToPath($occurrence, $link, $region, $path) {
    if (empty($occurrence)) {
      $occurrence = 1;
    }
    if (empty($region)) {
      $object = $this->getSession()->getPage();
    }
    else {
      $object = $this->getRegion($region);
    }

    // Find all links within the region
    $links = $object->findAll('named', ['link', $link]);

    if (empty($links)) {
      throw new \Exception(sprintf('The link "%s" was not found in the region "%s" on the page %s', $link, $region, $this->getSession()->getCurrentUrl()));
    }
    elseif (count($links) < $occurrence) {
      throw new \Exception(sprintf('Less than %s links were found in the given page/region.', $occurrence));
    }
    else {
      $outer_html = $links[$occurrence - 1]->getOuterHtml();
      preg_match('/href(?:| *)=(?:| *)["\'](?<link>[^"\']*)["\']/', $outer_html, $matches);
      $url = $matches['link'] ?? "";
      if (empty($url)) {
        throw new \Exception(sprintf('href tag not found in html element "%s".', $outer_html));
      }
      elseif (strpos($url, $path) === FALSE) {
        throw new \Exception(sprintf('Path "%s" not found. href value was set to "%s".', $path, $url));
      }
    }
  }

  /**
   * Checks, that nth matching link has html tag with specified value, optionally filtered by region.
   *
   * Example: Then the link "Zimmer Biomet" should have tag "rel" set to "nofollow"
   * Example: And the 1st occurrence of link "Zimmer Biomet" should have tag "rel" set to "nofollow"
   * Example: And the 2nd occurrence of link "Zimmer Biomet" in the "content" region should have tag "rel" set to "nofollow"
   *
   * @Then /^the (?:|(\d+)(?:st|nd|rd|th) occurrence of )link "(?<link>[^"]*)"(?:| in the "(?<region>[^"]*)" region) should have tag "(?<tag>[^"]*)" set to "(?<value>[^"]*)"$/
   *
   * @throws \Exception
   *   If region or text-based link within it cannot be found.
   */
  public function assertNthLinkHasTagValue($occurrence, $link, $region, $tag, $value) {
    if (empty($occurrence)) {
      $occurrence = 1;
    }
    if (empty($region)) {
      $object = $this->getSession()->getPage();
    }
    else {
      $object = $this->getRegion($region);
    }

    // Find all links within the region
    $links = $object->findAll('named', ['link', $link]);

    if (empty($links)) {
      throw new \Exception(sprintf('The link "%s" was not found in the region "%s" on the page %s', $link, $region, $this->getSession()->getCurrentUrl()));
    }
    elseif (count($links) < $occurrence) {
      throw new \Exception(sprintf('Less than %s links were found in the given page/region.', $occurrence));
    }
    else {
      $outer_html = $links[$occurrence - 1]->getOuterHtml();
      preg_match('/' . $tag . '(?:| *)=(?:| *)["\'](?<value>[^"\']*)["\']/', $outer_html, $matches);
      $actual = $matches['value'] ?? "";
      if (empty($actual)) {
        $message = sprintf(
          'Tag "%s" not found in html element "%s".',
          $tag,
          $outer_html
        );
        throw new ExpectationException($message, $this->getSession()->getDriver());
      }
      elseif ($actual != $value) {
        $message = sprintf(
          'The value of tag "%s" is set to "%s".',
          $tag,
          $actual
        );
        throw new ExpectationException($message, $this->getSession()->getDriver());
      }
    }
  }

  /**
   * Visit a given path, and additionally check for HTTP response code 200.
   *
   * Overriding MinkContent::assertAtPath() because, when checking for http
   * response code 200, code 304 should also be considered acceptable.
   */
  public function assertAtPath($path) {
    $this->getSession()->visit($this->locatePath($path));

    // If available, add extra validation that this is a 200 (or 304) response.
    try {
      $this->getSession()->getStatusCode();

      $actual = $this->getSession()->getStatusCode();
      $message = sprintf('Current response status code is %d, but 200/304 expected.', $actual);

      // This is the part being overridden, to include 304 as acceptable.
      if (in_array(intval($actual), [200, 304])) {
        return;
      }
      throw new ExpectationException($message, $this->getSession()->getDriver());
    }
    catch (UnsupportedDriverActionException $e) {
      // Simply continue on, as this driver doesn't support HTTP response codes.
    }
  }

}
