# CMS
A PHP Library that allows you to decode ASN.1 CMS using Basic Encoding Rules (BER)

![build status](https://github.com/adapik/CMS/actions/workflows/tests.yaml/badge.svg?branch=master)
[[![codecov](https://codecov.io/github/adapik/cms/graph/badge.svg?token=KCBH7EV2J0)](https://codecov.io/github/adapik/cms)

[![Latest Stable Version](https://poser.pugx.org/Adapik/cms/v/stable.png)](https://packagist.org/packages/Adapik/cms)
[![Total Downloads](https://poser.pugx.org/Adapik/cms/downloads.png)](https://packagist.org/packages/Adapik/cms)
[![License](https://poser.pugx.org/Adapik/cms/license.png)](https://packagist.org/packages/Adapik/cms)

## Install

```
composer require adapik/cms
```

## About

This package allow to convert common cryptographic structures from binary ASN.1 format to PHP Objects.

- CMS (according to [RFC3852](https://datatracker.ietf.org/doc/html/rfc3852))
- CAdES (according to [RFC5126](https://datatracker.ietf.org/doc/html/rfc5126))
- x.509 Certificates, CRLs (according to [RFC 5280](https://datatracker.ietf.org/doc/html/rfc5280))
- etc.

This is a pure PHP library and does not require any cryptographic extensions (like ext-openssl) to be installed.

You can find examples, how to use this library [here](example)

## How to extend

You can create your own Map (see examples under `Adapik\CMS\Maps` namespace) and Object (see examples under `Adapik\CMS`
namespace) and decode any ASN.1 structure.

## What this package is not

It's not a tool to create or validate CMS or CAdES, but you can build your own tool, using objects from this library.

