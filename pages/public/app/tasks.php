<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require "./server/database/tasks.php";
require "./server/database/teams.php";
require "./server/backend/EncryptionDecription.php";

function loadTeams() {
    $connection = teams();  

    $id = $_SESSION["id"];   

    $sql = "SELECT id, teamName FROM team WHERE acountID = ?";

    if ($stmt = $connection->prepare($sql)) {
        $stmt->bind_param("i", $id);

        if ($stmt->execute()) {
            $result = $stmt->get_result();
            $teams = [];

            while ($row = $result->fetch_assoc()) {
                $teams[] = [
                    'id' => $row['id'],
                    'name' => Secure::decryption($row['teamName'])
                ];
            }

            return !empty($teams) ? $teams : [];
        } else {
            return [];
        }
    } else {
        return [];
    }
}

function loadData() {
    $connection = tasks();  
    $id = $_SESSION["id"];  

    // Query to select tasks
    $sql = "SELECT id, title, status, team, teamID FROM task WHERE acountID = ?";

    if ($stmt = $connection->prepare($sql)) {  
        $stmt->bind_param("i", $id); 

        if ($stmt->execute()) {
            $result = $stmt->get_result();
            $tasks = [];

            while ($row = $result->fetch_assoc()) {
                $tasks[] = [
                    'id' => $row['id'],
                    'title' => Secure::decryption($row['title']),
                    'status' => Secure::decryption($row['status']),
                    'team' => Secure::decryption($row['team']),
                    'teamID' => $row['teamID'] 
                ];
            }
            return !empty($tasks) ? $tasks : [];
        }
    }
    return [];
}


if (isset($_SESSION["id"])) {
    $tasks = loadData();
    $teams = loadTeams();
}

function getTeamName($id) {
  if(isset($_SESSION["id"])) {
  $connection = teams();  
  $sql = "SELECT teamName AS name FROM team WHERE id = ? AND acountID = ?";
  $stmt = mysqli_prepare($connection,$sql);
  if($stmt) {
     mysqli_stmt_bind_param($stmt,"ii" , $id,$_SESSION["id"]);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if($row =mysqli_fetch_assoc($result)) {
      return Secure::decryption($row["name"]);
    }
    else{
      return $id;
    }
    
  }
}
else session_destroy();
}
?>
<body class="bg-gray-100 text-gray-800">
  <div class="flex h-screen overflow-hidden">
    <?php require 'nav.php'; ?>

    <main class="flex-1 overflow-y-auto p-8 space-y-8 bg-gray-50">
      <!-- Page Header -->
      <section>
        <h1 class="text-3xl font-bold text-gray-800 mb-1">Tasks Management</h1>
        <p class="text-gray-500">Create and manage your tasks below.</p>
      </section>

      <!-- Add Task -->
      <section class="bg-white p-6 rounded-xl shadow">
        <div class="flex justify-between items-center mb-4">
          <h2 class="text-xl font-semibold">Add New Task</h2>
        </div>
        <form method="POST" action="./server/backend/addTasks.php" class="flex gap-4 items-center">
          <input required type="text" name="task_title" required placeholder="Task Title"
                 class="flex-1 p-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600" />

          <select name="task_status" required
            class="p-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600">
            <option value="" disabled selected>Select Status</option>
            <option value="To Do">🟡 To Do</option>
            <option value="In Progress">🔵 In Progress</option>
         <!--    -->
          </select>

          <select name="task_team" required
            class="p-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600">
            <option value="" disabled selected>Select Team</option>
            <?php foreach ($teams as $team) : ?>
              <option value="<?= $team['id'] . '-' . htmlspecialchars($team['name']) ?>">
                <?= htmlspecialchars($team['name']) ?>
              </option>
            <?php endforeach; ?>
          </select>

          <button type="submit" name="add_task"
                  class="bg-blue-600 text-white px-5 py-2 rounded hover:bg-blue-700 transition">
            Add Task
          </button>
        </form>
      </section>

      <!-- Existing Tasks Table -->
      <section class="bg-white p-6 rounded-xl shadow">
        <h2 class="text-xl font-semibold mb-4">Existing Tasks</h2>
        <div class="overflow-x-auto">
          <table class="min-w-full text-sm text-left">
            <thead class="bg-gray-100 text-gray-700 uppercase tracking-wide">
              <tr>
                <th class="py-3 px-4">Title</th>
                <th class="py-3 px-4">Status</th>
                <th class="py-3 px-4">Team</th>
                <th class="py-3 px-4">Actions</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
              <?php foreach ($tasks as $task) : ?>
  <tr data-task-id="<?= $task['id'] ?>" data-team-id="<?= $task['teamID'] ?>">
    <td class="title py-3 px-4 font-medium"><?= htmlspecialchars($task['title']) ?></td>
    <td class="state py-3 px-4">
      <?php
        echo match ($task['status']) {
          'To Do' => '🟡 To Do',
          'In Progress' => '🔵 In Progress',
          'Done' => '✅ Done',
          default => htmlspecialchars($task['status'])
        };
      ?>
    </td>
    <td class="team py-3 px-4"><?= getTeamName(htmlspecialchars($task['teamID'])) ?></td>
    <td class="py-3 px-4 flex gap-2 flex-wrap">
      <button
        data-task-id="<?= $task['id'] ?>"
        type="button"
        class="px-4 py-2 text-sm bg-gray-200 hover:bg-gray-300 rounded transition editBTN"
      >Edit</button>
    </td>
  </tr>
<?php endforeach; ?>
        
            </tbody>
          </table>
        </div>
      </section>

     <!-- Edit Task Modal -->
<div id="editTaskModal" class="fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center p-4 hidden z-50">
  <div class="bg-white rounded-xl shadow-lg p-6 w-full max-w-md relative">
    <h3 class="text-2xl font-semibold mb-4 text-gray-800 border-b pb-2">Edit Task</h3>

    <form method="POST" id="editTaskForm" action="./server/backend/editTasks.php" class="space-y-4">
      <input type="hidden" name="task_id" id="editTaskId" />

      <input type="text" name="task_title" id="editTaskTitle" required placeholder="Task Title"
             class="w-full p-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600" />

      <select name="task_status" id="editTaskStatus" required
              class="w-full p-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600">
        <option value="To Do">🟡 To Do</option>
        <option value="In Progress">🔵 In Progress</option>
        <option value="Done">✅ Done</option>
      </select>

      <select name="task_team" id="editTaskTeam" required
              class="w-full p-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600">
        <?php foreach ($teams as $team) : ?>
          <option value="<?= $team['id'] ?>"><?= htmlspecialchars($team['name']) ?></option>
        <?php endforeach; ?>
      </select>

      <div class="flex justify-between gap-4">
        <button name="edit_task" type="submit"
                class="flex-1 px-5 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition">
          Save
        </button>
        <button type="button" id="deleteButton" onclick="deleteTask()"
                class="flex-1 px-5 py-2 bg-red-600 text-white rounded hover:bg-red-700 transition">
          Delete
        </button>
      </div>
    </form>

    <button type="button" onclick="document.getElementById('editTaskModal').classList.add('hidden')"
            class="absolute top-4 right-4 text-2xl font-bold text-gray-500 hover:text-red-600">✖</button>
  </div>
</div>

    </main>
  </div>

  <script src="./client/tasks.js"></script>
</body>