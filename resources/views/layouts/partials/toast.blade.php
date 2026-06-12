<div id="toast-container" class="toast-container"></div>

<style>
.toast-container {
    position: fixed;
    top: 20px;
    right: 20px;
    z-index: 9999;
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.custom-toast {
    min-width: 300px;
    background-color: #fff;
    border-radius: 8px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    padding: 15px 20px;
    color: #333;
    font-family: 'Inter', sans-serif;
    font-size: 14px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    opacity: 0;
    transform: translateY(-20px);
    transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    border-left: 5px solid #ccc;
}

.custom-toast.show {
    opacity: 1;
    transform: translateY(0);
}

.custom-toast-success {
    border-left-color: #1cc88a;
    background-color: #f6fff9;
}

.custom-toast-error {
    border-left-color: #e74a3b;
    background-color: #fff6f6;
}

.custom-toast-warning {
    border-left-color: #f6c23e;
    background-color: #fffdf5;
}

.custom-toast-info {
    border-left-color: #36b9cc;
    background-color: #f5fcff;
}

.toast-close-btn {
    background: none;
    border: none;
    color: #999;
    font-size: 18px;
    cursor: pointer;
    margin-left: 15px;
    padding: 0;
    line-height: 1;
}

.toast-close-btn:hover {
    color: #333;
}
</style>

<script>
function showToast(message, type = 'info', duration = 5000) {
    const container = document.getElementById('toast-container');
    if (!container) return;

    const toast = document.createElement('div');
    toast.className = `custom-toast custom-toast-${type}`;
    
    // Add icon based on type
    let icon = '<i class="fas fa-info-circle mr-2"></i>';
    if (type === 'success') icon = '<i class="fas fa-check-circle mr-2 text-success"></i>';
    if (type === 'error') icon = '<i class="fas fa-exclamation-circle mr-2 text-danger"></i>';
    if (type === 'warning') icon = '<i class="fas fa-exclamation-triangle mr-2 text-warning"></i>';

    toast.innerHTML = `
        <div class="d-flex align-items-center">
            ${icon}
            <div>${message}</div>
        </div>
        <button class="toast-close-btn" onclick="this.parentElement.remove()">&times;</button>
    `;

    container.appendChild(toast);

    // Trigger animation
    setTimeout(() => {
        toast.classList.add('show');
    }, 10);

    // Remove toast after duration
    setTimeout(() => {
        toast.classList.remove('show');
        setTimeout(() => {
            toast.remove();
        }, 400);
    }, duration);
}

document.addEventListener('DOMContentLoaded', function () {
    @if(session('success'))
        showToast("{{ session('success') }}", 'success');
    @endif

    @if(session('error'))
        showToast("{{ session('error') }}", 'error');
    @endif

    @if(session('status'))
        showToast("{{ session('status') }}", 'info');
    @endif
});
</script>
