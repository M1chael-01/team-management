

<?php

// Dummy data (replace with DB for production)
$folders = [
    ['id' => 1, 'name' => 'Project Docs'],
    ['id' => 2, 'name' => 'Invoices'],
];
$documents = [
    ['id' => 1, 'folder_id' => 1, 'name' => 'DesignBrief.pdf'],
    ['id' => 2, 'folder_id' => 1, 'name' => 'Specs.docx'],
    ['id' => 3, 'folder_id' => 2, 'name' => 'Invoice_Jan.pdf'],
];

$message = '';
function sanitize($input) {
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Using a helper function to update arrays
    if (!empty($_POST['add_folder'])) {
        $folderName = sanitize($_POST['folder_name'] ?? '');
        if ($folderName !== '') {
            $folders[] = ['id' => count($folders) + 1, 'name' => $folderName];
            $message = "Folder '$folderName' created!";
        }
    }

    if (!empty($_POST['upload_doc'])) {
        $docName = sanitize($_POST['doc_name'] ?? '');
        $folderId = intval($_POST['folder_id'] ?? 0);
        if ($docName !== '' && $folderId > 0) {
            $documents[] = ['id' => count($documents) + 1, 'folder_id' => $folderId, 'name' => $docName];
            $message = "Document '$docName' uploaded!";
        }
    }

    if (!empty($_POST['edit_folder'])) {
        $editFolderId = intval($_POST['folder_id'] ?? 0);
        $newFolderName = sanitize($_POST['folder_name'] ?? '');
        foreach ($folders as &$folder) {
            if ($folder['id'] === $editFolderId && $newFolderName !== '') {
                $folder['name'] = $newFolderName;
                $message = "Folder renamed to '$newFolderName'.";
                break;
            }
        }
        unset($folder);
    }

    if (!empty($_POST['delete_folder'])) {
        $delFolderId = intval($_POST['folder_id'] ?? 0);
        if ($delFolderId > 0) {
            $folders = array_filter($folders, fn($f) => $f['id'] !== $delFolderId);
            $documents = array_filter($documents, fn($d) => $d['folder_id'] !== $delFolderId);
            $message = "Folder and its documents deleted!";
        }
    }

    if (!empty($_POST['edit_doc'])) {
        $editDocId = intval($_POST['doc_id'] ?? 0);
        $newDocName = sanitize($_POST['doc_name'] ?? '');
        foreach ($documents as &$doc) {
            if ($doc['id'] === $editDocId && $newDocName !== '') {
                $doc['name'] = $newDocName;
                $message = "Document renamed to '$newDocName'.";
                break;
            }
        }
        unset($doc);
    }

    if (!empty($_POST['delete_doc'])) {
        $delDocId = intval($_POST['doc_id'] ?? 0);
        if ($delDocId > 0) {
            $documents = array_filter($documents, fn($d) => $d['id'] !== $delDocId);
            $message = "Document deleted!";
        }
    }
}

// Default to first folder if none selected
$currentFolderId = isset($_GET['folder']) ? intval($_GET['folder']) : ($folders[0]['id'] ?? null);
$currentFolderName = 'None';
foreach ($folders as $folder) {
    if ($folder['id'] === $currentFolderId) {
        $currentFolderName = $folder['name'];
        break;
    }
}

function isActiveFolder($folderId, $currentFolderId) {
    return $folderId === $currentFolderId ? 'text-yellow-500' : '';
}
?>

<body class="bg-gray-100 text-gray-800">

<div class="flex h-screen overflow-hidden">

  <?php require 'nav.php'; ?>

  <main class="flex-1 overflow-y-auto p-8 space-y-8 bg-gray-50">

    <h1 class="text-3xl font-bold mb-6">Documents Management</h1>

    <?php if ($message) : ?>
      <div
        role="alert"
        class="mb-6 p-4 bg-green-300 text-green-900 rounded shadow font-semibold"
      >
        <?= $message ?>
      </div>
    <?php endif; ?>

    <div class="flex gap-8">

      <!-- Folders Panel -->
      <section class="w-1/3 bg-white rounded-xl shadow p-6" aria-label="Folders panel">
        <h2 class="text-2xl font-semibold mb-4 border-b border-gray-300 pb-2">Folders</h2>

        <ul class="space-y-3">
          <?php foreach ($folders as $folder) : ?>
            <li class="flex items-center justify-between border border-gray-200 rounded-lg p-3 hover:shadow transition">
              <a
                href="?folder=<?= $folder['id'] ?>"
                class="font-semibold text-lg hover:underline <?= isActiveFolder($folder['id'], $currentFolderId) ?>"
                aria-current="<?= $folder['id'] === $currentFolderId ? 'page' : 'false' ?>"
              >
                <?= htmlspecialchars($folder['name']) ?>
              </a>

              <div class="flex gap-2">
                <button
                  type="button"
                  aria-label="Edit folder <?= htmlspecialchars($folder['name']) ?>"
                  onclick="openEditFolderModal(<?= $folder['id'] ?>, <?= json_encode($folder['name']) ?>)"
                  class="px-2 py-1 border border-gray-700 font-semibold rounded hover:bg-gray-700 hover:text-white transition"
                >
                  ✏️
                </button>
                <form
                  method="POST"
                  onsubmit="return confirm('Delete folder and all documents?');"
                  class="inline"
                >
                  <input type="hidden" name="folder_id" value="<?= $folder['id'] ?>" />
                  <button
                    type="submit"
                    name="delete_folder"
                    aria-label="Delete folder <?= htmlspecialchars($folder['name']) ?>"
                    class="px-2 py-1 border border-red-600 text-red-600 font-semibold rounded hover:bg-red-600 hover:text-white transition"
                  >
                    🗑️
                  </button>
                </form>
              </div>
            </li>
          <?php endforeach; ?>
        </ul>

        <form method="POST" class="mt-6 flex gap-2" aria-label="Add new folder form">
          <input
            type="text"
            name="folder_name"
            placeholder="New Folder Name"
            required
            aria-required="true"
            class="flex-1 p-3 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-600"
          />
          <button
            type="submit"
            name="add_folder"
            class="px-6 py-3 bg-blue-600 text-white font-semibold rounded hover:bg-blue-700 transition"
          >
            Add
          </button>
        </form>
      </section>

      <!-- Documents Panel -->
      <section class="flex-1 bg-white rounded-xl shadow p-6" aria-label="Documents panel">
        <h2 class="text-2xl font-semibold mb-4 border-b border-gray-300 pb-2">
          Documents in: <?= htmlspecialchars($currentFolderName) ?>
        </h2>

        <table class="w-full border-collapse text-left text-sm font-medium" role="table" aria-describedby="docTableDesc">
          <caption id="docTableDesc" class="sr-only">List of documents in current folder</caption>
          <thead class="bg-gray-200 uppercase tracking-wide text-gray-700">
            <tr>
              <th class="py-3 px-6 border-b border-gray-300" scope="col">Document Name</th>
              <th class="py-3 px-6 border-b border-gray-300">Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php
            $docsInFolder = array_filter($documents, fn($d) => $d['folder_id'] === $currentFolderId);
            if (count($docsInFolder) === 0) :
            ?>
              <tr>
                <td colspan="2" class="py-6 px-6 text-center text-gray-500">No documents here</td>
              </tr>
            <?php else: ?>
              <?php foreach ($docsInFolder as $doc) : ?>
                <tr class="hover:bg-yellow-100 transition cursor-pointer">
                  <td class="py-3 px-6 border-b border-gray-300"><?= htmlspecialchars($doc['name']) ?></td>
                  <td class="py-3 px-6 border-b border-gray-300 flex gap-2">
                    <button
                      type="button"
                      aria-label="Edit document <?= htmlspecialchars($doc['name']) ?>"
                      onclick="openEditDocModal(<?= $doc['id'] ?>, <?= json_encode($doc['name']) ?>)"
                      class="px-3 py-1 bg-blue-600 text-white rounded hover:bg-blue-700 transition"
                    >
                      ✏️ Edit
                    </button>

                    <form
                      method="POST"
                      onsubmit="return confirm('Delete document?');"
                      class="inline"
                    >
                      <input type="hidden" name="doc_id" value="<?= $doc['id'] ?>" />
                      <button
                        type="submit"
                        name="delete_doc"
                        aria-label="Delete document <?= htmlspecialchars($doc['name']) ?>"
                        class="px-3 py-1 bg-red-600 text-white rounded hover:bg-red-700 transition"
                      >
                        🗑️ Delete
                      </button>
                    </form>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php endif; ?>
          </tbody>
        </table>

        <form method="POST" class="mt-6 flex gap-2 items-center" aria-label="Upload document form">
          <input type="hidden" name="folder_id" value="<?= $currentFolderId ?>" />
          <input
            type="text"
            name="doc_name"
            placeholder="Document Name"
            required
            aria-required="true"
            class="flex-grow p-3 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-600"
          />
          <button
            type="submit"
            name="upload_doc"
            class="px-6 py-3 bg-blue-600 text-white rounded hover:bg-blue-700 transition"
          >
            Upload
          </button>
        </form>
      </section>
    </div>
  </main>

  <!-- Edit Folder Modal -->
  <dialog id="editFolderModal" class="p-0 rounded-xl w-96" aria-modal="true" role="dialog" aria-labelledby="editFolderTitle" style="border: 4px solid black;">
    <form method="POST" class="flex flex-col gap-6 p-8">
      <h2 id="editFolderTitle" class="text-2xl font-semibold border-b border-gray-300 pb-3 mb-4">Edit Folder Name</h2>
      <input type="hidden" name="folder_id" id="editFolderId" />
      <input
        id="editFolderName"
        name="folder_name"
        type="text"
        required
        aria-required="true"
        class="p-3 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-600"
      />
      <div class="flex justify-between">
        <button
          type="submit"
          name="edit_folder"
          class="px-6 py-3 bg-blue-600 text-white rounded hover:bg-blue-700 transition"
        >
          Save
        </button>
        <button
          type="button"
          class="px-6 py-3 border border-red-600 text-red-600 rounded hover:bg-red-600 hover:text-white transition"
          onclick="closeModal('editFolderModal')"
        >
          Cancel
        </button>
      </div>
    </form>
  </dialog>

  <!-- Edit Document Modal -->
  <dialog id="editDocModal" class="p-0 rounded-xl w-96" aria-modal="true" role="dialog" aria-labelledby="editDocTitle" style="border: 4px solid black;">
    <form method="POST" class="flex flex-col gap-6 p-8">
      <h2 id="editDocTitle" class="text-2xl font-semibold border-b border-gray-300 pb-3 mb-4">Edit Document Name</h2>
      <input type="hidden" name="doc_id" id="editDocId" />
      <input
        id="editDocName"
        name="doc_name"
        type="text"
        required
        aria-required="true"
        class="p-3 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-600"
      />
      <div class="flex justify-between">
        <button
          type="submit"
          name="edit_doc"
          class="px-6 py-3 bg-blue-600 text-white rounded hover:bg-blue-700 transition"
        >
          Save
        </button>
        <button
          type="button"
          class="px-6 py-3 border border-red-600 text-red-600 rounded hover:bg-red-600 hover:text-white transition"
          onclick="closeModal('editDocModal')"
        >
          Cancel
        </button>
      </div>
    </form>
  </dialog>

<script src = "./client/documents.js">  </script>

</div>

</body>
