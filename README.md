# Omnipay: SecPay

**SecPay for the Omnipay PHP payment processing library**

[![Build Status](https://travis-ci.org/JustinBusschau/omnipay-secpay.png?branch=master)](https://travis-ci.org/JustinBusschau/omnipay-secpay)
[![Latest Stable Version](https://poser.pugx.org/justinbusschau/omnipay-secpay/version.png)](https://packagist.org/packages/justinbusschau/omnipay-secpay)
[![Total Downloads](https://poser.pugx.org/justinbusschau/omnipay-secpay/d/total.png)](https://packagist.org/packages/justinbusschau/omnipay-secpay)

[Omnipay](https://github.com/omnipay/omnipay) is a framework agnostic, multi-gateway payment
processing library for PHP 5.3+. This package implements SecPay support for Omnipay.

[SecPay](http://www.paypoint.net/support/gateway/integration-guides/) is the payment gateway
offering from [PayPoint.net](http://www.paypoint.net/). This package is an implementation of
the PayPoint.net 'Web Freedom' product.

## Installation

Omnipay is installed via [Composer](http://getcomposer.org/). To install, simply add it
to your `composer.json` file:

```json
{
    "require": {
        "justinbusschau/omnipay-secpay": "~2.0"
    }
}
```

And run composer to update your dependencies:

$ curl -s http://getcomposer.org/installer | php
$ php composer.phar update

## TODO

I started building and testing support for a few things that I have not yet had the time to see
through. For this reason you will find there are areas that are incomplete and for which I have
not committed tests. Feel free to pick these up any time :)

* 3D Secure
* SecPay reports - a function that returns transaction history in CSV or XML format

## Basic Usage

The following gateway is provided by this package:

* SecPay - PayPoint.net's Web Freedom product

For general usage instructions, please see the main [Omnipay](https://github.com/omnipay/omnipay)
repository.

## Support

If you are having general issues with Omnipay, we suggest posting on
[Stack Overflow](http://stackoverflow.com/). Be sure to add the
[omnipay tag](http://stackoverflow.com/questions/tagged/omnipay) so it can be easily found.

If you want to keep up to date with release anouncements, discuss ideas for the project,
or ask more detailed questions, there is also a [mailing list](https://groups.google.com/forum/#!forum/omnipay) which
you can subscribe to.

If you believe you have found a bug, please report it using the [GitHub issue tracker](https://github.com/omnipay/authorizenet/issues),
or better yet, fork the library and submit a pull request.
