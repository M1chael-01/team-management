<?php

require_once "./server/database/teams.php";
require_once "./server/database/users.php";
require_once "./server/backend/EncryptionDecription.php";

function sanitize(string $value): string {
    return htmlspecialchars(trim($value), ENT_QUOTES, 'UTF-8');
}

$team = [];
$teamName = '';
$teamID = null;
$message = '';

if (isset($_GET['group'])) {
    $targetTeam = $_GET['group'];
    $conn = teams();

    $result = mysqli_query($conn, "SELECT * FROM team");
    while ($row = mysqli_fetch_assoc($result)) {
        $decryptedName = Secure::decryption($row['teamName']);
        if ($decryptedName === $targetTeam) {
            $teamName = $decryptedName;
            $teamID = $row['id'];
            $team = json_decode($row['members'], true) ?? [];
            break;
        }
    }

    if (!$teamName) {
        die("Team not found.");
    }
    $_SESSION["edit_team_id"] = $teamID;
}

// Step 2: Handle POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $conn = teams();
    $teamID = $_SESSION["edit_team_id"] ?? null;

    if (!$teamID) {
        $message = "No team selected.";
    } else {
        // Add member
        if (isset($_POST['add_member']) && isset($_GET["group"])) {
            $name = sanitize($_POST['name'] ?? '');
            $teamName = sanitize($_GET['group']);
            $role = 'user'; // Default role

            if ($name && $teamName && $role) {
                // Add new member in format: [name, teamName, role]
                $team[] = [$name, $teamName, $role];
                $message = "Added member '$name' to team '$teamName' with role '$role'.";
            } else {
                $message = "All fields are required.";
            }
        }

        // Edit member
        if (isset($_POST['edit_member'])) {
            $id = (int) ($_POST['member_id'] ?? -1);
            $name = sanitize($_POST['name'] ?? '');
            $role = sanitize($_POST['role'] ?? '');

            if (isset($team[$id])) {
                $team[$id][0] = $name;
                //structure is [name, teamName, role]
                $team[$id][2] = $role;
                $message = "Updated member '$name'.";
            }
        }

        // Delete member
        if (isset($_POST['delete_member'])) {
            $id = (int) ($_POST['member_id'] ?? -1);
            if (isset($team[$id])) {
                $deletedName = $team[$id][0];
                unset($team[$id]);
                $team = array_values($team); // Reindex array
                $message = "Deleted member '$deletedName'.";
            }
        }

        // Save updated team to DB
        $json = json_encode($team);
        $stmt = mysqli_prepare($conn, "UPDATE team SET members = ? WHERE id = ?");
        mysqli_stmt_bind_param($stmt, "si", $json, $teamID);
        mysqli_stmt_execute($stmt);
    }
}


// Load available users and teams
function selectNames($id) {
    $con = users();
    $sql = "SELECT name FROM user WHERE acountID = ? AND role = ?";
    $stmt = mysqli_prepare($con, $sql);
    $names = [];

    if ($stmt) {
        $role = "user";
        mysqli_stmt_bind_param($stmt, "is", $id, $role);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        if ($result) {
            while ($row = mysqli_fetch_assoc($result)) {
                $names[] = Secure::decryption($row['name']);
            }
        }
        mysqli_stmt_close($stmt);
    }

    return $names;
}

function selectTeams($id) {
    $conn = teams();
    $teams = [];
    $sql = "SELECT teamName FROM team WHERE acountID = ?";
    $stmt = mysqli_prepare($conn, $sql);

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        if ($result) {
            while ($row = mysqli_fetch_assoc($result)) {
                $teams[] = Secure::decryption($row['teamName']);
            }
        }
        mysqli_stmt_close($stmt);
    }

    return $teams;
}

$data = $_SESSION["id"] ? selectNames($_SESSION["id"]) : [];
$teams = $_SESSION["id"] ? selectTeams($_SESSION["id"]) : [];
?>



<div class="flex h-screen">
    <?php require 'nav.php'; ?>
    <main class="flex-1 p-8">
        <h1 class="text-3xl font-bold mb-6">Manage Team: <?= htmlspecialchars($teamName) ?></h1>

        <?php if ($message): ?>
            <div class="mb-4 p-4 bg-green-100 text-green-800 rounded">
                <?= $message ?>
            </div>
        <?php endif; ?>

        <!-- Add Member -->
        <!-- Add Member -->
<!-- Add Member -->
<section class="bg-white p-6 rounded shadow mb-8">
    <h2 class="text-2xl font-semibold mb-4">Add Team Member</h2>
    <form method="POST" class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <!-- Name dropdown -->
        <select name="name" required class="w-full p-2 border border-gray-300 rounded-md">
            <option value="" disabled selected>Select a user</option>
            <?php foreach ($data as $user): ?>
                <option value="<?= htmlspecialchars($user) ?>"><?= htmlspecialchars($user) ?></option>
            <?php endforeach; ?>
        </select>

       

        <button type="submit" name="add_member" class="bg-blue-600 text-white p-3 rounded hover:bg-blue-700">
            Add Member
        </button>
    </form>
</section>

<!-- Members Table -->
<section class="bg-white p-8 rounded-lg shadow-md">
    <h2 class="text-3xl font-bold mb-6 text-gray-800">Team Members</h2>

    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 border rounded-lg overflow-hidden">
            <thead class="bg-gray-100 text-gray-700 text-md uppercase">
                <tr>
                    <th class="px-6 py-4 text-left">Name</th>
                    <th class="px-6 py-4 text-left">Role</th>
                    <th class="px-6 py-4 text-center">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-100">
                <?php if (!empty($team)): ?>
                    <?php foreach ($team as $index => $member): ?>
                        <tr class="hover:bg-gray-50 transition">
                            <form method="POST" class="contents">
                                <input type="hidden" name="member_id" value="<?= $index ?>">

                                <!-- Name Field -->
                                <td class="px-6 py-4">
                                    <input type="text" name="name" value="<?= htmlspecialchars($member[0] ?? '') ?>"
                                           class="w-full p-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-400"
                                           required>
                                </td>

                                <!-- Role Field -->
                                <td class="px-6 py-4">
                                    <input type="text" name="role" value="<?= htmlspecialchars($member[1] ?? '') ?>"
                                           class="w-full p-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-400"
                                           required>
                                </td>

                                <!-- Action Buttons -->
                                <td class="px-6 py-4 flex items-center justify-center gap-4">
                                    <!-- Save Button -->
                                    <button type="submit" name="edit_member"
                                            class="bg-yellow-500 hover:bg-yellow-600 text-white font-semibold px-6 py-3 rounded-lg shadow transition w-32 text-center">
                                        Save
                                    </button>
                            </form>

                            <!-- Delete Button -->
                            <form method="POST" onsubmit="return confirm('Delete this team member?')">
                                <input type="hidden" name="member_id" value="<?= $index ?>">
                                <button type="submit" name="delete_member"
                                        class="bg-red-600 hover:bg-red-700 text-white font-semibold px-6 py-3 rounded-lg shadow transition w-32 text-center">
                                    Delete
                                </button>
                            </form>
                                </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="3" class="text-center px-6 py-6 text-gray-500 text-lg">No members found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>

</div>
