# Installation Verification Checklist

Use this checklist to verify your Banking Management System is properly installed and ready to use.

## Pre-Installation Requirements

- [ ] Apache web server installed and running
- [ ] MySQL/MariaDB installed and running
- [ ] PHP 7.0+ installed
- [ ] phpMyAdmin available (optional but recommended)
- [ ] Project extracted to `C:\xampp\htdocs\BankingSystem`

## Database Setup

- [ ] Database `banking_system` created
- [ ] `database_setup.sql` imported successfully
- [ ] All 8 tables exist:
  - [ ] `users`
  - [ ] `customers`
  - [ ] `accounts`
  - [ ] `transactions`
  - [ ] `loans`
  - [ ] `loan_payments`
  - [ ] `auto_debits`
  - [ ] `checks_clearing`
- [ ] Sample data inserted (admin, teller, customer)
- [ ] Indexes created

## Configuration Verification

- [ ] `config/database.php` exists
- [ ] Database credentials are correct:
  - [ ] Host: `localhost`
  - [ ] User: `root`
  - [ ] Password: (blank or your password)
  - [ ] Database: `banking_system`
- [ ] File permissions allow reading (777 or 755)
- [ ] No PHP errors in error logs

## File Integrity

Essential files that must exist:
- [ ] `login.php`
- [ ] `dashboard.php`
- [ ] `logout.php`
- [ ] `customers.php`
- [ ] `customers_search.php`
- [ ] `deposit.php`
- [ ] `withdrawal.php`
- [ ] `loans.php`
- [ ] `accounts.php`
- [ ] `check_clearing.php`
- [ ] `transactions.php`
- [ ] `reports.php`
- [ ] `config/database.php`
- [ ] `config/auth.php`
- [ ] `controller/function.php`
- [ ] `assets/style.css`
- [ ] `assets/script.js`

## Functionality Tests

### Test 1: Login Page
- [ ] Navigate to `http://localhost/BankingSystem/`
- [ ] Redirects to login page
- [ ] Page loads without errors
- [ ] CSS styling applied (blue gradient, professional look)
- [ ] Demo credentials displayed

### Test 2: Admin Login
- [ ] Username: `admin`
- [ ] Password: `admin`
- [ ] Successfully logs in
- [ ] Redirects to dashboard
- [ ] Statistics displayed
- [ ] Sidebar shows "Customers, Accounts, Loans, Reports"

### Test 3: Teller Login
- [ ] Logout from admin account
- [ ] Username: `teller1`
- [ ] Password: `admin`
- [ ] Successfully logs in
- [ ] Dashboard shows recent transactions
- [ ] Sidebar shows "Search Customer, Deposit, Withdrawal, etc."

### Test 4: Customer Creation (Admin)
- [ ] Click "Customers" in sidebar
- [ ] Form displays with all fields
- [ ] Fill in test customer data
- [ ] Click "Create Customer Account"
- [ ] Success message appears
- [ ] New customer appears in list
- [ ] Unique Customer ID generated

### Test 5: Customer Search (Teller)
- [ ] Click "Search Customer"
- [ ] Enter `CUST000001` (demo customer)
- [ ] Customer details display
- [ ] Account information shows
- [ ] Current balance: $5000.00
- [ ] Links to Deposit/Withdrawal available

### Test 6: Deposit Processing
- [ ] From customer search, click "Process Deposit"
- [ ] Account ID pre-filled
- [ ] Enter amount (e.g., 500)
- [ ] Select payment method
- [ ] Submit
- [ ] Success message with reference number
- [ ] Balance updated

### Test 7: Withdrawal Processing
- [ ] Process withdrawal of $200
- [ ] Verify balance updated (5000 + 500 - 200 = 5300)
- [ ] Transaction recorded
- [ ] Reference number generated

### Test 8: Loan Management (Admin)
- [ ] Click "Loans"
- [ ] Show pending applications
- [ ] Approve/Reject buttons functional
- [ ] All loans displayed with status

### Test 9: Reports (Admin)
- [ ] Click "Reports"
- [ ] Displays statistics
- [ ] Table showing transactions
- [ ] Date range filter works
- [ ] Transactions display correctly

### Test 10: Check Clearing (Teller)
- [ ] Click "Check Clearing"
- [ ] Form displays
- [ ] Submit check details
- [ ] Check appears in records
- [ ] Status shows "pending"

## Browser Compatibility

Test in different browsers:
- [ ] Chrome - OK
- [ ] Firefox - OK
- [ ] Edge - OK
- [ ] Safari - OK

## Responsive Design

- [ ] Desktop (1920x1080) - Looks good
- [ ] Tablet (768x1024) - Responsive
- [ ] Mobile (375x812) - Mobile-friendly

## JavaScript Functionality

- [ ] Form validation works
- [ ] Error messages display
- [ ] Alerts auto-dismiss
- [ ] Tables display correctly
- [ ] Buttons are clickable
- [ ] Links navigate correctly

## Security Checks

- [ ] Password is bcrypt hashed
- [ ] Session expires after logout
- [ ] Cannot access pages without login
- [ ] Cannot access admin pages as teller
- [ ] Cannot access teller pages as admin
- [ ] SQL queries sanitized

## Performance Checks

- [ ] Pages load within 2 seconds
- [ ] Table displays 50+ records smoothly
- [ ] No console errors in browser developer tools
- [ ] CSS and JS load without errors

## Data Integrity

- [ ] Transactions record correctly
- [ ] Balance updates accurately
- [ ] Multiple transactions sum correctly
- [ ] Insufficient funds error works
- [ ] Reference numbers are unique

## Final Verification

After all checks pass:

- [ ] System is fully functional
- [ ] All features working
- [ ] No errors or warnings
- [ ] Professional appearance
- [ ] Ready for use

## If Something Fails

1. **Login Page Won't Load**
   - Check database connection in `config/database.php`
   - Verify MySQL is running
   - Check file permissions

2. **Login Fails**
   - Verify `users` table has data
   - Check password is: `admin`
   - Clear browser cache

3. **Missing Data**
   - Re-import `database_setup.sql`
   - Verify all tables exist
   - Check for SQL import errors

4. **CSS Not Loading**
   - Verify `assets/style.css` exists
   - Check file path is correct
   - Clear browser cache

5. **JavaScript Not Working**
   - Verify `assets/script.js` exists
   - Check browser console for errors
   - Ensure JavaScript is enabled

## Support Resources

- See `README.md` for full documentation
- See `SETUP.md` for quick setup
- See `PROJECT_INVENTORY.md` for file listing
- Check PHP error logs: `C:\xampp\apache\logs\`
- Check MySQL error logs: `C:\xampp\mysql\data\`

---

**Verification Complete!** ✓

When all items are checked, your Banking Management System is ready to use.

System Status: **READY FOR PRODUCTION** (after security hardening)
