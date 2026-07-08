'use strict';

self.addEventListener('push', function(event) {
  console.log(event);
  const title = event.data.title;
  const options = {
    body: event.data.text(),
    icon: 'resources/images/varcreative.jpg',
    badge: 'resources/images/varcreative.jpg'
  };
  event.waitUntil(self.registration.showNotification(title, options));
});

self.addEventListener('notificationclick', function(event) {
  console.log('[Service Worker] Event:');
  console.log(event);
  event.notification.close();
  /*
  event.waitUntil(
    clients.openWindow('https://developers.google.com/web/')
  );
  */
});