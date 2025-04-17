<style>
    #notification-container {
        position: fixed;
        top: 20px;
        left: 50%;
        transform: translateX(-50%);
        z-index: 9999;
    }

    .custom-notification {
        padding: 15px 20px;
        margin-bottom: 10px;
        border-radius: 4px;
        color: white;
        font-family: Arial, sans-serif;
        font-size: 14px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        min-width: 300px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        opacity: 0;
        transition: opacity 0.3s ease-in-out;
    }

    .custom-notification.show {
        opacity: 1;
    }

    .custom-notification.error {
        background-color: #f44336;
        /* background-color: white; */
    }

    .custom-notification.success {
        background-color: #4CAF50;
        /* background-color: white; */
    }

    .custom-notification.warning {
        background-color: #ff9800;
        /* background-color: white; */
    }

    .notification-close {
        background: none;
        border: none;
        color: white;
        font-size: 20px;
        cursor: pointer;
        padding: 0;
        margin-left: 10px;
    }
</style>

<div id="notification-container"></div>

<script>
    function showNotification(message, type) {
        const container = document.getElementById('notification-container');
        const notification = document.createElement('div');
        notification.className = `custom-notification ${type}`;
        notification.innerHTML = `
        <span>${message}</span>
        <button class="notification-close" onclick="this.parentElement.remove()">&times;</button>
    `;
        container.appendChild(notification);

        // Trigger reflow to enable transition
        notification.offsetHeight;
        notification.classList.add('show');

        // 자동으로 알림 제거
        setTimeout(() => {
            notification.classList.remove('show');
            setTimeout(() => notification.remove(), 300);
        }, 5000);
    }

    document.addEventListener('DOMContentLoaded', function() {
        @if (session('error'))
            @if (is_array(session('error')))
                @foreach (session('error') as $item)
                    showNotification("{{ __($item) }}", "error");
                @endforeach
            @endif
        @elseif (session('success'))
            @if (is_array(session('success')))
                @foreach (session('success') as $item)
                    showNotification("{{ __($item) }}", "success");
                @endforeach
            @endif
        @elseif (session('warning'))
            @if (is_array(session('warning')))
                @foreach (session('warning') as $item)
                    showNotification("{{ __($item) }}", "warning");
                @endforeach
            @endif
        @elseif ($errors->any())
            @foreach ($errors->all() as $item)
                showNotification("{{ __($item) }}", "error");
            @endforeach
        @endif
    });
</script>
