# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [0.3.0] - 2025-03-18

- Fixed a bug where resources were considered deleted right before their
  deletion, which caused problems if the deletion failed. Now resources are
  considered deleted only after their successful deletion.

## [0.2.0] - 2023-09-28

### Fixed

- Avoid fatal error when deleting a media from the item modification page

### Added

- Added a PHP script to export IDs of deleted resources.
  See usage with `php data/scripts/export_since_date.php --help`.

## [0.1.0] - 2023-01-20

Initial release

[0.3.0]: https://github.com/biblibre/omeka-s-module-Necropolis/releases/tag/v0.3.0
[0.2.0]: https://github.com/biblibre/omeka-s-module-Necropolis/releases/tag/v0.2.0
[0.1.0]: https://github.com/biblibre/omeka-s-module-Necropolis/releases/tag/v0.1.0
