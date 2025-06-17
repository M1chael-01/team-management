<?php
require "./server/database/users.php";
require  "./server/backend/EncryptionDecription.php";

$conn = users();

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


function loadUser() {
  global $conn;
  $userId = $_SESSION["id"];
     $sql = "SELECT id, name, email, role FROM user WHERE acountID = ?";
    $stmt = mysqli_prepare($conn, $sql);
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "i", $userId);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        return mysqli_fetch_all($result,MYSQLI_ASSOC);
       
    }
    return [];

}

if (isset($_SESSION["id"])) {
    $users = loadUser();
}

$message = '';

function sanitize(string $data): string {
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}
function isValidEmail(string $email): bool {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_user'])) {
        $name = sanitize($_POST['name'] ?? '');
        $email = sanitize($_POST['email'] ?? '');
        $role = sanitize($_POST['role'] ?? '');

        if (!$name || !$email || !$role) {
            $message = 'All fields are required to add a user.';
        } elseif (!isValidEmail($email)) {
            $message = 'Invalid email format.';
        } else {
            $users[] = ['id' => count($users) + 1, 'name' => $name, 'email' => $email, 'role' => $role];
            $message = "User '$name' added successfully!";
        }
    }

    if (isset($_POST['edit_user'])) {
        $id = intval($_POST['user_id'] ?? 0);
        $name = sanitize($_POST['name'] ?? '');
        $email = sanitize($_POST['email'] ?? '');
        $role = sanitize($_POST['role'] ?? '');

        foreach ($users as &$user) {
            if ($user['id'] === $id) {
                $user['name'] = $name;
                $user['email'] = $email;
                $user['role'] = $role;
                $message = "User '$name' updated successfully.";
                break;
            }
        }
        unset($user);
    }

    if (isset($_POST['delete_user'])) {
        $id = intval($_POST['user_id'] ?? 0);
        foreach ($users as $key => $user) {
            if ($user['id'] === $id) {
                $message = "User '{$user['name']}' deleted.";
                unset($users[$key]);
                $users = array_values($users);
                break;
            }
        }
    }
}
?>
</head>
<body class="bg-gray-100 text-gray-800 ">
  <div class="flex h-screen overflow-hidden">
   <?php require 'nav.php'; ?>

  <?php if ($message): ?>
    
    <div class="p-4 rounded-lg bg-green-100 border border-green-400 text-green-800 font-medium mb-10 shadow">
      <?= htmlspecialchars($message) ?>
    </div>
  <?php endif; ?>

  <main>

  <!-- Add New User -->
  <section class="bg-white p-6 rounded-xl shadow mb-10">
    <h2 class="text-2xl font-semibold mb-4">Add New User</h2>
    <form method="POST" action="./server/backend/users.php?add=<?= urlencode(Secure::encryption($_SESSION["code"])) ?>" class="flex flex-wrap gap-4 items-end">
      <input
        type="text"
        name="name"
        placeholder="Name"
        required
        class="flex-1 min-w-[150px] p-3 border rounded focus:outline-none focus:ring-2"
      />
      <input
        type="email"
        name="email"
        placeholder="Email"
        required
        class="flex-1 min-w-[200px] p-3 border rounded focus:outline-none focus:ring-2"
      />
      <input
        type="text"
        id="passwordField"
        name="password"
        placeholder="Password"
        required
        readonly
        class="flex-1 min-w-[150px] p-3 border rounded focus:outline-none focus:ring-2"
      />
      <button
        type="button"
        onclick="generatePassword()"
        class="bg-gray-200 text-gray-800 px-4 py-2 rounded hover:bg-gray-300 transition"
      >
        🔐 Generate
      </button>
      <button
        type="submit"
        name="add_user"
        class="bg-blue-600 text-white px-5 py-3 rounded hover:bg-blue-700 transition"
      >
        Add User
      </button>
    </form>
  </section>

  <!-- User List Table -->
  <section class="bg-white p-6 rounded-xl shadow">
    <h2 class="text-2xl font-semibold mb-4">User List</h2>
    <div class="overflow-x-auto">
      <table class="min-w-full text-sm text-left">
        <thead class="bg-gray-100 text-gray-700 uppercase tracking-wide">
          <tr>
            <th>Name</th>
            <th>Email</th>
            <th>Role</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
          <?php foreach ($users as $user): ?>
            <tr>
              <td><?= htmlspecialchars(Secure::decryption($user['name'])) ?></td>
              <td><?= htmlspecialchars(Secure::decryption($user['email'])) ?></td>
              <td><?= htmlspecialchars($user['role']) ?></td>
              <td class="flex gap-2">
                <button
                  onclick="openEditUserModal(<?= htmlspecialchars(json_encode($user['id'])) ?>, <?= htmlspecialchars(json_encode(Secure::decryption($user['name']))) ?>, <?= htmlspecialchars(json_encode(Secure::decryption($user['email']))) ?>, <?= htmlspecialchars(json_encode($user['role'])) ?>)"
                  class="px-3 py-1 text-sm bg-blue-600 text-white rounded hover:bg-blue-700 transition"
                >
                  Edit
                </button>
                <form method="POST" action = "./server/backend/users.php?delete=<?= urlencode(Secure::encryption($_SESSION["code"])) ?>" onsubmit="return confirm('Are you sure you want to delete <?= htmlspecialchars(Secure::decryption($user['name'])) ?>?');">
                  <input type="hidden" name="user_id" value="<?= $user['id'] ?>" />
                  <button type="submit" name="delete_user" class="px-3 py-1 text-sm bg-red-600 text-white rounded hover:bg-red-700 transition">
                    Delete
                  </button>
                </form>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </section>

  <!-- Edit User Modal -->
  <dialog id="editUserModal" class="bg-white rounded-xl p-6 w-full max-w-md shadow hidden">
    <form method="POST" action = "./server/backend/users.php?edit=<?= urlencode(Secure::encryption($_SESSION["code"])) ?>" class="space-y-4">
      <h3 class="text-2xl font-semibold border-b pb-2">Edit User</h3>
      <input type="hidden" name="user_id" id="editUserId" />
      <input
        type="text"
        id="editUserName"
        name="name"
        required
        class="w-full p-3 border rounded focus:ring-2 focus:ring-blue-600"
        placeholder="Name"
      />
      <input
        type="email"
        id="editUserEmail"
        name="email"
        required
        class="w-full p-3 border rounded focus:ring-2 focus:ring-blue-600"
        placeholder="Email"
      />
      <select
        name="role"
        id="editUserRole"
        required
        class="w-full p-3 border rounded focus:ring-2 focus:ring-blue-600"
      >
        <option value="">Select Role</option>
        <option>admin</option>
        <option>user</option>
      </select>
      <div class="flex justify-between gap-4 pt-2">
        <button
          name="edit_user"
          type="submit"
          class="flex-1 bg-blue-600 text-white py-2 rounded hover:bg-blue-700 transition"
        >
          Save
        </button>
        <button
          type="button"
          onclick="closeEditModal()"
          class="flex-1 border border-red-600 text-red-600 py-2 rounded hover:bg-red-600 hover:text-white transition"
        >
          Cancel
        </button>
      </div>
    </form>
  </dialog>
          </main>

</div>

<script src = "./client/users.js"></script>
</body>
</html>
