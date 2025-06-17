<?php
if(isset($_GET["wrong-password"])) {redirect("wrong-password");};
if(isset($_GET["login-200"])) {redirect("login-200");};
if(isset($_GET["profile-created"])) {redirect("profile-created");};
if(isset($_GET["user-not-found"])) {redirect("user-not-found");};
if(isset($_GET["event-created"])) {redirect("event-created");};
if(isset($_GET["event-deleted"])) {redirect("event-deleted");};
if(isset($_GET["event-updated"])) {redirect("event-updated");};
if(isset($_GET["error"])) {redirect("error");};
if(isset($_GET["missing-required"])) {redirect("missing-required");};

function redirect($par) {
    echo "<script>location.href = `../../../?{$par}`</script>";
   
}