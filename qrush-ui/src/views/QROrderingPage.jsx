import { useEffect, useState } from 'react';
import {useParams} from 'react-router-dom'
import axiosClient from '../axios-client.js';
import MenuCategory from '../components/MenuCategory.jsx';

export function QROrderingPage() {
    const {token} = useParams();
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState(null);
    const [TableSession, setTableSession] = useState(null);       

    useEffect(() => {
        axiosClient.get(`qr/table_sessions/${token}`)
        .then(({data}) => {
            setTableSession(data);
            setLoading(false);
        })
        .catch((error) => {
            console.log(error);
            setError(error.message);
            setLoading(false);
        })
    }, [])


    return (
       <div className='space-y-10'>
            <h1>QR Ordering Page</h1>
            <p>{
                loading ?
                'Loading...' :
                error ?
                `Error: ${error}`
                : <p>Token: {token}</p>    
            }</p>

            <div>
                <MenuCategory />
            </div>
       </div>
    );
}