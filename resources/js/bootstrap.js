import axios from 'axios';

const token = localStorage.getItem('barn2AppToken');

if (token) {
    axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;
} else {
    const host = new URLSearchParams(location.search).get("host");
    if ( host && typeof shopify === 'object' ) {
        // Add authorization token into localstorage to use in API requests
        shopify.idToken().then((token) => {
            localStorage.setItem('barn2AppToken', token);
        });
    }
}

axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').getAttribute('content');