<?php
  function isActive($page) {
    return (isset($_GET[$page])) ? 'active-link' : '';
  }
?>

<!-- Sidebar -->
<aside class="w-64 bg-white shadow-md border-r flex flex-col" role="navigation" aria-label="Main Sidebar">
  <a href="">
    <div class="p-5 text-xl font-bold text-blue-600 border-b">
      Application
    </div>
  </a>

  <nav class="flex-1 px-4 py-6 space-y-2 text-sm">
    <a href="?dashboard" class="flex items-center gap-2 px-4 py-2 rounded-lg hover:bg-blue-50 transition font-semibold <?= isActive('dashboard') ?>">🏠 Dashboard</a>
    <a href="?teams" class="flex items-center gap-2 px-4 py-2 rounded-lg hover:bg-blue-50 transition font-semibold <?= isActive('teams') ?>">👥 Teams</a>
    <a href="?tasks" class="flex items-center gap-2 px-4 py-2 rounded-lg hover:bg-blue-50 transition font-semibold <?= isActive('tasks') ?>">✅ Tasks</a>
<!--  -->
    <a href="?calendar" class="flex items-center gap-2 px-4 py-2 rounded-lg hover:bg-blue-50 transition font-semibold <?= isActive('calendar') ?>">📅 Calendar</a>
    <a href="?users" class="flex items-center gap-2 px-4 py-2 rounded-lg hover:bg-blue-50 transition font-semibold <?= isActive('users') ?>">🪪 Users</a>
  </nav>

  <div class="p-4">
    <a href="?logout" class="block w-full text-center bg-red-100 text-red-600 px-4 py-2 rounded-lg font-semibold hover:bg-red-200 transition" role="button">
      Logout
    </a>
  </div>
</aside>
