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

## Release history

### Contao 5.7 support

* Added support for Contao 5.7 and PHP 8.3 or newer.
* Migrated all supplied templates to Twig.
* Preserved existing literature references and author/editor records during the update. Missing author sort values are filled automatically without overwriting existing values.
* Updated optional tag editing and filtering for the Contao 5.7-compatible `hschottm/tags` extension.
* Added independent pagination for each literature list on a page.
* Updated image rendering and file downloads for Contao 5.7.
* Improved BibTeX imports: files are validated before database changes are made, and temporary uploads are removed even if an import fails.

### Updating existing installations

Run the Contao database migrations after updating the extension, using the
Contao Manager or the command line:

```sh
php vendor/bin/contao-console contao:migrate
```

Custom `.html5` template overrides must be converted to Twig. The supplied
reference template is `litref_standard.html.twig`; list and search templates
are `mod_literaturelist.html.twig` and `mod_literaturesearch.html.twig`.
The insert tag remains `{{insert_literature::123}}`.
