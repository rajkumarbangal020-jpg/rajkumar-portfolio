/**
 * Admin Panel JavaScript
 */
document.addEventListener('DOMContentLoaded', function () {
    // Delete confirmation helper
    const deleteButtons = document.querySelectorAll('.btn-delete-confirm');
    deleteButtons.forEach(btn => {
        btn.addEventListener('click', function (e) {
            if (!confirm('Are you sure you want to delete this record? This action cannot be undone.')) {
                e.preventDefault();
            }
        });
    });

    // Auto dismiss alerts
    const alerts = document.querySelectorAll('.alert-dismissible');
    alerts.forEach(alert => {
        setTimeout(() => {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        }, 5000);
    });
});
