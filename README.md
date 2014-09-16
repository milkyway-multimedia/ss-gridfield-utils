GridField Utilities
======
**GridField Utilities** are a collection of GridField components that you can use with any GridField.

Includes:
- Milkyway\Assets : This is a class that gives additional functionality to the Silverstripe Requirements Engine. The new backend is automatically disabled for the administration section.
- Milkyway\Director : Some additional controller specific methods and globals

### Milkyway\Assets
This adds a couple of new methods that you can control:

- Milkyway\Assets::defer($file) : Defer a file (loaded after rest of content has finished downloading, using Google Async method)
- Milkyway\Assets::inline($file, $top = false) : Inline a file (output contents of file directly to specific section
- Milkyway\Assets::add(array $files, 'first/last/defer/inline/inline-head', $before = '') : Add a requirement to the page in a specific section/way.
- Milkyway\Assets::remove(array $files, 'first/last/defer/inline/inline-head') : Remove a requirement (only works on those added using this interface). If you leave out the second argument, it will search all requirements and remove it

## Install
Add the following to your composer.json file

```

    "require"          : {
		"milkyway-multimedia/silverstripe-mwm": "dev-master"
	}

```

## License
* MIT

## Version
* Version 0.1 - Alpha

## Contact
#### Milkyway Multimedia
* Homepage: http://milkywaymultimedia.com.au
* E-mail: mell@milkywaymultimedia.com.au
* Twitter: [@mwmdesign](https://twitter.com/mwmdesign "mwmdesign on twitter")