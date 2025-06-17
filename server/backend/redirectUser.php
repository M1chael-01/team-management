<?php
class RedirectUser{
    public static function redirectUser($par) {
            $protocol = (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] != "off")  ? "https" : "http";
            if($par) {
                $host = $_SERVER["HTTP_HOST"]; 
                header("Location: {$protocol}://{$host}/team-management/pages/public/app/info?{$par}");
                die();
            }
    }
}


