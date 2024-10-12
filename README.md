<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>



## Apache Status Helper

I manage some cPanel/WHM servers for hosting, and they get hammered with bot traffic.  One way to deal with that is to look at the Apache `server-status` page, which lists IP addresses & request URIs.

I often see a bunch of POST requests to `wp-login.php`, or requests to strange URIs like `alfa-rex2.php`.  If I do an IP address lookup, those addresses often belong to VPN providers or to Cloud hosting providers.  That's a pretty clear sign of bot traffic, so I can ban the IP address using ConfigServer Firewall (csf).

But that's a lot of steps.
1. Check the `server-status` page and look for IP addresses that repeat.
2. Use a 3rd party website to look up each address.
3. Use my terminal to SSH to the server and run the `csf` command to ban that IP address.
 
### Let's make life easier

I've built this app on Laravel, and set it up to run as a Docker container.  You can configure it through .env variables.  It provides a web interface, which checks the `server-status` page of each configured server, and provides a combined list of IP addresses.

It provides a tool to look up an IP address with a single click, showing the country it's registered to and which network it belongs to.  There is a tickbox next to each IP address as well, and when selected, a list of commands will appear at the bottom of the page, making it easy to copy/paste the `csf -td` command to block the selected IP addresses.

## Installation

TODO

### How to configure

Use a .env file which provides some settings like:
```
APP_TIMEZONE=Australia/Brisbane
APACHE_STATUS_URLS=https://server1.com/server-status,https://server2.com/server-status
CACHE_STATUS_SECONDS=90 # How long to cache the /server-status pages, in seconds.
CACHE_IP_SECONDS=43200 # How long to cache the individual IP address lookups, in seconds.
```
Provide a comma-separated list of Apache server-status URLs, which must be accessible by the app.  On a cPanel server, this be configured via the WHM control panel.  Go to Server Configuration > Tweak Settings, then into the System tab looking for "Allow server-info and server-status".  You can configure which IP addresses are allowed to view that page here.

Other settings, for timezone and cache duration are optional.


