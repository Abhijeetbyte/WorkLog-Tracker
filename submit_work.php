<?php
// Helper function to convert time to hours
function convertToHours($time, $unit) {
    switch ($unit) {
        case 'days':
            return $time * 24;
        case 'weeks':
            return $time * 168;
        case 'hours':
            return $time;
        default:
            return 0;
    }
}

// Fetch and sanitize input
$title = filter_input(INPUT_POST, 'title', FILTER_SANITIZE_STRING);
$authkey = filter_input(INPUT_POST, 'authkey', FILTER_SANITIZE_STRING);
$description = filter_input(INPUT_POST, 'content', FILTER_SANITIZE_STRING);
$label = filter_input(INPUT_POST, 'label', FILTER_SANITIZE_STRING);
$time = filter_input(INPUT_POST, 'time', FILTER_SANITIZE_NUMBER_INT);
$timeUnit = filter_input(INPUT_POST, 'time-unit', FILTER_SANITIZE_STRING);

// Convert time to hours
$timeInHours = convertToHours($time, $timeUnit);

// Get the current timestamp
$timestamp = date('Y-m-d H:i:s');

// Define the directory and filename
$dir = 'WORK_RECORD_DB'; // Folder where CSV files are stored
$filename = $dir . '/' . $authkey . '.csv'; // File name based on the authentication key

// Check if the file exists
if (file_exists($filename)) {
    // Open the file for appending
    $fileHandle = fopen($filename, 'a');

    if ($fileHandle !== false) {
        // Acquire an exclusive lock
        if (flock($fileHandle, LOCK_EX)) {
            // Create a CSV line with the sanitized data and timestamp
            $csvLine = array($title, $description, $label, $timeInHours, $timestamp);

            // Write the data to the CSV file
            if (fputcsv($fileHandle, $csvLine) !== false) {
                echo "Work record successfully added.";
            } else {
                echo "Error adding record.";
            }

            // Release the lock
            flock($fileHandle, LOCK_UN);
        } else {
            echo "Could not acquire file lock.";
        }

        // Close the file handle
        fclose($fileHandle);
    } else {
        echo "Error while submitting, try later.";
    }
} else {
    echo "Authentication key does not match.";
}
?>
