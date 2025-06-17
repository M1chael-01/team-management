const modal = document.getElementById('editUserModal');

  function openEditUserModal(id, name, email, role) {
   
    modal.classList.remove('hidden');
    modal.setAttribute('open', 'true');
    document.getElementById('editUserId').value = id;
    document.getElementById('editUserName').value = name;
    document.getElementById('editUserEmail').value = email;
    document.getElementById('editUserRole').value = role;

   
  }

  function closeEditModal() {
    modal.classList.add('hidden');
    modal.removeAttribute('open');
  }

  // Close modal on Escape key
  window.addEventListener('keydown', e => {
    if (e.key === 'Escape') closeEditModal();
  });

  // Close modal on outside click
  window.addEventListener('click', e => {
    if (e.target === modal) closeEditModal();
  });

  function generatePassword(length = 10) {
    const chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()";
    let password = "";
    for (let i = 0; i < length; i++) {
      password += chars.charAt(Math.floor(Math.random() * chars.length));
    }
    document.getElementById("passwordField").value = password;
  }

