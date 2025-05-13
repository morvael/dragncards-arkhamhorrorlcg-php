# dragncards-arkhamhorrorlcg-php

This script generates filled customizable card upgrade cards.

#### Requests

Sample request formats:

1. `09021-0-MHwxLDF8MiwyfDIsM3wyLDR8Miw1fDMsNnwz.webp`
2. `09042-0-MHwwfDAxMDYxLDF8MSwyfDEsM3wyLDR8MnwwNDAyOV4wMTA2MCw1fDIsNnwzLDd8NA.webp`
3. `09060-0-MHwwfElsbGljaXQsMXwxLDJ8MnxUcmljaywzfDIsNHwyLDV8Miw2fDMsN3wz.webp`
4. `09079-0-MHwwfHdpbGxwb3dlciwxfDEsMnwxLDN8Miw0fDJ8aW50ZWxsZWN0LDV8M3xhZ2lsaXR5LDZ8Myw3fDM.webp`

First element is card id, second is taboo id. Then there's Base64 encoded (URL
safe, no padding) customization info in the same format as in the deck JSON.

#### Dictionaries

For some cards the script needs to map card ids into card names to be rendered.
There's a default mapping data file, but it may be slightly outdated, and it
doesn't handle custom content. Extra mapping may be provided in an optional
fourth element as Base64 encoded (URL safe, no padding) JSON. The format is as
follows:

```
{
  "names": {
    "<id1>": "<name1>",
    ...
    "<idN>": "<nameN>"
  }
}
```

Sample request format with extra parameter:

1. `09042-0-MHwwfGlkMSwxfDEsMnwxLDN8Miw0fDJ8aWQyXmlkMyw1fDIsNnwzLDd8NA-eyJuYW1lcyI6eyJpZDEiOiJDYXJkIEEiLCJpZDIiOiJDYXJkIEIiLCJpZDMiOiJDYXJkIEMifX0.webp`

#### Rewriting requests

Plain file requests are redirected to the `image.php` script using the following
`mod_rewrite` rules:

```
AddDefaultCharset UTF-8
RewriteEngine On
RewriteRule ^/favicon\.ico$ favicon.ico [NC,L]
RewriteRule ^/robots\.txt$ robots.txt [NC,L]
RewriteRule ^/([0-9]{4,12}[a-z]?)-([0-9]{1,3})-([A-Za-z0-9-_]{0,1024})-([A-Za-z0-9-_]{0,2048})\.webp$ image.php?id=$1&taboo=$2&data=$3&dictionaries=$4 [NC,L]
RewriteRule ^/([0-9]{4,12}[a-z]?)-([0-9]{1,3})-([A-Za-z0-9-_]{0,1024})\.webp$ image.php?id=$1&taboo=$2&data=$3 [NC,L]
RewriteRule ^.*$ - [R=404,L]
```

#### Repository contents

Actual icons, images, fonts and data files are not part of this github repo.
