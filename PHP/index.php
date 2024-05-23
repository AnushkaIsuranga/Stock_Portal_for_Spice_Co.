<?php
    require 'config.php';

    // Check if a session is already active before starting a new one
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }

    // Checks if the user is not authorized
    if (!isset($_SESSION["id"])) {
        header("Location: login.php");
        exit();
    }

    $id = $_SESSION["id"];

    // Use a prepared statement to prevent SQL injection
    $stmt = $conn->prepare("SELECT * FROM users_table WHERE User_ID = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user_row = $result->fetch_assoc();
    $stmt->close();
?>

<!DOCTYPE html>
<html>
    <head>
        <title>Order History</title>
        <link rel="stylesheet" href="styles.css">
    </head>
    <body>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                //Gathering elements into variables
                const yearSelect = document.getElementById('year');
                const monthSelect = document.getElementById('month');
                const dateSelect = document.getElementById('date');

                // Initialize year dropdown menu
                yearSelect.innerHTML = '<option value="0">All Years</option>';
                for (let year = 2023; year <= 4000; year++) {
                    const option = document.createElement('option');
                    option.value = year;
                    option.text = year;
                    yearSelect.appendChild(option);
                }

                // Initialize month dropdown menu
                monthSelect.innerHTML = '<option value="0">All Months</option>';
                for (let month = 1; month <= 12; month++) {
                    const option = document.createElement('option');
                    option.value = month;
                    option.text = month;
                    monthSelect.appendChild(option);
                }

                // Initialize date dropdown menu
                dateSelect.innerHTML = '<option value="0">All Dates</option>';
                for (let date = 1; date <= 31; date++) {
                    const option = document.createElement('option');
                    option.value = date;
                    option.text = date;
                    dateSelect.appendChild(option);
                }

                // Function to filter data based on selections
                function filterData() {
                    const selectedYear = yearSelect.value;
                    const selectedMonth = monthSelect.value;
                    const selectedDate = dateSelect.value;

                    // Create an XMLHttpRequest object to automatically refresh the table with selected values
                    const xhttp = new XMLHttpRequest();
                    xhttp.onreadystatechange = function() {
                        if (this.readyState === 4 && this.status === 200) {
                            const filteredData = JSON.parse(this.responseText);
                            updateTable(filteredData);
                        }
                    };
                    xhttp.open("POST", "filter_data.php", true);
                    xhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
                    xhttp.send("year=" + selectedYear + "&month=" + selectedMonth + "&date=" + selectedDate);
                }

                // Function to update the table with filtered data
                function updateTable(data) {
                    const tableBody = document.getElementById("tableBody");
                    tableBody.innerHTML = "";

                    if (data.length === 0) {
                        tableBody.innerHTML = "<tr><td colspan='4'>No records found based on your filter selection.</td></tr>";
                    } else {
                        for (const row of data) {
                            const tableRow = document.createElement("tr");
                            tableRow.innerHTML = `
                                <td>${row["Order_ID"]}</td>
                                <td>${row["Date"]}</td>
                                <td>${row["Quantity"]}</td>
                                <td>${row["Price_Rs"]}</td>
                            `;
                            tableBody.appendChild(tableRow);
                        }
                    }
                }

                // Call filterData on combobox selection change
                yearSelect.addEventListener('change', filterData);
                monthSelect.addEventListener('change', filterData);
                dateSelect.addEventListener('change', filterData);

                // Initial load of all data
                filterData();
            });
        </script>

        <h1>Welcome, <?php echo htmlspecialchars($user_row['U_Name']); ?></h1>
        <a href="logout.php">Logout</a><br><br>

        <select id="year"></select>
        <select id="month"></select>
        <select id="date"></select>

        <table>
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Date</th>
                    <th>Quantity</th>
                    <th>Price</th>
                </tr>
            </thead>
            <tbody id="tableBody">
                <!-- Table content will be dynamically populated here -->
            </tbody>
        </table>
    </body>
</html>
