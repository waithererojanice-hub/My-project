<?php  
include 'db_connect.php';  
session_start();  
?>  
  
<!DOCTYPE html>  
<html lang="en">  
<head>  
<meta charset="UTF-8">  
<title>HOD Report — SmartScheduler</title>  
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>  
<style>  
    body {  
        margin: 0;  
        font-family: "Poppins", sans-serif;  
        background-color: #f4f7fc;  
    }  
    .sidebar {  
        width: 240px;  
        background-color: #0a58ca;  
        color: white;  
        height: 100vh;  
        position: fixed;  
        padding: 20px;  
    }  
    .sidebar h2 {  
        text-align: center;  
        margin-bottom: 30px;  
    }  
    .sidebar a {  
        display: block;  
        color: white;  
        text-decoration: none;  
        padding: 12px;  
        margin: 6px 0;  
        border-radius: 6px;  
        transition: background 0.3s;  
    }  
    .sidebar a:hover {  
        background-color: #084298;  
    }  
    .main {  
        margin-left: 260px;  
        padding: 20px 40px;  
    }  
    h1 {  
        color: #0a58ca;  
    }  
    .summary-cards {  
        display: flex;  
        gap: 20px;  
        margin-bottom: 30px;  
        flex-wrap: wrap;  
    }  
    .card {  
        flex: 1;  
        min-width: 200px;  
        background: white;  
        border-radius: 10px;  
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);  
        text-align: center;  
        padding: 20px;  
    }  
    .card h3 {  
        color: #0a58ca;  
        margin-bottom: 10px;  
    }  
    .card p {  
        font-size: 20px;  
        font-weight: bold;  
        color: #333;  
    }  
    table {  
        width: 100%;  
        border-collapse: collapse;  
        background: #fff;  
        border-radius: 10px;  
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);  
        margin-bottom: 40px;  
    }  
    th, td {  
        padding: 12px;  
        border: 1px solid #ddd;  
        text-align: center;  
    }  
    th {  
        background: #0a58ca;  
        color: white;  
    }  
    .chart-container {  
        width: 100%;  
        max-width: 800px;  
        margin: 0 auto 40px;  
        background: #fff;  
        padding: 20px;  
        border-radius: 10px;  
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);  
    }  
    .export-buttons {  
        text-align: right;  
        margin-bottom: 20px;  
    }  
    .export-buttons button {  
        background: #0a58ca;  
        color: #fff;  
        border: none;  
        padding: 10px 15px;  
        margin-left: 10px;  
        border-radius: 6px;  
        cursor: pointer;  
    }  
    .export-buttons button:hover {  
        background: #084298;  
    }  
</style>  
</head>  
<body>  
  
<div class="sidebar">  
    <h2>HOD Panel</h2>  
    <a href="hod_dashboard.php">🏠 Dashboard</a>  
    <a href="hod_report.php">📊 Department Report</a>  
    <a href="logout.php">🚪 Logout</a>  
</div>  
  
<div class="main">  
    <h1>📊 Department Report</h1>  

    <?php  
    // Fetch summary data  
    $totCourses = $conn->query("SELECT COUNT(*) AS total FROM courses")->fetch_assoc()['total'] ?? 0;  
    $totUnits = $conn->query("SELECT COUNT(*) AS total FROM units")->fetch_assoc()['total'] ?? 0;  
    $totLecturers = $conn->query("SELECT COUNT(*) AS total FROM lecturers")->fetch_assoc()['total'] ?? 0;  
    $totStudents = $conn->query("SELECT COUNT(*) AS total FROM students")->fetch_assoc()['total'] ?? 0;  
    ?>  

    <!-- Summary Cards -->  
    <div class="summary-cards">  
        <div class="card">  
            <h3>🏫 Total Courses</h3>  
            <p><?php echo $totCourses; ?></p>  
        </div>  
        <div class="card">  
            <h3>📚 Total Units</h3>  
            <p><?php echo $totUnits; ?></p>  
        </div>  
        <div class="card">  
            <h3>👨‍🏫 Total Lecturers</h3>  
            <p><?php echo $totLecturers; ?></p>  
        </div>  
        <div class="card">  
            <h3>👩‍🎓 Total Students</h3>  
            <p><?php echo $totStudents; ?></p>  
        </div>  
    </div>  

    <div class="export-buttons">  
        <button onclick="exportToExcel()">⬇ Export to Excel</button>  
        <button onclick="window.print()">🖨 Print / Save PDF</button>  
    </div>  

    <!-- Tabular Report -->  
    <table id="reportTable">  
        <tr>  
            <th>Course Name</th>  
            <th>Total Units</th>  
            <th>Total Lecturers</th>  
            <th>Total Students</th>  
        </tr>  

        <?php  
        $query = "  
            SELECT   
                c.course_name,  
                COUNT(DISTINCT u.unit_id) AS total_units,  
                COUNT(DISTINCT l.lecturer_id) AS total_lecturers,  
                COUNT(DISTINCT s.student_id) AS total_students  
            FROM courses c  
            LEFT JOIN units u ON c.course_id = u.course_id  
            LEFT JOIN lecturers l ON c.department_id = l.department_id  
            LEFT JOIN students s ON c.course_id = s.course_id  
            GROUP BY c.course_id  
        ";  
        $result = $conn->query($query);  

        if ($result->num_rows > 0) {  
            while ($row = $result->fetch_assoc()) {  
                echo "<tr>  
                        <td>{$row['course_name']}</td>  
                        <td>{$row['total_units']}</td>  
                        <td>{$row['total_lecturers']}</td>  
                        <td>{$row['total_students']}</td>  
                      </tr>";  
            }  
        } else {  
            echo "<tr><td colspan='4'>No records found.</td></tr>";  
        }  
        ?>  
    </table>  

    <!-- Bar Chart Section -->  
    <div class="chart-container">  
        <h2 style="text-align:center; color:#0a58ca;">📈 Timetable Approval Status</h2>  
        <canvas id="approvalChart"></canvas>  
    </div>  

    <?php  
    // Fetch data for the bar chart  
    $approvedCount = $conn->query("SELECT COUNT(*) AS total FROM timetables WHERE hod_approval='Approved'")->fetch_assoc()['total'] ?? 0;  
    $pendingCount = $conn->query("SELECT COUNT(*) AS total FROM timetables WHERE hod_approval!='Approved' OR hod_approval IS NULL")->fetch_assoc()['total'] ?? 0;  
    ?>  

</div>  

<script>  
// Bar Chart Configuration  
const ctx = document.getElementById('approvalChart');  
new Chart(ctx, {  
    type: 'bar',  
    data: {  
        labels: ['Approved Timetables', 'Pending Timetables'],  
        datasets: [{  
            label: 'Number of Timetables',  
            data: [<?php echo $approvedCount; ?>, <?php echo $pendingCount; ?>],  
            backgroundColor: ['#198754', '#dc3545'],  
            borderWidth: 1  
        }]  
    },  
    options: {  
        responsive: true,  
        plugins: {  
            legend: { display: false },  
        },  
        scales: {  
            y: {  
                beginAtZero: true,  
                ticks: { stepSize: 1 }  
            }  
        }  
    }  
});  

// Export to Excel Function  
function exportToExcel() {  
    let table = document.getElementById("reportTable");  
    let html = table.outerHTML;  
    let url = 'data:application/vnd.ms-excel,' + escape(html);  
    let link = document.createElement("a");  
    link.href = url;  
    link.setAttribute("download", "HOD_Report.xls");  
    link.click();  
}  
</script>  
</body>  
</html>