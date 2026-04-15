# SETUP GUIDE - Banking Management System

## Quick Start (5 Minutes)

### Step 1: Import Database
1. Open **phpMyAdmin** at `http://localhost/phpmyadmin`
2. Click **"New"** database
3. Name: `banking_system` → Click **"Create"**
4. Go to **"Import"** tab
5. Select file: `database_setup.sql` from your BankingSystem folder
6. Click **"Import"**

### Step 2: Verify Installation
1. Open browser: `http://localhost/BankingSystem/`
2. Should redirect to login page

### Step 3: Login with Demo Accounts

**Admin Account:**
```
Username: admin
Password: admin
```

**Teller Account:**
```
Username: teller1  
Password: admin
```

## File Structure

```
BankingSystem/
├── config/           # Configuration files
├── controller/       # Business logic
├── assets/          # CSS & JavaScript
├── *.php            # Page files
├── database_setup.sql    # Database schema
└── README.md        # Full documentation
```

## Default Database Credentials

Edit `config/database.php` if different:

```
Host: localhost
User: root
Password: (empty)
Database: banking_system
```

## Testing

### Admin Access
1. Login with `admin` / `admin`
2. View Dashboard with statistics
3. Go to "Customers" → Add new customer
4. Go to "Loans" → View pending applications
5. Go to "Reports" → View transaction reports

### Teller Access
1. Login with `teller1` / `admin`
2. Click "Search Customer"
3. Search: `CUST000001` (demo customer)
4. Process a deposit or withdrawal
5. View transaction history

## Demo Customer Info

**For Testing Deposits/Withdrawals:**

```
Customer ID: CUST000001
Name: Robert John Smith
Email: robert@email.com
Account Number: ACC000001
Current Balance: $5000.00
Account Type: Savings
```

## Common Issues

### Issue: Page shows blank/error
**Solution:** 
- Check PHP error log
- Verify `config/database.php` settings
- Restart Apache and MySQL

### Issue: Can't login
**Solution:**
- Clear browser cache
- Verify database is running
- Check MySQL is started

### Issue: Tables don't exist
**Solution:**
- Return to Step 1 (Import Database)
- Or manually import using MySQL command line:
  ```sql
  mysql -u root banking_system < database_setup.sql
  ```

## Security Notes

⚠️ **For Development Only**

- Change demo passwords after setup
- Use strong passwords in production
- Implement HTTPS
- Add firewall rules
- Regular backups

## System Features at a Glance

✓ User Authentication
✓ Account Management
✓ Deposits & Withdrawals
✓ Loan System
✓ Transaction History
✓ Check Clearing
✓ Comprehensive Reports
✓ Role-Based Access
✓ Input Validation
✓ Transaction Security

## Troubleshooting Checklist

- [ ] MySQL is running
- [ ] Apache is running
- [ ] Database `banking_system` exists
- [ ] All tables imported successfully
- [ ] `config/database.php` has correct credentials
- [ ] File permissions allow reading
- [ ] PHP version 7.0+
- [ ] Browser cache cleared

## What to Do Next

1. **Explore Features** - Test both Admin and Teller roles
2. **Create More Customers** - Test the system limits
3. **Process Transactions** - Try deposits & withdrawals
4. **Manage Loans** - Test approval workflow
5. **Review Reports** - Check transaction history

## Support

See README.md for:
- Complete feature documentation
- Database schema details
- Development guidelines
- Future enhancements

---

**Status:** Ready to Use ✓
**Version:** 1.0
**Last Updated:** 2026
