# Changelog

Changes are grouped by release line, newest version first within each line.
This repository-wide history includes all release tags; entries for other lines
do not change the compatibility of the installed package. Historical entries
were reconstructed from tag diffs and the former README release history.

## Contao 5.7 — 4.2.x

### [4.2.3] - 2026-11-10

- Moved release history from the README into a complete changelog with dated entries and links for every release tag.
- Kept update instructions in the README as a separate top-level section. No runtime or dependency changes.

### [4.2.2] - 2026-11-10

- Added the shared SVG package icon with a document and [1] citation marker, a README preview and Contao Manager logo documentation.
- Normalized the package license and source notices to LGPL-3.0-or-later.

### [4.2.1] - 2026-10-28

- Added twelve language catalogues (Czech, Spanish, Persian, French, Italian, Japanese, Dutch, Polish, Russian, Slovenian, Swedish and Turkish), bringing coverage to fourteen languages including English and German.

### [4.2.0] - 2026-10-28

* Added support for Contao 5.7 and PHP 8.3 or newer.
* Migrated all supplied templates to Twig.
* Preserved existing literature references and author/editor records during the update. Missing author sort values are filled automatically without overwriting existing values.
* Updated optional tag editing and filtering for the Contao 5.7-compatible `hschottm/tags` extension.
* Added independent pagination for each literature list on a page.
* Updated image rendering and file downloads for Contao 5.7.
* Improved BibTeX imports: files are validated before database changes are made, and temporary uploads are removed even if an import fails.
* Set the MultiTextWizard requirement to the stable ^1.4 release line.

## Contao 4.4 — 4.0.x

### [4.0.1] - 2026-11-10

- Added the shared SVG package icon with a document and [1] citation marker, a README preview and Contao Manager logo documentation.

### [4.0.0] - 2020-06-19

- Released the Contao 4.4 bundle with Composer and Contao Manager integration.
- Rebuilt BibTeX import using the BibTeX parser dependency and integrated MultiTextWizard.
- Kept tagging optional and documented reference editing, bibliography lists, search and insert tags.

## Contao 3 — 3.2.x

### [3.2.1] - 2026-11-10

- Added the shared SVG package icon with a document and [1] citation marker, a README preview and Contao Manager logo documentation.
- Asset/documentation update for the legacy Contao 3 line.

### [3.2.0] - 2017-03-15

- Fixed the tag-filter option in literature lists.
- Added PHP 7 compatibility to the legacy classes and frontend modules.

## Legacy Contao 2.11

### [legacy-contao-2.11-icon-1] - 2026-11-10

- Added the shared SVG package icon with a document and [1] citation marker, a README preview and Contao Manager logo documentation.
- Asset/documentation update for the legacy Contao 2.11 branch.

[4.2.3]: https://github.com/hschottm/literature/releases/tag/4.2.3
[4.2.2]: https://github.com/hschottm/literature/releases/tag/4.2.2
[4.2.1]: https://github.com/hschottm/literature/releases/tag/4.2.1
[4.2.0]: https://github.com/hschottm/literature/releases/tag/4.2.0
[4.0.1]: https://github.com/hschottm/literature/releases/tag/4.0.1
[4.0.0]: https://github.com/hschottm/literature/releases/tag/4.0.0
[3.2.1]: https://github.com/hschottm/literature/releases/tag/3.2.1
[3.2.0]: https://github.com/hschottm/literature/releases/tag/3.2.0
[legacy-contao-2.11-icon-1]: https://github.com/hschottm/literature/releases/tag/legacy-contao-2.11-icon-1
