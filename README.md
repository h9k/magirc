MagIRC
======

Thank you for your interest in MagIRC, a PHP-based Web Frontend for IRC Services released under the GPLv3 license.

About MagIRC
------------
This software is a complete rewrite of phpDenora, a PHP-based Web Frontend for the [Denora Stats](http://www.denorastats.org) project.
In the future it will interface with other IRC services like [Anope](http://www.anope.org/) but the priority for now is to replace phpDenora since it is aged.

Main features
-------------
* [Smarty](http://www.smarty.net/) templating engine
* [jQuery](http://www.jquery.com/)-based UI with AJAX interactions
* HTML5 and CSS3
* Easy installation
* Administration panel
* Slick design

Requirements
------------
* Web Server with PHP 5.3+
* [Denora Stats](http://www.denorastats.org) with MySQL enabled
* Modern Web Browser supporting HTML5 and CSS3

Limitations
-----------
The current version is considered to be **alpha** and has some known limitations:
* English only: MagIRC already has gettext localization built in, but it will be activated only once the software is nearly finished to avoid too much work maintaining language files for rapidly changing code. This is scheduled for the **beta** release.
* Only one default theme: MagIRC will allow to install additional themes, but things need to be sorted out before allowing this. This is scheduled for the **release candidate**.
