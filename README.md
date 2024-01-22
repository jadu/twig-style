# Jadu Twig Style

Jadu Twig style is powered by [Twig-CS-Fixer](https://github.com/VincentLanglet/Twig-CS-Fixer).

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

## Jadu Twig Coding Standard Rules

This standard is based on the [official Twig coding standards](https://twig.symfony.com/doc/3.x/coding_standards.html), with the following additions and changes:

### Block spacing

There should be one blank line before block tags and one blank line after endblock tags.

```twig

{% block aside %}
    <div class="aside">

        {% block aside_inner %}

            {% block before_primary_supplements %}
            {% endblock %}

            {% block primary_supplements %}
            {% endblock %}

            {% block after_primary_supplements %}
            {% endblock %}

        {% endblock %}

    </div>
{% endblock %}


```

The following exceptions apply:

- Inline blocks are allowed.

    ```twig
    <body class="{% block body_classes %}{{ bodyClasses }}{% endblock %}">
    ```

- Comments on the line above block tags are allowed.

    ```twig
    {# This block adds a container around the aside #}
    {% block aside_container %}
    ```

### Endblock names

Any `endblock` tags must be followed by the name of the block they are closing.

```twig
{% block aside_container %}
{% endblock aside_container %}
```

### No spaceless tags

The `spaceless` tag was deprecated in Twig 1.38 and 2.7.3[^1] and an equivalent `spaceless` filter was introduced. Usages of the `spaceless` tag are replaced with the equivalent `apply spaceless` filter.

```twig
{% apply spaceless %}
{% endapply %}
```

### Punctuation spacing

A single space is required after the opening and before the closing of a hash.

```twig
{{ { 'foo': 'bar', 'baz': 'qux' } }}
```

The following exceptions apply:

- Empty hashes must not contain any whitespace.

```twig
{% set emptyHash = {} %}
```

[^1]: https://symfony.com/blog/better-white-space-control-in-twig-templates#added-a-spaceless-filter
