# Banking Management System

A complete, interactive banking system built with PHP and MySQL for managing financial transactions, customer accounts, deposits, withdrawals, loans, and comprehensive reporting.

## System Overview

This Banking Management System provides a complete solution for simulating real-world banking operations with proper security, transaction tracking, and role-based access control.

### Key Features

- **User Authentication System**
  - Secure login with password hashing (bcrypt)
  - Role-based access (Admin, Teller)
  - Session management

- **Account Management**
  - Create new customer accounts with comprehensive verification
  - Generate unique account and customer IDs
  - Store customer details (name, IDs, signature reference)
  - View and manage account information

- **Deposit System**
  - Process deposits into customer accounts
  - Support cash, check, card, and transfer methods
  - Real-time balance updates
  - Complete transaction history

- **Withdrawal System**
  - Process withdrawals with balance validation
  - Support multiple payment methods
  - Record all withdrawal transactions
  - Prevent overdrafts

- **Loan Management**
  - Customer loan applications
  - Admin approval/rejection system
  - Collateral tracking
  - Interest rate calculation
  - Loan payment tracking

- **Transaction Processing**
  - Record all transactions with full details
  - Include transaction type, amount, date, and teller ID
  - Basic check clearing system
  - Transaction history and reporting

- **Check Clearing**
  - Submit checks for clearing
  - Track clearing status
  - Validation of check details

- **Auto-Debit System**
  - Automated payment deductions
  - Support for recurring transactions
  - Comprehensive logging

- **Reporting Dashboard**
  - View daily transactions
  - Monitor total deposits and withdrawals
  - Loan reports and summaries
  - Account balance overview
  - Account-affected metrics

- **Security Features**
  - Input validation and sanitization
  - Role-based access control
  - Transaction logging for auditing
  - Password hashing with bcrypt

## Technology Stack

- **Backend:** PHP 7.0+ (Core PHP, no framework)
- **Database:** MySQL 5.7+
- **Frontend:** HTML5, CSS3, JavaScript ES6+
- **Server:** Apache/Nginx

## Installation & Setup

### Prerequisites

- PHP 7.0 or higher
- MySQL 5.7 or higher
- Apache/Nginx web server
- XAMPP (recommended for local development)

### Step 1: Extract Files

Extract the project to your web directory:
```
C:\xampp\htdocs\BankingSystem
```

### Step 2: Create Database

1. Open phpMyAdmin (http://localhost/phpmyadmin)
2. Create a new database named `banking_system`
3. Import the SQL file:
   - Go to "Import" tab
   - Select `database_setup.sql` from your project folder
   - Click "Import"

Alternatively, run this SQL directly in phpMyAdmin:

```sql
-- Run the contents of database_setup.sql
```

### Step 3: Configure Database Connection

Edit `config/database.php` if using different database credentials:

```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'banking_system');
```

### Step 4: Access the System

1. Start Apache and MySQL (if using XAMPP)
2. Open browser and go to: `http://localhost/BankingSystem/`
3. You will be redirected to login page

### Default Credentials

**Admin Account:**
- Username: `admin`
- Password: `admin`

**Teller Account:**
- Username: `teller1`
- Password: `admin`

**Demo Customer:**
- Customer ID: `CUST000001`
- Name: Robert John Smith
- Email: robert@email.com
- Account Number: `ACC000001`
- Initial Balance: $5000.00

## Project Structure

```
BankingSystem/
├── assets/
│   ├── style.css              # Complete styling
│   └── script.js              # JavaScript interactivity
├── config/
│   ├── auth.php               # Authentication functions
│   └── database.php           # Database connection
├── controller/
│   └── function.php           # Core business functions
├── structure/
│   └── index.php              # Main redirect page
├── login.php                  # Login interface
├── dashboard.php              # Main dashboard
├── logout.php                 # Logout handler
├── customers.php              # Customer management (Admin)
├── customers_search.php       # Customer search (Teller)
├── accounts.php               # Account management (Admin)
├── deposit.php                # Deposit processing (Teller)
├── withdrawal.php             # Withdrawal processing (Teller)
├── loans.php                  # Loan management (Admin)
├── check_clearing.php         # Check clearing (Teller)
├── transactions.php           # Transaction history (Teller)
├── reports.php                # Reports & Analytics (Admin)
├── database_setup.sql         # SQL setup script
└── README.md                  # This file
```

## Database Schema

### Tables

1. **users** - Bank employees (admin, tellers)
2. **customers** - Customer information
3. **accounts** - Customer bank accounts
4. **transactions** - All financial transactions
5. **loans** - Loan applications and tracking
6. **loan_payments** - Loan payment records
7. **auto_debits** - Auto-debit configuration
8. **checks_clearing** - Check clearing records

## User Roles & Access

### Admin Access
- Dashboard with statistics
- Customer management
- Account management
- Loan approval/rejection
- Reports and analytics
- Transaction monitoring

### Teller Access
- Dashboard with recent transactions
- Search customers
- Process deposits
- Process withdrawals
- Check clearing
- View transaction history

## Features Walkthrough

### Admin Workflow

1. **Login** → Dashboard shows statistics
2. **Add Customers** → Fill customer form with complete details
3. **Manage Loans** → Review pending applications, approve/reject
4. **Monitor Finances** → View accounts and transactions
5. **Generate Reports** → Analyze daily activities

### Teller Workflow

1. **Login** → Dashboard with quick actions
2. **Search Customer** → Find customer by ID, email, or phone
3. **Process Deposit** → Add funds to account
4. **Process Withdrawal** → Verify balance and deduct funds
5. **Clear Checks** → Submit checks for clearing
6. **View History** → Track all processed transactions

## Core Functions

### Authentication
- `login($username, $password)` - User login
- `logout()` - User logout
- `isLoggedIn()` - Check session status
- `checkRole($role)` - Verify user role
- `requireLogin()` - Force login redirect
- `requireRole($role)` - Force role verification

### Customers
- `createCustomerAccount(...)` - Create new customer
- `getCustomerByID($id)` - Retrieve customer
- `getAllCustomers()` - List all customers
- `getCustomerAccounts($customer_id)` - Get customer's accounts

### Transactions
- `processDeposit($account_id, $amount, ...)` - Handle deposits
- `processWithdrawal($account_id, $amount, ...)` - Handle withdrawals
- `getAccountTransactions($account_id, $limit)` - Get transaction history
- `getTransactionsByDateRange($start, $end)` - Range queries

### Loans
- `createLoanApplication(...)` - Submit loan application
- `approveLoan($loan_id, $admin_id)` - Approve loan
- `rejectLoan($loan_id, $admin_id)` - Reject loan
- `recordLoanPayment($loan_id, $amount, ...)` - Track payments

## JavaScript Features

The system includes interactive JavaScript for:

- **Form Validation**
  - Email validation
  - Phone number validation
  - Amount validation
  - Required field checking

- **User Experience**
  - Real-time form validation
  - Dynamic error messages
  - Success/error alerts
  - Table filtering
  - Data export to CSV

- **Utilities**
  - Currency formatting
  - Date formatting
  - Confirm dialogs
  - Loading states

## Security Considerations

1. **Password Security**
   - Passwords are hashed using bcrypt
   - Never stored in plain text

2. **Input Validation**
   - All user inputs are sanitized
   - SQL injection prevented
   - XSS protection enabled

3. **Access Control**
   - Role-based permissions
   - Session verification on each page
   - Role-specific redirects

4. **Transaction Safety**
   - Database transactions for deposits/withdrawals
   - Rollback on errors
   - Balance verification

5. **Audit Trail**
   - All transactions logged
   - Teller ID recorded
   - Transaction timestamps
   - Reference numbers for tracking

## Common Tasks

### Change Password (Admin)

Passwords are set during user creation. To change:
1. Update users table directly or create user management page

### Monthly Reports

1. Go to Reports page
2. Select date range
3. View detailed statistics
4. Export as needed

### Process Large Deposit

1. Search customer
2. Enter amount
3. Submit
4. System auto-updates balance
5. Transaction recorded with reference

### Create New Customer

1. Admin → Customers page
2. Fill form completely
3. Submit
4. Customer ID and Account Number generated
5. Account created with $0 balance

## Troubleshooting

### Login Issues
- Ensure database connection is configured
- Check user exists in database
- Verify password is correct

### Permission Denied
- Check user role (Admin vs Teller)
- Verify session is active
- Login again if session expired

### Database Errors
- Verify MySQL is running
- Check database name is `banking_system`
- Ensure all tables exist

### Balance Issues
- Check transaction history
- Verify all deposits/withdrawals recorded
- Review account status (active/frozen)

## Future Enhancements

- Email notifications for transactions
- SMS alerts for large transactions
- Advanced analytics and charts
- API for mobile app integration
- AES encryption for sensitive data
- Two-factor authentication
- Account freezing/closure
- Interest calculations
- Account overdraft protection
- Transaction reversal system

## Best Practices

1. **Backup Database Regularly**
   - Export database monthly
   - Keep backups secure

2. **Monitor Transactions**
   - Review reports daily
   - Watch for suspicious activity

3. **User Management**
   - Review active users regularly
   - Disable unused accounts
   - Update passwords periodically

4. **System Maintenance**
   - Keep PHP updated
   - Update MySQL regularly
   - Monitor server logs

## Support & Documentation

For additional help:
- Review the code comments
- Check function documentation in `controller/function.php`
- Review database schema in `database_setup.sql`
- Test with demo accounts

## License

This project is provided as-is for educational and demonstration purposes.

## Author

Banking Management System - Demo Version

---

**System Version:** 1.0
**Last Updated:** 2026
**Database Version:** MySQL 5.7+
