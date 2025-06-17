<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Landing Page</title>
   <!-- Google Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
   <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Lato:ital,wght@0,100;0,300;0,400;0,700;0,900;1,100;1,300;1,400;1,700;1,900&family=Montserrat:ital,wght@0,100..900;1,100..900&family=Open+Sans:ital,wght@0,300..800;1,300..800&family=Poppins:ital,wght@0,100..900;1,100..900&family=Quicksand:wght@300..700&family=Roboto:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">

  <link rel="stylesheet" href="./styles/public/header.css" />
   <!-- Stylesheets -->
  <link rel="stylesheet" href="./styles/public/landing.css">
  <link rel="stylesheet" href="./styles/public/features.css">
  <link rel="stylesheet" href="./styles/public/pricing.css">
  <link rel="stylesheet" href="./styles/public/contact.css">
  <link rel="stylesheet" href="./styles/public/createAcc.css">
  <link rel="stylesheet" href="./styles/public/login.css">
  <link rel="stylesheet" href="./styles/public/dashboard.css">
  <link rel="stylesheet" href="./styles/public/dialog.css">
  <link rel="stylesheet" href="./styles/public/users.css">
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body>

<?php

  $base = empty($_GET) ? '' : '?';

 
  $home = $base . '#';
  $features = $base . '#features';
  $pricing = $base . '#pricing';
  $contact = $base . '#contact';

  $hidePages = ["chat", "dashboard", "teams", "tasks", "documents", "calendar", "users" , "login-200"];

  $showHeader = true;
  foreach ($hidePages as $page) {
      if (isset($_GET[$page])) {
          $showHeader = false;
          break;
      }
  }
?>

<?php if ($showHeader): ?>
<header class="navbar">
  <a href="<?= htmlspecialchars($home) ?>">
    <div class="logo">Team Connection</div>
  </a>
  <nav>
    <ul class="nav-links">
      <li><a class="active" href="<?= htmlspecialchars($home) ?>">Home</a></li>
      <li><a href="<?= htmlspecialchars($features) ?>">Features</a></li>
      <li><a href="<?= htmlspecialchars($pricing) ?>">Pricing</a></li>
      <li><a href="<?= htmlspecialchars($contact) ?>">Contact</a></li>
    </ul>
  </nav>
  <div class="auth">
    <a href="?createAccount" class="btn">Create Account</a>
  </div>
</header>
<?php endif; ?>


