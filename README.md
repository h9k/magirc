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
* Web Server with PHP 5.3+ and the PHP MySQL module installed
* Modern Web Browser supporting HTML5, CSS3 and JavaScript
* [Denora Stats](http://www.denorastats.org) with MySQL enabled
* Supported IRC Daemons: Bahamut, Charybdis, InspIRCd, ircd-rizon, IRCu, Nefarious, Ratbox, ScaryNet, Unreal

Limitations
-----------
The current version is considered to be **alpha** and has some known limitations:

* English only: MagIRC already has gettext localization built in, but it will be activated only once the software is nearly finished to avoid too much work maintaining language files for rapidly changing code. This is scheduled for the **beta** release.
* Only one default theme: MagIRC will allow to install additional themes, but things need to be sorted out before allowing this. This is scheduled for the **release candidate**.

Required Denora settings
------------------------
**Change** this to a higher value, such as 15 days (15d) to keep information for a longer time.
Important: the servercache value must NOT be smaller than the usercache value!

    usercache 15d;
    servercache 30d;

**Change** this to 1h

    uptimefreq 1h;

**Enable** the following parameters by removing the '#' in front:

    ctcpusers;
    keepusers;
    keepservers;

**Disable** the following parameter by adding a '#' in front:

    #largenet;

Optional Denora settings
------------------------
Limiting chanstats to +r users improves nick tracking.
To use this feature **enable** the following parameters by removing the '#' in front:

    ustatsregistered;
