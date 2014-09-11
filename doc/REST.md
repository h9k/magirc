# MagIRC Web Service Reference #

MagIRC runs on top of a RESTful Web Service to get data. This gives you powerful possibilities for expanding your existing website or developing your own frontend for IRC stats.
All responses are given in JSON format, which can be easily parsed.

Here are all requests you can make to get info. You need to prefix each path with the full URL to the REST service.
For example, http://www.denorastats.org/magirc/rest/service.php

### Network-related ###
* **Get Current Network Status**  
This will give you current network stats such as opers, channels, users, etc...
> /network/status

* **Get Max Network Stats**  
This will give you max network stats such as opers, channels, users, etc...
> /network/max

* **Get the global client stats**  
> /network/clients

* **Get the global country stats**  
> /network/countries

### Server-related ###
* **Get List of Servers**  
This will give you a list of servers.
> /servers

* **Get Hourly Stats**  
This will give hourly stats whose time is in the form of a unix timestamp.
> /servers/hourlystats

* **Get A Servers MOTD**  
This will show a servers MOTD
> /servers/`<server-name>`

* **Get the per server client stats**  
> /servers/`<server-name>`/clients

* **Get the per server country stats**  
> /servers/`<server-name>`/countries

### Channel-related ###

* **Get List of Channels**  
This will get a list of channels with the current topic, topic author, users, max users, etc...
> /channels

* **Get The Hourly Stats for Channels**  
This will get hourly stats for the number of channels on the network. The time is unix timestamp in milliseconds.
> /channels/hourlystats

* **Get A List of The Biggest Channels**  
This will get a list of the biggest channels on the network. A limit can be defined (eg. 10, 5, 2).
> /channels/biggest/`<limit>`

* **Get A List of The Top Channels**  
This will get a list of the top channels on the network. A limit can be defined (eg. 10, 5, 2).
> /channels/top/`<limit>`

* **Get Channels Acticity Stats**  
This will get a list of channels and their activity stats. Type can be total, monthly, weekly, daily.
> /channels/activity/`<type>`
 
* **Get Stats for a Specific Channel**  
This will show the stats for a specific channel. Stats include name, max users, topic, topic author and more.
> /channels/%23`<channel>`

* **Get Users in a Specific Channel**  
This will show the stats for a specific channel. Stats include name, max users, topic, topic author and more.
> /channels/%23`<channel>`/users
 
* **Get Activity Stats in a Specific Channel**  
This will show the activity stats for a specific channel. Type can be total, monthly, weekly, daily.
> /channels/%23`<channel>`/activity/`<type>`

* **Get Hourly Activity Stats in a Specific Channel**  
This will show the hourly activity stats for a specific channel. Type can be total, monthly, weekly, daily.
> /channels/%23`<channel>`/hourly/activity/`<type>`

* **Check if channel is being monitored by Chanstats**  
> /channels/%23`<channel>`/checkstats

* **Get the per channel client stats**  
> /channels/%23`<channel>`/clients

* **Get the per channel country stats**  
> /channels/%23`<channel>`/countries

* **Get Hourly User Stats**  
This will show hourly stats for users on the network with unix timestamps in milliseconds.
> /channels/users/hourlystats

* **Get Top User Stats**  
This will show hourly stats for users on the network with unix timestamps in milliseconds.
> /channels/users/top/`<limit>`

### User-related ###
Users can be addressed in two ways: stats and nick.

Nick tells magirc to treat the user parameter as nickname and look for it in the user table.

Stats tells magirc to treat the user as stats user and looks for it in the ustats table.

* **Get User Activity Stats**  
This will show the activity stats for a specific user. Type can be total, monthly, weekly, daily.
> /users/activity/`<type>`

* **Get User Specific Stats**  
This will show user specific stats such as real name, alias, username, nick, etc...
> /users/nick/`<nick>`  
> /users/stats/`<nick>`

* **Get User Channels**  
This will show a list of channels in which a given users resides.
> /users/nick/`<nick>`/channels  
> /users/stats/`<nick>`/channels

* **Get Acticity Stats for a Specific User**  
This will show activity stats for a specific user in a given channel.
> /users/nick/`<nick>`/channels  
> /users/stats/`<nick>`/channels
   
* **Get Acticity Stats for a Specific User**  
This will show hourly activity stats for a specific user in a given channel
> /users/nick/`<nick>`/hourly/`<channel>`/`<type>`  
> /users/stats/`<nick>`/hourly/`<channel>`/`<type>`

* **Check if user is being monitored by Chanstats**  
> /users/nick/`<nick>`/checkstats  
> /users/stats/`<nick>`/checkstats

* **Get List of IRC Operators**  
This will show a list of IRC Operators along with the server that they reside on as well as nick, country, level, away, etc...
> /operators
 