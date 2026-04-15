<?php
require_once 'config/auth.php';
require_once 'controller/function.php';

requireRole('teller');

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $check_number = sanitizeInput($_POST['check_number']);
    $account_id = intval($_POST['account_id']);
    $amount = floatval($_POST['amount']);
    $payee_name = sanitizeInput($_POST['payee_name']);
    $check_date = $_POST['check_date'];
    
    if (empty($check_number) || empty($account_id) || empty($amount) || empty($payee_name) || empty($check_date)) {
        $error = 'Please fill all required fields';
    } else {
        $query = "INSERT INTO checks_clearing (check_number, account_id, amount, payee_name, check_date, status) 
                  VALUES ('$check_number', $account_id, $amount, '$payee_name', '$check_date', 'pending')";
        
        if ($conn->query($query)) {
            $success = 'Check submitted for clearing successfully';
        } else {
            $error = 'Error processing check: ' . $conn->error;
        }
    }
}

$checks_result = $conn->query("SELECT c.*, acc.account_number, cust.first_name, cust.last_name 
                              FROM checks_clearing c 
                              JOIN accounts acc ON c.account_id = acc.id 
                              JOIN customers cust ON acc.customer_id = cust.id 
                              ORDER BY c.created_at DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Check Clearing - Banking System</title>
    <link rel="stylesheet" href="assets/style.css">
    <script src="assets/script.js"></script>
</head>
<body>
    <div class="container-fluid">
        <nav class="navbar navbar-dark bg-dark">
            <div class="navbar-container">
                <div class="navbar-brand">Banking Management System</div>
                <div class="navbar-user">
                    <span><?php echo htmlspecialchars($_SESSION['full_name']); ?> (Teller)</span>
                    <a href="logout.php" class="btn btn-sm btn-danger">Logout</a>
                </div>
            </div>
        </nav>
        
        <div class="main-content">
            <aside class="sidebar">
                <nav class="sidebar-nav">
                    <ul>
                        <li><a href="dashboard.php">Dashboard</a></li>
                        <li><a href="customers_search.php">Search Customer</a></li>
                        <li><a href="deposit.php">Process Deposit</a></li>
                        <li><a href="withdrawal.php">Process Withdrawal</a></li>
                        <li><a href="check_clearing.php" class="active">Check Clearing</a></li>
                        <li><a href="transactions.php">Transactions</a></li>
                    </ul>
                </nav>
            </aside>
            
            <main class="content">
                <header class="content-header">
                    <h1>Check Clearing System</h1>
                </header>
                
                <?php if ($error): ?>
                    <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                <?php endif; ?>
                
                <?php if ($success): ?>
                    <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
                <?php endif; ?>
                
                <div class="form-section">
                    <h2>Submit Check for Clearing</h2>
                    <form method="POST" class="check-form">
                        <div class="form-row">
                            <div class="form-group">
                                <label for="check_number">Check Number *</label>
                                <input type="text" id="check_number" name="check_number" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label for="account_id">Account ID *</label>
                                <input type="number" id="account_id" name="account_id" class="form-control" required>
                            </div>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="amount">Amount ($) *</label>
                                <input type="number" id="amount" name="amount" class="form-control" min="0.01" step="0.01" required>
                            </div>
                            <div class="form-group">
                                <label for="payee_name">Payee Name *</label>
                                <input type="text" id="payee_name" name="payee_name" class="form-control" required>
                            </div>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="check_date">Check Date *</label>
                                <input type="date" id="check_date" name="check_date" class="form-control" required>
                            </div>
                        </div>
                        
                        <button type="submit" class="btn btn-primary">Submit for Clearing</button>
                    </form>
                </div>
                
                <div class="table-section" style="margin-top: 40px;">
                    <h2>Checks Clearing Records</h2>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Check #</th>
                                    <th>Account</th>
                                    <th>Customer</th>
                                    <th>Payee</th>
                                    <th>Amount</th>
                                    <th>Check Date</th>
                                    <th>Status</th>
                                    <th>Cleared Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($check = $checks_result->fetch_assoc()): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($check['check_number']); ?></td>
                                        <td><?php echo htmlspecialchars($check['account_number']); ?></td>
                                        <td><?php echo htmlspecialchars($check['first_name'] . ' ' . $check['last_name']); ?></td>
                                        <td><?php echo htmlspecialchars($check['payee_name']); ?></td>
                                        <td><?php echo formatCurrency($check['amount']); ?></td>
                                        <td><?php echo formatDate($check['check_date']); ?></td>
                                        <td><span class="badge badge-<?php echo $check['status'] == 'cleared' ? 'success' : 'warning'; ?>"><?php echo ucfirst($check['status']); ?></span></td>
                                        <td><?php echo $check['cleared_date'] ? formatDate($check['cleared_date']) : 'Pending'; ?></td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </main>
        </div>
    </div>
</body>
</html>
