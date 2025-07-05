<?php
require_once __DIR__ . '/services/api.php';
$api = new API();
$allSpeedTestData = $api->getAllSpeedTestResults(); // Get all records for chart
?>

<!doctype html>
<html>

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      darkMode: 'class',
    }
  </script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <title>All Speed Test Data Chart</title>
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
    <div class="text-center mb-10">
      <h1 class="text-5xl font-extrabold text-purple-400 mb-3 animate-fade-in-down dark:text-purple-700">All Speed Test Data Chart</h1>
      <p class="text-gray-300 text-lg mb-6 animate-fade-in-up dark:text-gray-700">Comprehensive view of all recorded speed tests</p>
      
      <!-- Back Button -->
      <div class="flex justify-center space-x-6 animate-fade-in">
        <a href="dashboard.php" 
           class="inline-flex items-center px-8 py-4 bg-gradient-to-r from-blue-500 to-indigo-600 hover:from-blue-600 hover:to-indigo-700 text-white font-bold rounded-full shadow-lg hover:shadow-xl transform hover:scale-105 transition-all duration-300 ease-in-out">
          <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 17l-5-5m0 0l5-5m-5 5h12"></path>
          </svg>
          Back to Dashboard
        </a>
      </div>
    </div>

    <!-- Main Content -->
    <div class="flex justify-center w-full max-w-6xl">
      
      <!-- Chart Container -->
      <div class="bg-gray-800 rounded-xl shadow-2xl overflow-hidden w-full lg:w-3/4 xl:w-2/3 p-8 transform transition-all duration-300 hover:scale-[1.01] dark:bg-gray-200">
        <h2 class="text-3xl font-bold mb-6 text-purple-400 border-b border-gray-700 pb-4 dark:text-purple-700 dark:border-gray-300">Speed Trend Over Time</h2>
        <div class="h-96">
          <canvas id="speedTestChart"></canvas>
        </div>
      </div>
    </div>

  <script>
    // On page load, check for dark mode preference
    if (localStorage.getItem('darkMode') === 'enabled') {
      document.documentElement.classList.add('dark');
    }

    // Prepare chart data
    const allSpeedTestData = <?php echo json_encode(array_reverse($allSpeedTestData)); ?>;
    
    const labels = allSpeedTestData.map(record => {
      const date = new Date(record.timestamp);
      return date.toLocaleTimeString('en-US', { 
        hour: '2-digit', 
        minute: '2-digit',
        hour12: false
      }) + ' - ' + record.device_unique_id; // Include device_unique_id in label
    });
    
    const uploadSpeeds = allSpeedTestData.map(record => parseFloat(record.upload));
    const downloadSpeeds = allSpeedTestData.map(record => parseFloat(record.download));
    const latencies = allSpeedTestData.map(record => parseFloat(record.latency));

    // Create the chart
    const ctx = document.getElementById('speedTestChart').getContext('2d');
    new Chart(ctx, {
      type: 'line',
      data: {
        labels: labels,
        datasets: [
          {
            label: 'Download (Mbps)',
            data: downloadSpeeds,
            borderColor: '#3b82f6',
            backgroundColor: 'rgba(59, 130, 246, 0.1)',
            borderWidth: 3,
            fill: true,
            tension: 0.4,
            pointBackgroundColor: '#3b82f6',
            pointBorderColor: '#ffffff',
            pointBorderWidth: 2,
            pointRadius: 4,
            pointHoverRadius: 6
          },
          {
            label: 'Upload (Mbps)',
            data: uploadSpeeds,
            borderColor: '#10b981',
            backgroundColor: 'rgba(16, 185, 129, 0.1)',
            borderWidth: 3,
            fill: true,
            tension: 0.4,
            pointBackgroundColor: '#10b981',
            pointBorderColor: '#ffffff',
            pointBorderWidth: 2,
            pointRadius: 4,
            pointHoverRadius: 6
          },
          {
            label: 'Latency (ms)',
            data: latencies,
            borderColor: '#ef4444',
            backgroundColor: 'rgba(239, 68, 68, 0.1)',
            borderWidth: 3,
            fill: true,
            tension: 0.4,
            pointBackgroundColor: '#ef4444',
            pointBorderColor: '#ffffff',
            pointBorderWidth: 2,
            pointRadius: 4,
            pointHoverRadius: 6
          }
        ]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: {
            labels: {
              color: '#ffffff',
              font: {
                size: 14
              }
            }
          }
        },
        scales: {
          x: {
            ticks: {
              color: '#9ca3af',
              maxTicksLimit: 10
            },
            grid: {
              color: 'rgba(156, 163, 175, 0.2)'
            },
            title: {
              display: true,
              text: 'Date/Time (H:m:s)',
              color: '#9ca3af'
            }
          },
          y: {
            ticks: {
              color: '#9ca3af'
            },
            grid: {
              color: 'rgba(156, 163, 175, 0.2)'
            },
            title: {
              display: true,
              text: 'Speed/Latency',
              color: '#9ca3af'
            }
          }
        },
        interaction: {
          intersect: false,
          mode: 'index'
        }
      }
    });
  </script>

</body>

</html>
