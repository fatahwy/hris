<?php

namespace app\components;

class StyleHelper {

    public static function buttonActionStyle($style = "") {
        return [
            'style' => "white-space: nowrap;width:150px;$style",
//            'style'=>'max-width:150px; overflow: auto; white-space: normal; word-wrap: break-word;',
        ];
    }

}
