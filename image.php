<?php

function get_upgrade($items, $index) {
    if (array_key_exists($index, $items)) {
        return $items[$index];
    } else {
        $result = array();
        $result[0] = 0;
        return $result;
    }
}

function load_map($fileName, $dictionaries, $dictionary) {
    $result = array();
    if ($dictionaries !== null) {
        $dicts = json_decode(base64_decode(strtr($dictionaries, '-_', '+/'), false), true, 10, JSON_INVALID_UTF8_SUBSTITUTE | JSON_OBJECT_AS_ARRAY);
        if (array_key_exists($dictionary, $dicts)) {
            foreach ($dicts[$dictionary] as $id => $name) {
                $result[$id] = $name;
            }
        }
    }
    $data = file($fileName);
    foreach ($data as $i => $line) {
        $item = explode("\t", $line);
        $result[trim($item[0])] = trim($item[1]);
    }
    return $result;
}

function load_set($fileName) {
    $result = array();
    $data = file($fileName);
    foreach ($data as $i => $line) {
        $result[trim($line)] = trim($line);
    }
    return $result;
}

function squares($image, $color, $upgrade, $max, $x, $y, $totalWidth, $width, $height) {
    if ($upgrade[0] > 0) {
        if ($upgrade[0] > $max) {
            $upgrade[0] = $max;
        }
        $spacing = $max > 1 ? ($totalWidth - ($max * $width)) / ($max - 1) : 0;
        for ($i = 0; $i < $upgrade[0]; $i++) {
            imagefilledrectangle($image, $x, $y, $x + $width, $y + $height, $color);
            $x += $width + $spacing;
        }
    }
}

function circle($image, $color, $upgrade, $max, $xWillpower, $xIntellect, $xCombat, $xAgility, $y, $width, $height, $index) {
    if ($upgrade[0] >= $max && array_key_exists($index, $upgrade) && ($upgrade[$index] == 'willpower' || $upgrade[$index] == 'intellect' || $upgrade[$index] == 'combat' || $upgrade[$index] == 'agility')) {
        $x = $upgrade[$index] == 'willpower' ? $xWillpower : ($upgrade[$index] == 'intellect' ? $xIntellect : ($upgrade[$index] == 'combat' ? $xCombat : $xAgility));
        $size = 2 * min($width, $height);
        $bbox = imagettfbbox($size, 0, './FASHIONV.TTF', '0');
        $cx = $x - $bbox[0] - ($bbox[2] - $bbox[0]) / 2;
        $cy = $y - $bbox[1] - ($bbox[7] - $bbox[1]) / 2;
        imagefttext($image, $size, 0, $cx, $cy, $color, './FASHIONV.TTF', '0');
    }
}

function rectangle($image, $color, $upgrade, $max, $x, $y, $width, $height, $index, $expected) {
    if ($upgrade[0] >= $max && array_key_exists($index, $upgrade) && $upgrade[$index] == $expected) {
        imagefilledrectangle($image, $x - 2, $y - 2, $x + $width + 2, $y + 2, $color);
        imagefilledrectangle($image, $x - 2, $y + $height - 2, $x + $width + 2, $y + $height + 2, $color);
        imagefilledrectangle($image, $x - 2, $y, $x + 2, $y + $height, $color);
        imagefilledrectangle($image, $x + $width - 2, $y, $x + $width + 2, $y + $height, $color);
    }
}

function text($image, $fontName, $fontSize, $color, $upgrade, $max, $x, $y, $index, $names) {
    if ($upgrade[0] >= $max && array_key_exists($index, $upgrade) && array_key_exists($upgrade[$index], $names)) {
        imagefttext($image, $fontSize, 0, $x, $y, $color, $fontName, $names[$upgrade[$index]]);
    }
}

function textOptionalDirect($image, $fontName, $fontSize, $color, $upgrade, $max, $x, $y, $index, $names) {
    if ($upgrade[0] >= $max && array_key_exists($index, $upgrade) && array_key_exists($upgrade[$index], $names)) {
        imagefttext($image, $fontSize, 0, $x, $y, $color, $fontName, $names[$upgrade[$index]]);
    } else if ($upgrade[0] >= $max && array_key_exists($index, $upgrade)) {
        imagefttext($image, $fontSize, 0, $x, $y, $color, $fontName, $upgrade[$index]);
    }
}

function textDirect($image, $fontName, $fontSize, $color, $upgrade, $max, $x, $y, $index) {
    if ($upgrade[0] >= $max && array_key_exists($index, $upgrade)) {
        imagefttext($image, $fontSize, 0, $x, $y, $color, $fontName, $upgrade[$index]);
    }
}

function filterLanguage($lng) {
    if ($lng == 'EN' || $lng == 'IT') {
        return $lng;
    } else {
        return 'EN';
    }
}

$lang = filter_input(INPUT_GET, 'lang', FILTER_VALIDATE_REGEXP, array('options' => array('regexp' => '/^[A-Z]{2,2}$/', 'flags' => FILTER_NULL_ON_FAILURE)));
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_REGEXP, array('options' => array('regexp' => '/^[0-9]{4,12}[a-z]?$/', 'flags' => FILTER_NULL_ON_FAILURE)));
$taboo = filter_input(INPUT_GET, 'taboo', FILTER_VALIDATE_REGEXP, array('options' => array('regexp' => '/^[0-9]{1,3}$/', 'flags' => FILTER_NULL_ON_FAILURE)));
$data = filter_input(INPUT_GET, 'data', FILTER_VALIDATE_REGEXP, array('options' => array('regexp' => '/^[A-Za-z0-9-_]{0,1024}$/', 'flags' => FILTER_NULL_ON_FAILURE)));
$dictionaries = filter_input(INPUT_GET, 'dictionaries', FILTER_VALIDATE_REGEXP, array('options' => array('regexp' => '/^[A-Za-z0-9-_]{0,2048}$/', 'flags' => FILTER_NULL_ON_FAILURE)));
if ($lang !== null && $id !== null && $taboo !== null) {
    try {
        $fileToLoad = 'upgrade.webp';
        if ($id == '09021') {
            $fileToLoad = '09021a.webp';
        } else if ($id == '09022') {
            $fileToLoad = $taboo >= 6 ? '09022-6a.webp' : '09022a.webp';
        } else if ($id == '09023') {
            $fileToLoad = '09023a.webp';
        } else if ($id == '09040') {
            $fileToLoad = '09040a.webp';
        } else if ($id == '09041') {
            $fileToLoad = '09041a.webp';
        } else if ($id == '09042') {
            $fileToLoad = '09042a.webp';
        } else if ($id == '09059') {
            $fileToLoad = '09059a.webp';
        } else if ($id == '09060') {
            $fileToLoad = '09060a.webp';
        } else if ($id == '09061') {
            $fileToLoad = '09061a.webp';
        } else if ($id == '09079') {
            $fileToLoad = '09079a.webp';
        } else if ($id == '09080') {
            $fileToLoad = '09080a.webp';
        } else if ($id == '09081') {
            $fileToLoad = $taboo >= 6 ? '09081-6a.webp' : '09081a.webp';
        } else if ($id == '09099') {
            $fileToLoad = '09099a.webp';
        } else if ($id == '09100') {
            $fileToLoad = '09100a.webp';
        } else if ($id == '09101') {
            $fileToLoad = '09101a.webp';
        } else if ($id == '09119') {
            $fileToLoad = '09119a.webp';
        }
        //https://fusionauth.io/dev-tools/base64-encoder-decoder
        if ($fileToLoad !== 'upgrade.webp') {
            $items = array();
            if ($data !== null && strlen($data) > 0) {
                $dataArray = explode(",", base64_decode(strtr($data, '-_', '+/'), false));
                foreach ($dataArray as $item) {
                    $itemArray = explode("|", $item);
                    if (array_key_exists(0, $itemArray) && array_key_exists(1, $itemArray) && ctype_digit($itemArray[0]) && ctype_digit($itemArray[1])) {
                        $upgrade = array(intval($itemArray[1]));
                        if (array_key_exists(2, $itemArray)) {
                            $subitemArray = explode("^", $itemArray[2]);
                            foreach ($subitemArray as $subitem) {
                                $upgrade[] = $subitem;
                            }
                        }
                        $items[intval($itemArray[0])] = $upgrade;
                    }
                }
            }
            $upgrade0 = get_upgrade($items, 0);
            $upgrade1 = get_upgrade($items, 1);
            $upgrade2 = get_upgrade($items, 2);
            $upgrade3 = get_upgrade($items, 3);
            $upgrade4 = get_upgrade($items, 4);
            $upgrade5 = get_upgrade($items, 5);
            $upgrade6 = get_upgrade($items, 6);
            $upgrade7 = get_upgrade($items, 7);
            $upgrade8 = get_upgrade($items, 8);
            //
            $flang = filterLanguage($lang);
            $image = imagecreatefromwebp('./images/' . $flang . '/' . $fileToLoad);
            $black = imagecolorallocate($image, 0, 0, 0);
            if ($fileToLoad == '09021a.webp') {
                if ($flang == 'IT') {
                    //paint Hunter's Armor https://ahlcg.derwinski.pl/IT-09021-0-MHwxLDF8MiwyfDIsM3wyLDR8Miw1fDMsNnwz.webp
                    //"cus_09021":"0|1,1|2,2|2,3|2,4|2,5|3,6|3"
                    squares($image, $black, $upgrade0, 1, 64, 210, 18, 18, 18);
                    squares($image, $black, $upgrade1, 2, 64, 319, 43, 18, 18);
                    squares($image, $black, $upgrade2, 2, 64, 428, 43, 18, 18);
                    squares($image, $black, $upgrade3, 2, 64, 504, 43, 18, 18);
                    squares($image, $black, $upgrade4, 2, 64, 579, 43, 18, 18);
                    squares($image, $black, $upgrade5, 3, 64, 688, 67, 18, 18);
                    squares($image, $black, $upgrade6, 3, 64, 831, 67, 18, 18);
                } else {
                    //paint Hunter's Armor https://ahlcg.derwinski.pl/EN-09021-0-MHwxLDF8MiwyfDIsM3wyLDR8Miw1fDMsNnwz.webp
                    //"cus_09021":"0|1,1|2,2|2,3|2,4|2,5|3,6|3"
                    squares($image, $black, $upgrade0, 1, 65, 210, 18, 18, 18);
                    squares($image, $black, $upgrade1, 2, 65, 325, 43, 18, 18);
                    squares($image, $black, $upgrade2, 2, 65, 441, 43, 18, 18);
                    squares($image, $black, $upgrade3, 2, 65, 485, 43, 18, 18);
                    squares($image, $black, $upgrade4, 2, 65, 530, 43, 18, 18);
                    squares($image, $black, $upgrade5, 3, 65, 645, 69, 18, 18);
                    squares($image, $black, $upgrade6, 3, 65, 797, 69, 18, 18);
                }
            } else if ($fileToLoad == '09022a.webp') {
                if ($flang == 'IT') {
                    //paint Runic Axe (original) https://ahlcg.derwinski.pl/IT-09022-0-MHwxLDF8MSwyfDEsM3wxLDR8MSw1fDMsNnwzLDd8NA.webp
                    //"cus_09022":"0|1,1|1,2|1,3|1,4|1,5|3,6|3,7|4"
                    squares($image, $black, $upgrade0, 1, 64, 203, 16, 16, 16);
                    squares($image, $black, $upgrade1, 1, 64, 269, 16, 16, 16);
                    squares($image, $black, $upgrade2, 1, 64, 393, 16, 16, 16);
                    squares($image, $black, $upgrade3, 1, 64, 518, 16, 16, 16);
                    squares($image, $black, $upgrade4, 1, 64, 614, 16, 16, 16);
                    squares($image, $black, $upgrade5, 3, 64, 737, 59, 16, 16);
                    squares($image, $black, $upgrade6, 3, 64, 803, 59, 16, 16);
                    squares($image, $black, $upgrade7, 4, 64, 869, 81, 16, 16);
                } else {
                    //paint Runic Axe (original) https://ahlcg.derwinski.pl/EN-09022-0-MHwxLDF8MSwyfDEsM3wxLDR8MSw1fDMsNnwzLDd8NA.webp
                    //"cus_09022":"0|1,1|1,2|1,3|1,4|1,5|3,6|3,7|4"
                    squares($image, $black, $upgrade0, 1, 65, 203, 16, 16, 16);
                    squares($image, $black, $upgrade1, 1, 65, 273, 16, 16, 16);
                    squares($image, $black, $upgrade2, 1, 65, 375, 16, 16, 16);
                    squares($image, $black, $upgrade3, 1, 65, 509, 16, 16, 16);
                    squares($image, $black, $upgrade4, 1, 65, 610, 16, 16, 16);
                    squares($image, $black, $upgrade5, 3, 65, 744, 62, 16, 16);
                    squares($image, $black, $upgrade6, 3, 65, 814, 62, 16, 16);
                    squares($image, $black, $upgrade7, 4, 65, 884, 86, 16, 16);
                }
            } else if ($fileToLoad == '09022-6a.webp') {
                if ($flang == 'IT') {
                    //paint Runic Axe (taboo 6+) https://ahlcg.derwinski.pl/IT-09022-6-MHwxLDF8MSwyfDEsM3wyLDR8MSw1fDMsNnwzLDd8NA.webp
                    //"cus_09022":"0|1,1|1,2|1,3|2,4|1,5|3,6|3,7|4"
                    squares($image, $black, $upgrade0, 1, 64, 204, 16, 16, 16);
                    squares($image, $black, $upgrade1, 1, 64, 270, 16, 16, 16);
                    squares($image, $black, $upgrade2, 1, 64, 394, 16, 16, 16);
                    squares($image, $black, $upgrade3, 2, 64, 518, 37, 16, 16);
                    squares($image, $black, $upgrade4, 1, 64, 642, 16, 16, 16);
                    squares($image, $black, $upgrade5, 3, 64, 767, 58, 16, 16);
                    squares($image, $black, $upgrade6, 3, 64, 833, 58, 16, 16);
                    squares($image, $black, $upgrade7, 4, 64, 898, 80, 16, 16);
                } else {
                    //paint Runic Axe (taboo 6+) https://ahlcg.derwinski.pl/EN-09022-6-MHwxLDF8MSwyfDEsM3wyLDR8MSw1fDMsNnwzLDd8NA.webp
                    //"cus_09022":"0|1,1|1,2|1,3|2,4|1,5|3,6|3,7|4"
                    squares($image, $black, $upgrade0, 1, 65, 205, 16, 16, 16);
                    squares($image, $black, $upgrade1, 1, 65, 276, 16, 16, 16);
                    squares($image, $black, $upgrade2, 1, 65, 377, 16, 16, 16);
                    squares($image, $black, $upgrade3, 2, 65, 509, 45, 16, 16);
                    squares($image, $black, $upgrade4, 1, 65, 610, 16, 16, 16);
                    squares($image, $black, $upgrade5, 3, 65, 744, 62, 16, 16);
                    squares($image, $black, $upgrade6, 3, 65, 814, 62, 16, 16);
                    squares($image, $black, $upgrade7, 4, 65, 884, 85, 16, 16);
                }
            } else if ($fileToLoad == '09023a.webp') {
                if ($flang == 'IT') {
                    //paint Custom Modifications https://ahlcg.derwinski.pl/IT-09023-0-MHwxLDF8MiwyfDIsM3wzLDR8Myw1fDQ.webp
                    //"cus_09023":"0|1,1|2,2|2,3|3,4|3,5|4"
                    squares($image, $black, $upgrade0, 1, 64, 209, 18, 18, 18);
                    squares($image, $black, $upgrade1, 2, 64, 346, 42, 18, 18);
                    squares($image, $black, $upgrade2, 2, 64, 420, 42, 18, 18);
                    squares($image, $black, $upgrade3, 3, 64, 525, 65, 18, 18);
                    squares($image, $black, $upgrade4, 3, 64, 630, 65, 18, 18);
                    squares($image, $black, $upgrade5, 4, 64, 832, 89, 18, 18);
                } else {
                    //paint Custom Modifications https://ahlcg.derwinski.pl/EN-09023-0-MHwxLDF8MiwyfDIsM3wzLDR8Myw1fDQ.webp
                    //"cus_09023":"0|1,1|2,2|2,3|3,4|3,5|4"
                    squares($image, $black, $upgrade0, 1, 65, 211, 18, 18, 18);
                    squares($image, $black, $upgrade1, 2, 65, 362, 44, 18, 18);
                    squares($image, $black, $upgrade2, 2, 65, 442, 44, 18, 18);
                    squares($image, $black, $upgrade3, 3, 65, 557, 70, 18, 18);
                    squares($image, $black, $upgrade4, 3, 65, 673, 70, 18, 18);
                    squares($image, $black, $upgrade5, 4, 65, 789, 96, 18, 18);
                }
            } else if ($fileToLoad == '09040a.webp') {
                if ($flang == 'IT') {
                    //paint Alchemical Distillation https://ahlcg.derwinski.pl/IT-09040-0-MHwxLDF8MSwyfDEsM3wxLDR8Miw1fDQsNnw1.webp
                    //"cus_09040":"0|1,1|1,2|1,3|1,4|2,5|4,6|5"
                    squares($image, $black, $upgrade0, 1, 65, 211, 18, 18, 18);
                    squares($image, $black, $upgrade1, 1, 65, 291, 18, 18, 18);
                    squares($image, $black, $upgrade2, 1, 65, 371, 18, 18, 18);
                    squares($image, $black, $upgrade3, 1, 65, 486, 18, 18, 18);
                    squares($image, $black, $upgrade4, 2, 65, 565, 44, 18, 18);
                    squares($image, $black, $upgrade5, 4, 65, 645, 98, 18, 18);
                    squares($image, $black, $upgrade6, 5, 65, 831, 123, 18, 18);
                } else {
                    //paint Alchemical Distillation https://ahlcg.derwinski.pl/EN-09040-0-MHwxLDF8MSwyfDEsM3wxLDR8Miw1fDQsNnw1.webp
                    //"cus_09040":"0|1,1|1,2|1,3|1,4|2,5|4,6|5"
                    squares($image, $black, $upgrade0, 1, 65, 209, 18, 18, 18);
                    squares($image, $black, $upgrade1, 1, 65, 289, 18, 18, 18);
                    squares($image, $black, $upgrade2, 1, 65, 369, 18, 18, 18);
                    squares($image, $black, $upgrade3, 1, 65, 485, 18, 18, 18);
                    squares($image, $black, $upgrade4, 2, 65, 565, 44, 18, 18);
                    squares($image, $black, $upgrade5, 4, 65, 645, 96, 18, 18);
                    squares($image, $black, $upgrade6, 5, 65, 797, 122, 18, 18);
                }
            } else if ($fileToLoad == '09041a.webp') {
                if ($flang == 'IT') {
                    //paint Empirical Hypothesis https://ahlcg.derwinski.pl/IT-09041-0-MHwxLDF8MSwyfDEsM3wxLDR8Miw1fDIsNnwzLDd8NA.webp
                    //"cus_09041":"0|1,1|1,2|1,3|1,4|2,5|2,6|3,7|4"
                    squares($image, $black, $upgrade0, 1, 64, 204, 16, 16, 16);
                    squares($image, $black, $upgrade1, 1, 64, 270, 16, 16, 16);
                    squares($image, $black, $upgrade2, 1, 64, 336, 16, 16, 16);
                    squares($image, $black, $upgrade3, 1, 64, 401, 16, 16, 16);
                    squares($image, $black, $upgrade4, 2, 64, 496, 38, 16, 16);
                    squares($image, $black, $upgrade5, 2, 64, 621, 38, 16, 16);
                    squares($image, $black, $upgrade6, 3, 64, 715, 59, 16, 16);
                    squares($image, $black, $upgrade7, 4, 64, 811, 81, 16, 16);
                } else {
                    //paint Empirical Hypothesis https://ahlcg.derwinski.pl/EN-09041-0-MHwxLDF8MSwyfDEsM3wxLDR8Miw1fDIsNnwzLDd8NA.webp
                    //"cus_09041":"0|1,1|1,2|1,3|1,4|2,5|2,6|3,7|4"
                    squares($image, $black, $upgrade0, 1, 65, 206, 16, 16, 16);
                    squares($image, $black, $upgrade1, 1, 65, 276, 16, 16, 16);
                    squares($image, $black, $upgrade2, 1, 65, 347, 16, 16, 16);
                    squares($image, $black, $upgrade3, 1, 65, 417, 16, 16, 16);
                    squares($image, $black, $upgrade4, 2, 65, 487, 39, 16, 16);
                    squares($image, $black, $upgrade5, 2, 65, 619, 39, 16, 16);
                    squares($image, $black, $upgrade6, 3, 65, 721, 62, 16, 16);
                    squares($image, $black, $upgrade7, 4, 65, 823, 84, 16, 16);
                }
            } else if ($fileToLoad == '09042a.webp') {
                if ($flang == 'IT') {
                    //paint The Raven Quill https://ahlcg.derwinski.pl/IT-09042-0-MHwwfDAxMDYxLDF8MSwyfDEsM3wyLDR8MnwwNDAyOV4wMTA2MCw1fDIsNnwzLDd8NA.webp
                    //"cus_09042":"0|0|01061,1|1,2|1,3|2,4|2|04029^01060,5|2,6|3,7|4"
                    //with custom card name mapping https://ahlcg.derwinski.pl/IT-09042-0-MHwwfGlkMSwxfDEsMnwxLDN8Miw0fDJ8aWQyXmlkMyw1fDIsNnwzLDd8NA-eyJuYW1lcyI6eyJpZDEiOiJDYXJ0YSBBIiwiaWQyIjoiQ2FydGEgQiIsImlkMyI6IkNhcnRhIEMifX0.webp
                    //0|0|id1,1|1,2|1,3|2,4|2|id2^id3,5|2,6|3,7|4
                    //{"names":{"id1":"Carta A","id2":"Carta B","id3":"Carta C"}}
                    squares($image, $black, $upgrade1, 1, 64, 267, 16, 16, 16);
                    squares($image, $black, $upgrade2, 1, 64, 333, 16, 16, 16);
                    squares($image, $black, $upgrade3, 2, 64, 399, 37, 16, 16);
                    squares($image, $black, $upgrade4, 2, 64, 465, 37, 16, 16);
                    squares($image, $black, $upgrade5, 2, 64, 530, 37, 16, 16);
                    squares($image, $black, $upgrade6, 3, 64, 655, 59, 16, 16);
                    squares($image, $black, $upgrade7, 4, 64, 779, 80, 16, 16);
                    $names = load_map('raven_quill_it.tsv', $dictionaries, 'names');
                    text($image, './Arkhamic_v2.1.ttf', 23, $black, $upgrade0, 0, 524, 223, 1, $names);
                    text($image, './Arkhamic_v2.1.ttf', 23, $black, $upgrade4, 2, 211, 510, 1, $names);
                    text($image, './Arkhamic_v2.1.ttf', 23, $black, $upgrade4, 2, 450, 510, 2, $names);
                } else {
                    //paint The Raven Quill https://ahlcg.derwinski.pl/EN-09042-0-MHwwfDAxMDYxLDF8MSwyfDEsM3wyLDR8MnwwNDAyOV4wMTA2MCw1fDIsNnwzLDd8NA.webp
                    //"cus_09042":"0|0|01061,1|1,2|1,3|2,4|2|04029^01060,5|2,6|3,7|4"
                    //with custom card name mapping https://ahlcg.derwinski.pl/EN-09042-0-MHwwfGlkMSwxfDEsMnwxLDN8Miw0fDJ8aWQyXmlkMyw1fDIsNnwzLDd8NA-eyJuYW1lcyI6eyJpZDEiOiJDYXJkIEEiLCJpZDIiOiJDYXJkIEIiLCJpZDMiOiJDYXJkIEMifX0.webp
                    //0|0|id1,1|1,2|1,3|2,4|2|id2^id3,5|2,6|3,7|4
                    //{"names":{"id1":"Card A","id2":"Card B","id3":"Card C"}}
                    squares($image, $black, $upgrade1, 1, 66, 272, 16, 16, 16);
                    squares($image, $black, $upgrade2, 1, 66, 342, 16, 16, 16);
                    squares($image, $black, $upgrade3, 2, 66, 412, 39, 16, 16);
                    squares($image, $black, $upgrade4, 2, 66, 482, 39, 16, 16);
                    squares($image, $black, $upgrade5, 2, 66, 552, 39, 16, 16);
                    squares($image, $black, $upgrade6, 3, 66, 653, 62, 16, 16);
                    squares($image, $black, $upgrade7, 4, 66, 755, 84, 16, 16);
                    $names = load_map('raven_quill_en.tsv', $dictionaries, 'names');
                    text($image, './Arkhamic_v2.1.ttf', 23, $black, $upgrade0, 0, 400, 226, 1, $names);
                    text($image, './Arkhamic_v2.1.ttf', 23, $black, $upgrade4, 2, 144, 529, 1, $names);
                    text($image, './Arkhamic_v2.1.ttf', 23, $black, $upgrade4, 2, 417, 529, 2, $names);
                }
            } else if ($fileToLoad == '09059a.webp') {
                if ($flang == 'IT') {
                    //paint Damning Testimony https://ahlcg.derwinski.pl/IT-09059-0-MHwxLDF8MiwyfDIsM3wzLDR8Myw1fDQ.webp
                    //"cus_09059":"0|1,1|2,2|2,3|3,4|3,5|4"
                    squares($image, $black, $upgrade0, 1, 64, 202, 18, 18, 18);
                    squares($image, $black, $upgrade1, 2, 64, 344, 43, 18, 18);
                    squares($image, $black, $upgrade2, 2, 64, 453, 43, 18, 18);
                    squares($image, $black, $upgrade3, 3, 64, 529, 67, 18, 18);
                    squares($image, $black, $upgrade4, 3, 64, 671, 67, 18, 18);
                    squares($image, $black, $upgrade5, 4, 64, 780, 92, 18, 18);
                } else {
                    //paint Damning Testimony https://ahlcg.derwinski.pl/EN-09059-0-MHwxLDF8MiwyfDIsM3wzLDR8Myw1fDQ.webp
                    //"cus_09059":"0|1,1|2,2|2,3|3,4|3,5|4"
                    squares($image, $black, $upgrade0, 1, 66, 202, 18, 18, 18);
                    squares($image, $black, $upgrade1, 2, 66, 353, 43, 18, 18);
                    squares($image, $black, $upgrade2, 2, 66, 433, 43, 18, 18);
                    squares($image, $black, $upgrade3, 3, 66, 513, 68, 18, 18);
                    squares($image, $black, $upgrade4, 3, 66, 663, 68, 18, 18);
                    squares($image, $black, $upgrade5, 4, 66, 778, 94, 18, 18);
                }
            } else if ($fileToLoad == '09060a.webp') {
                if ($flang == 'IT') {
                    //paint Friends in Low Places https://ahlcg.derwinski.pl/IT-09060-0-MHwwfElsbGljaXQsMXwxLDJ8MnxUcmljaywzfDIsNHwyLDV8Miw2fDMsN3wz.webp
                    //"cus_09060":"0|0|Illicit,1|1,2|2|Trick,3|2,4|2,5|2,6|3,7|3"
                    //with custom trait names https://ahlcg.derwinski.pl/IT-09060-0-MHwwfFRyYWl0IEEsMXwxLDJ8MnxUcmFpdCBCLDN8Miw0fDIsNXwyLDZ8Myw3fDM.webp
                    //0|0|Trait A,1|1,2|2|Trait B,3|2,4|2,5|2,6|3,7|3
                    squares($image, $black, $upgrade1, 1, 64, 267, 16, 16, 16);
                    squares($image, $black, $upgrade2, 2, 64, 368, 40, 16, 16);
                    squares($image, $black, $upgrade3, 2, 64, 500, 40, 16, 16);
                    squares($image, $black, $upgrade4, 2, 64, 601, 40, 16, 16);
                    squares($image, $black, $upgrade5, 2, 64, 703, 40, 16, 16);
                    squares($image, $black, $upgrade6, 3, 64, 774, 62, 16, 16);
                    squares($image, $black, $upgrade7, 3, 64, 844, 62, 16, 16);
                    $traits = load_map('traits_it.tsv', null, 'traits');
                    textOptionalDirect($image, './arnopro6.otf', 20, $black, $upgrade0, 0, 224, 221, 1, $traits);
                    textOptionalDirect($image, './arnopro6.otf', 20, $black, $upgrade2, 2, 475, 384, 1, $traits);
                } else {
                    //paint Friends in Low Places https://ahlcg.derwinski.pl/EN-09060-0-MHwwfElsbGljaXQsMXwxLDJ8MnxUcmljaywzfDIsNHwyLDV8Miw2fDMsN3wz.webp
                    //"cus_09060":"0|0|Illicit,1|1,2|2|Trick,3|2,4|2,5|2,6|3,7|3"
                    //with custom trait names https://ahlcg.derwinski.pl/EN-09060-0-MHwwfFRyYWl0IEEsMXwxLDJ8MnxUcmFpdCBCLDN8Miw0fDIsNXwyLDZ8Myw3fDM.webp
                    //0|0|Trait A,1|1,2|2|Trait B,3|2,4|2,5|2,6|3,7|3
                    squares($image, $black, $upgrade1, 1, 65, 267, 16, 16, 16);
                    squares($image, $black, $upgrade2, 2, 65, 368, 39, 16, 16);
                    squares($image, $black, $upgrade3, 2, 65, 501, 39, 16, 16);
                    squares($image, $black, $upgrade4, 2, 65, 602, 39, 16, 16);
                    squares($image, $black, $upgrade5, 2, 65, 703, 39, 16, 16);
                    squares($image, $black, $upgrade6, 3, 65, 773, 61, 16, 16);
                    squares($image, $black, $upgrade7, 3, 65, 843, 61, 16, 16);
                    textDirect($image, './arnopro6.otf', 20, $black, $upgrade0, 0, 239, 219, 1);
                    textDirect($image, './arnopro6.otf', 20, $black, $upgrade2, 2, 494, 382, 1);
                }
            } else if ($fileToLoad == '09061a.webp') {
                if ($flang == 'IT') {
                    //paint Honed Instinct https://ahlcg.derwinski.pl/IT-09061-0-MHwxLDF8MSwyfDEsM3wxLDR8MSw1fDIsNnwzLDd8NQ.webp
                    //"cus_09061":"0|1,1|1,2|1,3|1,4|1,5|2,6|3,7|5"
                    squares($image, $black, $upgrade0, 1, 64, 210, 16, 16, 16);
                    squares($image, $black, $upgrade1, 1, 64, 280, 16, 16, 16);
                    squares($image, $black, $upgrade2, 1, 64, 381, 16, 16, 16);
                    squares($image, $black, $upgrade3, 1, 64, 451, 16, 16, 16);
                    squares($image, $black, $upgrade4, 1, 64, 552, 16, 16, 16);
                    squares($image, $black, $upgrade5, 2, 64, 622, 40, 16, 16);
                    squares($image, $black, $upgrade6, 3, 64, 693, 63, 16, 16);
                    squares($image, $black, $upgrade7, 5, 64, 794, 110, 16, 16);
                } else {
                    //paint Honed Instinct https://ahlcg.derwinski.pl/EN-09061-0-MHwxLDF8MSwyfDEsM3wxLDR8MSw1fDIsNnwzLDd8NQ.webp
                    //"cus_09061":"0|1,1|1,2|1,3|1,4|1,5|2,6|3,7|5"
                    squares($image, $black, $upgrade0, 1, 65, 208, 16, 16, 16);
                    squares($image, $black, $upgrade1, 1, 65, 278, 16, 16, 16);
                    squares($image, $black, $upgrade2, 1, 65, 349, 16, 16, 16);
                    squares($image, $black, $upgrade3, 1, 65, 419, 16, 16, 16);
                    squares($image, $black, $upgrade4, 1, 65, 489, 16, 16, 16);
                    squares($image, $black, $upgrade5, 2, 65, 559, 39, 16, 16);
                    squares($image, $black, $upgrade6, 3, 65, 629, 62, 16, 16);
                    squares($image, $black, $upgrade7, 5, 65, 730, 108, 16, 16);
                }
            } else if ($fileToLoad == '09079a.webp') {
                if ($flang == 'IT') {
                    //paint Living Ink https://ahlcg.derwinski.pl/IT-09079-0-MHwwfHdpbGxwb3dlciwxfDEsMnwxLDN8Miw0fDJ8aW50ZWxsZWN0LDV8M3xhZ2lsaXR5LDZ8Myw3fDM.webp
                    //"cus_09079":"0|0|willpower,1|1,2|1,3|2,4|2|intellect,5|3|agility,6|3,7|3"
                    squares($image, $black, $upgrade1, 1, 64, 276, 18, 18, 18);
                    squares($image, $black, $upgrade2, 1, 64, 385, 16, 18, 18);
                    squares($image, $black, $upgrade3, 2, 64, 527, 42, 18, 18);
                    squares($image, $black, $upgrade4, 2, 64, 636, 42, 18, 18);
                    squares($image, $black, $upgrade5, 3, 64, 678, 66, 18, 18);
                    squares($image, $black, $upgrade6, 3, 64, 721, 66, 18, 18);
                    squares($image, $black, $upgrade7, 3, 64, 863, 66, 18, 18);
                    circle($image, $black, $upgrade0, 0, 371, 461, 556, 643, 224, 64, 64, 1);
                    circle($image, $black, $upgrade4, 2, 371, 461, 556, 643, 224, 64, 64, 1);
                    circle($image, $black, $upgrade5, 3, 371, 461, 556, 643, 224, 64, 64, 1);
                } else {
                    //paint Living Ink https://ahlcg.derwinski.pl/EN-09079-0-MHwwfHdpbGxwb3dlciwxfDEsMnwxLDN8Miw0fDJ8aW50ZWxsZWN0LDV8M3xhZ2lsaXR5LDZ8Myw3fDM.webp
                    //"cus_09079":"0|0|willpower,1|1,2|1,3|2,4|2|intellect,5|3|agility,6|3,7|3"
                    squares($image, $black, $upgrade1, 1, 65, 279, 18, 18, 18);
                    squares($image, $black, $upgrade2, 1, 65, 395, 16, 18, 18);
                    squares($image, $black, $upgrade3, 2, 65, 545, 43, 18, 18);
                    squares($image, $black, $upgrade4, 2, 65, 661, 43, 18, 18);
                    squares($image, $black, $upgrade5, 3, 65, 705, 68, 18, 18);
                    squares($image, $black, $upgrade6, 3, 65, 750, 68, 18, 18);
                    squares($image, $black, $upgrade7, 3, 65, 865, 68, 18, 18);
                    circle($image, $black, $upgrade0, 0, 346, 441, 541, 633, 224, 64, 64, 1);
                    circle($image, $black, $upgrade4, 2, 346, 441, 541, 633, 224, 64, 64, 1);
                    circle($image, $black, $upgrade5, 3, 346, 441, 541, 633, 224, 64, 64, 1);
                }
            } else if ($fileToLoad == '09080a.webp') {
                if ($flang == 'IT') {
                    //paint Summoned Servitor https://ahlcg.derwinski.pl/IT-09080-0-MHwxLDF8MSwyfDEsM3wxLDR8MSw1fDJ8MCw2fDMsN3w1.webp
                    //"cus_09080":"0|1,1|1,2|1,3|1,4|1,5|2|0,6|3,7|5"
                    squares($image, $black, $upgrade0, 1, 64, 200, 16, 16, 16);
                    squares($image, $black, $upgrade1, 1, 64, 292, 16, 16, 16);
                    squares($image, $black, $upgrade2, 1, 64, 412, 16, 16, 16);
                    squares($image, $black, $upgrade3, 1, 64, 534, 16, 16, 16);
                    squares($image, $black, $upgrade4, 1, 64, 598, 16, 16, 16);
                    squares($image, $black, $upgrade5, 2, 64, 690, 37, 16, 16);
                    rectangle($image, $black, $upgrade5, 2, 216, 711, 78, 35, 1, 0);
                    rectangle($image, $black, $upgrade5, 2, 312, 711, 81, 35, 1, 1);
                    squares($image, $black, $upgrade6, 3, 64, 755, 58, 16, 16);
                    squares($image, $black, $upgrade7, 5, 64, 876, 100, 16, 16);
                } else {
                    //paint Summoned Servitor https://ahlcg.derwinski.pl/EN-09080-0-MHwxLDF8MSwyfDEsM3wxLDR8MSw1fDJ8MCw2fDMsN3w1.webp
                    //"cus_09080":"0|1,1|1,2|1,3|1,4|1,5|2|0,6|3,7|5"
                    squares($image, $black, $upgrade0, 1, 65, 201, 16, 16, 16);
                    squares($image, $black, $upgrade1, 1, 65, 303, 16, 16, 16);
                    squares($image, $black, $upgrade2, 1, 65, 405, 16, 16, 16);
                    squares($image, $black, $upgrade3, 1, 65, 537, 16, 16, 16);
                    squares($image, $black, $upgrade4, 1, 65, 607, 16, 16, 16);
                    squares($image, $black, $upgrade5, 2, 65, 709, 38, 16, 16);
                    rectangle($image, $black, $upgrade5, 2, 276, 732, 83, 36, 1, 0);
                    rectangle($image, $black, $upgrade5, 2, 377, 732, 48, 36, 1, 1);
                    squares($image, $black, $upgrade6, 3, 65, 779, 61, 16, 16);
                    squares($image, $black, $upgrade7, 5, 65, 881, 107, 16, 16);
                }
            } else if ($fileToLoad == '09081a.webp') {
                if ($flang == 'IT') {
                    //paint Power Word (original) https://ahlcg.derwinski.pl/IT-09081-0-MHwxLDF8MSwyfDEsM3wxLDR8Miw1fDMsNnwzLDd8Mw.webp
                    //"cus_09081":"0|1,1|1,2|1,3|1,4|2,5|3,6|3,7|3",
                    squares($image, $black, $upgrade0, 1, 64, 203, 16, 16, 16);
                    squares($image, $black, $upgrade1, 1, 64, 318, 16, 16, 16);
                    squares($image, $black, $upgrade2, 1, 64, 435, 16, 16, 16);
                    squares($image, $black, $upgrade3, 1, 64, 522, 16, 16, 16);
                    squares($image, $black, $upgrade4, 2, 64, 640, 34, 16, 16);
                    squares($image, $black, $upgrade5, 3, 64, 701, 52, 16, 16);
                    squares($image, $black, $upgrade6, 3, 64, 789, 52, 16, 16);
                    squares($image, $black, $upgrade7, 3, 64, 852, 52, 16, 16);
                } else {
                    //paint Power Word (original) https://ahlcg.derwinski.pl/EN-09081-0-MHwxLDF8MSwyfDEsM3wxLDR8Miw1fDMsNnwzLDd8Mw.webp
                    //"cus_09081":"0|1,1|1,2|1,3|1,4|2,5|3,6|3,7|3",
                    squares($image, $black, $upgrade0, 1, 66, 207, 16, 16, 16);
                    squares($image, $black, $upgrade1, 1, 66, 308, 16, 16, 16);
                    squares($image, $black, $upgrade2, 1, 66, 410, 16, 16, 16);
                    squares($image, $black, $upgrade3, 1, 66, 510, 16, 16, 16);
                    squares($image, $black, $upgrade4, 2, 66, 612, 37, 16, 16);
                    squares($image, $black, $upgrade5, 3, 66, 682, 59, 16, 16);
                    squares($image, $black, $upgrade6, 3, 66, 783, 59, 16, 16);
                    squares($image, $black, $upgrade7, 3, 66, 854, 59, 16, 16);
                }
            } else if ($fileToLoad == '09081-6a.webp') {
                if ($flang == 'IT') {
                    //paint Power Word (taboo 6+) https://ahlcg.derwinski.pl/IT-09081-6-MHwxLDF8MSwyfDEsM3wxLDR8Miw1fDMsNnwzLDd8Mw.webp
                    //"cus_09081":"0|1,1|1,2|1,3|1,4|2,5|3,6|3,7|3"
                    squares($image, $black, $upgrade0, 1, 64, 203, 16, 16, 16);
                    squares($image, $black, $upgrade1, 1, 64, 318, 16, 16, 16);
                    squares($image, $black, $upgrade2, 1, 64, 380, 16, 16, 16);
                    squares($image, $black, $upgrade3, 1, 64, 469, 16, 16, 16);
                    squares($image, $black, $upgrade4, 2, 64, 586, 34, 16, 16);
                    squares($image, $black, $upgrade5, 3, 64, 647, 52, 16, 16);
                    squares($image, $black, $upgrade6, 3, 64, 736, 52, 16, 16);
                    squares($image, $black, $upgrade7, 3, 64, 798, 52, 16, 16);
                } else {
                    //paint Power Word (taboo 6+) https://ahlcg.derwinski.pl/EN-09081-6-MHwxLDF8MSwyfDEsM3wxLDR8Miw1fDMsNnwzLDd8Mw.webp
                    //"cus_09081":"0|1,1|1,2|1,3|1,4|2,5|3,6|3,7|3"
                    squares($image, $black, $upgrade0, 1, 65, 206, 16, 16, 16);
                    squares($image, $black, $upgrade1, 1, 65, 307, 16, 16, 16);
                    squares($image, $black, $upgrade2, 1, 65, 377, 16, 16, 16);
                    squares($image, $black, $upgrade3, 1, 65, 478, 16, 16, 16);
                    squares($image, $black, $upgrade4, 2, 65, 579, 37, 16, 16);
                    squares($image, $black, $upgrade5, 3, 65, 649, 58, 16, 16);
                    squares($image, $black, $upgrade6, 3, 65, 751, 58, 16, 16);
                    squares($image, $black, $upgrade7, 3, 65, 821, 58, 16, 16);
                }
            } else if ($fileToLoad == '09099a.webp') {
                if ($flang == 'IT') {
                    //paint Pocket Multi Tool https://ahlcg.derwinski.pl/IT-09099-0-MHwxLDF8MSwyfDIsM3wyLDR8Miw1fDMsNnw0.webp
                    //"cus_09099":"0|1,1|1,2|2,3|2,4|2,5|3,6|4"
                    squares($image, $black, $upgrade0, 1, 64, 210, 18, 18, 18);
                    squares($image, $black, $upgrade1, 1, 64, 319, 18, 18, 18);
                    squares($image, $black, $upgrade2, 2, 64, 428, 42, 18, 18);
                    squares($image, $black, $upgrade3, 2, 64, 503, 42, 18, 18);
                    squares($image, $black, $upgrade4, 2, 64, 613, 42, 18, 18);
                    squares($image, $black, $upgrade5, 3, 64, 722, 67, 18, 18);
                    squares($image, $black, $upgrade6, 4, 64, 798, 91, 18, 18);
                } else {
                    //paint Pocket Multi Tool https://ahlcg.derwinski.pl/EN-09099-0-MHwxLDF8MSwyfDIsM3wyLDR8Miw1fDMsNnw0.webp
                    //"cus_09099":"0|1,1|1,2|2,3|2,4|2,5|3,6|4"
                    squares($image, $black, $upgrade0, 1, 66, 212, 18, 18, 18);
                    squares($image, $black, $upgrade1, 1, 66, 327, 18, 18, 18);
                    squares($image, $black, $upgrade2, 2, 66, 407, 42, 18, 18);
                    squares($image, $black, $upgrade3, 2, 66, 487, 42, 18, 18);
                    squares($image, $black, $upgrade4, 2, 66, 566, 42, 18, 18);
                    squares($image, $black, $upgrade5, 3, 66, 647, 67, 18, 18);
                    squares($image, $black, $upgrade6, 4, 66, 727, 93, 18, 18);
                }
            } else if ($fileToLoad == '09100a.webp') {
                if ($flang == 'IT') {
                    //paint Makeshift Trap https://ahlcg.derwinski.pl/IT-09100-0-MHwxLDF8MSwyfDIsM3wyLDR8Miw1fDMsNnw0.webp
                    //"cus_09100":"0|1,1|1,2|2,3|2,4|2,5|3,6|4"
                    squares($image, $black, $upgrade0, 1, 64, 209, 18, 18, 18);
                    squares($image, $black, $upgrade1, 1, 64, 312, 18, 18, 18);
                    squares($image, $black, $upgrade2, 2, 64, 415, 41, 18, 18);
                    squares($image, $black, $upgrade3, 2, 64, 486, 41, 18, 18);
                    squares($image, $black, $upgrade4, 2, 64, 589, 41, 18, 18);
                    squares($image, $black, $upgrade5, 3, 64, 692, 64, 18, 18);
                    squares($image, $black, $upgrade6, 4, 64, 795, 87, 18, 18);
                } else {
                    //paint Makeshift Trap https://ahlcg.derwinski.pl/EN-09100-0-MHwxLDF8MSwyfDIsM3wyLDR8Miw1fDMsNnw0.webp
                    //"cus_09100":"0|1,1|1,2|2,3|2,4|2,5|3,6|4"
                    squares($image, $black, $upgrade0, 1, 66, 209, 18, 18, 18);
                    squares($image, $black, $upgrade1, 1, 66, 290, 18, 18, 18);
                    squares($image, $black, $upgrade2, 2, 66, 405, 43, 18, 18);
                    squares($image, $black, $upgrade3, 2, 66, 485, 43, 18, 18);
                    squares($image, $black, $upgrade4, 2, 66, 601, 43, 18, 18);
                    squares($image, $black, $upgrade5, 3, 66, 718, 69, 18, 18);
                    squares($image, $black, $upgrade6, 4, 66, 833, 95, 18, 18);
                }
            } else if ($fileToLoad == '09101a.webp') {
                if ($flang == 'IT') {
                    //paint Grizzled https://ahlcg.derwinski.pl/IT-09101-0-MHwwfEh1bWFub2lkXk1vbnN0ZXIsMXwxfFNwZWN0cmFsLDJ8MnxFbGl0ZSwzfDMsNHw0LDV8NQ.webp
                    //"cus_09101":"0|0|Humanoid^Monster,1|1|Spectral,2|2|Elite,3|3,4|4,5|5"
                    squares($image, $black, $upgrade1, 1, 64, 273, 18, 18, 18);
                    squares($image, $black, $upgrade2, 2, 64, 355, 43, 18, 18);
                    squares($image, $black, $upgrade3, 3, 64, 436, 67, 18, 18);
                    squares($image, $black, $upgrade4, 4, 64, 619, 93, 18, 18);
                    squares($image, $black, $upgrade5, 5, 64, 767, 117, 18, 18);
                    $traits = load_map('traits_it.tsv', null, 'traits');
                    textOptionalDirect($image, './arnopro6.otf', 20, $black, $upgrade0, 0, 240, 227, 1, $traits);
                    textOptionalDirect($image, './arnopro6.otf', 20, $black, $upgrade0, 0, 460, 227, 2, $traits);
                    textOptionalDirect($image, './arnopro6.otf', 20, $black, $upgrade1, 1, 62, 323, 1, $traits);
                    textOptionalDirect($image, './arnopro6.otf', 20, $black, $upgrade2, 2, 62, 405, 1, $traits);
                } else {
                    //paint Grizzled https://ahlcg.derwinski.pl/EN-09101-0-MHwwfEh1bWFub2lkXk1vbnN0ZXIsMXwxfFNwZWN0cmFsLDJ8MnxFbGl0ZSwzfDMsNHw0LDV8NQ.webp
                    //"cus_09101":"0|0|Humanoid^Monster,1|1|Spectral,2|2|Elite,3|3,4|4,5|5"
                    squares($image, $black, $upgrade1, 1, 65, 275, 18, 18, 18);
                    squares($image, $black, $upgrade2, 2, 65, 361, 44, 18, 18);
                    squares($image, $black, $upgrade3, 3, 65, 447, 70, 18, 18);
                    squares($image, $black, $upgrade4, 4, 65, 639, 96, 18, 18);
                    squares($image, $black, $upgrade5, 5, 65, 795, 122, 18, 18);
                    textDirect($image, './arnopro6.otf', 20, $black, $upgrade0, 0, 265, 226, 1);
                    textDirect($image, './arnopro6.otf', 20, $black, $upgrade0, 0, 479, 226, 2);
                    textDirect($image, './arnopro6.otf', 20, $black, $upgrade1, 1, 61, 327, 1);
                    textDirect($image, './arnopro6.otf', 20, $black, $upgrade2, 2, 61, 413, 1);
                }
            } else if ($fileToLoad == '09119a.webp') {
                if ($flang == 'IT') {
                    //paint Hyperphysical Shotcaster: Theoretical Device https://ahlcg.derwinski.pl/IT-09119-0-MHwyLDF8MiwyfDIsM3wyLDR8Miw1fDQsNnw0.webp
                    //"cus_09119":"0|2,1|2,2|2,3|2,4|2,5|4,6|4",
                    squares($image, $black, $upgrade0, 2, 64, 208, 37, 16, 16);
                    squares($image, $black, $upgrade1, 2, 64, 297, 37, 16, 16);
                    squares($image, $black, $upgrade2, 2, 64, 416, 37, 16, 16);
                    squares($image, $black, $upgrade3, 2, 64, 562, 37, 16, 16);
                    squares($image, $black, $upgrade4, 2, 64, 680, 37, 16, 16);
                    squares($image, $black, $upgrade5, 4, 64, 825, 78, 16, 16);
                    squares($image, $black, $upgrade6, 4, 64, 887, 78, 16, 16);
                } else {
                    //paint Hyperphysical Shotcaster: Theoretical Device https://ahlcg.derwinski.pl/EN-09119-0-MHwyLDF8MiwyfDIsM3wyLDR8Miw1fDQsNnw0.webp
                    //"cus_09119":"0|2,1|2,2|2,3|2,4|2,5|4,6|4",
                    squares($image, $black, $upgrade0, 2, 65, 210, 38, 16, 16);
                    squares($image, $black, $upgrade1, 2, 65, 308, 38, 16, 16);
                    squares($image, $black, $upgrade2, 2, 65, 437, 38, 16, 16);
                    squares($image, $black, $upgrade3, 2, 65, 598, 38, 16, 16);
                    squares($image, $black, $upgrade4, 2, 65, 728, 38, 16, 16);
                    squares($image, $black, $upgrade5, 4, 65, 857, 84, 16, 16);
                    squares($image, $black, $upgrade6, 4, 65, 924, 84, 16, 16);
                }
            }
            header('Cache-Control: max-age=2592000');
            header('Content-Type: image/webp');
            header('Content-Disposition: inline; filename="' . $id . '-' . $taboo . '-' . $data . '.webp"');
            imagewebp($image);
            imagedestroy($image);
            exit(0);
        }
    } catch (Exception $e) {
        error_log('id=' . $id . ', taboo=' . $taboo . ', data=' . $data . ', fileToLoad=' . $fileToLoad . ', exception=' . $e->getMessage());
    }
}
$image = imagecreatefromwebp('./upgrade.webp');
header('Cache-Control: max-age=2592000');
header('Content-Type: image/webp');
header('Content-Disposition: inline; filename="' . $id . '-' . $taboo . '-' . $data . '.webp"'); //to enable caching of wrong images, mod_rewrite should keep this safe, script will not be called for nonmatching params
imagewebp($image);
imagedestroy($image);
