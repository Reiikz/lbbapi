# Installation

## Dependancies:
### You'll need:
- PHP enabled web server.
- Bind9 Server.
- The sudo command for allowing the web server to restart/reload the bind9 server.
- Systemd enabled OS.

After installing sudo add the following lines to your  `/etc/sudoers` file.
```
    www-data ALL=NOPASSWD:/usr/bin/systemctl start bind9
    www-data ALL=NOPASSWD:/usr/bin/systemctl stop bind9
    www-data ALL=NOPASSWD:/usr/bin/systemctl restart bind9
    www-data ALL=NOPASSWD:/usr/bin/systemctl reload bind9
    www-data ALL=NOPASSWD:/usr/bin/systemctl status bind9
```
> **Note:** That it is asumed your web server's user is www-data and the Bind9 server's unit file is bind9, if it's not adjust acordingly.

### Dependancies:

php-pear Net_IPv6 is used, it has been included as a standalone file as the currently available version on the repo is not compatible with php 8.1 due to a deprecated syntax I've simply patched this.
The only function that was needed from it was this: https://pear.php.net/manual/en/package.networking.net-ipv6.compress.php

## Web server confuration recomendations:

- You should disable indexing.
- Use HTTPS

## Installing the files:

Clone the repository into your web server

> **Note:** That the API is meant to be able to function anywhere on the web server.

```bash
    git clone http://git.reiikz.net/reiikz/lbbapi.git
```

Configure the locations of the bind9 files.
You can use the provided config.php.sample file provided like so:

```bash
    cp config.php.sample config.php
```
You require an administrator user, by default the first user created is the administrator.
To create the user access the registration form directly as follows: `http://Web-server.net/LBBAPI_PATH/cpanel/auth/register.php`

> **Note:** Security of the API is dependant on you setting up **HTTPS** and making sure the permissions to write Bind9 configurations are correct.
Another alternative is to access the API over an encrypted tunnel like a VPN or SSH tunnel arguably even safer as the traffic is obfuscated.

Bind9 configuration files must be writable by the web server user.
Check your web server documentation but some common usernames for the web server include:
- www-data (Debian based)
- http  (Arch based)

# API

This is a web API meant to be used with your curl client of choice.

All successfull requests will return an empty 200.
Otherwise the header is set to a meaningful http error and a help string is returned.

> Use the cpanel to generate an access token and give it the appropriate permissions.

> **Note:** you are expected to use the token the server generates for you and it is filtered to contain random plaintext hex data in all caps. While you can use cookies to access all non API functions of LBBAPI this is not intended use and therefore will not be supported.

<ins>Every API call that ends up causing a delition/update/creation will trigger a Bind9 server reload and a database serialization bump.</ins>

## Permissions

- `admin`
- - Allows doing anything.

The admin permission should only be given to the administrator user.
The admin permission will be automatically set to the first user to register through `/lbbapi/cpanel/auth/register.php`

## DNS database permissions

The permissions are given out to tokens on a DNS by DNS basis.
Ending with the type of operation allowed for that token.

Like so:

- `example.com.delete` - would allow deletion of all records matching example.com

Similarly wildcards can be used

- `*.example.com.delete` - would allow deleting all records matching < anything >.example.com as well as example.com itself.

Available permissions are:

- `example.com.new`
  - Create new record
- `example.com.delete`
  - delete new record
- `example.com.update`
  - update record
- `example.com.show`
  - see the record on the control panel
- `token.new`
  - allow a certain user to create tokens
- `token.update`
  - allow a certain user to update tokens
- `token.delete`
  - allow a certain user to delete tokens
- `restartBind9`
  - allow restart the bind9 service

> By default all users will have the "token" permissions so which means all users have access to the API by default.

## Record Creation

> **Note:** That DNS record names aren't unique but rather the value+record tuple is, so for delition queries both must be given otherwise we would delete the entire recordset for a given domain.

If we were to create the `TXT` record `femboy.s.cooldomain.net` with value "`shody`".

Aditionally the record type is required.

The post request should look something like this:

```
POST to https://cooldomain.net/lbbapi/API/record/new.php
    token:algo
    type:TXT
    record:femboy.s.cooldomain.net
    value:shody
```

### CURL example:
```BASH
    curl -X POST http://cooldomain.net/lbbapi/API/record/new.php \
            -d "token=9694A108D745E7EE41F3815B32DCC14789F6F9733253BB05E53EE6FC312370BD" \
            -d "record=femboy.s.cooldomain.net" \
            -d "type=TXT" \
            -d "value=UwU" \
            -d "ttl=74" \
            -o -
```

## Record Deletion

> **Note:** That DNS record names aren't unique but rather the value+record tuple is, so for delition queries both must be given otherwise we would delete the entire recordset for a given domain.

If we were to delete the `TXT` record `femboy.s.cooldomain.net` with value "`shody`".

Aditionally the record type is required.

The post request should look something like this:

```
POST to https://cooldomain.net/lbbapi/API/record/delete.php
    token:algo
    type:TXT
    record:femboy.s.cooldomain.net
    value:shody
```

### CURL example:
```BASH
    curl -X POST http://cooldomain.net/lbbapi/API/record/delete.php \
            -d "token=9694A108D745E7EE41F3815B32DCC14789F6F9733253BB05E53EE6FC312370BD" \
            -d "record=femboy.s.cooldomain.net" \
            -d "type=TXT" \
            -d "value=UwU" \
            -d "ttl=74" \
            -o -
```

> **Note:** Every record/type/value tuple must be deleted separately.

## Record update

> **Note:** That DNS record names aren't unique but rather the value+record tuple is, so for delition queries both must be given otherwise we would delete the entire recordset for a given domain.

If we were to update the `TXT` record `femboy.s.cooldomain.net` with value "`shody`" to contain "`shody tool`".

The post request should look something like this:

```
POST to https://cooldomain.net/lbbapi/API/record/updateRecord.php
    token:algo
    type:TXT
    record:femboy.s.cooldomain.net
    value:shody
    newValue:shody tool
    newTTL:60
```

### CURL example:
```BASH
    curl -X POST http://cooldomain.net/lbbapi/API/record/updateRecord.php \
            -d "token=9694A108D745E7EE41F3815B32DCC14789F6F9733253BB05E53EE6FC312370BD" \
            -d "record=femboy.s.cooldomain.net" \
            -d "type=TXT" \
            -d "value=UwU" \
            -d "ttl=74" \
            -d "newValue=shody tool" \
            -d "newTTL=60" \
            -o -
```

Note that the values `newValue` and `newTTL` were added, containing our new value and TTL respectively.

> **Note:** The record name cannot be updated! To update the record name delete it and create a new one with a different name.

**Also note that capitalization is important!**

## Token Management

> IMPORTANT: deleted users retain their tokens, if you need to remove a user and its tokens you may its tokens manually.

# Upgrading LBBAPI

> Assuming you followed the installation steps provided you should have a git repository.

It is advisable you backup the entire LBBAPI repository.
However the currently relevant folders are:

- `LBBAPI/users.php`
- `LBBAPI/userPermissions`
- `LBBAPI/tokens`
- `LBBAPI/config`

These should be in the `.giignore` so they shouldn't be touched by git.

<ins>Be warned this may be subject to change and may not be set in stone so it is your resposibility to make sure no data is lost during an upgrade procedure!</ins>

perform a git pull:
```
    git pull
```

Profit!

# Internal decoding formats

>Bind9 zone configuration files and databses are decoded to PHP arrays that can later be encoded back into databses and config files making interacting with them exctremely simple.

## Zones
>**Note:** Refer to the API reference for details on how to interact with the files indirectly.

> DNS zones are read from the file set by: `$CONFIG["ZoneConfigFile"]` in `lbbapi/config/config.php`

The function `bind9_zoneconfig_decode($file)` located in `core/parser.php` is the one resposible for this task.
- `$file` - The zone config file to decode.

The returned array will contain the following format:

- `zones` - Array with the zone domains
- `<zone-domain>` - Associative array with the zone details
- - [`key`] - `value` pair.

> **Note:** `allow-transfer` is represented as an array itself as it may contain more than one value.

**IE:**
A zone file containing:
```
zone "localdomain" IN {
        type master;
        file "/etc/bind/zones/localdomain.db";
        allow-transfer { none; };
}
```
Then becomes:
```
    Array
    (
        [zones] => Array
            (
                [0] => localdomain
            )

        [localdomain] => Array
            (
                [type] => master
                [file] => /etc/bind/zones/localdomain.db
                [allow-transfer] => Array
                    (
                        [0] =>  none
                    )

            )

    )
```

## DNS Databases

>**Note:** Refer to the API reference for details on how to interact with the files indirectly.

> DNS Databses are read from the file set by: `<$decoded_zone_config>[<zone-name>]["file"]`

The function `bind9_zonedb_decode($file)` located in `core/parser.php` is the one resposible for this task.
- `$file` - The zone config file to decode.

The database is decoded into a PHP array with the following structure:

- `["DEFAULT_TTL"]` - Default database TTL
- `["SOA"]` - Start of Authority
- `["SOA_SERVER]` - Authoritative NameServer
- `["SERIAL"]` - Database version
- `["REFRESH"]` - DNS DB Refresh
- `["RETRY"]` - DNS DB transfer retry
- `["EXPIRE"]` - Expiration time
- `["NEGATIVE_CACHE_TTL"]` - Time To Live of a negative response
- `["recordset"]` - PHP Array with all the database records

### Recordset section
- `["<record-name>"]`
- - `["types"]` - All the types that this name resolves to
- - `[<DNS record type>]` - Array of all the records of this type for this name

**IE:**
A DNS Database containing:
```
$TTL      60
@      IN      SOA     localdomain.      ns1.localdomain (
                       0      ; SERIAL
                       3600      ; REFRESH
                       31536000      ; RETRY
                       604800      ; EXPIRE
                       300 )     ; NEGATIVE_CACHE_TTL


;------------------------------- RECORDSET


;----------- @
@             300           IN      NS            ns1
@             400           IN      NS            ns2


;----------- ns1
ns1           500           IN      A             10.69.51.11


;----------- ns2
ns2           600           IN      A             10.69.51.11


;----------- test
test          700           IN      A             0.0.0.0
test                        IN      TXT           test1
test                        IN      TXT           test2


;----------- testnottl
testnottl                   IN      A             0.0.0.0
```

Then becomes:
```
Array
(
    [DEFAULT_TTL] => 60
    [SOA] => localdomain.
    [SOA_SERVER] => ns1.localdomain
    [SERIAL] => 0
    [REFRESH] => 3600
    [RETRY] => 31536000
    [EXPIRE] => 604800
    [NEGATIVE_CACHE_TTL] => 300
    [recordset] => Array
        (
            [localdomain.] => Array
                (
                    [types] => Array
                        (
                            [0] => NS
                        )

                    [NS] => Array
                        (
                            [0] => Array
                                (
                                    [value] => ns1
                                    [ttl] => 300
                                )

                            [1] => Array
                                (
                                    [value] => ns2
                                    [ttl] => 400
                                )

                        )

                )

            [ns1] => Array
                (
                    [types] => Array
                        (
                            [0] => A
                        )

                    [A] => Array
                        (
                            [0] => Array
                                (
                                    [value] => 10.69.51.11
                                    [ttl] => 500
                                )

                        )

                )

            [ns2] => Array
                (
                    [types] => Array
                        (
                            [0] => A
                        )

                    [A] => Array
                        (
                            [0] => Array
                                (
                                    [value] => 10.69.51.11
                                    [ttl] => 600
                                )

                        )

                )

            [test] => Array
                (
                    [types] => Array
                        (
                            [0] => A
                            [1] => TXT
                        )

                    [A] => Array
                        (
                            [0] => Array
                                (
                                    [value] => 0.0.0.0
                                    [ttl] => 700
                                )

                        )

                    [TXT] => Array
                        (
                            [0] => Array
                                (
                                    [value] => test1
                                    [ttl] => 60
                                )

                            [1] => Array
                                (
                                    [value] => test2
                                    [ttl] => 60
                                )

                        )

                )

            [testnottl] => Array
                (
                    [types] => Array
                        (
                            [0] => A
                        )

                    [A] => Array
                        (
                            [0] => Array
                                (
                                    [value] => 0.0.0.0
                                    [ttl] => 60
                                )

                        )

                )

        )

)
```
> **Note:** that the @ symbol becomes the start of authority ended by a dot in the PHP representation.

# Internal encoding

>**Note:** Refer to the API reference for details on how to interact with the files indirectly.

To encode database and zone file configurations back into Bind9 recognized formats use the following functions:
- `bind9_zoneconfig_encode($ConfigurationArray)`
- - `$ConfigurationArray` - being An array in the previously described format.
- `bind9_zonedb_encode($DBArray)`
- - `$DBArray `- being an array in the previously described format.

Both of these functions will return a string with the parsed data but they will not write to disk.

