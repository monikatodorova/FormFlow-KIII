import axios from 'axios'

export const API_URL = "http://127.0.0.1:8080/api";

// Default axios request method
const repository = axios.create({
    baseURL: API_URL,
    timeout: 1000000,
    method: 'GET',
    headers: {
        'Content-Type': 'application/json',
    }
})

// Add the user's token to each request
repository.interceptors.request.use(config => {
    let token = localStorage.getItem("token");
    if (token !== null) {
        config.headers['Authorization'] = `Bearer ${token}`;
    }
    return config;
});

export default repository;