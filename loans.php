<?php
require_once 'config/auth.php';
require_once 'controller/function.php';

requireRole('admin');

$error = '';
$success = '';

// Handle loan approval/rejection
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'] ?? '';
    $loan_id = $_POST['loan_id'] ?? '';
    
    if ($action == 'approve') {
        $result = approveLoan(intval($loan_id), $_SESSION['user_id']);
        if ($result['success']) {
            $success = $result['message'];
        } else {
            $error = $result['message'];
        }
    } elseif ($action == 'reject') {
        $result = rejectLoan(intval($loan_id), $_SESSION['user_id']);
        if ($result['success']) {
            $success = $result['message'];
        } else {
            $error = $result['message'];
        }
    }
}

$pending_loans = getPendingLoans();
$all_loans = getAllLoans();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Loans - Banking System</title>
    <link rel="stylesheet" href="assets/style.css">
    <script src="assets/script.js"></script>
</head>
<body>
    <div class="container-fluid">
        <nav class="navbar navbar-dark bg-dark">
            <div class="navbar-container">
                <div class="navbar-brand">Banking Management System</div>
                <div class="navbar-user">
                    <span><?php echo htmlspecialchars($_SESSION['full_name']); ?> (Admin)</span>
                    <a href="logout.php" class="btn btn-sm btn-danger">Logout</a>
                </div>
            </div>
        </nav>
        
        <div class="main-content">
            <aside class="sidebar">
                <nav class="sidebar-nav">
                    <ul>
                        <li><a href="dashboard.php">Dashboard</a></li>
                        <li><a href="customers.php">Customers</a></li>
                        <li><a href="accounts.php">Accounts</a></li>
                        <li><a href="loans.php" class="active">Loans</a></li>
                        <li><a href="reports.php">Reports</a></li>
                    </ul>
                </nav>
            </aside>
            
            <main class="content">
                <header class="content-header">
                    <h1>Loan Management</h1>
                </header>
                
                <?php if ($error): ?>
                    <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                <?php endif; ?>
                
                <?php if ($success): ?>
                    <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
                <?php endif; ?>
                
                <div class="table-section">
                    <h2>Pending Loan Applications</h2>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Loan #</th>
                                    <th>Customer</th>
                                    <th>Amount</th>
                                    <th>Interest Rate</th>
                                    <th>Term (months)</th>
                                    <th>Purpose</th>
                                    <th>Applied Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($loan = $pending_loans->fetch_assoc()): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($loan['loan_number']); ?></td>
                                        <td><?php echo htmlspecialchars($loan['first_name'] . ' ' . $loan['last_name']); ?></td>
                                        <td><?php echo formatCurrency($loan['principal_amount']); ?></td>
                                        <td><?php echo $loan['interest_rate'] . '%'; ?></td>
                                        <td><?php echo $loan['loan_term_months']; ?></td>
                                        <td><?php echo htmlspecialchars($loan['purpose']); ?></td>
                                        <td><?php echo formatDate($loan['created_at']); ?></td>
                                        <td>
                                            <form method="POST" style="display: inline;">
                                                <input type="hidden" name="loan_id" value="<?php echo $loan['id']; ?>">
                                                <button type="submit" name="action" value="approve" class="btn btn-sm btn-success">Approve</button>
                                                <button type="submit" name="action" value="reject" class="btn btn-sm btn-danger">Reject</button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                
                <div class="table-section" style="margin-top: 40px;">
                    <h2>All Loans</h2>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Loan #</th>
                                    <th>Customer</th>
                                    <th>Amount</th>
                                    <th>Interest Rate</th>
                                    <th>Term</th>
                                    <th>Collateral Value</th>
                                    <th>Status</th>
                                    <th>Start Date</th>
                                    <th>End Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($loan = $all_loans->fetch_assoc()): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($loan['loan_number']); ?></td>
                                        <td><?php echo htmlspecialchars($loan['first_name'] . ' ' . $loan['last_name']); ?></td>
                                        <td><?php echo formatCurrency($loan['principal_amount']); ?></td>
                                        <td><?php echo $loan['interest_rate'] . '%'; ?></td>
                                        <td><?php echo $loan['loan_term_months'] . ' months'; ?></td>
                                        <td><?php echo formatCurrency($loan['collateral_value'] ?? 0); ?></td>
                                        <td><span class="badge badge-<?php echo $loan['status'] == 'approved' ? 'success' : ($loan['status'] == 'pending' ? 'warning' : 'danger'); ?>"><?php echo ucfirst($loan['status']); ?></span></td>
                                        <td><?php echo $loan['start_date'] ? formatDate($loan['start_date']) : 'N/A'; ?></td>
                                        <td><?php echo $loan['end_date'] ? formatDate($loan['end_date']) : 'N/A'; ?></td>
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
