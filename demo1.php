<?php
class Wrapper {
    function wrap($text, $lineLength) {
        if (strlen($text) > $lineLength){
            //return substr ($text, 0, $lineLength) . "\n" . substr ($text, $lineLength);
            return substr ($text, 0, $lineLength) . "\n" . $this->wrap(substr($text, $lineLength), $lineLength);
        }
        return $text;
    }
}