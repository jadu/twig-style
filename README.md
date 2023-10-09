# Jadu Twig Style

## Installation

1. Require the jadu/twig-style dev dependency:

```sh
composer require --dev jadu/twig-style
```

2. Add the twig-cs-fixer config file `.twig-cs-fixer.php`:

```php
<?php

use Jadu\Style\Twig\Standard\JaduStandard;
use TwigCsFixer\Config\Config;
use TwigCsFixer\File\Finder;
use TwigCsFixer\Ruleset\Ruleset;

$finder = Finder::create()
    ->in(__DIR__ . '/src')
    ->ignoreVCSIgnored(true);

$config = new Config();
$config->setFinder($finder);

$ruleset = new Ruleset();
$ruleset->addStandard(new JaduStandard());
$config->setRuleset($ruleset);

return $config;

```

3. Add `.twig-cs-fixer.cache` to your project's `.gitignore` file.

## Usage

### Dry run

To lint your project's twig files, run the following dry run command:

```sh
vendor/bin/twig-cs-fixer lint
```

This command will return a list of twig-cs-fixer violations and is recommended for build tasks.

### Fix

To fix any reported fixable violations, run the following fix command:

```sh
vendor/bin/twig-cs-fixer lint --fix
```
