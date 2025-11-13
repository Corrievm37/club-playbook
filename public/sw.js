self.addEventListener('push', function (event) {
  if (!event.data) return;
  const data = event.data.json ? event.data.json() : {};
  const title = data.title || 'Notification';
  const body = data.body || '';
  const icon = data.icon || '/favicon.ico';
  const options = { body, icon, data: data.data || {} };
  event.waitUntil(self.registration.showNotification(title, options));
});

self.addEventListener('notificationclick', function (event) {
  event.notification.close();
  const targetUrl = (event.notification.data && event.notification.data.url) || '/';
  event.waitUntil(
    clients.matchAll({ type: 'window', includeUncontrolled: true }).then(function (clientList) {
      for (const client of clientList) {
        if (client.url === targetUrl && 'focus' in client) {
          return client.focus();
        }
      }
      if (clients.openWindow) {
        return clients.openWindow(targetUrl);
      }
    })
  );
});
