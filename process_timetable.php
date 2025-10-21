<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'timetabler') {
    header("Location: login.php");
    exit();
}

// Dummy confirmation feature without changing database
if (isset($_GET['mark']) && isset($_GET['id'])) {
    $_SESSION['marked'][$_GET['id']] = true;
    echo "<script>alert('✅ Timetable marked as processed (temporary)!'); window.location='process_timetable.php';</script>";
}

// Get all timetable records
$result = $conn->query("SELECT t.timetable_id, c.course_name, u.unit_name, l.full_name AS lecturer, 
                               r.room_name, t.day_of_week, t.start_time, t.end_time
                        FROM timetables t
                        JOIN courses c ON t.course_id = c.course_id
                        JOIN units u ON t.unit_id = u.unit_id
                        JOIN lecturers l ON t.lecturer_id = l.lecturer_id
                        JOIN rooms r ON t.room_id = r.room_id
                        ORDER BY t.day_of_week, t.start_time");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Process Timetable</title>
    <style>
        body { font-family: 'Segoe UI'; margin: 40px; background: #f0f8f7; }
        h2 { color: #004d40; }
        table { width: 100%; border-collapse: collapse; background: #fff; margin-top: 15px; }
        th, td { padding: 10px; border: 1px solid #ccc; text-align: center; }
        th { background: #00695c; color: white; }
        .process-btn { background: #00897b; color: white; padding: 5px 10px; border-radius: 5px; text-decoration: none; }
        .process-btn:hover { background: #004d40; }
        .processed { color: green; font-weight: bold; }
        .pending { color: red; font-weight: bold; }
    </style>
</head>
<body>
<h2>⚙ Process Timetables</h2>

<table>
<tr>
    <th>Course</th>
    <th>Unit</th>
    <th>Lecturer</th>
    <th>Room</th>
    <th>Day</th>
    <th>Time</th>
    <th>Status</th>
    <th>Action</th>
</tr>

<?php while($row = $result->fetch_assoc()): 
    $id = $row['timetable_id'];
    $processed = isset($_SESSION['marked'][$id]);
?>
<tr>
    <td><?= htmlspecialchars($row['course_name']) ?></td>
    <td><?= htmlspecialchars($row['unit_name']) ?></td>
    <td><?= htmlspecialchars($row['lecturer']) ?></td>
    <td><?= htmlspecialchars($row['room_name']) ?></td>
    <td><?= htmlspecialchars($row['day_of_week']) ?></td>
    <td><?= htmlspecialchars($row['start_time']) ?> - <?= htmlspecialchars($row['end_time']) ?></td>
    <td class="<?= $processed ? 'processed' : 'pending' ?>">
        <?= $processed ? '✅ Processed' : '❌ Pending' ?>
    </td>
    <td>
        <?php if (!$processed): ?>
            <a href="?mark=1&id=<?= $id ?>" class="process-btn">Mark Processed</a>
        <?php else: ?>
            <span style="color: gray;">Done</span>
        <?php endif; ?>
    </td>
</tr>
<?php endwhile; ?>
</table>

</body>
</html>