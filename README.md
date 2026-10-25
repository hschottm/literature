[![Latest Version on Packagist](http://img.shields.io/packagist/v/hschottm/contao-literature.svg?style=flat)](https://packagist.org/packages/hschottm/contao-literature)
[![Installations via composer per month](http://img.shields.io/packagist/dm/hschottm/contao-literature.svg?style=flat)](https://packagist.org/packages/hschottm/contao-literature)
[![Installations via composer total](http://img.shields.io/packagist/dt/hschottm/contao-literature.svg?style=flat)](https://packagist.org/packages/hschottm/contao-literature)
# contao-literature
This extension allows the input of literature references in the backend and list output of literature references in the frontend.
You can use the literature reference editor in the backend to enter and group your literature references. The editor distinguishes between the following types of references:

* Periodically published journals, and so on.
* Non-periodically published works
* Parts of a non-periodic work (e.g. book chapters)

The literature references can be displayed in the frontend with the help of a literature list module for literature references. The output style of the literature references can be defined using your own templates. Literature references can be tagged if the hschottm/tags Contao bundle is installed. The list output can be filtered with tags.
Single literature references can be displayed everywhere in the frontend (e.g. articles) using the insert tag insert_literature. The module literature search is a filter module that filters literature lists by matching search terms.


## Contao 5.7 LTS

This branch requires PHP 8.3+ and Contao 5.7.x. It uses the migrated
`contao-multitextwizard` development branch until a compatible stable release
is available. With the repositories configured in your Contao project:

```sh
composer require hschottm/contao-literature:dev-contao57 hschottm/contao-multitextwizard:dev-contao57 -W
php vendor/bin/contao-console contao:migrate
```

`dev-master` is also accepted for multitextwizard after merging its migration.
Existing references and author/editor tables are retained. The migration fills
missing author sort values; it does not replace existing values. Optional tag
editing/filtering requires the Contao 5.7-compatible `hschottm/tags` version.

All supplied templates now use Twig. Convert custom `.html5` overrides to Twig
before using them with this version. The reference template is
`litref_standard.html.twig`; list and search templates are
`mod_literaturelist.html.twig` and `mod_literaturesearch.html.twig`.
The insert tag remains `{{insert_literature::123}}`.
Pagination uses a separate `page_l<ID>` query parameter per list module.

Images use Contao's Image Studio and figure component. Downloads delegate to
Contao 5.7's supported legacy `ContentDownloads` implementation, including its
file selection and download checks; no core method bodies are copied.
BibTeX files are parsed before importing in a database transaction. Temporary
uploads are removed after processing, including on failure.

## Integration checks

`tests/integration.php` boots a Contao 5.7 installation with this bundle and
checks DCA loading, Twig, BibTeX import, reference rendering, author editing,
author-sort migration, ISBN validation, insert tags, search, sorting,
pagination, optional tag filtering, images and downloads. It uses temporary
SQL tables and removes its file fixtures. Run against a development installation
with database access and permission to create temporary tables:

```sh
LITERATURE_TEST_APP=/path/to/contao LITERATURE_TEST_PARSER=/path/to/renanbr/bibtex-parser/src php tests/integration.php
```

The test registers the bundle itself; use an installation where it has not yet
been registered. The parser path is only needed if the parser is not already
autoloaded by that installation.
