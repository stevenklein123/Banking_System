<?php
session_start();
require_once "db.php";

$_SESSION['user_id'] = 1;

$user_id = $_SESSION['user_id'];

try {

    $stmt = $conn->prepare("SELECT id FROM accounts WHERE user_id = :user_id");
    $stmt->bindParam(":user_id", $user_id, PDO::PARAM_INT);
    $stmt->execute();

    $account = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$account) {
        die("Account not found.");
    }

    $account_id = $account['id'];

    $stmt = $conn->prepare("
        SELECT type, amount, date 
        FROM transactions 
        WHERE account_id = :account_id 
        ORDER BY date DESC
    ");

    $stmt->bindParam(":account_id", $account_id, PDO::PARAM_INT);
    $stmt->execute();

    $transactions = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Transaction History</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="container">
    <h2>Transaction History</h2>

    <table>
        <tr>
            <th>Type</th>
            <th>Amount</th>
            <th>Date</th>
        </tr>

        <?php if (!empty($transactions)): ?>
            <?php foreach ($transactions as $t): ?>
                <tr>
                    <td class="<?php echo htmlspecialchars($t['type']); ?>">
                        <?php echo strtoupper(htmlspecialchars($t['type'])); ?>
                    </td>
                    <td>
                        ₱<?php echo number_format($t['amount'], 2); ?>
                    </td>
                    <td>
                        <?php echo $t['date']; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="3">No transactions yet.</td>
            </tr>
        <?php endif; ?>

    </table>

    <a class="back" href="dashboard.php">← Back to Dashboard</a>
</div>

</body>
</html>