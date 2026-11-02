# Changelog

All notable changes to Global New Topic are documented in this file.

## [1.0.1] - 2026-11-02

### Added

- Added a package upgrade path from version 1.0.0 to 1.0.1.
- Added the complete `Sources` and `Themes` files to the repository.

### Fixed

- Fixed the global composer not being shown to guests who may submit moderated topics via SMF's `post_unapproved_topics` permission.
- Initialized the JavaScript `current_board` value and updated it whenever the destination board changes, so native attachment uploads use the selected board.
- Integrated SMF 2.1's native guest name, email, and post verification controls.

### Removed

- Removed the unused `mcp_gnt_get_message_icons()` function.

## [1.0.0] - 2026-10-03

### Added

- Initial release.
- Added a collapsible global new-topic composer to normal SMF 2.1 forum pages.
- Added a category-grouped destination-board selector using native SMF posting permissions.
- Added native SMF editor, preview, draft, attachment, and additional-option support.
- Added per-member-group visibility settings and English, German, and German informal translations.

[1.0.1]: https://github.com/Matthias1372/SMF-Global-New-Topic/compare/48df2e15d2512f643079cb037be8c5825265f6a1...95a275c8111a39e93c8e3be26fd9f2dc4004b70d
[1.0.0]: https://github.com/Matthias1372/SMF-Global-New-Topic/releases/tag/1.0.0
