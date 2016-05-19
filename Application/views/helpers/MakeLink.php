<?php

class Zend_View_Helper_MakeLink extends Zend_View_Helper_Abstract {

    public function makeLink($text) {
        $text = preg_replace('%(((f|ht){1}tp[s]?://)[-a-zA-^Z0-9@:\%_\+.,!~#?&//=]+)%i', '<a href="\\1">\\1</a>', $text);
        $text = preg_replace('%([[:space:]()[{}])(www.[-a-zA-Z0-9@:\%_\+.,!~#?&//=]+)%i', '\\1<a href="http://\\2">\\2</a>', $text);

        return $text;
    }

}
