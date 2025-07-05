<!doctype html>
<html>

<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/services/api.php';
$api = new API();
$devices = $api->getDevices(); // Get all registered devices
?>

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="refresh" content="30">
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      darkMode: 'class',
    }
  </script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <title>Speed Test Dashboard</title>
  <style>
    /* Custom Scrollbar for Webkit browsers */
    .custom-scrollbar::-webkit-scrollbar {
      width: 12px;
    }

    .custom-scrollbar::-webkit-scrollbar-track {
      background: #2d2d2d;
      border-radius: 10px;
    }

    .custom-scrollbar::-webkit-scrollbar-thumb {
      background: #8b5cf6; /* Purple-500 */
      border-radius: 10px;
      border: 3px solid #2d2d2d;
    }

    .custom-scrollbar::-webkit-scrollbar-thumb:hover {
      background: #a78bfa; /* Purple-400 */
    }

    /* Dark mode styles */
    html.dark .custom-scrollbar::-webkit-scrollbar-track {
      background: #e0e0e0;
    }

    html.dark .custom-scrollbar::-webkit-scrollbar-thumb {
      background: #6d28d9; /* Darker purple for light mode scrollbar */
      border: 3px solid #e0e0e0;
    }

    html.dark .custom-scrollbar::-webkit-scrollbar-thumb:hover {
      background: #5b21b6;
    }

    /* Animations */
    @keyframes fadeInDown {
      from {
        opacity: 0;
        transform: translateY(-20px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    @keyframes fadeInUp {
      from {
        opacity: 0;
        transform: translateY(20px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    @keyframes fadeIn {
      from {
        opacity: 0;
      }
      to {
        opacity: 1;
      }
    }

    .animate-fade-in-down {
      animation: fadeInDown 0.8s ease-out forwards;
    }

    .animate-fade-in-up {
      animation: fadeInUp 0.8s ease-out forwards;
    }

    .animate-fade-in {
      animation: fadeIn 0.8s ease-out forwards;
    }
  </style>
</head>

<body class="bg-gray-900 text-gray-100 font-sans dark:bg-gray-100 dark:text-gray-900">
  <div class="min-h-screen p-6 flex flex-col items-center">
    <!-- Header -->
    <div class="relative w-full max-w-6xl mx-auto">
      <!-- Dark Mode Toggle -->
      <div class="absolute top-0 right-0 mt-6 mr-6 z-20">
        <button id="darkModeToggle" class="p-3 rounded-full bg-gray-700 text-gray-200 hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-opacity-50 transition-all duration-300 ease-in-out dark:bg-gray-200 dark:text-gray-800 dark:hover:bg-gray-300">
          <svg id="moonIcon" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path>
          </svg>
          <svg id="sunIcon" class="w-6 h-6 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d='M12 3v1m0 16v1m9-9h1M3 12H2m15.325-7.757l-.707-.707M6.343 17.657l-.707.707M16.95 12.001l.001.001M7.05 12.001l-.001.001M12 7a5 5 0 110 10 5 5 0 010-10z'></path>
          </svg>
        </button>
      </div>
    </div>

    <div class="text-center mb-10">
      <h1 class="text-5xl font-extrabold text-purple-400 mb-3 animate-fade-in-down dark:text-purple-700">Speed Test Dashboard</h1>
      <p class="text-gray-300 text-lg mb-6 animate-fade-in-up dark:text-gray-700">Real-time internet speed data monitoring</p>
      
      <!-- Action Buttons -->
      <div class="flex justify-center space-x-6 animate-fade-in">
        <a href="download_csv.php?download=csv" 
           class="inline-flex items-center px-8 py-4 bg-gradient-to-r from-green-500 to-emerald-600 hover:from-green-600 hover:to-emerald-700 text-white font-bold rounded-full shadow-lg hover:shadow-xl transform hover:scale-105 transition-all duration-300 ease-in-out">
          <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
          </svg>
          Download All Data (CSV)
        </a>
        <a href="speed_chart.php" 
           class="inline-flex items-center px-8 py-4 bg-gradient-to-r from-blue-500 to-indigo-600 hover:from-blue-600 hover:to-indigo-700 text-white font-bold rounded-full shadow-lg hover:shadow-xl transform hover:scale-105 transition-all duration-300 ease-in-out">
          <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M7 4v16M17 4v16M4 8h4m-4 8h4m-4-4h16m-9-4h9'></path>
          </svg>
          View Speed Chart
        </a>
        <button id="addDeviceBtn" class="inline-flex items-center px-8 py-4 bg-gradient-to-r from-purple-500 to-pink-600 hover:from-purple-600 hover:to-pink-700 text-white font-bold rounded-full shadow-lg hover:shadow-xl transform hover:scale-105 transition-all duration-300 ease-in-out">
          <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
          </svg>
          Add New Device
        </button>
      </div>
      
      <!-- Error Message Display -->
      <?php if (isset($_GET['error']) && $_GET['error'] === 'download_failed'): ?>
        <div class="mt-6 p-4 bg-red-700 text-white rounded-lg max-w-md mx-auto shadow-md animate-fade-in">
          <p class="text-base flex items-center justify-center"><span class="mr-2 text-xl">⚠️</span> Download failed. Please try again.</p>
        </div>
      <?php endif; ?>
    </div>

    <!-- Main Content -->
    <div class="flex justify-center w-full max-w-6xl">
      
      <!-- Devices Table -->
      <div class="bg-gray-800 rounded-xl shadow-2xl overflow-hidden w-full lg:w-3/4 xl:w-2/3 transform transition-all duration-300 hover:scale-[1.01] dark:bg-gray-200">
        <div class="p-8">
          <h2 class="text-3xl font-bold mb-6 text-purple-400 border-b border-gray-700 pb-4 dark:text-purple-700 dark:border-gray-300">Registered Devices</h2>
          <div class="overflow-y-auto max-h-[600px] custom-scrollbar">
            <table class="min-w-full divide-y divide-gray-700 dark:divide-gray-300">
              <thead class="bg-gray-700 sticky top-0 z-10 dark:bg-gray-300">
                <tr>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider dark:text-gray-700">Device ID</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider dark:text-gray-700">Unique Device ID</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider dark:text-gray-700">Registered On</th>
                  <th class="px-6 py-3 text-center text-xs font-medium text-gray-300 uppercase tracking-wider dark:text-gray-700">Action</th>
                </tr>
              </thead>
              <tbody class="bg-gray-800 divide-y divide-gray-700 dark:bg-gray-100 dark:divide-gray-300">
                <?php 
                ob_start(); // Start output buffering for the table body
                if (!empty($devices['devices'])) { // Access the 'devices' key
                    foreach ($devices['devices'] as $device) { // Iterate over the 'devices' array
                        $deviceId = $device['id']; // Use 'id' instead of 'device_id'
                        $deviceUniqueId = htmlspecialchars($device['device_unique_id']);
                        $createdAt = date('M d, Y, H:i:s', strtotime($device['created_at']));
                        echo "<tr class='hover:bg-gray-700 transition-colors duration-200 dark:hover:bg-gray-200'>";
                        echo "<td class='px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-200 dark:text-gray-800'>$deviceId</td>";
                        echo "<td class='px-6 py-4 whitespace-nowrap text-sm text-blue-400 font-semibold dark:text-blue-700'>$deviceUniqueId</td>";
                        echo "<td class='px-6 py-4 whitespace-nowrap text-sm text-gray-400 dark:text-gray-600'>$createdAt</td>";
                        echo "<td class='px-6 py-4 whitespace-nowrap text-center text-sm font-medium'>";
                        echo "<a href='device_records.php?device_id=$deviceId' class='inline-flex items-center px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white font-semibold rounded-md transition-colors duration-200 shadow-md mb-2 lg:mb-0 lg:mr-2'>";
                        echo "<svg class='w-5 h-5 mr-2' fill='none' stroke='currentColor' viewBox='0 0 24 24'>";
                        echo "<path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002 2'></path>";
                        echo "</svg>";
                        echo "View";
                        echo "</a>";
                        echo "<button onclick='deleteDevice(" . $deviceId . ")' class='inline-flex items-center px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-md transition-colors duration-200 shadow-md'>";
                        echo "<svg class='w-5 h-5 mr-2' fill='none' stroke='currentColor' viewBox='0 0 24 24'>";
                        echo "<path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16'></path>";
                        echo "</svg>";
                        echo "Delete";
                        echo "</button>";
                        echo "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='4' class='px-6 py-4 text-center text-gray-400 text-base dark:text-gray-600'>No devices registered yet</td></tr>";
                }
                $tableBodyContent = ob_get_clean(); // Get the buffered content and clean the buffer
                file_put_contents('debug_table_body.html', $tableBodyContent); // Write to file
                echo $tableBodyContent; // Output the content to the browser
                ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>

    <!-- Current Status -->
    <div class="mt-10 text-center animate-fade-in-up">
      <div class="inline-block bg-gray-800 rounded-lg px-8 py-5 shadow-xl dark:bg-gray-200">
        <p class="text-gray-300 text-lg dark:text-gray-700">Last Updated: <span class="text-white font-semibold dark:text-gray-900"><?php echo date('M d, Y H:i:s'); ?></span></p>
        <p class="text-gray-300 text-lg mt-2 dark:text-gray-700">Total Devices: <span class="text-white font-semibold dark:text-gray-900"><?php echo $devices['count']; ?></span></p>
      </div>
    </div>

  </div>

  <!-- Add Device Modal -->
  <div id="addDeviceModal" class="fixed inset-0 bg-gray-900 bg-opacity-75 flex items-center justify-center z-50 hidden dark:bg-gray-100 dark:bg-opacity-75">
    <div class="bg-gray-800 p-8 rounded-lg shadow-xl w-full max-w-md animate-scale-in dark:bg-gray-200">
      <h3 class="text-2xl font-bold text-purple-400 mb-6 border-b border-gray-700 pb-3 dark:text-purple-700 dark:border-gray-300">Add New Device</h3>
      <form id="addDeviceForm" method="POST" action="services/api.php">
        <input type="hidden" name="action" value="addDevice">
        <div class="mb-4">
          <label for="device_unique_id" class="block text-gray-300 text-sm font-bold mb-2 dark:text-gray-700">Device Unique ID:</label>
          <input type="text" id="device_unique_id" name="device_unique_id" class="shadow appearance-none border border-gray-700 rounded w-full py-3 px-4 bg-gray-700 text-gray-200 leading-tight focus:outline-none focus:shadow-outline focus:border-purple-500 dark:border-gray-300 dark:bg-gray-300 dark:text-gray-800 dark:focus:border-purple-700" placeholder="Enter unique device ID" required>
        </div>
        <div class="flex items-center justify-between">
          <button type="submit" class="bg-purple-600 hover:bg-purple-700 text-white font-bold py-3 px-6 rounded-full focus:outline-none focus:shadow-outline transition-colors duration-200">
            Add Device
          </button>
          <button type="button" id="closeModalBtn" class="bg-gray-600 hover:bg-gray-700 text-white font-bold py-3 px-6 rounded-full focus:outline-none focus:shadow-outline transition-colors duration-200">
            Cancel
          </button>
        </div>
      </form>
    </div>
  </div>

  <script>
    const addDeviceBtn = document.getElementById('addDeviceBtn');
    const addDeviceModal = document.getElementById('addDeviceModal');
    const closeModalBtn = document.getElementById('closeModalBtn');
    const addDeviceForm = document.getElementById('addDeviceForm');
    document.addEventListener('DOMContentLoaded', function () {
    const darkModeToggle = document.getElementById('darkModeToggle');
    const moonIcon = document.getElementById('moonIcon');
    const sunIcon = document.getElementById('sunIcon');

    // Function to apply dark mode
    const applyDarkMode = (isDark) => {
        if (isDark) {
            document.documentElement.classList.add('dark');
            moonIcon.classList.add('hidden');
            sunIcon.classList.remove('hidden');
        } else {
            document.documentElement.classList.remove('dark');
            moonIcon.classList.remove('hidden');
            sunIcon.classList.add('hidden');
        }
    };

    // On page load, check for dark mode preference
    const userPrefersDark = localStorage.getItem('darkMode') === 'enabled';
    applyDarkMode(userPrefersDark);

    // Toggle dark mode on button click
    darkModeToggle.addEventListener('click', () => {
        const isDark = document.documentElement.classList.toggle('dark');
        localStorage.setItem('darkMode', isDark ? 'enabled' : 'disabled');
        applyDarkMode(isDark);
        console.log('Dark mode toggled:', isDark ? 'enabled' : 'disabled');
    });
});

    addDeviceBtn.addEventListener('click', () => {
      addDeviceModal.classList.remove('hidden');
    });

    closeModalBtn.addEventListener('click', () => {
      addDeviceModal.classList.add('hidden');
      addDeviceForm.reset(); // Clear form fields on close
    });

    addDeviceModal.addEventListener('click', (e) => {
      if (e.target === addDeviceModal) {
        addDeviceModal.classList.add('hidden');
        addDeviceForm.reset();
      }
    });

    addDeviceForm.addEventListener('submit', function(e) {
      e.preventDefault(); // Prevent default form submission

      const formData = new FormData(this);
      
      fetch('services/api.php', {
        method: 'POST',
        body: new URLSearchParams(formData) // Use URLSearchParams for x-www-form-urlencoded
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          alert(data.message);
          addDeviceModal.classList.add('hidden');
          addDeviceForm.reset();
          location.reload(); // Reload page to show new device
        } else {
          alert('Error: ' + data.message);
        }
      })
      .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while adding the device.');
      });
    });

    function deleteRecord(recordId, buttonElement) {
      if (confirm('Are you sure you want to delete this record?')) {
        fetch('services/api.php', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
          },
          body: 'action=deleteRecord&id=' + recordId
        })
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            alert(data.message);
            // Remove the row from the table
            const row = buttonElement.closest('tr');
            if (row) {
              row.remove();
            }
          } else {
            alert('Failed to delete record: ' + data.message);
          }
        })
        .catch(error => {
          console.error('Error:', error);
          alert('An error occurred while deleting the record.');
        });
      }
    }

    function deleteDevice(deviceId) {
      if (confirm('Are you sure you want to delete this device and ALL its associated speed test records? This action cannot be undone.')) {
        fetch('services/api.php', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
          },
          body: 'action=deleteDevice&device_id=' + deviceId
        })
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            alert(data.message);
            location.reload(); // Reload page to reflect changes
          } else {
            alert('Failed to delete device: ' + data.message);
          }
        })
        .catch(error => {
          console.error('Error:', error);
          alert('An error occurred while deleting the device.');
        });
      }
    }
  </script>

</body>
</html>
