<h1 align="center">
  Spryker Community CLI Toolkit
  <br>
</h1>

<h4 align="center">A suite of utilities designed to enhance performance in your day-to-day operations with Spryker Commerce OS.</h4>

<p align="center">
  <a href="#installation">Installation</a> •
  <a href="#usage">Usage</a> •
  <a href="#contributing">Contributing</a>
</p>

<p align="center">
    <a href="https://github.com/spryker-community/cli-toolkit/actions?query=workflow%3ACI+branch%3Amain">
        <img src="https://img.shields.io/github/actions/workflow/status/spryker-community/cli-toolkit/ci.yml?branch=main&label=CI&logo=github&style=flat-square"/>
    </a>
    <a href="https://packagist.org/packages/spryker-community/sprkyer-translations">
        <img src="https://img.shields.io/packagist/v/spryker-community/cli-toolkit?style=flat-square">
    </a>
    <a href="LICENSE">
        <img src="https://img.shields.io/packagist/l/spryker-community/cli-toolkit?style=flat-square">
    </a>
    <a href="https://commercequest.space/">
        <img src="https://img.shields.io/badge/join-commercequest-blue.svg?logo=data:image/svg%2bxml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iVVRGLTgiIHN0YW5kYWxvbmU9Im5vIj8+CjwhRE9DVFlQRSBzdmcgUFVCTElDICItLy9XM0MvL0RURCBTVkcgMS4xLy9FTiIgImh0dHA6Ly93d3cudzMub3JnL0dyYXBoaWNzL1NWRy8xLjEvRFREL3N2ZzExLmR0ZCI+CjxzdmcgdmVyc2lvbj0iMS4xIiBpZD0iTGF5ZXJfMSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB4bWxuczp4bGluaz0iaHR0cDovL3d3dy53My5vcmcvMTk5OS94bGluayIgeD0iMHB4IiB5PSIwcHgiIHdpZHRoPSIxNnB4IiBoZWlnaHQ9IjE2cHgiIHZpZXdCb3g9IjAgMCAxNiAxNiIgZW5hYmxlLWJhY2tncm91bmQ9Im5ldyAwIDAgMTYgMTYiIHhtbDpzcGFjZT0icHJlc2VydmUiPiAgPGltYWdlIGlkPSJpbWFnZTAiIHdpZHRoPSIxNiIgaGVpZ2h0PSIxNiIgeD0iMCIgeT0iMCIKICAgIGhyZWY9ImRhdGE6aW1hZ2UvcG5nO2Jhc2U2NCxpVkJPUncwS0dnb0FBQUFOU1VoRVVnQUFBQkFBQUFBUUNBTUFBQUFvTFE5VEFBQUFCR2RCVFVFQUFMR1BDL3hoQlFBQUFDQmpTRkpOCkFBQjZKZ0FBZ0lRQUFQb0FBQUNBNkFBQWRUQUFBT3BnQUFBNm1BQUFGM0NjdWxFOEFBQUNXRkJNVkVVQUFBQUFydThBcmU0QXNQSGQKQ3BIckFJdnNBSXpxQUl2WkFJRUFpcjBBc1BEWURaTGVBb2NBcWVnQXJlMEFwdU1BU0dRQWw5Q0xmdjhBREE4QW9OeW5BRnNBWG9IRApCbndBLy84QWxNdUJRYWJvQUlyVkFINEFIeW9BZmF3QWxjMEJDUTBmQUJKQUFDWWpBQlgvQVAvL0FLY0FydThBcnUvc0FJenBBSXJoCkFJWHJBSXZzQUl3QXJlNEFydThBcnU4QXJ1OEFydThBc1BIL0FIVHNBSXpzQUl6cUFJdm9BSXJyQUl6ckFJc0FyT3dBcnU4QXJ1OEEKcCtQTkQ0L3RBSXpsQUlqc0FJd0FyZTRBck8wQW5OWUFvdDhBbHMwOVNJZm9BSXJWQUg2SEFGRFlBSURyQUlzQXJPd0FydThBbGN3QQpOa3NBVm5jQVZXL3BBSVhtQUltbEFHSUFBQURrQUlmbEFJZ0FvTndBcmUwQXBlTUFabzNyQUh6ckFJdlhBSDhTQUF2bUFJanJBSXZiCkFJSUFxZWdBcnU0QWxNc0FBQURtQUlucEFJcStBSEhhQUlIb0FJcnFBSXZJQUhjQXF1b0FxZWdBZktzQUFBRG9BSW5pQUlhcEFHVG4KQUluckFJempBSWVnQUY4QXErc0FwT0VBVW5IS0FIanBBSXJjQUlQWkFJSHJBSXZOQUhvQUFBQUFyZTBBcGVQUkFIdnBBSXJnQUlYUgpBSHppQUlhWEFGa0FydThBck8wQXJPd0FyZTRBc08zQUJIam9BSXJzQUl6b0FJcm5BSW5VQUg0QXErd0FydThBcU9aRlJJYmVBSVBtCkFJam9BSXJxQUl2WUFJQUFsTXNBcXVvQW5OY0FjNThBRmdHWEFGbkRBSFRRQUh2RkFIV1VBRmpDQUhQZ0FJVzlBSENQQUZVQU8xRUEKYkpVQWU2a0FjWnNBUkYwQUFBQUFBUUFRQUFrMUFCOFVBQXdBQUFCdUFFR0JBRXhXQURNQUFBQUFyL0FBcmU3c0FJenFBSXNBcnUvdApBSXdBcnZBQXJ1N3RBSTBBcSt2Ly8vOG0yNkNrQUFBQXZYUlNUbE1BQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBCkFBQUFBQUFBQUFBQUFBb1lLaHdqWnlBSlliM2F0ejBDYk9TN3EvNitEcGI3czE3eSsveDkvdDdydzJmUjBXdk0rem5wdEROS05IdjAKVmdXMDV3ZWs0amdhMWJrTEplSzhQdXVPQTFyOFp4dDMvWHFPN0RzQm05azR0dXJpTTgzQ0R3N0hzbm4ybWdmeHBSL2ZzNkxtT2Y3TwpUSUJFSnVUMTZlRW85ZmpDS01mNis0OERyZnpjYUFkVnd0bkRkcWJ6bGhFamNaUjhOUVFER0NjWkFpWlhIZ1p5N2s2b0FBQUFBV0pMClIwVEhqUVZLV3dBQUFBZDBTVTFGQitjTURnb1VOM2srN0NRQUFBRWJTVVJCVkJqVEFSQUI3LzRBQUFBQUFRSUJBUUVEQkFVR0J3Z0gKQmdBQUNRRUNBU1luSmdvTERDZ3BLaXNzQUFBQkFpMHVMekF4TWpNME5UWTNPRGtBRFE0Nk96eTl2YjQ5UGorL1FNQy9RUUFQRUVMQgpRMFJGUmtkSXdrbEtTOEpNQUFGTlRzRlBVRkZTVTc5VVZWWlh2MWdBV1ZyRFcxd1JFbDFld2w5Z1lXSy9Zd0JrWmNSbVp4TUdhRUZwCmFtdHN2MjF1QUcvQmNIRVVGWEp6d25SMWRuZkNlSGtBZXNON2ZCWVhmWDdDZjRDQnY3K0Nnd0NFd1lVWUdScUdoNytJaWIvQ2lvc2IKQUl6QmpZNlBrSkdTdjVPVXY3K1ZsaHdBbDhHTW1MMlptcHZGeGNXY25jS2Vud0Nnb2NUR29xT2twYWFucUttcXE2eXRBSzZ2c0xHeQpzeDIwdGJhM3VMbTZ1d0FBSG5LOHVCOEFJQUFoSWlNQUpDUWxBR2pDWmVjTTg2dHVBQUFBSlhSRldIUmtZWFJsT21OeVpXRjBaUUF5Ck1ESXpMVEV5TFRFMFZERXdPakl3T2pVMEt6QXdPakF3RG13REFRQUFBQ1YwUlZoMFpHRjBaVHB0YjJScFpua0FNakF5TXkweE1pMHgKTkZReE1Eb3lNRG8xTkNzd01Eb3dNSDh4dTcwQUFBQW9kRVZZZEdSaGRHVTZkR2x0WlhOMFlXMXdBREl3TWpNdE1USXRNVFJVTVRBNgpNakE2TlRVck1EQTZNRENPVTVIV0FBQUFBRWxGVGtTdVFtQ0MiIC8+Cjwvc3ZnPgo=&style=flat-square"/>
    </a>
</p>

## Installation

### Git clone

```bash
git clone https://github.com/spryker-community/cli-toolkit && cd cli-toolkit
composer install
bin/cli-toolkit
```

### Composer 

```bash
composer require --dev spryker-community/cli-toolkit
vendor/bin/cli-toolkit
```

## Usage

You can see all the available commands by executing:

```bash
bin/cli-toolkit --help
```

### Prerequisites

Some of the commands use external services like OpenAI or DeepL that requires API credentials. In those cases you will require to add the credential to your `.env` file.

1. Copy the template for the environment variables:

```bash
cp .env.dist .env
```

2. Add your own auth credentials for the service you are using:
```
CLI_TOOLKIT_DEEPL_API_AUTH_KEY="your_deepl_api_token"
CLI_TOOLKIT_CHATGPT_API_AUTH_KEY="your_chatgpt_api_token"
```

### Generate translations for the Spryker Yves storefront

```bash
bin/cli-toolkit translation:yves:generate
```

#### Arguments

* `locales`: List of locales to which we want the Spryker glossary translated.

#### Options

* `--working-dir`: If specified, use the given directory as Spryker project working directory.
* `--translation-engine`: The translation engine to be used for translation generation. Allowed values are deepl or chatgpt [default: "chatgpt"]

#### Examples

1. Generate missing translations Yves glossary to Spanish from Spain (es_ES) by ChatGPT.

```bash
bin/cli-toolkit translation:yves:generate es_ES --working-dir=../b2b-demo-marketplace- --translation-engine=chatgpt
```

2. Generate missing translations Yves glossary to Spanish from Spain (es_ES) and French from France by DeepL.

```bash
bin/cli-toolkit translation:yves:generate es_ES fr_FR --working-dir=../b2b-demo-marketplace --translation-engine=deepl
```

### Generate translations for the Spryker Zed backoffice

```bash
bin/cli-toolkit translation:zed:generate
```

#### Arguments

* `locales`: List of locales to which we want the Spryker glossary translated.

#### Options

* `--working-dir`: If specified, use the given directory as Spryker project working directory.
* `--translation-engine`: The translation engine to be used for translation generation. Allowed values are `deepl` or `chatgpt` [default: `chatgpt`]

#### Examples

1. Generate missing translations Zed glossary to Spanish from Spain (es_ES) by ChatGPT.

```bash
bin/cli-toolkit translation:zed:generate es_ES --working-dir=../b2b-demo-marketplace --translation-engine=chatgpt
```

2. Generate missing translations Zed glossary to Spanish from Spain (es_ES) and French from France by DeepL.

```bash
bin/cli-toolkit translation:zed:generate es_ES fr_FR --working-dir=../b2b-demo-marketplace --translation-engine=deepl
```

## Contributing

We love contributions, big or small.  Please don't forget to read the [contribution guidelines](CONTRIBUTING.md).

## License

This package is released under the [MIT license](LICENSE)

#

<p align="center">
Supported with :heart: by the Spryker Community
</p>
