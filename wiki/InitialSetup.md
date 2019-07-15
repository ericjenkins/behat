# Behat Initial Setup

## Prerequisites

Before proceeding with this setup guide, you will need the following tools:
* git
* php
* composer
* drush
* Commandline-based ssh access to Eve
* Google Chrome

## Steps to begin writing and running Behat tests on your local PC.

1. Install [PhantomJS](http://phantomjs.org).

    * In Ubuntu:

        ```sh
        # Install PhantomJS (http://phantomjs.org/download.html)
        sudo npm install -g phantomjs-prebuilt
        ```

    * In Windows:

        * Download [PhantomJS](http://phantomjs.org/download.html)
        * Unzip the file to `C:\Users\staff\Downloads` and name the directory:
          `phantomjs`

2. Download this repo to your local development environment.

    ```sh
    cd [path to local dev projects]
    git clone git@gitlab.veritashealth.com:dev/testing-suite.git
    cd testing-suite
    ```

3. Download php dependencies with Composer.

    ```sh
    composer install
    ```

4. Copy `drush/` files to local `.drush/` directory.

    ```sh
    cp -R drush/* ~/.drush/
    # Check your installed drush version number.
    drush --version
    # If you are testing Drupal 7:
    cp ~/.drush/behat-drush-endpoint/behat.d7.drush.inc ~/.drush/behat-drush-endpoint/behat.drush.inc
    # If you are testing Drupal 8:
    cp ~/.drush/behat-drush-endpoint/behat.d8.drush.inc ~/.drush/behat-drush-endpoint/behat.drush.inc
    ```

5. Try running a Blackbox-based behat test.

    ```sh
    vendor/bin/behat features/blackboxDriver.feature
    ```

6. Try running a behat test that incorporates Drush.

    ```sh
    vendor/bin/behat features/drushDriver.feature
    ```

7. Try running a JS-based behat test.

    ```sh
    vendor/bin/behat -p chrome features/jsDesktopDriver.feature
    ```
