# moodle-factor_exemption

* [What is this?](#what-is-this)
* [Branches](#branches)
* [Installation](#installation)
* [Configuration](#configuration)
* [Security issues](#security-issues)
* [Support](#support)

## What is this?

This is a factor plugin for the Moodle [MFA Plugin](https://github.com/catalyst/moodle-tool_mfa) which allows for exempting users from MFA for a configurable period of time. This is useful for performing administration tasks on users such as password resets, without complicating the flow with MFA until other problems are resolved.

## Branches

All Moodle versions should be on the MOODLE_400_STABLE branch.

## Installation

### Step 1

Using Git:

`git clone git@github.com:catalyst/moodle-factor_exemption.git moodle/admin/tool/mfa/factor/exemption`

### Step 2

Run `moodle/admin/cli/upgrade.php` or complete the upgrade via the administration GUI.

## Configuration

Configure order and weight as other factors are configured in the tool_mfa README. The duration can be configured in the settings for the exemption plugin. The default exemption is 24 hours. Updating this value will only take affect for new exemptions/exemption extensions.

## Usage

After configuration, exemptions can be added for a user by any admin, by visiting the 'Manage User Exemptions' page, from the MFA administration menu. Entering a valid username or email address will add an exemption for the user. Extensions or deletions on an exemption can be performed using the buttons in the display table. When a record appears in the table, that user will receive MFA points for this factor at that time.

## Security issues

If you find a security issue with this or any catalyst plugin, please DO NOT open a github issue.

Instead please responsibly disclose the issue in private to us via email:

security@catalyst-au.net

## Support

If you have issues please log them in github here

https://github.com/catalyst/moodle-factor_exemption/issues

Please note our time is limited, so if you need urgent support or want to
sponsor a new feature then please contact Catalyst IT Australia:

https://www.catalyst-au.net/contact-us

This plugin was developed by Catalyst IT Australia:

https://www.catalyst-au.net/

<img alt="Catalyst IT" src="https://cdn.rawgit.com/CatalystIT-AU/moodle-auth_saml2/master/pix/catalyst-logo.svg" width="400">

