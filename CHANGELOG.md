# Change Log
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/)
and this project adheres to [Semantic Versioning](http://semver.org/).

## 2.0.0-alpha.1 - 2026-06-22
### Added
- Added support for Twig 3; Twig 3.15 or later is now required

### Removed
- Dropped support for Twig 2

### Fixed
- Fixed extra whitespace left when using rule to remove names after endblock tags

## 1.1.1 - 2026-06-09
### Fixed
- Prevented installation of jadu/twig-style with incompatible versions of Twig 3 where twig/twig >= 3.21.0

## 1.1.0 - 2024-11-01
### Added
- Added rule to replace filter tags with the apply tag

## 1.0.0 - 2024-02-19
### Added
- Added Jadu Twig coding standard
- Added rule to enforce block spacing
- Added rule to enforce punctuation spacing
- Added rule to enforce names after endblock tags
- Added rule to remove names after endblock tags
- Added rule to replace spaceless tags with the apply spaceless filter
- Added rule for development purposes to generate a report mapping token types to values when tokenizing a twig template
