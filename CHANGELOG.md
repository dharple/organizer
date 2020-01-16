# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]
### Changed
- Reduced complexity of import/export logic.

## [0.2.0] - 2020-01-15
### Added
- The ability to export from the command line. [#8]
- The ability to import from the command line. [#8]
- A Box Number separate from the ID field on the box table.  This ensures data
  integrity when exporting and re-importing the data, and it sets up the
  ability to have multiple users with different data sets. [#7] [#8] [#27]
- Create/Update/Delete timestamps on all entities using [DoctrineExtension].
  [#21] [#22] [#23]
- The ability to list boxes and locations from the command line.
- The ability to move boxes _en masse_ to a location from the command line.
- Support for YAML exports and imports. [#8] [#21]
- Migrations for MySQL.

### Changed
- PHPCS rules are now simpler.  This includes alphabetizing methods and
  properties, thanks to [ork/phpcs].
- Export formats that support indentation (JSON, XML, YAML) use it.

## [0.1.0] - 2020-01-07
### Added
- Basic box management. Create and edit boxes, locations, and box types.
- Basic user management.
- Export basic box information, or all information.

[Unreleased]: https://github.com/dharple/organizer/compare/0.2.0...develop
[0.2.0]: https://github.com/dharple/organizer/compare/0.1.0...0.2.0
[0.1.0]: https://github.com/dharple/organizer/releases/tag/0.1.0

[#27]: https://github.com/dharple/organizer/issues/27
[#23]: https://github.com/dharple/organizer/issues/23
[#22]: https://github.com/dharple/organizer/issues/22
[#21]: https://github.com/dharple/organizer/issues/21
[#8]: https://github.com/dharple/organizer/issues/8
[#7]: https://github.com/dharple/organizer/issues/7

[DoctrineExtension]: https://github.com/Atlantic18/DoctrineExtensions
[ork/phpcs]: https://github.com/AlexHowansky/ork-phpcs
