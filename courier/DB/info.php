<?php
$base_path = '/courier/';
session_start();

class Info_class {

    function route($path,$id) {
        return $path.'?id='.$id;
    }
}

