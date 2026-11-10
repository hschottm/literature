![Contao Literature](logo.svg)

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

## Changelog

See [CHANGELOG.md](CHANGELOG.md) for changes grouped by release tag.

## Updating existing installations

Run the Contao database migrations after updating the extension, using the
Contao Manager or the command line:

```sh
php vendor/bin/contao-console contao:migrate
```

Custom `.html5` template overrides must be converted to Twig. The supplied
reference template is `litref_standard.html.twig`; list and search templates
are `mod_literaturelist.html.twig` and `mod_literaturesearch.html.twig`.
The insert tag remains `{{insert_literature::123}}`.

## License

This extension is licensed under the GNU Lesser General Public License, version 3
or (at your option) any later version (`LGPL-3.0-or-later`). See [LICENSE](LICENSE).

## Package icon

The scalable [logo.svg](logo.svg) shows a document with a raised [1] citation
marker on an anthracite background with an orange corner. All shapes are vector paths.
For display in the Contao Manager, the logo is maintained separately in
[contao/package-metadata](https://github.com/contao/package-metadata) under
`meta/hschottm/contao-literature/logo.svg`.