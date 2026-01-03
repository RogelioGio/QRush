import axios from 'axios'

const axiosClient = axios.create({
    baseURL: 'http://127.0.0.1:8000/api/v1',
    headers: {
        'Content-Type': 'application/json',
        Accept: 'application/json'
    }
})

axiosClient.interceptors.request.use((config) => {
    // "1|SV5cEIDli1b2T7e3JXwtJVz5NTlARUICILFBswEq7802d647"
    const token = "2|1Zl3I3C7W2saYKpZfem6gyafeoqp9ZgtsvQuSBIs4e4a7665"
    if (token) {
        config.headers.Authorization = `Bearer ${token}`;
    }
    return config;},
    (error) => {
        return Promise.reject(error);
    }
)

axiosClient.interceptors.response.use(
    response => response,
    error => {
        console.error('API call error:', error)
        return Promise.reject(error)
    }

)

export default axiosClient;