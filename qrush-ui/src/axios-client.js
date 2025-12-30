import axios from 'axios'

const axiosClient = axios.create({
    baseURL: 'http://127.0.0.1:8000/api/v1',
    headers: {
        'Content-Type': 'application/json',
        Accept: 'application/json'
    }
})

axiosClient.interceptors.response.use(
    response => response,
    error => {
        console.error('API call error:', error)
        return Promise.reject(error)
    }

)

export default axiosClient;