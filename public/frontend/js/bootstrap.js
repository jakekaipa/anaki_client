import _ from 'lodash';
window._ = _;

import 'bootstrap';

/**
 * We'll load the axios HTTP library which allows us to easily issue requests
 * to our Laravel back-end. This library automatically handles sending the
 * CSRF token as a header based on the value of the "XSRF" token cookie.
 */

import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

/**
 * Echo exposes an expressive API for subscribing to channels and listening
 * for events that are broadcast by Laravel. Echo and event broadcasting
 * allows your team to easily build robust real-time web applications.
 */

// import Echo from 'laravel-echo';

// import Pusher from 'pusher-js';
// window.Pusher = Pusher;

// window.Echo = new Echo({
//     broadcaster: 'pusher',
//     key: import.meta.env.VITE_PUSHER_APP_KEY,
//     wsHost: import.meta.env.VITE_PUSHER_HOST ?? `ws-${import.meta.env.VITE_PUSHER_APP_CLUSTER}.pusher.com`,
//     wsPort: import.meta.env.VITE_PUSHER_PORT ?? 80,
//     wssPort: import.meta.env.VITE_PUSHER_PORT ?? 443,
//     forceTLS: (import.meta.env.VITE_PUSHER_SCHEME ?? 'https') === 'https',
//     enabledTransports: ['ws', 'wss'],
// });

class PusherSingleton {
    constructor() {
        if (!PusherSingleton.instance) {
            this.pusher = null;
            this.channel = null;
            PusherSingleton.instance = this;

            this.clearSessionStorage();
        }
        return PusherSingleton.instance;
    }

    clearSessionStorage() {
        // Pusher 관련 항목만 삭제
        Object.keys(sessionStorage).forEach(key => {
            if (key.startsWith('pusherConnected') || key === 'pusherState') {
                sessionStorage.removeItem(key);
            }
        });
    }

    connect(userId, appKey, cluster) {
        if (this.pusher) {
            return;
        }

        this.pusher = new Pusher(appKey, {
            cluster: cluster,
            encrypted: true,
            authEndpoint: '/broadcasting/auth'
        });

        this.channel = this.pusher.subscribe(`private-user.${userId}`);

        this.pusher.connection.bind('state_change', (states) => {
            console.log('Pusher state changed from ' + states.previous + ' to ' + states.current);
            sessionStorage.setItem('pusherState', states.current);
        });

        this.pusher.connection.bind('disconnected', () => {
            console.log('Disconnected from Pusher');
            setTimeout(() => {
                console.log('Attempting to reconnect to Pusher');
                this.pusher.connect();
            }, 3000);
        });

        this.pusher.connection.bind('error', (err) => {
            console.error('Pusher connection error:', err);
            this.refreshConnection(userId, appKey, cluster);
        });

        sessionStorage.setItem('pusherConnected', 'true');
    }

    refreshConnection(userId, appKey, cluster) {
        if (this.pusher) {
            this.pusher.disconnect();
        }
        sessionStorage.removeItem('pusherConnected');
        this.connect(userId, appKey, cluster);
    }

    bindEvent(event, callback) {
        if (this.channel) {
            this.channel.bind(event, callback);
        } else {
            console.error('Channel not initialized. Call connect() first.');
        }
    }

    static isConnected() {
        return sessionStorage.getItem('pusherConnected') === 'true';
    }
}

window.PusherSingleton = PusherSingleton;
window.pusherInstance = new PusherSingleton();
