<?php
require "./server/database/teams.php";
require "./server/database/users.php";
require "./server/backend/EncryptionDecription.php";
require "./server/database/tasks.php";

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if(isset($_GET["group"])) {
    require "./pages/public/app/editTeam.php";
    exit;
}

function selectAllUsers() {
    if (isset($_SESSION["id"])) {
        $conn = users(); // your DB connection
        $id = $_SESSION["id"];
        
        $sql = "SELECT * FROM user WHERE acountID = ?";
        $stmt = mysqli_prepare($conn, $sql);
        
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "i", $id);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);

            if ($result) {
                $usersArray = [];
                while ($row = mysqli_fetch_assoc($result)) {
                    $usersArray[] = $row;
                }
                mysqli_stmt_close($stmt);
                return $usersArray;
            } else {
                mysqli_stmt_close($stmt);
                return [];
            }
        }
    }
    return [];
}

$users = selectAllUsers();

foreach ($users as &$user) {
    $user['active'] = false; 
}
unset($user); 


$message = '';
$teams = [];

function getData() {
    $connection = teams(); 
    $acount = $_SESSION["id"];
    $teams = [];

    $sql = "SELECT * FROM team WHERE acountID = ?";
    $stmt = mysqli_prepare($connection, $sql);

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "i", $acount);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if ($result) {
            while ($row = mysqli_fetch_assoc($result)) {
                $teams[] = [
                    'id' => $row['id'],
                    'name' => Secure::decryption($row['teamName']),
                    'members' => count(json_decode($row['members'], true) ?? []),
                    'tasks' => $row['countTasks'],
                    'users' => json_decode($row['members'], true) ?? [],
                ];
            }
        }
    }

    return $teams;
}

if (isset($_SESSION["id"])) {
    $teams = getData();
}

function selectTaskCount($teamName) {
  $count = 0;
  $taskConn = tasks();
  $encryptedTeam = Secure::encryption($teamName);

  $sql = "SELECT team FROM task;";
  $result = mysqli_query($taskConn, $sql);

  if($result && mysqli_num_rows($result) >0) {
    while($row = mysqli_fetch_assoc($result)) {
      $decryptedTeam = Secure::decryption($row["team"]);
      if($decryptedTeam == $teamName) {
        $count++;
      }
    }
  }
return $count;
}


?>
<body class="bg-gray-100 text-gray-800">

<div class="flex h-screen overflow-hidden">
  <?php require 'nav.php'; ?>

  <main class="flex-1 overflow-y-auto p-8 space-y-8 bg-gray-50">

    <section>
      <h1 class="text-3xl font-bold text-gray-800 mb-1">Teams Management</h1>
      <p class="text-gray-500">Create and manage your teams below.</p>
    </section>

    <?php if ($message) : ?>
      <div class="p-4 bg-green-100 border border-green-400 text-green-800 rounded">
        <?= htmlspecialchars($message) ?>
      </div>
    <?php endif; ?>

    <section class="bg-white p-6 rounded-xl shadow">
      <div class="flex justify-between items-center mb-4">
        <h2 class="text-xl font-semibold">Add New Team</h2>
      </div>
     <form method="POST" action="./server/backend/teams.php" class="flex gap-4 items-center" novalidate>
  <input type="text" name="team_name_created" required placeholder="Team Name"
         class="flex-1 p-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600">

  <!-- CSRF protection token -->
  <input type="hidden" name="code" value="<?= isset($_SESSION['code']) ? Secure::encryption($_SESSION['code']) : '' ?>">

  <button type="submit" name="add_team"
          class="bg-blue-600 text-white px-5 py-2 rounded hover:bg-blue-700 transition">
    Add Team
  </button>
</form>

    </section>

    <!-- Teams Table -->
    <section class="bg-white p-6 rounded-xl shadow">
      <h2 class="text-xl font-semibold mb-4">Existing Teams</h2>
      <div class="overflow-x-auto">
        <table class="min-w-full text-sm text-left">
          <thead class="bg-gray-100 text-gray-700 uppercase tracking-wide">
            <tr>
              <th class="py-3 px-4">Team Name</th>
              <th class="py-3 px-4">Members</th>
              <th class="py-3 px-4">Tasks</th>
              <th class="py-3 px-4">Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-200">
            <?php foreach ($teams as $team) : ?>
              <tr class="hover:bg-gray-50 transition">
                <td class="py-3 px-4 font-medium"><?= htmlspecialchars($team['name']) ?></td>
                <td class="py-3 px-4"><?= (int)$team['members'] ?></td>
                <td class="py-3 px-4"><?=selectTaskCount($team["name"]) ?></td>
                <td class="py-3 px-4 flex gap-2 flex-wrap">
                  <button
                    btnEdit="<?= htmlspecialchars($team["name"]) ?>"
                    btnID="<?= (int)$team["id"] ?>"
                    type="button"
                    class="px-4 py-2 text-sm bg-gray-200 hover:bg-gray-300 rounded transition"
                  >Edit</button>

                  <a href="?teams&group=<?= htmlspecialchars(urlencode($team['name'])) ?>">
                    <button type="button"
                            class="px-4 py-2 text-sm bg-blue-600 text-white hover:bg-blue-700 rounded transition">
                      Manage Members
                    </button>
                  </a>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </section>


  </main>
 <!-- Edit Team Modal -->
<div id="editModal" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 hidden z-50">
  <div class="bg-white rounded-xl shadow-lg w-96 max-w-full p-6 relative">
    <h2 class="text-2xl font-semibold mb-4">Edit Team</h2>
    <form id="editTeamForm" method="POST" class="flex flex-col gap-4">
      <input
        id="editTeamName"
        name="team_name_edited"
        type="text"
        required
        placeholder="Team Name"
        class="p-3 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-600"
      />
      <input type="hidden" name="team_id" id="editTeamId" />
    <input type="hidden" 
       value="<?= isset($_SESSION['code']) ? Secure::encryption($_SESSION['code']) : '' ?>" 
       name="code" 
       id="editTeamId" />


      <div class="flex justify-between items-center gap-2">
        <button
          type="submit"
          name="edit_team"
          id = "save"
          class="flex-1 bg-blue-600 text-white px-5 py-2 rounded hover:bg-blue-700 transition"
        >
          Save
        </button>

        <button
          type="button"
          onclick="closeEditModal()"
          class="flex-1 text-gray-700 px-5 py-2 rounded border border-gray-400 hover:bg-gray-200 transition"
        >
          Cancel
        </button>

        <button
          type="submit"
          name="delete_team"
          id = "delete"
          class="flex-1 bg-red-600 text-white px-5 py-2 rounded hover:bg-red-700 transition"
        >
          Delete
        </button>
      </div>
    </form>
  </div>
</div>


</div>


<script src="./client/teams.js"></script>
</body>
