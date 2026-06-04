const currentUser = JSON.parse(localStorage.getItem('kasiAdmin')) || null;

function checkAccess(requiredRole) {
    if (currentUser && (currentUser.role !== requiredRole && currentUser.role !== 'admin')) {
        document.body.innerHTML = '<div class="container mt-5 alert alert-danger">Access Denied</div>';
        return false;
    }
    return true;
}

document.addEventListener('DOMContentLoaded', function () {
    if (document.getElementById('adminSidebar')) {
        if (currentUser && (currentUser.role === 'dispatcher' || currentUser.role === 'moderator')) {
            const navUsers = document.getElementById('navUsers');
            if (navUsers) navUsers.style.display = 'none';
        }
        if (currentUser && currentUser.role !== 'admin') {
            const addBtn = document.getElementById('addUserBtn');
            if (addBtn) addBtn.style.display = 'none';
        }
    }
});