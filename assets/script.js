// ==================== FORM VALIDATION ==================== */

/**
 * Validate email format
 */
function validateEmail(email) {
    const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return re.test(email);
}

/**
 * Validate phone number (10-15 digits)
 */
function validatePhone(phone) {
    const re = /^[0-9]{10,15}$/;
    return re.test(phone.replace(/\D/g, ''));
}

/**
 * Validate amount (greater than 0)
 */
function validateAmount(amount) {
    return !isNaN(amount) && amount > 0;
}

/**
 * Validate account number
 */
function validateAccountNumber(accountNumber) {
    return accountNumber && accountNumber.trim().length > 0;
}

/**
 * General form validation
 */
function validateForm(formId) {
    const form = document.getElementById(formId);
    if (!form) return false;
    
    let isValid = true;
    const inputs = form.querySelectorAll('input[required], select[required], textarea[required]');
    
    inputs.forEach(input => {
        if (!input.value.trim()) {
            showFieldError(input, 'This field is required');
            isValid = false;
        } else {
            clearFieldError(input);
        }
    });
    
    return isValid;
}

/**
 * Show field error
 */
function showFieldError(field, message) {
    field.classList.add('error');
    field.style.borderColor = '#dc3545';
    
    let errorDiv = field.parentElement.querySelector('.error-message');
    if (!errorDiv) {
        errorDiv = document.createElement('div');
        errorDiv.className = 'error-message';
        field.parentElement.appendChild(errorDiv);
    }
    errorDiv.textContent = message;
    errorDiv.style.color = '#dc3545';
    errorDiv.style.fontSize = '12px';
    errorDiv.style.marginTop = '5px';
}

/**
 * Clear field error
 */
function clearFieldError(field) {
    field.classList.remove('error');
    field.style.borderColor = '';
    
    const errorDiv = field.parentElement.querySelector('.error-message');
    if (errorDiv) {
        errorDiv.remove();
    }
}

/**
 * Validate customer form
 */
function validateCustomerForm() {
    const email = document.getElementById('email');
    const phone = document.getElementById('phone');
    const dob = document.getElementById('dob');
    
    let isValid = true;
    
    // Validate email
    if (email && email.value && !validateEmail(email.value)) {
        showFieldError(email, 'Please enter a valid email address');
        isValid = false;
    } else if (email) {
        clearFieldError(email);
    }
    
    // Validate phone
    if (phone && phone.value && !validatePhone(phone.value)) {
        showFieldError(phone, 'Please enter a valid phone number (10-15 digits)');
        isValid = false;
    } else if (phone) {
        clearFieldError(phone);
    }
    
    // Validate birth date
    if (dob && dob.value) {
        const age = new Date().getFullYear() - new Date(dob.value).getFullYear();
        if (age < 18) {
            showFieldError(dob, 'Customer must be at least 18 years old');
            isValid = false;
        } else {
            clearFieldError(dob);
        }
    }
    
    return isValid;
}

/**
 * Validate transaction form
 */
function validateTransactionForm() {
    const accountId = document.getElementById('account_id');
    const amount = document.getElementById('amount');
    
    let isValid = true;
    
    // Validate account ID
    if (!validateAccountNumber(accountId.value)) {
        showFieldError(accountId, 'Please enter a valid account ID');
        isValid = false;
    } else {
        clearFieldError(accountId);
    }
    
    // Validate amount
    if (!validateAmount(amount.value)) {
        showFieldError(amount, 'Please enter a valid amount greater than 0');
        isValid = false;
    } else {
        clearFieldError(amount);
    }
    
    return isValid;
}

/**
 * Format currency display
 */
function formatCurrency(amount) {
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'USD'
    }).format(amount);
}

/**
 * Format date display
 */
function formatDate(dateString) {
    const options = { year: 'numeric', month: 'short', day: 'numeric' };
    return new Date(dateString).toLocaleDateString('en-US', options);
}

/**
 * Show success message
 */
function showSuccessMessage(message) {
    const alertDiv = document.createElement('div');
    alertDiv.className = 'alert alert-success';
    alertDiv.textContent = message;
    
    const mainContent = document.querySelector('.content');
    if (mainContent) {
        mainContent.insertBefore(alertDiv, mainContent.firstChild);
        setTimeout(() => alertDiv.remove(), 5000);
    }
}

/**
 * Show error message
 */
function showErrorMessage(message) {
    const alertDiv = document.createElement('div');
    alertDiv.className = 'alert alert-danger';
    alertDiv.textContent = message;
    
    const mainContent = document.querySelector('.content');
    if (mainContent) {
        mainContent.insertBefore(alertDiv, mainContent.firstChild);
        setTimeout(() => alertDiv.remove(), 5000);
    }
}

/**
 * Confirm action
 */
function confirmAction(message) {
    return confirm(message || 'Are you sure you want to proceed?');
}

/**
 * Table search/filter functionality
 */
function filterTable(inputId, tableId) {
    const input = document.getElementById(inputId);
    const table = document.getElementById(tableId);
    
    if (!input || !table) return;
    
    input.addEventListener('keyup', function() {
        const filter = this.value.toLowerCase();
        const rows = table.getElementsByTagName('tbody')[0].getElementsByTagName('tr');
        
        Array.from(rows).forEach(row => {
            const text = row.textContent.toLowerCase();
            row.style.display = text.includes(filter) ? '' : 'none';
        });
    });
}

/**
 * Export table to CSV
 */
function exportTableToCSV(tableId, filename) {
    const table = document.getElementById(tableId);
    if (!table) return;
    
    let csv = [];
    const rows = table.querySelectorAll('tr');
    
    rows.forEach(row => {
        const cols = row.querySelectorAll('td, th');
        const csvRow = Array.from(cols).map(col => {
            let text = col.innerText.trim();
            // Escape quotes
            text = text.replace(/"/g, '""');
            return `"${text}"`;
        }).join(',');
        csv.push(csvRow);
    });
    
    const csvContent = csv.join('\n');
    const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement('a');
    
    if (link.download !== undefined) {
        const url = URL.createObjectURL(blob);
        link.setAttribute('href', url);
        link.setAttribute('download', filename || 'export.csv');
        link.style.visibility = 'hidden';
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    }
}

/**
 * Print functionality
 */
function printContent(elementId) {
    const element = document.getElementById(elementId) || document;
    const printWindow = window.open('', '', 'height=600,width=800');
    
    let content = elementId ? element.innerHTML : document.body.innerHTML;
    
    printWindow.document.write('<html><head><title>Print</title>');
    printWindow.document.write('<link rel="stylesheet" href="assets/style.css">');
    printWindow.document.write('</head><body>');
    printWindow.document.write(content);
    printWindow.document.write('</body></html>');
    printWindow.document.close();
    
    setTimeout(() => printWindow.print(), 250);
}

/**
 * Debounce function for search
 */
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

/**
 * Real-time search
 */
function setupRealTimeSearch(inputId, resultsId, searchFunction) {
    const input = document.getElementById(inputId);
    const results = document.getElementById(resultsId);
    
    if (!input || !results) return;
    
    const debouncedSearch = debounce(function() {
        const query = input.value.trim();
        if (query.length > 2) {
            searchFunction(query, results);
        } else {
            results.innerHTML = '';
        }
    }, 300);
    
    input.addEventListener('keyup', debouncedSearch);
}

/**
 * Highlight search terms
 */
function highlightSearchTerms(text, term) {
    const regex = new RegExp(`(${term})`, 'gi');
    return text.replace(regex, '<mark>$1</mark>');
}

/**
 * Format number with commas
 */
function formatNumber(num) {
    return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',');
}

/**
 * Add loading state to button
 */
function setButtonLoading(buttonId, loading = true) {
    const button = document.getElementById(buttonId);
    if (!button) return;
    
    if (loading) {
        button.setAttribute('disabled', 'disabled');
        button.setAttribute('data-original-text', button.textContent);
        button.textContent = 'Loading...';
        button.classList.add('loading');
    } else {
        button.removeAttribute('disabled');
        button.textContent = button.getAttribute('data-original-text');
        button.classList.remove('loading');
    }
}

/**
 * Initialize tooltips
 */
function initTooltips() {
    const tooltips = document.querySelectorAll('[data-tooltip]');
    tooltips.forEach(element => {
        element.addEventListener('mouseenter', function() {
            const text = this.getAttribute('data-tooltip');
            const tooltip = document.createElement('div');
            tooltip.className = 'tooltip';
            tooltip.textContent = text;
            tooltip.style.cssText = `
                position: absolute;
                background: #333;
                color: white;
                padding: 8px 12px;
                border-radius: 4px;
                font-size: 12px;
                z-index: 1000;
                white-space: nowrap;
                bottom: 100%;
                left: 50%;
                transform: translateX(-50%);
                margin-bottom: 8px;
            `;
            document.body.appendChild(tooltip);
            
            this.addEventListener('mouseleave', () => tooltip.remove());
        });
    });
}

/**
 * Initialize all event listeners
 */
document.addEventListener('DOMContentLoaded', function() {
    // Initialize form validations
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            if (this.classList.contains('customer-form')) {
                if (!validateForm(this.id) || !validateCustomerForm()) {
                    e.preventDefault();
                }
            } else if (this.classList.contains('transaction-form')) {
                if (!validateTransactionForm()) {
                    e.preventDefault();
                }
            }
        });
    });
    
    // Initialize tooltips
    initTooltips();
    
    // Auto-hide alerts after 5 seconds
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.style.opacity = '0';
            alert.style.transition = 'opacity 0.3s ease';
            setTimeout(() => alert.remove(), 300);
        }, 5000);
    });
    
    // Add smooth scrolling
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            const href = this.getAttribute('href');
            if (href !== '#') {
                e.preventDefault();
                const target = document.querySelector(href);
                if (target) {
                    target.scrollIntoView({ behavior: 'smooth' });
                }
            }
        });
    });
});

/**
 * Export for use in other scripts
 */
if (typeof module !== 'undefined' && module.exports) {
    module.exports = {
        validateEmail,
        validatePhone,
        validateAmount,
        formatCurrency,
        formatDate,
        confirmAction,
        exportTableToCSV,
        printContent
    };
}
