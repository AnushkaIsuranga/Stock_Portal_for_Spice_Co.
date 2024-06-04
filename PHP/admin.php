<?php
require 'config.php';
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administrator</title>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Settitng elemets to variables
            const searchUserIDOrder = document.getElementById('txtUserID_SearchOrder');
            const yearSelectOrder = document.getElementById('yearOrder');
            const monthSelectOrder = document.getElementById('monthOrder');
            const dateSelectOrder = document.getElementById('dateOrder');
            const deleteButtonOrder = document.getElementById('deleteButtonOrder');

            const searchUserIDUser = document.getElementById('txtUserID_SearchUser');

            // Initialize year, month, and date selects  for order filtering
            yearSelectOrder.innerHTML = '<option value="0">All Years</option>';
            for (let year = 2023; year <= 4000; year++) {
                const option = document.createElement('option');
                option.value = year;
                option.text = year;
                yearSelectOrder.appendChild(option);
            }

            monthSelectOrder.innerHTML = '<option value="0">All Months</option>';
            for (let month = 1; month <= 12; month++) {
                const option = document.createElement('option');
                option.value = month;
                option.text = month;
                monthSelectOrder.appendChild(option);
            }

            dateSelectOrder.innerHTML = '<option value="0">All Dates</option>';
            for (let date = 1; date <= 31; date++) {
                const option = document.createElement('option');
                option.value = date;
                option.text = date;
                dateSelectOrder.appendChild(option);
            }

            // Filtering order table records
            function filterDataOrder() {
                var userID = searchUserIDOrder.value;
                const selectedYear = yearSelectOrder.value;
                const selectedMonth = monthSelectOrder.value;
                const selectedDate = dateSelectOrder.value;

                const xhttp = new XMLHttpRequest();
                xhttp.onreadystatechange = function() {
                    if (this.readyState === 4 && this.status === 200) {
                        console.log(this.responseText); // Debugging line
                        const filteredData = JSON.parse(this.responseText);
                        updateTableOrder(filteredData);
                    }
                };
                xhttp.open("POST", "filter_data_admin_order.php", true);
                xhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
                xhttp.send("userId=" + userID + "&year=" + selectedYear + "&month=" + selectedMonth + "&date=" + selectedDate);
            }

            // Updating order table
            function updateTableOrder(data) {
                const tableBody = document.getElementById("tableBodyOrder");
                tableBody.innerHTML = "";

                if (data.length === 0) {
                    tableBody.innerHTML = "<tr><td colspan='8'>No records found based on your filter selection.</td></tr>";
                } else {
                    data.forEach(row => {
                        const tableRow = document.createElement("tr");
                        tableRow.innerHTML = `
                            <td>${row["Order_ID"]}</td>
                            <td>${row["User_ID"]}</td>
                            <td>${row["Date"]}</td>
                            <td>${row["Quantity"]}</td>
                            <td>${row["Price_Rs"]}</td>
                            <td>${row["Add_Line1"]}</td>
                            <td>${row["Add_Line2"]}</td>
                            <td>${row["needs_verification"]}</td>
                        `;
                        tableBody.appendChild(tableRow);
                    });
                }
            }

            // Filtering user table records
            function filterDataUser() {
                var userID = searchUserIDUser.value;

                const xhttp = new XMLHttpRequest();
                xhttp.onreadystatechange = function() {
                    if (this.readyState === 4 && this.status === 200) {
                        console.log(this.responseText); // Debugging line
                        const filteredData = JSON.parse(this.responseText);
                        updateTableUser(filteredData);
                    }
                };
                xhttp.open("POST", "filter_data_admin_user.php", true);
                xhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
                xhttp.send("userId=" + userID);
            }

            // Updating user table
            function updateTableUser(data) {
                const tableBody = document.getElementById("tableBodyUser");
                tableBody.innerHTML = "";

                if (data.length === 0) {
                    tableBody.innerHTML = "<tr><td colspan='8'>No records found based on your filter selection.</td></tr>";
                } else {
                    data.forEach(row => {
                        const tableRow = document.createElement("tr");
                        tableRow.innerHTML = `
                            <td>${row["User_ID"]}</td>
                            <td>${row["Email"]}</td>
                            <td>${row["F_Name"]}</td>
                            <td>${row["L_Name"]}</td>
                            <td>${row["U_Name"]}</td>
                            <td>${row["NIC"]}</td>
                            <td>${row["Tel_No"]}</td>
                        `;
                        tableBody.appendChild(tableRow);
                    });
                }
            }

            // Deleting order table records
            function deleteFilteredDataOrder() {
                if (confirm('Are you sure you want to delete the filtered records?')) {
                    const userId = searchUserIDOrder.value;
                    const selectedYear = yearSelectOrder.value;
                    const selectedMonth = monthSelectOrder.value;
                    const selectedDate = dateSelectOrder.value;

                    const xhttp = new XMLHttpRequest();
                    xhttp.onreadystatechange = function() {
                        if (this.readyState === 4 && this.status === 200) {
                            console.log(this.responseText); // Debugging line
                            const response = JSON.parse(this.responseText);
                            if (response.success) {
                                alert('Records deleted successfully');
                                filterDataOrder(); // Refresh the table data
                            } else {
                                alert('Failed to delete records: ' + response.message);
                            }
                        }
                    };
                    xhttp.open("POST", "delete_records_order.php", true);
                    xhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
                    xhttp.send("userId=" + userId + "&year=" + selectedYear + "&month=" + selectedMonth + "&date=" + selectedDate);
                }
            }

            // Setting to run filterData functions when given elements change
            searchUserIDOrder.addEventListener('change', filterDataOrder);
            yearSelectOrder.addEventListener('change', filterDataOrder);
            monthSelectOrder.addEventListener('change', filterDataOrder);
            dateSelectOrder.addEventListener('change', filterDataOrder);
            deleteButtonOrder.addEventListener('click', deleteFilteredDataOrder);

            searchUserIDUser.addEventListener('change', filterDataUser);

            filterDataOrder();
            filterDataUser();
        });
    </script>
</head>
<body>
    <h1>Welcome, Administrator.</h1>
    <a href="logout.php">Logout</a><br><br>

    <h3>Order Details</h3>

    <label for="txtUserID_SearchOrder">Enter User ID to select the order list:</label><br>
    <input type="text" name="userIdSearchOrder" id="txtUserID_SearchOrder"><br><br>
    <select id="yearOrder"></select>
    <select id="monthOrder"></select>
    <select id="dateOrder"></select>
    <button id="deleteButtonOrder">Delete Filtered Orders</button><br><br>

    <table>
        <thead>
            <tr>
                <th>Order ID</th>
                <th>User ID</th>
                <th>Date</th>
                <th>Quantity</th>
                <th>Price</th>
                <th>Address Line 1</th>
                <th>Address Line 2</th>
                <th>Verification Status</th>
            </tr>
        </thead>
        <tbody id="tableBodyOrder">
            <!-- Table content will be dynamically populated here -->
        </tbody>
    </table><br><br>

    <h3>User Details</h3>

    <label for="txtUserID_SearchUser">Enter User ID to select the user list:</label><br>
    <input type="text" name="userIdSearchUser" id="txtUserID_SearchUser"><br><br>

    <table>
        <thead>
            <tr>
                <th>User ID</th>
                <th>Email</th>
                <th>First Name</th>
                <th>Last Name</th>
                <th>Username</th>
                <th>NIC</th>
                <th>Telephone Number</th>
            </tr>
        </thead>
        <tbody id="tableBodyUser">
            <!-- Table content will be dynamically populated here -->
        </tbody>
    </table>
</body>
</html>
