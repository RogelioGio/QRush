import logo from '../assets/white-logo.svg';
import { Toaster, toast } from 'sonner'
import axiosClient from '../axios-client.js';
import { Navigate, useNavigate, useParams } from 'react-router-dom';
import { ShareIcon } from '@heroicons/react/24/solid'
import { useState } from 'react';

const TokenValidationModule = () => {
    const {token} = useParams();
    const nav = useNavigate();
    const [validating, setValidating] = useState();

    function handleStartOrder() {
        if(validating) return;
        setValidating(true);

        // const request = axiosClient.get(`qr/table_sessions/${token}`)
        // .then(({data}) => {
        //     nav(`/qr/create_order/${token}`);
        //     setValidating(false);
        // }).catch((error) => {
        //     setValidating(false);
        //     console.log(error);
        //     throw error;
        // });

        // toast.promise(
        //     request,
        //     {
        //         loading: 'Looking under the table… just kidding!',
        //         success: 'You’re all set! Let’s order',
        //         error: 'Something went wrong. Please try again.',
        //     }
        // );

        nav(`/qr/create_order/${token}`);
    }


    return <div className="grid grid-rows-[1fr_min-content] h-full">
        <title> QRush | QR code based ordering system</title>
        <div className="bg-amber-500 relative p-4">
            {/* System Logo */}
            <div className="absolute top-4 right-4">
                <img src={logo} alt="QRush Logo" className="w-10 h-10"/>
            </div>
        </div>
        <div className="p-4 flex flex-col justify-center space-y-4">
            <button className="btn-shadow-grey" onClick={()=>{handleStartOrder()}}>
                <p className="font-bold-custom text-lg">Start Order</p>
                <p className="font-regular-custom text-sm">Click to start ordering</p>
            </button>
            <button className="btn-white flex flex-row justify-center items-center group">
                <ShareIcon className="w-5 h-5 mr-2 group-hover:text-white"/>
                <p className="font-regular-custom text-xs">Share Table Code</p>
            </button>
        </div>
        
    </div>;
};
export default TokenValidationModule;