<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'dean') {
    header("Location: login.php");
    exit();
}

// ----------------- SUMMARY REPORT -----------------
$summary = [
    'departments' => 0,
    'courses' => 0,
    'lecturers' => 0,
    'students' => 0,
    'avg_courses_per_dept' => 0
];

$summaryQuery = "
SELECT 
    (SELECT COUNT(*) FROM departments) AS departments,
    (SELECT COUNT(*) FROM courses) AS courses,
    (SELECT COUNT(*) FROM lecturers) AS lecturers,
    (SELECT COUNT(*) FROM students) AS students
";
$sres = $conn->query($summaryQuery);
if ($sres && $sres->num_rows > 0) {
    $summary = $sres->fetch_assoc();
    if ($summary['departments'] > 0) {
        $summary['avg_courses_per_dept'] = round($summary['courses'] / $summary['departments'], 1);
    }
}

// ----------------- TABULAR REPORT -----------------
$query = "
SELECT d.department_name,
       COUNT(DISTINCT c.course_id) AS total_courses,
       COUNT(DISTINCT l.lecturer_id) AS total_lecturers,
       COUNT(DISTINCT s.student_id) AS total_students
FROM departments d
LEFT JOIN courses c ON d.department_id = c.department_id
LEFT JOIN lecturers l ON d.department_id = l.department_id
LEFT JOIN students s ON c.course_id = s.course_id
GROUP BY d.department_id, d.department_name
ORDER BY d.department_name
";
$result = $conn->query($query);

// Prepare data for Pie Chart
$chartLabels = [];
$chartData = [];
if ($result && $result->num_rows > 0) {
    // Store data for both table display and chart
    $rows = [];
    $result->data_seek(0);
    while ($r = $result->fetch_assoc()) {
        $rows[] = $r;
        $chartLabels[] = $r['department_name'];
        $chartData[] = $r['total_courses'];
    }
}
?>

<!DOCTYPE html>
<html lang='en'>
<head>
  <meta charset='UTF-8'>
  <title>Dean Department Report — SmartScheduler</title>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <style>
    body { font-family: 'Segoe UI', sans-serif; background: #f8f9fd; margin: 40px; }
    h1 { color: #004d40; }
    h2 { color: #006064; margin-top: 40px; }
    table { width: 100%; border-collapse: collapse; background: white; box-shadow: 0 2px 6px rgba(0,0,0,0.1); margin-bottom: 20px; }
    th, td { border: 1px solid #ccc; padding: 10px; text-align: center; }
    th { background-color: #006064; color: white; }
    tr:nth-child(even) { background: #f2f2f2; }
    .summary-grid { display: flex; gap: 20px; flex-wrap: wrap; margin-bottom: 30px; }
    .summary-box { flex: 1; min-width: 200px; background: #e0f2f1; border-radius: 10px; padding: 20px; text-align: center; box-shadow: 0 2px 6px rgba(0,0,0,0.1); }
    .summary-box h3 { margin: 0; color: #004d40; }
    .summary-box p { font-size: 22px; font-weight: bold; color: #00695c; margin-top: 5px; }
    .chart-container { width: 60%; margin: 0 auto; }
    .export-btns { margin-top: 20px; }
    .export-btns button { background: #006064; color: white; border: none; padding: 10px 16px; border-radius: 6px; cursor: pointer; margin-right: 10px; }
    .export-btns button:hover { background: #004d40; }
    a.back { display: inline-block; margin-top: 20px; padding: 10px 15px; background: #006064; color: white; border-radius: 5px; text-decoration: none; }
    a.back:hover { background: #00363a; }
  </style>
</head>
<body>

  <h1>🎓 Dean Department Report</h1>

  <!-- ✅ Summary Section -->
  <div class="summary-grid">
    <div class="summary-box">
      <h3>Total Departments</h3>
      <p><?= htmlspecialchars($summary['departments']) ?></p>
    </div>
    <div class="summary-box">
      <h3>Total Courses</h3>
      <p><?= htmlspecialchars($summary['courses']) ?></p>
    </div>
    <div class="summary-box">
      <h3>Total Lecturers</h3>
      <p><?= htmlspecialchars($summary['lecturers']) ?></p>
    </div>
    <div class="summary-box">
      <h3>Total Students</h3>
      <p><?= htmlspecialchars($summary['students']) ?></p>
    </div>
    <div class="summary-box">
      <h3>Avg. Courses per Department</h3>
      <p><?= htmlspecialchars($summary['avg_courses_per_dept']) ?></p>
    </div>
  </div>

  <!-- 🧾 Tabular Report -->
  <h2>📊 Department Breakdown</h2>
  <?php if (!empty($rows)): ?>
  <table>
    <tr>
      <th>Department</th>
      <th>Total Courses</th>
      <th>Total Lecturers</th>
      <th>Total Students</th>
    </tr>
    <?php foreach ($rows as $row): ?>
    <tr>
      <td><?= htmlspecialchars($row['department_name']) ?></td>
      <td><?= htmlspecialchars($row['total_courses']) ?></td>
      <td><?= htmlspecialchars($row['total_lecturers']) ?></td>
      <td><?= htmlspecialchars($row['total_students']) ?></td>
    </tr>
    <?php endforeach; ?>
  </table>
  <?php else: ?>
    <p>No department data found.</p>
  <?php endif; ?>

  <!-- 📈 Pie Chart -->
  <h2>🥧 Courses Distribution by Department</h2>
  <div class="chart-container">
    <canvas id="coursePie"></canvas>
  </div>

  <script>
    const ctx = document.getElementById('coursePie');
    new Chart(ctx, {
      type: 'pie',
      data: {
        labels: <?= json_encode($chartLabels) ?>,
        datasets: [{
          data: <?= json_encode($chartData) ?>,
          backgroundColor: [
            '#006064', '#00796b', '#00838f', '#0097a7', '#00acc1',
            '#26c6da', '#4dd0e1', '#80deea', '#b2ebf2', '#e0f7fa'
          ]
        }]
      },
      options: {
        responsive: true,
        plugins: {
          legend: { position: 'bottom' },
          title: { display: true, text: 'Courses per Department' }
        }
      }
    });
  </script>

  <!-- ⬇ Export Buttons -->
  <div class="export-btns">
    <form method="post" action="export_dean_report.php" style="display:inline;">
      <button type="submit">📄 Export to Excel</button>
    </form>
    <button onclick="window.print()">🖨 Print / Save as PDF</button>
  </div>

  <a href="dean_dashboard.php" class="back">⬅ Back to Dashboard</a>

</body>
</html>