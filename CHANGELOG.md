# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [1.0.1] - 2025-09-01

## Changed

- Updated 3rd party dependencies.

## [1.0.0] - 2024-12-01

### Added

- Enabled Symfony failed login throttling.

### Changed

- Box descriptions that are truncated can now be expaneded by clicking on them.
- Gravatar hashes now use SHA256 instead of MD5. [#38]
- Production logs are now written to a file instead of stderr.
- The minimum PHP version is now 8.1.  Applied Rector-suggested changes.
- Tweaked login form.
- Updated code to fix Symfony deprecation warnings.
- Updated deploy script to run `composer dump-env`.
- Upgraded to Bootstrap 5.3.
- Upgraded to Symfony 5.4 authentication and password hashing system.

### Fixed

- Fixed comment for `--format` on `data:export`.
- Fixed issues reported by PHPStan.
- Searching for a box number alwys shows that box first in the result set.

### Security

- Bumped PhpSpreadsheet from v1.29.0 to v2.3.0, to address [CVE-2024-45048] and
  [CVE-2024-45293].  Thanks to [@dependabot].
- Bumped Twig from v3.12.0 to v3.14.0, to address [CVE-2024-45411].
  Thanks to [@dependabot].
- Fixed multiple security alerts via [@dependabot].
- Removed unused RememberMeBadge from Authenticator.

## [0.8.0] - 2023-10-14

### Added

- Added support for hiding boxes in locations from searches.
- Added support for a description on a location.

### Changed

- Searching on a single number will show a hidden box with that exact number,
  as well as full text searches that match that number.
- Simplified search logic slightly.
- Updated 3rd party dependencies.

## [0.7.0] - 2022-01-31

### Changed

- Re-added composer.lock and symfony.lock.
- Removed UTC date time support.
- Updated 3rd party dependencies.
  - Upgraded Symfony to 5.4.

### Fixed

- Avoid a doctrine error for now by locking migrations to v2.x.
- Cleaned up class references.
- Fixed references to the moved Doctrine ManagerRegistry.
- Fixed a bug with export due to a change in behavior around Doctrine
  references.
- Removed unused variables.

### Thanks

- PhpStorm for help with static analysis.

## [0.6.1] - 2020-11-10

### Changed

- Removed symfony.lock.
- Added additional symfony config files after resetting symfony.lock.

## [0.6.0] - 2020-11-10

### Fixed

- The Box Model editor sets data-lpignore so that LastPass doesn't think it's a
  form that needs to be remembered.

### Changed

- Removed composer.lock.

## [0.5.1] - 2020-09-03

### Fixed

- Don't treat Migrations like services

### Changed

- Upgraded to Symfony 5.1.
- Updated 3rd party dependencies.

## [0.4.6] - 2020-09-02

### Changed

- Removed unused package symfony/http-client
- Updated 3rd party dependencies.

## [0.4.5] - 2020-06-20

### Fixed

- Gravatars now load over https.

## [0.4.4] - 2020-06-20

### Changed

- Updated 3rd party dependencies.

### Fixed

- Locked doctrine-migrations-bundle due to [backwards compatibility
  breaks](https://github.com/symfony/orm-pack/pull/22#pullrequestreview-355620860).

## [0.4.3] - 2020-05-21

### Changed

- Updated 3rd party dependencies.

## [0.4.2] - 2020-04-24

### Changed

- Updated 3rd party dependencies.

## [0.4.1] - 2020-04-09

### Changed

- Make titles on home page links.
- Add titles to see all locations and box models.

### Security

- Updated Symfony dependencies to address potential security risks.
  [CVE-2020-5255], [CVE-2020-5274], [CVE-2020-5275].

## [0.4.0] - 2020-01-25

### Added

- The homepage now shows the three most recently modified boxes.
- Added a new page to see recently modified boxes.

### Changed

- Moved the search bar to the top of the content area on small screens.

### Fixed

- Responsive margins and column sizes

## [0.3.0] - 2020-01-22

### Changed

- Upgraded to Symfony 5.0
- Updated the colors on the dolly icon from Font Awesome.

### Fixed

- Removed a flat tire on the PNG version of the dolly icon.

## [0.2.1] - 2020-01-15

### Changed

- Reduced complexity of import/export logic.

### Fixed

- Switched from 0.0.0 to v0.0.0 tags.

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

[Unreleased]: https://github.com/dharple/organizer/compare/v1.0.1...main
[1.0.1]: https://github.com/dharple/organizer/compare/v!.0.0...v1.0.1
[1.0.0]: https://github.com/dharple/organizer/compare/v0.8.0...v1.0.0
[0.8.0]: https://github.com/dharple/organizer/compare/v0.7.0...v0.8.0
[0.7.0]: https://github.com/dharple/organizer/compare/v0.6.1...v0.7.0
[0.6.1]: https://github.com/dharple/organizer/compare/v0.6.0...v0.6.1
[0.6.0]: https://github.com/dharple/organizer/compare/v0.5.1...v0.6.0
[0.5.1]: https://github.com/dharple/organizer/compare/v0.5.0...v0.5.1
[0.5.0]: https://github.com/dharple/organizer/compare/v0.4.6...v0.5.0
[0.4.6]: https://github.com/dharple/organizer/compare/v0.4.5...v0.4.6
[0.4.5]: https://github.com/dharple/organizer/compare/v0.4.4...v0.4.5
[0.4.4]: https://github.com/dharple/organizer/compare/v0.4.3...v0.4.4
[0.4.3]: https://github.com/dharple/organizer/compare/v0.4.2...v0.4.3
[0.4.2]: https://github.com/dharple/organizer/compare/v0.4.1...v0.4.2
[0.4.1]: https://github.com/dharple/organizer/compare/v0.4.0...v0.4.1
[0.4.0]: https://github.com/dharple/organizer/compare/v0.3.0...v0.4.0
[0.3.0]: https://github.com/dharple/organizer/compare/v0.2.1...v0.3.0
[0.2.1]: https://github.com/dharple/organizer/compare/v0.2.0...v0.2.1
[0.2.0]: https://github.com/dharple/organizer/compare/v0.1.0...v0.2.0
[0.1.0]: https://github.com/dharple/organizer/releases/tag/v0.1.0

[#38]: https://github.com/dharple/organizer/issues/38
[#27]: https://github.com/dharple/organizer/issues/27
[#23]: https://github.com/dharple/organizer/issues/23
[#22]: https://github.com/dharple/organizer/issues/22
[#21]: https://github.com/dharple/organizer/issues/21
[#8]: https://github.com/dharple/organizer/issues/8
[#7]: https://github.com/dharple/organizer/issues/7

[CVE-2020-5255]: https://nvd.nist.gov/vuln/detail/CVE-2020-5255
[CVE-2020-5274]: https://nvd.nist.gov/vuln/detail/CVE-2020-5274
[CVE-2020-5275]: https://nvd.nist.gov/vuln/detail/CVE-2020-5275
[CVE-2024-45048]: https://nvd.nist.gov/vuln/detail/CVE-2024-45048
[CVE-2024-45293]: https://nvd.nist.gov/vuln/detail/CVE-2024-45293
[CVE-2024-45411]: https://nvd.nist.gov/vuln/detail/CVE-2024-45411

[DoctrineExtension]: https://github.com/Atlantic18/DoctrineExtensions
[ork/phpcs]: https://github.com/AlexHowansky/ork-phpcs
[@dependabot]: https://github.com/dependabot
