<?php

/**
 * @file
 * Drupal Custom Context.
 *
 * Adapted from:
 * - https://www.drupal.org/project/drupalextension/issues/1846828
 */

use Behat\Behat\Context\SnippetAcceptingContext;
use Drupal\DrupalExtension\Context\DrupalContext;

/**
 * Defines application features from the specific context.
 */
class DrupalCustomContext extends DrupalContext implements SnippetAcceptingContext {

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

}
