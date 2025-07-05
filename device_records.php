<!doctype html>
<html>

<?php
require_once __DIR__ . '/services/api.php';
$api = new API();

$deviceSpeedTestData = [];
$deviceUniqueId = 'N/A';

if (isset($_GET['device_id'])) {
  $deviceId = intval($_GET['device_id']);
  $deviceSpeedTestData = $api->getDeviceSpeedTestResults($deviceId);

  // Fetch device_unique_id for display
  $devices_data = $api->getDevices(); // Get all registered devices
  foreach ($devices_data['devices'] as $device) { // Access the 'devices' key
    if ($device['id'] == $deviceId) { // Use 'id' instead of 'device_id'
      $deviceUniqueId = htmlspecialchars($device['device_unique_id']);
      break;
    }
  }
}
?>

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      darkMode: 'class',
    }
  </script>
  <title>Device Records - <?php echo $deviceUniqueId; ?></title>
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
      background: #8b5cf6;
      /* Purple-500 */
      border-radius: 10px;
      border: 3px solid #2d2d2d;
    }

    .custom-scrollbar::-webkit-scrollbar-thumb:hover {
      background: #a78bfa;
      /* Purple-400 */
    }

    /* Dark mode styles */
    html.dark .custom-scrollbar::-webkit-scrollbar-track {
      background: #e0e0e0;
    }

    html.dark .custom-scrollbar::-webkit-scrollbar-thumb {
      background: #6d28d9;
      /* Darker purple for light mode scrollbar */
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
  <div class="min-h-screen border border-gray-700 p-2 flex flex-col items-center">
    <!-- Header -->
    <div class="relative w-full max-w-6xl mx-auto flex justify-between items-center p-2">
      <!-- Left Actions -->
      <div class="z-20">
        <!-- Back Button -->
        <a href="dashboard.php" 
          class="p-3 text-white focus:outline-none shadow-lg hover:shadow-xl transform hover:scale-105 dark:bg-purple-500 dark:hover:bg-purple-600"
          aria-label="Back to Dashboard">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
          </svg>
        </a>
      </div>
      <!-- Title and Description -->
      <div class="text-center flex-grow">
        <h1 class="text-5xl font-extrabold text-purple-400 mb-3 animate-fade-in-down dark:text-purple-700">Records for
          Device: <?php echo $deviceUniqueId; ?></h1>
        <p class="text-gray-300 text-lg mb-6 animate-fade-in-up dark:text-gray-700">Detailed speed test data for this
          device</p>
      </div>
      <!-- Right Actions (empty for now, but keeps alignment) -->
      <div class="z-20 w-12"></div>
    </div>

    <!-- Main Content -->
    <div class="flex flex-col lg:flex-row justify-center w-full max-w-6xl gap-6">

      <!-- Speed Test Display Section (Left Side) -->
      <div
        class="bg-gray-800 rounded-xl shadow-2xl overflow-hidden w-full lg:w-1/3 transform transition-all duration-300 hover:scale-[1.01] dark:bg-gray-200 p-4 flex flex-col items-center justify-center text-center">
        <h2
          class="text-3xl font-bold mb-2 text-purple-400 border-b border-gray-700 pb-4 dark:text-purple-700 dark:border-gray-300 w-full">
          Latest Speed Test</h2>
        <div class="w-full max-w-md mt-2">
          <div class="text-center mb-6">
            <!-- <p
              class="text-gray-400 text-lg font-semibold border-b border-gray-700 pb-2 dark:text-gray-600 dark:border-gray-300">
              Internet Speed</p> -->
            <div class="flex justify-around mt-4">
              <div>
                <p class="text-gray-500 text-sm dark:text-gray-500">Download</p>
                <p class="text-4xl font-bold text-white dark:text-gray-900">31<span class="text-xl ml-1">Mbps</span></p>
              </div>
              <div>
                <p class="text-gray-500 text-sm dark:text-gray-500">Upload</p>
                <p class="text-4xl font-bold text-white dark:text-gray-900">218<span class="text-xl ml-1">Mbps</span></p>
              </div>
            </div>
          </div>

          <div class="text-center mb-6">
            <p
              class="text-gray-400 text-lg font-semibold border-b border-gray-700 pb-2 dark:text-gray-600 dark:border-gray-300">
              Latency</p>
            <div class="flex justify-around mt-4">
              <div>
                <p class="text-gray-500 text-sm dark:text-gray-500">Unloaded</p>
                <p class="text-4xl font-bold text-white dark:text-gray-900">31<span class="text-xl ml-1">ms</span></p>
              </div>
              <div>
                <p class="text-gray-500 text-sm dark:text-gray-500">Loaded</p>
                <p class="text-4xl font-bold text-white dark:text-gray-900">218<span class="text-xl ml-1">ms</span></p>
              </div>
            </div>
          </div>


        </div>

        <div class="mt-8 text-gray-400 text-sm w-full max-w-md">
          <p class="mb-2"><span class="font-semibold text-gray-300 dark:text-gray-700">Client:</span> Ketu Adie Owe, NG
            <span class="ml-4">102.89.83.219</span></p>
          <p><span class="font-semibold text-gray-300 dark:text-gray-700">Server(s):</span> Ojota, NG | Ikoyi, NG |
            Lagos, NG</p>
        </div>
      </div>

      <!-- Speed Test Table (Right Side) -->
      <div
        class="bg-gray-800 rounded-xl shadow-2xl overflow-hidden w-full lg:w-2/3 transform transition-all duration-300 hover:scale-[1.01] dark:bg-gray-200">
        <div class="p-8">
          <h2
            class="text-3xl font-bold mb-6 text-purple-400 border-b border-gray-700 pb-4 dark:text-purple-700 dark:border-gray-300">
            Recent Speed Test Results</h2>
          <div class="overflow-y-auto max-h-[600px] custom-scrollbar">
            <table class="min-w-full divide-y divide-gray-700 dark:divide-gray-300">
              <thead class="bg-gray-700 sticky top-0 z-10 dark:bg-gray-300">
                <tr>
                  <th
                    class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider dark:text-gray-700">
                    SN</th>
                  <th
                    class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider dark:text-gray-700">
                    Upload (Mbps)</th>
                  <th
                    class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider dark:text-gray-700">
                    Download (Mbps)</th>
                  <th
                    class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider dark:text-gray-700">
                    Latency (ms)</th>
                  <th
                    class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider dark:text-gray-700">
                    Date/Time</th>
                  <th
                    class="px-6 py-3 text-center text-xs font-medium text-gray-300 uppercase tracking-wider dark:text-gray-700">
                    Action</th>
                </tr>
              </thead>
              <tbody class="bg-gray-800 divide-y divide-gray-700 dark:bg-gray-100 dark:divide-gray-300">
                <?php
                if (!empty($deviceSpeedTestData)) {
                  foreach ($deviceSpeedTestData as $index => $record) {
                    $sn = $index + 1;
                    $upload = number_format($record['upload'], 2);
                    $download = number_format($record['download'], 2);
                    $latency = number_format($record['latency'], 2);
                    $timestamp = date('M d, Y, H:i:s', strtotime($record['timestamp']));
                    $recordId = $record['id'];
                    echo "<tr class='hover:bg-gray-700 transition-colors duration-200 dark:hover:bg-gray-200'>";
                    echo "<td class='px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-200 dark:text-gray-800'>$sn</td>";
                    echo "<td class='px-6 py-4 whitespace-nowrap text-sm text-green-400 font-semibold dark:text-green-700'>$upload</td>";
                    echo "<td class='px-6 py-4 whitespace-nowrap text-sm text-blue-400 font-semibold dark:text-blue-700'>$download</td>";
                    echo "<td class='px-6 py-4 whitespace-nowrap text-sm text-yellow-400 font-semibold dark:text-yellow-700'>$latency</td>";
                    echo "<td class='px-6 py-4 whitespace-nowrap text-sm text-gray-400 dark:text-gray-600'>$timestamp</td>";
                    echo "<td class='px-6 py-4 whitespace-nowrap text-center'>";
                    echo "<button onclick='deleteRecord($recordId, this)' class='text-red-500 hover:text-red-400 transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-opacity-50 rounded-full p-2' title='Delete record'>";
                    echo "<svg class='w-6 h-6' fill='none' stroke='currentColor' viewBox='0 0 24 24'>";
                    echo "<path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16'></path>";
                    echo "</svg>";
                    echo "</button>";
                    echo "</td>";
                    echo "</tr>";
                  }
                } else {
                  echo "<tr><td colspan='6' class='px-6 py-4 text-center text-gray-400 text-base dark:text-gray-600'>No speed test data available for this device</td></tr>";
                }
                ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script>
    // On page load, check for dark mode preference
    if (localStorage.getItem('darkMode') === 'enabled') {
      document.documentElement.classList.add('dark');
    }

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
  </script>
</body>

</html>
}
</script>
</body>

</html>