# WorkLog-Tracker
It helps track your contributions and performance in an organization. Create and manage them through the dashboard for efficient monitoring and evaluation.


To get started with the WorkLog Tracker, follow these steps:

1. **Clone the Repository**:
   ```bash
   git clone https://github.com/yourusername/WorkLog-tracker.git
   ```

2. **Upload to Hosting Server**:
   Upload the cloned repository to your hosting server.

3. **PHP Version**:
   Ensure that your server supports PHP 7.4 or above.

## Structure

- **`WORK_RECORD_DB`**: Contains CSV files with work records for different team members. Each file should follow the format described below.
- **`index.html`**: The HTML file used for the front end.
- **`work_dashboard.php`**: The PHP script that reads the CSV files, processes the data, and provides it to the front end ( dashboard ).

## Working

1. **CSV Data Handling**:
   The `work_dashboard.php` script reads CSV files from the `WORK_RECORD_DB` folder. Each CSV file contains work records with the following columns:
   - **Title**: The title of the work.
   - **Description**: A detailed description of the work.
   - **Label**: The category or label of the work.
   - **Duration**: Duration of work in hours.
   - **Timestamp**: The date and time of the work submission.

2. **Data Processing**:
   - Metrics such as total tokens, weekly average, and monthly average are calculated based on the work records.
   - Weekly and monthly averages are computed only if the relevant time periods (7 days for weekly average and 30 days for monthly average) have passed to ensure accurate results.

3. **Display Data**:
   The processed data is sent to the front-end, where it is displayed in the dashboard. The `showDetails` JavaScript function dynamically updates the dashboard with the selected team member's work details.

## Approach

- **CSV-Based Data**:
   Data is stored in CSV files for simplicity and ease of integration. Each CSV file represents an individual team member's work log.

- **Metrics Calculation**:
   Weekly and monthly averages are computed only if the respective time periods have passed. This avoids incorrect data due to incomplete periods.

- **Authentication**:
   Each team member has an associated authentication key used to log/submit their work records. This ensures that only authorized users can enter data the data.
  Auth key e.g 'employee_name_rsz8739tx2'
* To produce a new auth key go to the `WORK_RECORD_DB` folder and create a `.CSV` file with the auth key as the name of the file.

## Authentication Key

To ensure secure access to work records, each CSV file is linked to a unique authentication key. Users must provide the correct key to log their data. No auth key is required when accessing the dashboard.

---

Feel free to adjust any section based on additional specifics or preferences you might have!


![image](https://github.com/user-attachments/assets/e6bc4ac9-7ca1-4e82-8faf-c0ad457d28e6)
