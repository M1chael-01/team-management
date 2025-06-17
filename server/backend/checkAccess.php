<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
class CheckAccess{
    public static function checkAccess() {
        if(isset($_SESSION["code"]) && isset($_COOKIE["code"]) && isset($_SESSION["user"])) {
            if($_SESSION["code"] == $_COOKIE["code"]) return true;
            else return false;
        }
        else return false;
    }
}