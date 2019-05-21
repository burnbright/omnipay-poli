# Omnipay: Poli

**Poli driver for the Omnipay PHP payment processing library**

Website: http://www.polipay.co.nz
Developer Docs: http://www.polipaymentdeveloper.com/

[![Build Status](https://travis-ci.org/burnbright/omnipay-poli.png?branch=master)](https://travis-ci.org/burnbright/omnipay-poli)
[![Latest Stable Version](https://poser.pugx.org/burnbright/omnipay-poli/version.png)](https://packagist.org/packages/burnbright/omnipay-Poli)
[![Total Downloads](https://poser.pugx.org/burnbright/omnipay-poli/d/total.png)](https://packagist.org/packages/burnbright/omnipay-poli)

[Omnipay](https://github.com/omnipay/omnipay) is a framework agnostic, multi-gateway payment
processing library for PHP 5.3+. This package implements Poli support for Omnipay.

## Installation

Omnipay is installed via [Composer](http://getcomposer.org/). To install, simply add it
to your `composer.json` file:

```json
{
    "require": {
        "burnbright/omnipay-poli": "~1.0"
    }
}
```

And run composer to update your dependencies:

    $ curl -s http://getcomposer.org/installer | php
    $ php composer.phar update

## Basic Usage

The following gateways are provided by this package:

* Poli

For general usage instructions, please see the main [Omnipay](https://github.com/omnipay/omnipay)
repository.

## Tests

Run test suite with docker:
```sh
docker run -v $(pwd):/app --rm php:5.6 bash -c "cd /app; php -d date.timezone=Pacific/Auckland /app/vendor/bin/phpunit"
```

## Support

If you are having general issues with Omnipay, we suggest posting on
[Stack Overflow](http://stackoverflow.com/). Be sure to add the
[omnipay tag](http://stackoverflow.com/questions/tagged/omnipay) so it can be easily found.

If you want to keep up to date with release anouncements, discuss ideas for the project,
or ask more detailed questions, there is also a [mailing list](https://groups.google.com/forum/#!forum/omnipay) which
you can subscribe to.

If you believe you have found a bug, please report it using the [GitHub issue tracker](https://github.com/burnbright/omnipay-poli/issues), or better yet, fork the library and submit a pull request.
