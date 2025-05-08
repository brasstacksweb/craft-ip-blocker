# IP Blocker

IP address blocker for Craft CMS applications to bock traffic from malicious sources.

## Requirements

This plugin requires Craft CMS 4.0 or later, and PHP 8.0.2 or later.

## Installation

You can install this plugin from the Plugin Store or with Composer.

#### From the Plugin Store

Go to the Plugin Store in your project’s Control Panel and search for “IP Blocker”. Then press “Install”.

#### With Composer

Open your terminal and run the following commands:

```bash
# go to the project directory
cd /path/to/my-project.test

# tell Composer to load the plugin
composer require brasstacksweb/craft-ip-blocker

# tell Craft to install the plugin
./craft plugin/install craft-ip-blocker
```

## Configuration

IP Blocker supports adding one or many conditions to determine if request resulting in an [HTTP exception](https://www.yiiframework.com/doc/api/2.0/yii-web-httpexception) will be recorded and if the requesters I.P. address will be blocked.

![Settings](../docs/images/settings.png)

### Conditions

A condition can be added in the plugin settings and must consist of the following properties:

- __Pattern__: A string or regular expression (excluding the surrounding /) that will be matched against the message of the top level, or previous, exception thrown by the request.
- __Max Attempts__: The maximum number of allowed attempts matching the pattern before the I.P. address is blocked.
- __Detection Window__: The window of time (in seconds) that failed requests are counted toward the maximum.
- __Block Time__: The duration of time (in seconds) that an I.P. address is blocked after matching the pattern and meeting the maximum attempts.

## Stats

Statistics for failed attempts against a condition and I.P. addresses that have been blocked are visible in the IP Blocker section of the CMS.

![Attempts](../docs/images/attempts.png)

![Blocks](../docs/images/blocks.png)
