import axios from 'axios';
window.axios = axios;

const token = localStorage.getItem('barn2AppToken');

if (token) {
    axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;
}

axios.interceptors.request.use((config) => {
    config.headers['Authorization'] = `Bearer ${token}`;
    return config;
}, (error) => Promise.reject(error));