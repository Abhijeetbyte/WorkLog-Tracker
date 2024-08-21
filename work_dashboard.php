<?php
// Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Set up error logging
$logFile = 'error_log.txt';
ini_set('log_errors', 1);
ini_set('error_log', $logFile);

// Function to read CSV data and return it as an array
function readCsvData($filename) {
    if (!file_exists($filename)) {
        error_log("Error: File not found - $filename");
        return [];
    }

    $file = fopen($filename, "r");
    if (!$file) {
        error_log("Error: Could not open file - $filename");
        return [];
    }

    $data = [];
    while (($row = fgetcsv($file)) !== FALSE) {
        $data[] = $row;
    }
    fclose($file);
    return $data;
}

// Directory containing the CSV files
$csvDirectory = "WORK_RECORD_DB";

// Mapping of team member HTML card IDs to their respective CSV files
$teamMembers = [
"nexus_prime" => "nexus_prime_res25R5vt.csv",
"echo_volt" => "echo_volt_res74T6qz.csv",
"vortex_blaze" => "vortex_blaze_res88M2kq.csv",
"orion_flux" => "orion_flux_res39L7wp.csv"
    
];

// Initialize an array to store the data for each team member
$teamData = [];

// Loop through each team member and read their respective CSV file
foreach ($teamMembers as $memberId => $csvFile) {
    $filePath = $csvDirectory . '/' . $csvFile;
    if (file_exists($filePath)) {
        $teamData[$memberId] = readCsvData($filePath);
    } else {
        $teamData[$memberId] = [];
    }
}

// Function to calculate the total work tokens, weekly and monthly average working hours
function calculateMetrics($data) {
    $totalTokens = count($data);
    $totalHours = 0;
    $weeklyAverage = 0;
    $monthlyAverage = 0;
    $labels = [];
    $lastSubmission = "N/A";

    // Initialize start date for range calculations
    $startDate = null;

    foreach ($data as $row) {
        $totalHours += (float)$row[3];
        $labels[] = $row[2];

        // Assuming the last row in the CSV is the most recent submission
        $lastSubmission = $row[4];

        // Determine the start date
        $entryDate = new DateTime($row[4]);
        if ($startDate === null || $entryDate < $startDate) {
            $startDate = $entryDate;
        }
    }

    if ($startDate === null) {
        error_log("Error: No valid start date found.");
        return [
            "last_submission" => $lastSubmission,
            "total_tokens" => $totalTokens,
            "weekly_average" => "N/A",
            "monthly_average" => "N/A",
            "top_labels" => implode(", ", array_slice(array_keys(array_count_values($labels)), 0, 5))
        ];
    }

    // Calculate total days between the start date and current date
    $endDate = new DateTime();
    $totalDays = $endDate->diff($startDate)->days;

    // Initialize default values
    $showWeeklyAverage = false;
    $showMonthlyAverage = false;

    // Calculate the total number of weeks and months
    $totalWeeks = ceil($totalDays / 7);
    $totalMonths = ceil($totalDays / 30);

    // Check if 7 days have passed for weekly average
    if ($totalDays >= 7) {
        $showWeeklyAverage = true;
        $weeklyAverage = $totalHours / $totalWeeks;
    }

    // Check if 30 days have passed for monthly average
    if ($totalDays >= 30) {
        $showMonthlyAverage = true;
        $monthlyAverage = $totalHours / $totalMonths;
    }

    // Get the top 5 most common labels
    $labelCounts = array_count_values($labels);
    arsort($labelCounts);
    $topLabels = array_slice(array_keys($labelCounts), 0, 5);

    return [
        "last_submission" => $lastSubmission,
        "total_tokens" => $totalTokens,
        "weekly_average" => $showWeeklyAverage ? round($weeklyAverage, 2) : "N/A",
        "monthly_average" => $showMonthlyAverage ? round($monthlyAverage, 2) : "N/A",
        "top_labels" => implode(", ", $topLabels)
    ];
}


?>





<!DOCTYPE html>
<html lang="en" class="scroll-smooth">

<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    <title> Dashboard </title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,typography,aspect-ratio,line-clamp"></script>
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
    
</head>

<body>

    <!--Header section-->
    <header class="body-font bg-gradient-to-r from-cyan-500 via-teal-400 to-teal-0 shadow-md">

        <div class="container mx-auto flex flex-wrap p-2 items-center justify-between">
            <a href="index.html" class="flex title-font font-medium items-center text-gray-900 mb-4 md:mb-0">
                <img alt="logo0"
                    class="w-24 h-24 object-cover object-center rounded-full inline-block items-center justify-center bg-white"
                    src="https://dummyimage.com/80x80">
                <h1 class="ml-3 mt-5 sm:text-3xl text-2xl font-medium title-font font-sans mb-4 text-blue-900">Company Name: Dashboard </h1>
            </a>
        </div>
    </header>



    <!--Team description-->
    <section class="body-font">
        <div class="container px-5 py-12 mx-auto">
            <div class="flex flex-col text-center w-full mb-10" data-aos="fade-up" data-aos-duration="500">
                <h1 class="text-4xl md:text-5xl font-bold title-font text-teal-500 mb-6">Dashboard</h1>
            </div>
        </div>
    </section>

    <!-- Team Member Section -->
    <section id="our-team" class="relative bg-gray-100 py-12 mb-10">
        <div class="container mx-auto px-4 mb-10">
            
            
             <!-- Add portal button -->
            <div class="flex justify-end mt-2">
              <a href="index.html" target="_blank">
                <button class="bg-blue-500 hover:bg-blue-600 text-white py-2 px-4 rounded-md mt-2 mb-12"> Add work token </button>
                </a>
            </div>
            
            
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
                
                <!-- Team Member Cards will be generated by PHP -->

                <?php foreach ($teamMembers as $memberId => $csvFile): ?>
                    <?php $metrics = calculateMetrics($teamData[$memberId]); ?>
                    <div class="bg-white rounded-lg shadow-md p-6 my-6 text-center hover:-translate-y-1 hover:scale-105 duration-300" id="<?php echo $memberId; ?>">
                        <img src="<?php echo $memberId; ?>.png" alt="Team Member" class="w-full rounded-full mb-4">
                        <h3 class="text-xl font-semibold mb-2"><?php echo ucfirst(str_replace("_", " ", $memberId)); ?></h3>
                        <div class="text-gray-800 text-md px-4">
                            <ul >
                                <li class="inline-block mx-4 mb-2 text-left">
                                    <h1 class="text-gray-900 font-base py-2 font-bold">Highlights</h1>
                                    <h1>Last submission:  <br/> <?php echo $metrics["last_submission"]; ?></h1>
                                    <h1>Work tokens:  <?php echo $metrics["total_tokens"]; ?></h1>
                                    <h1>Weekly Avg :  <?php echo $metrics["weekly_average"]; ?> hr</h1>
                                    <h1>Monthly Avg:  <?php echo $metrics["monthly_average"]; ?> hr</h1>
                                    <h1>Most work includes:  <br/> <?php echo $metrics["top_labels"]; ?></h1>
                                </li>
                            </ul>
                        </div>
                        <button class="bg-blue-500 hover:bg-blue-600 text-white py-2 px-4 rounded-md mt-4" onclick="showDetails('<?php echo $memberId; ?>')">
                            View Details
                        </button>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

   <!-- Hidden div for view details -->
<div class="bg-white hidden" id="details-container">
    <div class="max-w-xl mx-auto p-8">
        <h2 id="member-name" class="text-2xl font-semibold mb-2">Member Name</h2> <!-- Placeholder for member's name -->
        <h2 class="text-xl font-semibold mb-4">( work log / tokens )</h2>
        <div class="flow-root">
            <ul id="work-details-list" class="-mb-8">
                <!-- Work details of the team member will be inserted here -->
            </ul>
        </div>
    </div>
</div>



  <script>
    // Function to show the details of a specific team member
    function showDetails(memberId) {
        // Hide the details container first
        document.getElementById('details-container').classList.add('hidden');

        // Clear any existing details
        document.getElementById('work-details-list').innerHTML = '';

        // Load the work details for the selected team member
        var teamData = <?php echo json_encode($teamData); ?>;
        var workDetails = teamData[memberId];

        // Reverse the order to show the latest work on top
        workDetails.reverse();

        // Update the member name at the top
        var memberName = memberId.replace(/_/g, ' ').replace(/\b\w/g, char => char.toUpperCase());  // replaces all underscores and capitalizes the first letter of each word
        var nameElement = document.getElementById('member-name');
        if (nameElement) {
            nameElement.textContent = memberName;
        }

        // Generate the list items for work details
        workDetails.forEach(function(detail) {
            var listItem = `
                <li>
                    <div class="relative pb-8">
                        <span class="absolute top-5 left-5 -ml-px h-full w-0.5 bg-gray-200" aria-hidden="true"></span>
                        <div class="relative flex items-start space-x-3">
                            <div>
                                <div class="relative px-1">
                                    <div class="h-8 w-8 bg-blue-500 rounded-full ring-8 ring-white flex items-center justify-center">
                                        <svg class="text-white h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                        </svg>
                                    </div>
                                </div>
                            </div>
                            <div class="min-w-0 flex-1 py-0">
                                <div class="text-md text-gray-500">
                                    <div>
                                        <a class="font-medium text-gray-900 mr-2">${detail[0]}</a>
                                        <a class="my-0.5 relative inline-flex items-center bg-white rounded-full border border-gray-300 px-3 py-0.5 text-sm">
                                            <div class="absolute flex-shrink-0 flex items-center justify-center">
                                                <span class="h-1.5 w-1.5 rounded-full bg-green-500" aria-hidden="true"></span>
                                            </div>
                                            <div class="ml-3.5 font-medium text-gray-900">${detail[2]}</div>
                                        </a>
                                    </div>
                                    <span class="whitespace-nowrap text-sm">On ${detail[4]}</span>, <span class="whitespace-nowrap text-sm">Work Duration ${detail[3]} hr</span> <!-- duration -->
                                </div>
                                <div class="mt-2 text-gray-700">
                                    <p>${detail[1]}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </li>
            `;
            document.getElementById('work-details-list').innerHTML += listItem;
        });

        // Show the details container
        document.getElementById('details-container').classList.remove('hidden');
    }
</script>










<!-- Footer section -->
    <footer class="relative bg-teal-500 text-white">
       
        <div class="pt-20 mx-auto max-w-screen-xl">
            <div class="md:flex md:justify-between m-5 text-center justify-center items-center flex-row">
                <div class="mb-6 md:mb-0 mt-6">
                    <a href="index.html" class="flex items-center">
                        <img alt="logo0"
                            class="w-24 h-24 object-cover object-center rounded-full inline-block items-center justify-center bg-white"
                            src="https://dummyimage.com/80x80">
                        <span
                            class="self-center text-2xl font-medium title-font font-sans whitespace-nowrap ms-5 text-wrap">Company Name</span>
                    </a>
                </div>
               
                    <div>
                        <h2 class="mb-4 text-md font-semibold text-white uppercase">News & Events</h2>
                        <ul class="text-white">
                            <li class="mb-4">
                                <a href="#" class="hover:underline"><i class="fa-solid fa-newspaper"></i> News</a>
                            </li>
                            <li class="mb-4">
                                <a href="#" class="hover:underline"><i class="fa-solid fa-chart-line"></i> Annual
                                    Report</a>
                            </li>
                            <li class="mb-4">
                                <a href="#" class="hover:underline"><i class="fa-solid fa-image"></i> Gallery</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            <hr class="border-gray-200 sm:mx-auto lg:mb-3" />
            <div class="sm:flex sm:items-center sm:justify-center text-center">
                <span class="text-sm text-white text-center">© 2024 WorkLog-Tracker ( Github )</span>
            </div>
        </div>
    </footer>



</body>

</html>
