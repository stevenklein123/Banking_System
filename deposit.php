<?php
require_once 'config/auth.php';
require_once 'controller/function.php';

requireRole('teller');

$error = '';
$success = '';
$account_id = $_GET['account_id'] ?? '';
$account_number = $_GET['account_number'] ?? '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $account_id = $_POST['account_id'] ?? '';
    $amount = $_POST['amount'] ?? '';
    $payment_method = $_POST['payment_method'] ?? '';
    $description = $_POST['description'] ?? '';
    
    if (empty($account_id) || empty($amount) || empty($payment_method)) {
        $error = 'Please fill all required fields';
    } else {
        $result = processDeposit(intval($account_id), floatval($amount), $payment_method, $_SESSION['user_id'], $description);
        if ($result['success']) {
            $success = 'Deposit processed successfully! Reference: ' . $result['reference_number'] . ' New Balance: ' . formatCurrency($result['new_balance']);
            $account_id = '';
            $account_number = '';
        } else {
            $error = $result['message'];
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Process Deposit - Banking System</title>
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
                        <li><a href="deposit.php" class="active">Process Deposit</a></li>
                        <li><a href="withdrawal.php">Process Withdrawal</a></li>
                        <li><a href="check_clearing.php">Check Clearing</a></li>
                        <li><a href="transactions.php">Transactions</a></li>
                    </ul>
                </nav>
            </aside>
            
            <main class="content">
                <header class="content-header">
                    <h1>Process Deposit</h1>
                </header>
                
                <?php if ($error): ?>
                    <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                <?php endif; ?>
                
                <?php if ($success): ?>
                    <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
                <?php endif; ?>
                
                <div class="form-section">
                    <form method="POST" class="transaction-form">
                        <div class="form-row">
                            <div class="form-group">
                                <label for="account_id">Account Number/ID *</label>
                                <input 
                                    type="text" 
                                    id="account_id" 
                                    name="account_id" 
                                    class="form-control" 
                                    value="<?php echo htmlspecialchars($account_id); ?>" 
                                    placeholder="Enter account ID (from search)"
                                    required
                                >
                            </div>
                            <div class="form-group">
                                <label for="amount">Amount ($) *</label>
                                <input 
                                    type="number" 
                                    id="amount" 
                                    name="amount" 
                                    class="form-control" 
                                    min="0.01" 
                                    step="0.01"
                                    placeholder="0.00"
                                    required
                                >
                            </div>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="payment_method">Payment Method *</label>
                                <select id="payment_method" name="payment_method" class="form-control" required>
                                    <option value="">Select Payment Method</option>
                                    <option value="cash">Cash</option>
                                    <option value="check">Check</option>
                                    <option value="transfer">Transfer</option>
                                    <option value="card">Card</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="description">Description/Notes</label>
                                <input 
                                    type="text" 
                                    id="description" 
                                    name="description" 
                                    class="form-control" 
                                    placeholder="Additional notes (optional)"
                                >
                            </div>
                        </div>
                        
                        <button type="submit" class="btn btn-primary">Process Deposit</button>
                        <a href="customers_search.php" class="btn btn-secondary">Search Customer</a>
                    </form>
                </div>
            </main>
        </div>
    </div>
    
    <script>
        // Simple form validation
        document.querySelector('.transaction-form').addEventListener('submit', function(e) {
            const accountId = document.getElementById('account_id').value.trim();
            const amount = parseFloat(document.getElementById('amount').value);
            
            if (!accountId) {
                alert('Please search for and enter a customer account first');
                e.preventDefault();
                return;
            }
            
            if (isNaN(amount) || amount <= 0) {
                alert('Please enter a valid amount greater than 0');
                e.preventDefault();
                return;
            }
        });
    </script>
</body>
</html>
