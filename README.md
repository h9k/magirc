MagIRC
======

Thank you for your interest in MagIRC, a PHP-based Web Frontend for IRC Services released under the GPLv3 license.

About MagIRC
------------
This software is a complete rewrite of phpDenora, a PHP-based Web Frontend for the [Denora Stats](http://www.denorastats.org) project.
In the future it will interface with other IRC services like [Anope](http://www.anope.org/) but the priority for now is to replace phpDenora since it is aged and ugly.

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
* Web Server with PHP 5.3+ and the *pdo_mysql*, *mcrypt* and *gettext* modules installed
* Web Browser supporting HTML5, CSS3 and JavaScript
* [Denora Stats](http://www.denorastats.org) server with MySQL enabled
* Supported IRC Daemons: Bahamut, Charybdis, InspIRCd, ircd-rizon, IRCu, Nefarious, Ratbox, ScaryNet, Unreal

Limitations
-----------
The current version is considered to be **beta** and has some known limitations:

* **Templating is not officially supported**: This means you are free to create your own templates for MagIRC, but we will not support you and will not guarantee troublefree operation between different versions of MagIRC, as things change rather often during development. This is scheduled for the **release candidate**.
* **No guarantees for API stability**: You are free to use the RESTful API MagIRC provides, however it is very likely that some things might change. The **release candidate** version will include an API definition that should not change for the whole MagIRC v1.0.x series.

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

Web Server configuration
------------------------
### Apache
The `AcceptPathInfo` directive should be set to `Default` or `On` in the Apache configuration. It is by default on most servers.

To enable URL rewriting make sure your apache has the `mod_rewrite` module enabled. Then rename `htaccess.txt` to `.htaccess` and enable rewriting in the MagIRC Admin Panel.
This is optional, MagIRC also works without rewriting on Apache.

It is also recommended, if you allow slashes `/` in your nicknames or channel names, to set `AllowEncodedSlashes On`

### Nginx
Your Nginx configuration file should contain this code, if Magirc is in the document root :

    index index.php index.html;
    location / {
            try_files $uri $uri/ /index.php;
    }

    location ~ ^(/.*\.php)(/.*)?$ {
            try_files $1 =404;
            include /etc/nginx/fastcgi.conf;
            fastcgi_pass  backend;
            fastcgi_index index.php;
           #fastcgi_intercept_errors on;
    }

or this for a directory in document root (`document_root/magirc_directory`) :

    index index.php index.html;
    location /magirc_directory {
            try_files $uri $uri/ /magirc_directory/index.php;
    }

    location ~ ^(/magirc_directory/.*\.php)(/.*)?$ {
            try_files $1 =404;
            include /etc/nginx/fastcgi.conf;
            fastcgi_pass  backend;
            fastcgi_index index.php;
           #fastcgi_intercept_errors on;
    }

This will work with or without Magirc rewrite.
Comment out `fastcgi_intercept_errors on;` to override Magirc 404 blue pages.
Don't forget to replace `fastcgi_pass  backend;` by your actual backend.

### lighttpd
Your lighttpd configuration file should contain this code (along with other settings you may need). This code requires lighttpd >= 1.4.24.

    url.rewrite-if-not-file = ("^" => "/index.php")

Installation
------------
1. Pull the latest MagIRC package from https://github.com/h9k/magirc
2. Extract the MagIRC archive to your web server and move its content to the MagIRC directory.
3. Use your web browser to navigate to the setup folder on your server and follow on-screen instructions.
   Example: http://`yourpathtomagirc`/setup/

Note: If you are on Apache you can enable mod_rewrite by using "a2enmod rewrite; service apache2 restart"
and rename htaccess.txt in the root  to .htaccess.

Upgrading
---------
1. Pull the latest MagIRC package from https://github.com/h9k/magirc
2. Extract the MagIRC archive to your web server and move its content to the MagIRC directory.
3. Use your web browser to navigate to the setup folder on your server and follow on-screen instructions.
   Example: http://`yourpathtomagirc`/setup/
