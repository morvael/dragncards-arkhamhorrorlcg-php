# dragncards-arkhamhorrorlcg-php

This script generates filled customizable card upgrade cards.

#### Requests

Sample request formats:

1. `09021_0_1_2_2_2_2_3_3_0_0.webp`
2. `09042_0_0|04311_1_1_2_2|04311|04311_2_3_4_0.webp`
3. `09060_0_0|HistoricalSociety_2_2|HistoricalSociety_2_2_2_3_3_0.webp`
4. `09079_0_0|willpower_1_1_2_2|intellect_3|agility_3_3_0.webp`

First element is card id, second is taboo id. Then there's 9 sections with
number of filled pips followed by any extra values.

#### Trait and card names

Despite allowing quite a lot of characters inside the `[^_\|\.]` section
(required to match non-ASCII characters) all trait and card values are mapped
via dictionaries, and if not present there - they won't be rendered. To ease the
problems with URL/Unicode encoding the following changes should be made to trait
names:

1. Spaces, `-`, and `'` must be removed.
1. `?` must be replaced with `Q`.

For example: trait name `A b-c'd?` must be represented as `AbcdQ`.

#### Rewriting requests

Plain file requests are redirected to the `image.php` script using the following
`mod_rewrite` rules:

```
AddDefaultCharset UTF-8
RewriteEngine On
RewriteRule ^/favicon\.ico$ favicon.ico [NC,L]
RewriteRule ^/robots\.txt$ robots.txt [NC,L]
RewriteRule ^/(t?[0-9]+(a-z)?_[0-9]+(_[0-9](\|[^_\|\.]*)*)*\.webp)$ image.php?file=$1 [NC,L]
RewriteRule ^.*$ - [R=404,L]
```

#### Repository contents

Actual icons, images, fonts and data files are not part of this github repo.
