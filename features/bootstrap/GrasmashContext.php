<?php

/**
 * @file
 * Custom contexts adapted from work by grasmash.
 *
 * See: https://gist.github.com/grasmash/9746671
 */

use Drupal\DrupalExtension\Context\DrupalContext;
use Drupal\DrupalExtension\Event\EntityEvent;
use Drupal\Component\Utility\Random;

use Behat\Behat\Context\BehatContext;
use Behat\Behat\Context\Step;
use Behat\Behat\Context\Step\Given;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;

class FeatureContext extends DrupalContext {

  /**
   * @var $output
   *   Command line output.
   */
  protected $output;

  public function __construct(array $parameters) {

    // Initiliaze subcontexts.
    // $this->useContext('OgContext', new OgContext($parameters));
  }

  /**
   * Returns the current, relative path.
   *
   * Simply using Drupal's current_path() or $_GET['q'] does not work.
   *
   * @return string
   */
  public function getCurrentPath() {
    $url = $this->getSession()->getCurrentUrl();
    $parsed_url = parse_url($url);
    $path = trim($parsed_url['path'], '/');

    return $path;
  }

  /**
   * Returns node currently being viewed. Assumes /node/[nid] URL.
   *
   * Using path-based loaders, like menu_load_object(), will not work.
   *
   * @return object
   *   The currently viewed node.
   *
   * @throws Exceptionq
   */
  public function getNodeFromUrl() {

    $path = $this->getCurrentPath();
    $system_path = drupal_lookup_path('source', $path);
    if (!$system_path) {
      $system_path = $path;
    }
    $menu_item = menu_get_item($system_path);
    if ($menu_item['path'] == 'node/%') {
      $node = node_load($menu_item['original_map'][1]);
    }
    else {
      throw \Exception(sprintf("Node could not be loaded from URL '%s'", $path));
    }
    return $node;
  }

  /**
   * Returns the most recently created node.
   *
   * @return object
   *   The most recently created node.
   */
  public function getLastCreatedNode() {
    $node = end($this->nodes);

    return $node;
  }

    /**
   * @Then /^I should not see the "([^"]*)" region$/
   */
  public function iShouldNotSeeTheRegion($region) {
    $session = $this->getSession();
    $regionObj = $session->getPage()->find('region', $region);
    if ($regionObj) {
      throw new \Exception(sprintf('The region "%s" was found on the page %s.', $region, $session->getCurrentUrl()));
    }

    return $regionObj;
  }

  /**
   * @Then /^I should see the css selector "([^"]*)"$/
   * @Then /^I should see the CSS selector "([^"]*)"$/
   */
  public function iShouldSeeTheCssSelector($css_selector) {
    $element = $this->getSession()->getPage()->find("css", $css_selector);
    if (empty($element)) {
        throw new \Exception(sprintf("The page '%s' does not contain the css selector '%s'", $this->getSession()->getCurrentUrl(), $css_selector));
    }
  }

  /**
   * @Then /^I should not see the css selector "([^"]*)"$/
   * @Then /^I should not see the CSS selector "([^"]*)"$/
   */
  public function iShouldNotSeeATheCssSelector($css_selector) {
    $element = $this->getSession()->getPage()->find("css", $css_selector);
    if (empty($element)) {
      throw new \Exception(sprintf("The page '%s' contains the css selector '%s'", $this->getSession()->getCurrentUrl(), $css_selector));
    }
  }

  /**
   * @Then /^I should see the css selector "([^"]*)" in the "([^"]*)" region$/
   * @Then /^I should see the CSS selector "([^"]*)" in the "([^"]*)" region$/
   */
  public function iShouldSeeTheCssSelectorInTheRegion($css_selector, $region) {
    $regionObj = $this->getRegion($region);
    $elements = $regionObj->findAll('css', $css_selector);
    if (empty($elements)) {
      throw new \Exception(sprintf('The css selector "%s" was not found in the "%s" region on the page %s', $css_selector, $region, $this->getSession()->getCurrentUrl()));
    }
  }

  /**
   *
   * @When /^(?:|I )click the element with CSS selector "([^"]*)"$/
   * @When /^(?:|I )click the element with css selector "([^"]*)"$/
   */
  public function iClickTheElementWithCssSelector($css_selector) {
    $element = $this->getSession()->getPage()->find("css", $css_selector);
    if (empty($element)) {
        throw new \Exception(sprintf("The page '%s' does not contain the css selector '%s'", $this->getSession()->getCurrentUrl(), $css_selector));
    }
    $element->click();
  }

  /**
   * @Given /^I create a "([^"]*)" node with title "([^"]*)"$/
   */
  public function iCreateANodeWithTitle($type, $title) {
    $this->createNode($type, array('title' => $title));
  }

  /**
   * @Given /^I am viewing (?:a|an) "([^"]*)" node$/
   * @Given /^I create (?:a|an) "([^"]*)" node$/
   *
   * This overrides the parent createNode() method, allowing node properties
   * to be passes via $properties argument.
   *
   * @override
   */
  public function createNode($type, $properties = array()) {

    $node = (object) array(
      'title' => Random::string(25),
      'type' => $type,
      'uid' => 1,
    );

    if ($properties) {
      foreach ($properties as $key => $value) {
        $node->$key = $value;
      }
    }

    $this->dispatcher->dispatch('beforeNodeCreate', new EntityEvent($this, $node));
    $saved = $this->getDriver()->createNode($node);
    $this->dispatcher->dispatch('afterNodeCreate', new EntityEvent($this, $saved));
    $this->nodes[] = $saved;

    // Set internal page on the new node.
    $this->getSession()->visit($this->locatePath('/node/' . $saved->nid));

    return $saved;
  }

  /**
   * Populates required fields before node creation.
   *
   * @beforeNodeCreate
   */
  public function nodePreSave(EntityEvent $event) {
    $node = $event->getEntity();
    $node->status = 1;

    // Prevent bug caused by pathauto menu rebuild outside of Drupal context.
    // @see http://previousnext.com.au/blog/using-behat-and-drupaldriver-beware-pathauto
    $node->path = array('pathauto' => 0);

    if (module_exists('workbench_moderation')) {
      $node->workbench_moderation_state_new = 'published';
    }
  }

  /**
   * Clean up after node save.
   *
   * @afterNodeCreate
   */
  public function nodePostSave(EntityEvent $event) {
    $node = $event->getEntity();

    // By default, workbench_moderation delays calling node_save() on new
    // revisions until the PHP proc is being shutdown.
    if (module_exists('workbench_moderation') && !empty($node->workbench_moderation['published'])) {
      workbench_moderation_store($node);
    }
  }

  /**
   * Modify user entity before saving.
   *
   * @beforeUserCreate
   */
  public function userPreSave(EntityEvent $event) {
    $user = $event->getEntity();

    // Prevent bug caused by pathauto menu rebuild outside of Drupal context.
    // @see http://previousnext.com.au/blog/using-behat-and-drupaldriver-beware-pathauto
    $user->path = array('pathauto' => 0);
  }

  /**
   * @Given /^I am viewing the "([^"]*)" theme$/
   */
  public function iAmViewingTheTheme($expected_theme) {
    global $theme;
    if ($theme !== $expected_theme) {
      throw new \Exception(sprintf("'%s' is not the active theme. '%s' is active instead.", $expected_theme, $theme));
    }
  }

  /**
   * @Then /^I should see a select element named "([^"]*)" containing "([^"]*)" as an option$/
   */
  public function iShouldSeeASelectElementNamedContainingAsAnOption($select, $optionValue) {
    $selectElement = $this->getSession()->getPage()->find('named', array('select', "\"{$select}\""));
    if (!$selectElement) {
      throw new \Exception(sprintf("Did not find a <select> element '%s'.", $select));
    }
    $optionElement = $selectElement->find('named', array('option', "\"{$optionValue}\""));
    if (!$optionElement) {
      throw new \Exception(sprintf("Did not find a <select> element '%s' with <option> '%s'.", $select, $optionValue));
    }
  }

  /**
   * @Given /^I should see a select element named "([^"]*)" that does not contain "([^"]*)" as an option$/
   */
  public function iShouldSeeASelectElementNamedThatDoesNotContainAsAnOption($select, $optionValue) {
    $selectElement = $this->getSession()->getPage()->find('named', array('select', "\"{$select}\""));
    if (!$selectElement) {
      throw new \Exception(sprintf("Did not find a <select> element '%s'.", $select));
    }
    $optionElement = $selectElement->find('named', array('option', "\"{$optionValue}\""));
    if ($optionElement) {
      throw new \Exception(sprintf("Found <select> element '%s' with <option> '%s'.", $select, $optionValue));
    }
  }

  /**
   * @When /^I select the following <fields> with <values>$/
   */
  public function iSelectTheFollowingFieldsWithValues(TableNode $table) {
    $multiple = TRUE;
    $table = $table->getHash();
    foreach ($table as $key => $value) {
      $select = $this->getSession()->getPage()->findField($table[$key]['fields']);
      if(empty($select)) {
        throw new Exception("The page does not have the field with id|name|label|value '" . $table[$key]['fields'] . "'");
      }
      // If multiple is always true we get "value cannot be an array" error for single select fields
      $multiple = $select->getAttribute('multiple') ? TRUE : FALSE;
      $this->getSession()->getPage()->selectFieldOption($table[$key]['fields'], $table[$key]['values'], $multiple);
    }
  }

  /**
   * @Given /^I wait (\d+) seconds$/
   */
  public function iWaitSeconds($seconds) {
    sleep($seconds);
  }

  /**
   * Wait for AJAX to finish.
   *
   * @Given /^I wait for AJAX to finish$/
   */
  public function iWaitForAjaxToFinish() {
    $this->getSession()->wait(10000, '(typeof(jQuery)=="undefined" || (0 === jQuery.active && 0 === jQuery(\':animated\').length))');
  }
}
