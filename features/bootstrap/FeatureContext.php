<?php

/**
 * @file
 * Custom Context.
 */

use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\Behat\Tester\Exception\PendingException;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;
use Drupal\DrupalExtension\Context\RawDrupalContext;

require_once 'PHPUnit/Autoload.php';
require_once 'PHPUnit/Framework/Assert/Functions.php';

/**
 * Defines application features from the specific context.
 */
class FeatureContext extends RawDrupalContext implements SnippetAcceptingContext {

  /**
   * Initializes context.
   *
   * Every scenario gets its own context instance.
   * You can also pass arbitrary arguments to the
   * context constructor through behat.yml.
   */
  public function __construct() {
  }

  /**
   * Wait a specified number of seconds.
   *
   * Example: When I wait 1 second
   * Example: And wait 2 seconds
   *
   * @When /^(?:|I )wait (\d+) seconds?$/
   */
  public function waitSeconds($seconds) {
    /*    * Adapted from:
     * - https://michaelheap.com/behat-selenium2-webdriver-with-minkextension/
     */
    $this->getSession()->wait(1000 * $seconds);
  }

  /**
   * Prints last response to console.
   *
   * @Then /^(?:|I )debug$/
   */
  public function debug() {
    echo ($this->getSession()->getCurrentUrl() . "\n\n"
      . $this->getSession()->getPage()->getContent());
  }

  /**
   * Enable/Disable/Uninstall a drupal module with drush.
   *
   * Example: Given I enable the module "update"
   * Example: And I disable module "update"
   *
   * @Given /^(?:|I )(enable|disable|uninstall)(?: the|) module "(?P<module>[^"]*)"$/
   */
  public function alterDrupalModule($operation, $module) {
    $this->getDriver('drush')->drush('pm-' . $operation, [$module], ['yes' => NULL]);
  }

  /**
   * Remove any created users.
   *
   * Overrides RawDrupalContext::cleanUsers(), because the deleteUser() method
   * called by RawDrupalContext::cleanUsers() does not appear to work with the
   * current version of Drush that we use on our Linode cloud servers.
   */
  public function cleanUsers() {
    // Remove any users that were created.
    if ($this->userManager->hasUsers()) {
      foreach ($this->userManager->getUsers() as $user) {
        /* Buggy method to bypass:
         * $this->getDriver('drush')->userDelete($user);
         */
        $arguments = array(sprintf('"%s"', $user->name));
        $options = array(
          'yes' => NULL,
          /* 'delete-content' => NULL, */
        );
        $this->getDriver('drush')->drush('user-cancel', $arguments, $options);
        /* End bypass. */
      }
      $this->getDriver('drush')->processBatch();
      $this->userManager->clearUsers();
      if ($this->loggedIn()) {
        $this->logout();
      }
    }
  }

}
