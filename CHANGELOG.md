# Changelog

All notable changes to Global New Topic are documented in this file.

## [1.0.5] - 2026-11-05

### Fixed

- Fixed guest previews remaining at "Fetching preview..." by resolving and validating the selected board at preview time and including it in both the preview URL and request data.
- Added a visible error fallback when SMF returns no valid preview XML response.

## [1.0.4] - 2026-11-05

### Changed

- Replaced the custom destination-board database query with SMF's native `getBoardList()` logic, including its board visibility and hierarchy checks.

### Removed

- Removed unused attachment context values, the unused board-list context value, an unused template global, and two unused language strings.

## [1.0.3] - 2026-11-02

### Fixed

- Added the selected board ID directly to every attachment upload request, ensuring SMF can perform board-specific attachment permission checks for members and guests without relying only on Dropzone's cached URL.

## [1.0.2] - 2026-11-02

### Fixed

- Updated the active SMF Dropzone upload URL whenever the destination board changes, ensuring that guest attachment uploads include the selected board and pass board-specific permission checks.

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

[1.0.5]: https://github.com/Matthias1372/SMF-Global-New-Topic/compare/1.0.1...1.0.5
[1.0.1]: https://github.com/Matthias1372/SMF-Global-New-Topic/compare/48df2e15d2512f643079cb037be8c5825265f6a1...95a275c8111a39e93c8e3be26fd9f2dc4004b70d
[1.0.0]: https://github.com/Matthias1372/SMF-Global-New-Topic/releases/tag/1.0.0
