Imager
======

An online visual link bookmarker and image scraper - Alpha release

Imager is a great way to bookmark or stumble through websites showing only the images. The best way to explain the concept is to have a look: http://imager.buzz

Imager is designed to be portable and easy to install on standard web hosting. No images are stored on the server, imager only collects page URL's. It then scrapes each page for the images and presents them in a pleasant grid with lightbox browsability. Each page link-back and meta title is unobtrusively presented so that you know the context of the images and can easily get back to the source.

With your own Imager, you will be able to configure it's:
public or private nature
Users and user roles
Invite new users

Planned for BETA:
Whitelist for allowed URL's
SFW filtering
Personal bookmarking
Accompanying Firefox add-on similar to Stumble.


Alpha is built on and tested with:
PHP 5.3.10
Postgresql 9.1
Apache2

Installation Instructions:
==========================

Download or clone Imager into your webroot and configure Apache or your web hosting appropriately
Create a new empty Postgresql database with UTF8 encoding
Point your browser to your imager URL and follow the onscreen instructions to connect to your database and create the first user.

Settings:

A GUI for settings is on the list for BETA.

For ALPHA: 
app settings are kept in [root]/settings.php
email and user settings are kept in [root]/user_mod/settings.php

Please ensure you have configured your email correctly to avoid invites and password resets being marked as spam.

Enjoy Imager!
