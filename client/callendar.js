 function openModal(dateStr) {
    editingEventIndex = null;
    document.getElementById('eventDate').value = dateStr;
    document.getElementById('eventName').value = "";
    document.getElementById('eventStart').value = "";
    document.getElementById('eventEnd').value = "";
    document.getElementById('eventId').value = "";
    document.getElementById('modalTitle').textContent = `Add Event - ${dateStr}`;

    document.getElementById('deleteBtn').classList.add("hidden");
    document.getElementById('updateBtn').classList.add("hidden");
    document.getElementById('saveBtn').classList.remove("hidden");

    document.getElementById('eventModal').classList.remove("hidden");
  }

  function closeModal() {
    document.getElementById('eventModal').classList.add("hidden");
  }

  function editEvent(date, index) {
    const evt = events[date][index];
    editingEventIndex = index;
    
   

    document.getElementById('eventDate').value = date;
    document.getElementById('eventName').value = evt.title;
    document.getElementById('eventStart').value = evt.start || "";
    document.getElementById('eventEnd').value = evt.end || "";
    document.getElementById('eventId').value = evt.id || "";
    document.getElementById('modalTitle').textContent = `Edit Event - ${date}`;

    document.getElementById('deleteBtn').classList.remove("hidden");
    document.getElementById('updateBtn').classList.remove("hidden");
    document.getElementById('saveBtn').classList.add("hidden");

    document.getElementById('eventModal').classList.remove("hidden");
  }
