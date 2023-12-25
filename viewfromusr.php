<?php
if (isset($_POST['start_date']) && isset($_POST['end_date'])) {
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];

    $sql = "SELECT * FROM `plug` WHERE `date` BETWEEN '$start_date' AND '$end_date' ORDER BY `id` DESC";
    $stmt = $pdo->query($sql);
    $tableData = $stmt->fetchAll();
}
?>

<!-- ส่วนของฟอร์ม HTML -->
<form method="post" action="">
    เลือกช่วงวันที่: <br>
    จาก: <input type="date" name="start_date">
    ถึง: <input type="date" name="end_date">
    <input type="submit" value="Submit">
</form>

<!-- ส่วนของตารางที่แสดงข้อมูล -->
<table class="table table-dark table-striped">
    <!-- ส่วนหัวของตาราง -->
    <!-- ส่วนเนื้อหาของตาราง -->
    <?php foreach ($tableData as $row): ?>
    <tr>
        <td><?php echo $row['date']; ?></td>
        <!-- แสดงข้อมูลอื่นๆ ตามที่คุณต้องการ -->
    </tr>
    <?php endforeach; ?>
</table>
