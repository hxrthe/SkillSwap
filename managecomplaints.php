
<?php
// Database connection
$servername = "localhost";
$username = "root"; // Change if needed
$password = ""; // Change if needed
$dbname = "skillswap"; // Make sure this is correct

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Messages</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-6 relative">
  <div class="w-full">
    <table class="w-full bg-white border border-gray-300">
      <thead>
        <tr class="bg-gray-200 text-left">
          <th class="py-3 px-6">ID</th>
          <th class="py-3 px-6">Name</th>
          <th class="py-3 px-6">Email</th>
          <th class="py-3 px-6">Contact No.</th>
          <th class="py-3 px-6">Action</th>
          <th class="py-3 px-6">Created At</th>
          <th class="py-3 px-6">Status</th>
        </tr>
      </thead>
      <tbody id="messageTable">
        <?php
        $sql = "SELECT * FROM complaints ORDER BY created_at DESC";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
          while ($row = $result->fetch_assoc()) {
            echo "<tr class='border-t border-gray-300'>";
            echo "<td class='py-3 px-6'>" . $row["id"] . "</td>";
            echo "<td class='py-3 px-6'>" . htmlspecialchars($row["name"]) . "</td>";
            echo "<td class='py-3 px-6'>" . htmlspecialchars($row["email"]) . "</td>";
            echo "<td class='py-3 px-6'>" . htmlspecialchars($row["contact_no"]) . "</td>";
            echo "<td class='py-3 px-6'>
                    <button onclick=\"showMessage('" . htmlspecialchars(addslashes($row["message"])) . "', " . $row["id"] . ", this)\" class='bg-yellow-400 hover:bg-yellow-500 text-white px-3 py-1 rounded'>
                      View Message
                    </button>
                  </td>";
            echo "<td class='py-3 px-6'>" . $row["created_at"] . "</td>";
            $statusClass = $row["status"] == 'Resolved' ? 'text-green-600' : 'text-yellow-600';
            echo "<td class='py-3 px-6 status-cell $statusClass font-semibold'>" . $row["status"] . "</td>";
            echo "</tr>";
          }
        } else {
          echo "<tr><td colspan='7' class='py-3 px-6 text-center'>No complaints found.</td></tr>";
        }
        ?>
      </tbody>
    </table>
  </div>

  <!-- Modal -->
  <div id="messageModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white p-6 rounded-lg shadow-lg max-w-md w-full">
      <p id="messageContent" class="mb-4 text-gray-800">Message goes here...</p>
      <div class="flex justify-end space-x-3">
        <button onclick="markAsSolved()" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded">
          Mark as Solved
        </button>
        <button onclick="closeMessage()" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded">
          Close
        </button>
      </div>
    </div>
  </div>

  <script>
    let currentRow = null;
    let currentId = null;

    function showMessage(message, id, button) {
      currentRow = button.closest('tr');
      currentId = id;
      document.getElementById('messageContent').textContent = message;
      document.getElementById('messageModal').classList.remove('hidden');
      document.getElementById('messageModal').classList.add('flex');
    }

    function closeMessage() {
      document.getElementById('messageModal').classList.add('hidden');
      document.getElementById('messageModal').classList.remove('flex');
    }

    function markAsSolved() {
      if (!currentId || !currentRow) return;

      fetch('update_status.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'id=' + encodeURIComponent(currentId)
      })
      .then(response => response.text())
      .then(result => {
        if (result === 'Success') {
          const statusCell = currentRow.querySelector('.status-cell');
          statusCell.textContent = 'Resolved';
          statusCell.classList.remove('text-yellow-600');
          statusCell.classList.add('text-green-600');
          closeMessage();
        } else {
          alert('Failed to update status.');
        }
      });
    }
  </script>
</body>
</html>
