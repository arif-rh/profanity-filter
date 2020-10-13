# Profanity Filter

A simple class to test if a string has a profanity in it.

Note: 

* This package is extended version from [DeveloperDino\ProfanityFilter](https://github.com/developerdino/ProfanityFilter), with some extra new features.

## NEW FEATURE
---------------
* `cleanWords` : obfuscate only the bad words, original package will obfuscate all sentence [see this issue](https://github.com/developerdino/ProfanityFilter/issues/20).
  ````
  // assume that 'badword' is a bad word
  $words = 'This is a badword';

  echo $check->cleanWords($words);
  // output -> This is a *******

  echo $check->cleanWords($words, '+');
  // output -> This is a +++++++
  ````
* `getBadWordsfound` -> get bad word string from check
  ````
  // assume that 'badword' is a bad word
  $words = 'This is a badword';

  $check->hasProfanity($words);
  echo $check->getBadWordsFound();
  // output -> badword

* Whitelists : Set some whitelist word to fix [false positive issue](https://github.com/developerdino/ProfanityFilter/issues/21) filter
  ````
  // Set whitelist when initialize the class
  $filter = new Check($profinities, $whitelist);

  See `NotProfaneTest` to see more example of whitelist test
  ````

## Checks performed

### Straight matching

Checks string for profanity as it is against list of bad words. E.g. `badword`

### Substitution

Checks string for profanity with characters substituted for each letter. E.g. `bâdΨ0rd`

### Obscured

Checks string for profanity obscured with punctuation between. E.g. `b|a|d|w|o|r|d`

### Doubled

Check string for profanity that has characters doubled up. E.g. `bbaaddwwoorrdd`

### Combinations

Also works with combinations of the above. E.g. `b|â|d|Ψ|0|rr|d`

## Installation

Install this package via composer.

```
php composer.phar require developer/profanity-filter
```

## Usage
```php
/* default constructor */
$check = new Check();
$hasProfanity = $check->hasProfanity($badWords);
$cleanWords = $check->obfuscateIfProfane($badWords);

/* customized word list from file */
$check = new Check('path.to/wordlist.php');

/* customized word list from array */
$badWords = array('bad', 'words'); // or load from db
$check = new Check($badWords);
```

### License

ProfanityFilter is open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT)
