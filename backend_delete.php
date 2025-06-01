<?php
require_once '_db.php';


$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $stmt = $db->prepare("DELETE FROM reservations WHERE id = :id");
    $stmt->bindParam(':id', $_POST['id'], PDO::PARAM_INT);
    $success = $stmt->execute();

    if ($success) {
        $message = "✅ Reservation ID {$_POST['id']} deleted successfully.";
    } else {
        $message = "❌ Failed to delete reservation ID {$_POST['id']}.";
    }
}


$reservations = $db->query("SELECT id, name FROM reservations ORDER BY id ASC")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Delete Reservation</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; }
        form { max-width: 400px; background: #f7f7f7; padding: 20px; border-radius: 8px; }
        select, button { padding: 8px; width: 100%; margin-top: 10px; }
        .message { margin-top: 15px; font-weight: bold; }
    </style>
</head>
<body>
    <h2>Delete Reservation</h2>

    <?php if (!empty($message)): ?>
        <div class="message"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <form method="POST">
        <label for="id">Choose reservation to delete:</label>
        <select name="id" id="id" required>
            <option value="">-- Select reservation --</option>
            <?php foreach ($reservations as $res): ?>
                <option value="<?= $res['id'] ?>">
                    ID <?= $res['id'] ?> - <?= htmlspecialchars($res['name']) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <button type="submit">Delete</button>
    </form>
</body>
</html>
