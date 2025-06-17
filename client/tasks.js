
let id;
document.querySelectorAll(".editBTN").forEach((button) => {
  button.addEventListener("click", () => {
    let tr = button.closest("tr");

    // Get task details from the table row
    let taskId = tr.dataset.taskId;
    let teamId = tr.dataset.teamId;
    let title = tr.querySelector(".title").textContent.trim();
    let rawStatus = tr.querySelector(".state").textContent.trim();


    id = taskId;
    // Map emoji status to actual status string
    let statusMap = {
      "🟡 To Do": "To Do",
      "🔵 In Progress": "In Progress",
      "✅ Done": "Done"
    };
    let statusValue = statusMap[rawStatus] ?? rawStatus;

    // Populate the modal with task details
    document.getElementById("editTaskId").value = taskId;
    document.getElementById("editTaskTitle").value = title;
    document.getElementById("editTaskStatus").value = statusValue;
    document.getElementById("editTaskTeam").value = teamId;

    // Show the modal
    document.getElementById("editTaskModal").classList.remove("hidden");
  });
});

// Delete Task Function
function deleteTask() {
  const taskId = document.getElementById("editTaskId").value;
  
  if (confirm("Are you sure you want to delete this task?")) {
    // Create the form data to send
    const formData = new FormData();
    formData.append("task_id", id);
   alert(id)

    // Send request to delete the task
    fetch('./server/backend/deleteTask.php', {
      method: 'POST',
      body: formData
    })
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        // Successfully deleted task, remove from table
        const taskRow = document.querySelector(`tr[data-task-id="${taskId}"]`);
        if (taskRow) taskRow.remove();
        alert("Task deleted successfully!");

        // Close the modal
        document.getElementById("editTaskModal").classList.add("hidden");
      } else {
        alert("Error deleting task: " + data.error);
      }
    })
    .catch(error => {
      console.error("Error:", error);
      alert("Failed to delete task!");
    });
  }
}
