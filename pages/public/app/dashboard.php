<?php
require "./server/backend/EncryptionDecription.php";
require "./server/database/users.php";


if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$userName = '';

if (isset($_SESSION["userID"])) {
    $userID = $_SESSION["userID"];
    $conn = users(); 

    $sql = "SELECT name FROM user WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "i", $userID);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if ($row = mysqli_fetch_assoc($result)) {
            $userName = Secure::decryption($row['name']);
        }

        mysqli_stmt_close($stmt);
    }

    mysqli_close($conn);
}

?>



<body class="bg-gray-100 text-gray-800">

<div class="flex h-screen overflow-hidden">

<?php require "./pages/public/app/nav.php"; ?>

  <!-- Main -->
  <main class="flex-1 overflow-y-auto p-8 space-y-10 bg-gray-50">

    <!-- Greeting -->
    <section>
      <h1 class="text-3xl font-bold text-gray-800 mb-1">Welcome back, <?=$userName ?> 👋</h1>
      <p class="text-gray-500">Manage your teams, tasks, and schedule with ease.</p>
    </section>

    <!-- What User Can Do -->
    <section class="bg-white p-8 rounded-2xl shadow-lg border border-gray-200">
      <h2 class="text-2xl font-semibold text-blue-700 mb-6">What You Can Do</h2>
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-gray-700 text-base leading-relaxed">

        <!-- Item -->
        <div class="flex items-start gap-4">
          <div class="text-blue-600 text-2xl">➕</div>
          <div>
            <h3 class="font-semibold text-gray-800">Create Teams</h3>
            <p>Create teams for different projects and assign members easily.</p>
          </div>
        </div>

        <!-- Item -->
        <div class="flex items-start gap-4">
          <div class="text-red-500 text-2xl">🗑️</div>
          <div>
            <h3 class="font-semibold text-gray-800">Delete Teams</h3>
            <p>Remove old or inactive teams to keep things tidy.</p>
          </div>
        </div>

        <!-- Item -->
        <div class="flex items-start gap-4">
          <div class="text-yellow-500 text-2xl">✏️</div>
          <div>
            <h3 class="font-semibold text-gray-800">Edit Teams</h3>
            <p>Rename teams, update goals, and manage members on the fly.</p>
          </div>
        </div>

        <!-- Item -->
        <div class="flex items-start gap-4">
          <div class="text-green-600 text-2xl">👥</div>
          <div>
            <h3 class="font-semibold text-gray-800">Add Users</h3>
            <p>Bring in new members and assign them to the right teams.</p>
          </div>
        </div>

        <!-- Item -->
        <div class="flex items-start gap-4">
          <div class="text-purple-600 text-2xl">🛠️</div>
          <div>
            <h3 class="font-semibold text-gray-800">Edit Users</h3>
            <p>Update user info, manage roles, or reset credentials.</p>
          </div>
        </div>

        <!-- Item -->
        <div class="flex items-start gap-4">
          <div class="text-indigo-600 text-2xl">🗓️</div>
          <div>
            <h3 class="font-semibold text-gray-800">Schedule Your Day</h3>
            <p>Plan meetings, manage personal tasks, and never miss deadlines.</p>
          </div>
        </div>

        <!-- Item -->
        <div class="flex items-start gap-4">
          <div class="text-pink-600 text-2xl">📌</div>
          <div>
            <h3 class="font-semibold text-gray-800">Manage Tasks</h3>
            <p>Create, assign, and track progress on important work items.</p>
          </div>
        </div>

        <!-- Item -->
        <div class="flex items-start gap-4">
          <div class="text-gray-600 text-2xl">📊</div>
          <div>
            <h3 class="font-semibold text-gray-800">View Team Insights</h3>
            <p>See performance metrics, task status, and who’s doing what.</p>
          </div>
        </div>

      </div>
    </section>

  </main>
  
</div>



</body>

