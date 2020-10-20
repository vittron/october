#LetsEncrypt Manager

## Overview
This plugin is in beta state, more improvements will be added soon.
LetsEncrypt Manager provides simple components and User Utility functions for complex interactions with users.

Let’s Encrypt is a free, automated, and open Certificate Authority

This plugin will only generate certificate and key files. Certificate has to be installed manually on your domain/host

## Installation
1. Install the plugin from store
2. Install this plugin and run
        php artisan october:up
3. You're done :)
4. Make sure to complete the instructions at https://octobercms.com/docs/setup/installation#crontab-setup for auto renew to work.


## Usage
* Just goto settings and click LetsEncrypt
* Click create button to create new certificate request.
* Add email, domain, Website root path and certificate storage path and Click Save
* This will enable Generate Certificate button
* Click Generate Certificate, this will take some time.
* Check the generate log for more info

## Feature List
#### As of 1.0.3
* Backend Setting for Certificate creation
* Certificate Auto Renew 


## Planned Features
* 

## Details
We reccomend to read more information at https://letsencrypt.org. Only expert user with the knowledge of SSL installation should use this plugin
