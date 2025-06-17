<body>
<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once "./server/backend/checkAccess.php";
require "./pages/public/header.php";

// Shortcuts
$hasAccess = CheckAccess::checkAccess();
$redirectHome = "<script>location.href = './';</script>";
$redirectDashboard = "<script>location.href = './?dashboard';</script>";

// Redirect to dashboard if logged in and no specific route
if (empty($_GET) && $hasAccess) {
    echo $redirectDashboard;
    exit;
}

// Pages that don't require login
if (isset($_GET["createAccount"])) {
    if ($hasAccess) {
        echo $redirectDashboard;
    } else {
        require "./pages/public/createAcc.php";
    }
    exit;
}

if (isset($_GET["login"])) {
    if ($hasAccess) {
        echo $redirectDashboard;
    } else {
        require "./pages/public/login.php";
    }
    exit;
}

// Special messages
if (isset($_GET["wrong-password"]) || isset($_GET["profile-created"]) || isset($_GET["user-not-found"])
|| isset($_GET["event-updated"])  || isset($_GET["event-created"]) || isset($_GET["event-deleted"])
|| isset($_GET["error"]) || isset($_GET["missing-required"])) {
    require "./pages/public/app/message.php";
    exit;
}
if(isset($_GET["logout"])) {
    session_destroy();
    setcookie("code" , "" , time()-3600 , "/");
    echo "<script>location.href = `?`</script>";
}

// Pages that require login
$protectedRoutes = [
    "dashboard"  => "dashboard.php",
    "chat"       => "chat.php",
    "teams"      => "teams.php",
    "users"      => "users.php",
    "tasks"      => "tasks.php",
    "documents"  => "documents.php",
    "calendar"   => "callendar.php"
];

foreach ($protectedRoutes as $key => $file) {
    if (isset($_GET[$key])) {
        if ($hasAccess) {
            require "./pages/public/app/{$file}";
        } else {
            echo $redirectHome;
        }
        exit;
    }
}

// Special case for login success
if (isset($_GET["login-200"])) {
    if ($hasAccess) {
        require "./pages/public/app/dashboard.php";
    } else {
        echo $redirectHome;
    }
    exit;
}

// Default fallback
require "./pages/public/landing.php";

// Final script: optional redirection for logged-in users

?>
</body>
