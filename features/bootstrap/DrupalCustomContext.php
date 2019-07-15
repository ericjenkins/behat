<?php

/**
 * @file
 * Drupal Custom Context.
 *
 * Adapted from:
 * - https://www.drupal.org/project/drupalextension/issues/1846828
 */

use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use Behat\Behat\Hook\Scope\AfterScenarioScope;
use Behat\Testwork\Hook\Scope\BeforeSuiteScope;
use Behat\Testwork\Hook\Scope\AfterSuiteScope;
use Behat\Behat\Tester\Exception\PendingException;
use Drupal\DrupalExtension\Context\DrupalContext;

/**
 * Defines application features from the specific context.
 */
class DrupalCustomContext extends DrupalContext implements SnippetAcceptingContext {

  /**
   * Watchdog starting id.
   *
   * @var int
   */
  protected $startWid;

  /**
   * Watchdog Warnings counter.
   *
   * @var int
   */
  protected static $watchdogWarnings = array();

  /**
   * Watchdog Errors counter.
   *
   * @var int
   */
  protected static $watchdogErrors = array();

  /**
   * Log in as existing drupal user.
   *
   * Overrides DrupalContext::assertLoggedInByName() to log in as an existing
   * user instead of a randomly-generated one. No user entity should be created
   * or destroyed on the site.
   */
  public function assertLoggedInByName($name) {
    $user = (object) array(
      'name' => $name,
    );

    // Login.
    $this->login($user);
  }

  /**
   * Log-in the given user by using `drush user-login`.
   *
   * Overrides DrupalContext::login() because the default method of logging
   * in via the `/user` page with user/pass field values was failing to work as
   * intended. This method bypasses the `/user` page by utilizing `drush uli`.
   *
   * @param \stdClass $user
   *   The user to log in.
   */
  public function login(\stdClass $user) {
    $domain = $this->getMinkParameter('base_url');
    // Pass base url to drush command.
    $uli = $this->getDriver('drush')->drush('uli', array(
      "'" . $user->name . "'",
      "--browser=0",
      "--uri=$domain",
    ));
    // Trim EOL characters.
    $uli = trim($uli);
    $this->getSession()->visit($uli);
  }

  /**
   * Take note of the most recent Watchdog ID, for tracking new log events.
   *
   * @BeforeScenario @api,@javascript
   */
  public function recordStartWatchdogId(BeforeScenarioScope $scope) {
    $tags = array_merge($scope->getFeature()->getTags(), $scope->getScenario()->getTags());

    // Bypass the error checking if the scenario has the @errors tag.
    if (in_array('noerrors', $tags)) {
      return;
    }

    $options = ['format' => 'json', 'count' => '1'];
    $log = json_decode($this->getDriver('drush')->drush('watchdog-show', [], $options), TRUE);

    $this->startWid = array_keys($log)[0];
    print_r($this->stardWid);
  }

  /**
   * Checks up to 10 latest log messages for warnings/errors.
   *
   * Checks up to 10 messages after the scenario starts.
   * - Use tag @nowarnings to ignore Watchdog warnings and notices.
   * - Use tag @noerrors to ignore all Watchdog messages.
   *
   * Adapted from:
   *   - https://git.drupalcode.org/project/lightning/blob/HEAD/tests/features/bootstrap/lightning.behat.inc
   *   - https://www.drupal.org/project/drupalextension/issues/2943574
   *
   * @AfterScenario @api,@javascript
   */
  public function checkWatchdog(AfterScenarioScope $scope) {
    $tags = array_merge($scope->getFeature()->getTags(), $scope->getScenario()->getTags());

    // Bypass the error checking if the scenario has the @errors tag.
    if (in_array('noerrors', $tags)) {
      return;
    }

    $error_levels = ['emergency', 'alert', 'critical', 'error'];
    $warning_levels = ['warning', 'notice'];

    $severity_levels = $error_levels;
    if (!in_array('nowarnings', $tags)) {
      $severity_levels = array_merge($severity_levels, $warning_levels);
    }

    // Drush command options for watchdog-show.
    $options = [
      'format' => 'json',
    ];

    $log = json_decode($this->getDriver('drush')->drush('watchdog-show', [], $options), TRUE);

    if (!empty($log)) {
      $warnings = array();
      $errors = array();
      foreach ($log as $wid => $entry) {
        if ($wid > $this->startWid && in_array($entry['severity'], $severity_levels)) {
          // Make the substitutions easier to read in the log.
          $msg = $entry['date'] . " - " . $entry['type'] . "\n";
          $msg .= ucfirst($entry['severity']) . ": " . $entry['message'];
          if (in_array($entry['severity'], $warning_levels)) {
            $warnings[$wid] = $msg;
            self::$watchdogWarnings[] = $scope->getFeature()->getFile() . ":" . $scope->getScenario()->getLine();
          }
          elseif (in_array($entry['severity'], $error_levels)) {
            $errors[$wid] = $msg;
            self::$watchdogErrors[] = $scope->getFeature()->getFile() . ":" . $scope->getScenario()->getLine();
          }
        }
      }

      if (!empty($warnings) && !empty($errors)) {
        $events = array_merge($warnings, $errors);
        ksort($events);
        throw new Exception(sprintf("Drupal warnings & errors logged to Watchdog in this scenario:\n\n%s\n\n",
          implode("\n\n", $events)));
      }
      elseif (!empty($warnings)) {
        ksort($warnings);
        throw new PendingException(sprintf("Drupal warnings logged to Watchdog in this scenario:\n\n%s",
          implode("\n\n", $warnings)));
      }
      elseif (!empty($errors)) {
        ksort($errors);
        throw new Exception(sprintf("Drupal errors logged to Watchdog in this scenario:\n\n%s\n\n",
          implode("\n\n", $errors)));
      }
    }
  }

  /**
   * Report any recorded watchdog log events.
   *
   * @param Behat\Behat\Hook\Scope\AfterSuiteScope $scope
   *   After Suite hook scope.
   *
   * @AfterSuite
   */
  public static function reportWatchdogEvents(AfterSuiteScope $scope) {
    if (!empty(self::$watchdogWarnings) && !empty(self::$watchdogErrors)) {
      throw new Exception(sprintf("Drupal warnings & errors thrown during scenarios:\n\n%s\n\n",
        implode("\n", array_unique(array_merge(self::$watchdogWarnings, self::$watchdogErrors)))));
    }
    elseif (!empty(self::$watchdogWarnings)) {
      throw new PendingException(sprintf("Drupal warnings thrown during scenarios:\n\n%s",
        implode("\n", array_unique(self::$watchdogWarnings))));
    }
    elseif (!empty(self::$watchdogErrors)) {
      throw new Exception(sprintf("Drupal errors thrown during scenarios:\n\n%s\n\n",
        implode("\n", array_unique(self::$watchdogErrors))));
    }
  }

}
