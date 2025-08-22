import axios from 'axios';

// Set CSRF token for all requests
axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

// Set default headers
axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

// Add request interceptor to ensure CSRF token is always included
axios.interceptors.request.use(function (config) {
    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    if (token) {
        config.headers['X-CSRF-TOKEN'] = token;
    }
    return config;
}, function (error) {
    return Promise.reject(error);
});

export default axios;
