document.addEventListener('DOMContentLoaded', function () {
    const container = document.getElementById('notificationContainer');
    const bellBadge = document.getElementById('bellBadgeCount');
    const headerBadge = document.getElementById('headerBadgeCount');

    if (container) {
        container.addEventListener('click', function (e) {
            
            // Find the closest parent notification-item row
            const item = e.target.closest('.notification-item');
            if (!item) return;

            // Prevent default routing or dropdown closing behaviors 
            e.preventDefault();

            const notificationId = item.getAttribute('data-id');
            const targetUrl = item.getAttribute('href');

            // Send AJAX Request to mark it read
            fetch(`/notifications/${notificationId}/read`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Smoothly remove the item from display listing
                    item.remove();

                    // Dynamically calculate and decrement the notification count badge
                    let currentCount = parseInt(headerBadge.innerText);
                    if (currentCount > 1) {
                        let newCount = currentCount - 1;
                        headerBadge.innerText = `${newCount} New`;
                        bellBadge.innerText = newCount;
                    } else {
                        // Reset everything back to empty layout displays if count hits 0
                        headerBadge.innerText = '0 New';
                        bellBadge.classList.add('d-none');
                        container.innerHTML = `
                            <div class="px-4 py-3 text-center text-muted" id="emptyNotificationMessage" style="font-size: 0.875rem;">
                                No new notifications.
                            </div>`;
                    }
                    
                    //Optional: If you want to redirect the user to a page after clicking, uncomment below:
                    // window.location.href = item.getAttribute('href');
                    if (targetUrl && targetUrl !== '#') {
                        window.location.href = targetUrl;
                    }
                }
            })
            .catch(error => console.error('Error updating notification details:', error));
        });
    }
});
