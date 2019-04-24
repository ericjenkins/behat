<?php

/**
 * @file
 * Sample drush aliases file.
 */

// Local environment.
$aliases['local'] = [
  'uri' => 'https://local.sample.com',
  'path-aliases' => [
    '%files' => 'files',
  ],
  // Allow sql overwrites in non-prod enviornments.
  'command-specific' => [
    'sql-sync' => [
      'simulate' => '0',
    ],
  ],
];

// VirtualBox environment.
$aliases['vagrant'] = [
  'uri' => 'https://local.sample.com',
  'remote-host' => 'local.sample.com',
  'remote-user' => 'root',
  'root' => '/var/www/local.sample.com/web',
  'ssh-options' => '-q -o "ForwardAgent yes" -o "StrictHostKeyChecking no" '
  . '-o "LogLevel ERROR" -o "RemoteForward 9222 localhost:9222"',
  'path-aliases' => [
    '%files' => 'files',
    '%drush-script' => '/usr/local/src/composer/vendor/bin/drush',
  ],
];

// Eve environment.
$aliases['dev'] = [
  'uri' => 'https://dev.sample.com',
  'remote-host' => 'dev.sample.com',
  'remote-user' => 'root',
  'root' => '/var/www/dev.sample.com/web',
  'ssh-options' => '-o "ForwardAgent yes"',
  'path-aliases' => [
    '%files' => 'files',
    '%drush-script' => '/usr/local/src/composer/vendor/bin/drush',
  ],
];

// Stage environment.
$aliases['stage'] = [
  'uri' => 'https://stage.sample.com',
  'remote-host' => 'stage.sample.com',
  'remote-user' => 'root',
  'root' => '/var/www/stage.sample.com/web',
  'ssh-options' => '-o "ForwardAgent yes"',
  'path-aliases' => [
    '%files' => 'files',
    '%drush-script' => '/usr/local/src/composer/vendor/bin/drush',
  ],
];

// Prime environment.
$aliases['prod'] = [
  'uri' => 'https://www.sample.com',
  'remote-host' => 'www.sample.com',
  'remote-user' => 'root',
  'root' => '/var/www/www.sample.com/web',
  'ssh-options' => '-o "ForwardAgent yes"',
  'path-aliases' => [
    '%files' => 'files',
    '%drush-script' => '/usr/local/src/composer/vendor/bin/drush',
  ],
  // Prevent accidental sql-sync and rsync overwrites in Prod.
  'command-specific' => [
    'sql-sync' => [
      'simulate' => '1',
    ],
    'sql-drop' => [
      'simulate' => '1',
    ],
    'sql-create' => [
      'simulate' => '1',
    ],
  ],
];

