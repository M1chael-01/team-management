<?php
require "./server/database/calendar.php";
require "./server/backend/EncryptionDecription.php";

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$connection = calendar();

$events = [];

if (isset($_SESSION["id"])) {
    $storedID = $_SESSION["id"];

    $sql = "SELECT id, eventTitle, date, timeStart, timeEnd FROM info WHERE acountID = ?";
    $stmt = mysqli_prepare($connection, $sql);

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "i", $storedID);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if ($result && mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                $date = $row['date'];
                if (!isset($events[$date])) {
                    $events[$date] = [];
                }
                $events[$date][] = [
                    "id"    => $row["id"],
                    "title" => Secure::decryption(htmlspecialchars($row['eventTitle'])),
                    "start" => $row['timeStart'],
                    "end"   => $row['timeEnd']
                ];
            }
        }
    } 
}
?>

<body class="h-screen bg-gray-100 text-gray-800 font-sans">

  <div class="flex h-full">
    <?php require "./pages/public/app/nav.php"; ?>

    <main class="flex-1 p-8 overflow-auto">
      <div class="flex justify-between items-center mb-6">
        <button onclick="changeMonth(-1)" class="px-3 py-1 text-lg bg-white border rounded hover:bg-gray-100">⬅️</button>
        <h2 id="monthYear" class="text-3xl font-semibold text-center"></h2>
        <button onclick="changeMonth(1)" class="px-3 py-1 text-lg bg-white border rounded hover:bg-gray-100">➡️</button>
      </div>

      <div class="grid grid-cols-7 gap-2 text-center text-sm font-semibold text-gray-500 uppercase">
        <div>Sun</div><div>Mon</div><div>Tue</div><div>Wed</div><div>Thu</div><div>Fri</div><div>Sat</div>
      </div>

      <div id="calendarDays" class="grid grid-cols-7 gap-2 mt-2"></div>
    </main>
  </div>

  <form action="./server/backend/createEvent.php" method="POST">
    <div id="eventModal" class="fixed inset-0 bg-black/30 backdrop-blur-sm flex items-center justify-center hidden z-50">
      <div class="bg-white rounded-xl p-6 w-full max-w-md mx-4 shadow-2xl space-y-4">
        <h2 id="modalTitle" class="text-xl font-bold text-blue-600">New Event</h2>

        <input type="hidden" id="eventDate" name="date">
        <input type="hidden"  id="eventId" name="event_id" />

        <div>
          <label class="block text-sm font-medium mb-1">Event Title</label>
          <input type="text" id="eventName" name="eventName" placeholder="Enter title..." class="w-full border px-3 py-2 rounded focus:ring-2 focus:ring-blue-400" />
        </div>

        <div class="flex gap-2">
          <div class="flex-1">
            <label class="block text-sm font-medium mb-1">Start Time</label>
            <input name="start" type="time" id="eventStart" class="w-full border px-3 py-2 rounded focus:ring-2 focus:ring-blue-400" />
          </div>
          <div class="flex-1">
            <label class="block text-sm font-medium mb-1">End Time</label>
            <input name="end" type="time" id="eventEnd" class="w-full border px-3 py-2 rounded focus:ring-2 focus:ring-blue-400" />
          </div>
        </div>

        <div class="flex justify-between items-center gap-2 pt-4 border-t mt-4">
          <button type="button" onclick="closeModal()" class="flex-1 px-4 py-2 bg-gray-200 rounded hover:bg-gray-300">Cancel</button>

          <button name="action" value="delete" id="deleteBtn" type="submit" class="hidden flex-1 px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600">Delete</button>

          <button name="action" value="save" id="saveBtn" type="submit" class="flex-1 px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Save</button>

          <button name="action" value="update" id="updateBtn" type="submit" class="hidden flex-1 px-4 py-2 bg-yellow-500 text-white rounded hover:bg-yellow-600">Update</button>
        </div>

      </div>
    </div>
  </form>

<script>
  const calendarEl = document.getElementById('calendarDays');
  const monthYearEl = document.getElementById('monthYear');
  let currentDate = new Date();
  const events = <?php echo json_encode($events, JSON_PRETTY_PRINT); ?>;
  let editingEventIndex = null;

  function renderCalendar() {
    calendarEl.innerHTML = "";
    const year = currentDate.getFullYear();
    const month = currentDate.getMonth();
    monthYearEl.textContent = `${currentDate.toLocaleString('default', { month: 'long' })} ${year}`;

    const firstDay = new Date(year, month, 1).getDay();
    const daysInMonth = new Date(year, month + 1, 0).getDate();

    for (let i = 0; i < firstDay; i++) {
      calendarEl.innerHTML += `<div></div>`;
    }

    for (let d = 1; d <= daysInMonth; d++) {
      const dateStr = `${year}-${String(month+1).padStart(2, '0')}-${String(d).padStart(2, '0')}`;
      const dayCell = document.createElement('div');
      dayCell.className = "bg-white border h-32 rounded-lg p-2 text-left text-sm hover:bg-blue-50 cursor-pointer flex flex-col overflow-auto shadow-sm";
      dayCell.innerHTML = `<div class="font-semibold text-gray-700">${d}</div>`;

      if (events[dateStr]) {
        events[dateStr].forEach((e, i) => {
          const evt = document.createElement('div');
          evt.className = "mt-1 text-xs bg-blue-100 text-blue-800 rounded px-2 py-1 truncate shadow-sm";
          evt.textContent = `${e.start ? e.start + ' - ' : ''}${e.title}`;
          evt.onclick = (e2) => {
            e2.stopPropagation();
            editEvent(dateStr, i);
          };
          dayCell.appendChild(evt);
        });
      }

      dayCell.onclick = () => openModal(dateStr);
      calendarEl.appendChild(dayCell);
      
    }
  }
  function changeMonth(offset) {
    currentDate.setMonth(currentDate.getMonth() + offset);
    renderCalendar();
  }


  renderCalendar();
</script>


<script src = "./client/callendar.js">  </script>

</body>
