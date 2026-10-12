(() => {
  if (! ('serviceWorker' in navigator)) return;
  const swUrl = document.body?.dataset?.sw;
  if (! swUrl) return;
  window.addEventListener('load', () => {
    navigator.serviceWorker.register(swUrl).catch(() => {});
  });
})();
