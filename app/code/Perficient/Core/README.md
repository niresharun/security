Magento 2.2.3 Core Module ver. 0.0.1
====================
Last tested on Magento version 2.2.3

Features
-----
 - Added Helper for generic method i.e. isB2bCompanyTypeUser to check if the customer is B2B company type user or not.
 
Install
-----
Manually:
To install this module copy the code from this repo to `app/code` folder of your Magento 2 instance,
If you do this after installing Magento 2 you need to run `php bin/magento setup:upgrade`

Uninstall
--------
If you installed it manually:
 - remove the folder `app/code/Perficient/Core`
 - remove the module `Perficient_Core` from `app/etc/config.php`
 - remove the module `Perficient_Core` from table `setup_module`: `DELETE FROM setup_module WHERE module='Perficient_Core'`

If you installed it via composer:
 - run this in console  `bin/magento module:uninstall -r Perficient_Core`. You might have some problems while uninstalling. See more [details here](http://magento.stackexchange.com/q/123544/146):