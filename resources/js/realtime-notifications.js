function bindNotify() {
  if (!window.Echo || !window.Laravel?.userId) return;

  // Livewire might load after echo sometimes
  if (!window.Livewire) return;

  const channel = `users.${window.Laravel.userId}`;

  // prevent duplicates
  if (window.__notifyBound === channel) return;
  window.__notifyBound = channel;

  window.Echo.private(channel).notification((notification) => {
    console.log("✅ admin got:", notification);
    window.Livewire.dispatch("rt-notification", notification);
  });
}

document.addEventListener("livewire:init", bindNotify);
document.addEventListener("livewire:navigated", () => {
  // when SPA navigates, Livewire reboots parts
  window.__notifyBound = null;
  bindNotify();
});
