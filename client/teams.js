
const form = document.querySelector("#editTeamForm"),
saveBtn = document.querySelector("#save"),
deleteBtn = document.querySelector("#delete");

saveBtn.addEventListener("click" , saveTeam);
deleteBtn.addEventListener("click" , deleteTeam);


function closeEditModal() {
  document.getElementById('editModal').classList.add('hidden');
}

document.querySelectorAll('button[btnEdit]').forEach(button => {
  button.addEventListener('click', () => {
    const modal = document.getElementById('editModal');
    modal.classList.remove('hidden');

    const teamName = button.getAttribute('btnEdit');
    const teamId = button.getAttribute('btnID');

    document.getElementById('editTeamName').value = teamName;
    document.getElementById('editTeamId').value = teamId;

    // Focus input for convenience
    document.getElementById('editTeamName').focus();
  });
});

function saveTeam() {
form.action = "./server/backend/teams.php?save";
}

function deleteTeam() {
  let q = confirm("are u sure");
  if(q) {
   form.action = "./server/backend/teams.php?delete";
  }
}
