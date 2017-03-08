# MagIRC #

Thank you for your interest in MagIRC, a PHP-based Web Frontend for IRC Services released under the GPLv3 license.

This software is a complete rewrite of phpDenora, a PHP-based Web Frontend for the [Denora Stats](http://www.denorastats.org) project.

Meanwhile, MagIRC also works with [Anope](http://www.anope.org/) 2.0, which supersedes Denora.
We recommend using Anope, since it is being actively maintained and has improved performance and stability over Denora.
In case you want to migrate from Denora to Anope, we created a script for this task (see below).

### Main features ###
* REST service
* [Twig](http://twig.sensiolabs.org) templating engine
* [jQuery](http://www.jquery.com/)-based UI with AJAX interactions
* HTML5 and CSS3
* Easy installation
* Administration panel
* Slick design

### Requirements ###
* Web Server with PHP 5.5+ and the `pdo_mysql`, `mcrypt` and `gettext` modules installed
* Web Browser supporting HTML5, CSS3 and JavaScript
* Any of the following:
	* [Denora Stats](http://www.denorastats.org) v1.5 server with MySQL enabled
	* [Anope](http://www.anope.org/) v2.0 with the `m_mysql`, `m_chanstats` and `irc2sql` modules enabled
* Supported IRC Daemons: Bahamut, Charybdis, InspIRCd, ircd-rizon, IRCu, Nefarious, Ratbox, ScaryNet, Unreal


## Magirc installation / upgrade ##

### Using [composer](http://getcomposer.org) and [bower](http://bower.io) (recommended) ###

1. Execute the following command:
	- To install:
	    - `composer create-project magirc/magirc`
	    - `bower install`
	- To update:
	    - `composer update`
	    - `bower update`
	    - Clean up cached pages with `rm -r tmp/*`
2. Use your web browser to navigate to the setup folder on your server and follow on-screen instructions.
   Example: http://`yourpathtomagirc`/setup/

### Using a release package ###
1. Download the latest MagIRC release package from [magirc.org](http://www.magirc.org/)
2. Extract the MagIRC archive to your web server and move its content to the MagIRC directory.
3. Use your web browser to navigate to the setup folder on your server and follow on-screen instructions.
   Example: http://`yourpathtomagirc`/setup/

### Using git ###
You need a git client, [composer](http://getcomposer.org) and [bower](http://bower.io)

1. Execute the following commands:
	- To install:
	    - `git clone git://github.com/h9k/magirc.git`
	    - `composer install`
	    - `bower install`
	- To update:
	    - `git pull`
	    - `composer update`
	    - `bower update`
2. Use your web browser to navigate to the setup folder on your server and follow on-screen instructions.
   Example: http://`yourpathtomagirc`/setup/


## Anope configuration ###
You need Anope 2.0.0 or later and the following modules enabled and set up:

    m_mysql
    m_chanstats
    irc2sql

These modules are included in the Anope codebase under `extra`. Please refer to the Anope documentation on how to set those up.

Also, you will need additional database tables, views and stored procedures for the Anope database in order to get the data needed by MagIRC.
Please look at the `setup/sql/anope.sql` file and adapt it if needed (table prefixes, etc.) and run it against your Anope database.

Note that you need the MySQL `event_scheduler` set to `ON` in the MySQL server. If you have enough rights, you can turn it on via `SET GLOBAL event_scheduler = ON;`.

### Migrating from Denora to Anope ###
If you want to switch from Denora to Anope, please proceed as follows:

1. Install Anope (see above)
2. Shut down Denora
3. Make Anope join the network and double check that it is working fine, e.g. the MySQL tables are being filled with data
4. Configure the `setup/tools/denora2anope.php` script and then run it from command line with `php denora2anope.php`. Be patient and do not interrupt the process!


## Denora configuration ##

### Required Denora settings ###

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

### Optional Denora settings ###
Limiting chanstats to +r users improves nick tracking.
To use this feature **enable** the following parameters by removing the '#' in front:

    ustatsregistered;


## Web Server configuration ##

### Apache ###
The `AcceptPathInfo` directive should be set to `Default` or `On` in the Apache configuration. It is by default on most servers.

To enable URL rewriting make sure your apache has the `mod_rewrite` module enabled. Then rename `htaccess.txt` to `.htaccess` and enable rewriting in the MagIRC Admin Panel.
This is optional, MagIRC also works without rewriting on Apache.

It is also recommended, if you allow slashes `/` in your nicknames or channel names, to set `AllowEncodedSlashes On`

### Nginx ###
Your Nginx configuration file should look like this, adapted to your needs of course:

    server {
        listen 80;
        server_name example.com;
        index index.php;
        error_log /path/to/example.error.log;
        access_log /path/to/example.access.log;
        root /path/to/public;

        location / {
            try_files $uri $uri/ /index.php$is_args$args;
        }

        location ~ \.php {
            try_files $uri =404;
			fastcgi_split_path_info ^(.+\.php)([\:\/A-z0-9]+)$;
            include fastcgi_params;
            fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
            fastcgi_param SCRIPT_NAME $fastcgi_script_name;
            fastcgi_index index.php;
            fastcgi_pass 127.0.0.1:9000;
        }
    }

This will work with or without Magirc rewrite.
Don't forget to replace `fastcgi_pass  backend;` by your actual backend.
If you do not have `/etc/nginx/fastcgi.conf`, include `/etc/nginx/fastcgi_params`.

### lighttpd ###
Your lighttpd configuration file should contain this code (along with other settings you may need). This code requires lighttpd >= 1.4.24.

    url.rewrite-if-not-file = ("^" => "/index.php")
