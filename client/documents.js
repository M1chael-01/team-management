function openEditFolderModal(id, name) {
    const modal = document.getElementById('editFolderModal');
    modal.showModal();
    document.getElementById('editFolderId').value = id;
    document.getElementById('editFolderName').value = name;
    document.getElementById('editFolderName').focus();
  }

  function openEditDocModal(id, name) {
    const modal = document.getElementById('editDocModal');
    modal.showModal();
    document.getElementById('editDocId').value = id;
    document.getElementById('editDocName').value = name;
    document.getElementById('editDocName').focus();
  }

  function closeModal(id) {
    document.getElementById(id).close();
  }

  ['editFolderModal', 'editDocModal'].forEach(id => {
    const dialog = document.getElementById(id);
    dialog.addEventListener('keydown', (e) => {
      if (e.key === 'Escape') {
        e.preventDefault();
        dialog.close();
      }
    });
  });