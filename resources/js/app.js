import "./sidebar/sidebar";
import "./sidebar/dishSidebar";
import "./echo";

/**
 * ✅ Real-time notification binding (Flux + Livewire v3 + wire:navigate safe)
 */
function bindRealtimeNotifications() {
    if (!window.Laravel?.userId || !window.Echo || !window.Livewire) return;

    // ✅ Always stop previous subscription first (prevents duplicates)
    try {
        window.Echo.leave(`private-users.${window.Laravel.userId}`);
    } catch (e) {}

    window.Echo.private(`users.${window.Laravel.userId}`)
        .notification((notification) => {
            window.Livewire.dispatch("rt-notification", notification);
        });
}

/**
 * ✅ Safe initializer (won’t crash if Livewire isn’t ready yet)
 */
function initRealtime() {
    // If Livewire not ready yet, retry soon
    if (!window.Livewire) {
        setTimeout(initRealtime, 50);
        return;
    }
    bindRealtimeNotifications();
}

/**
 * ✅ Run after Livewire boots
 */
document.addEventListener("livewire:init", () => {
    initRealtime();

    // ✅ Re-bind after navigate (Livewire v3)
    document.addEventListener("livewire:navigated", () => {
        bindRealtimeNotifications();
    });
});
