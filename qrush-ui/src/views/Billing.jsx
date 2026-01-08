import { useNavigate, useParams } from "react-router-dom";
import axiosClient from "../axios-client";
import { useEffect, useState } from "react";
import { toast } from "sonner";
import axios from "axios";
import { useStateContext } from "../contexts/StateContext";
import { Clock, HandPlatter, UtensilsCrossed } from "lucide-react";

export default function Billing() {
    const {session_id} = useParams();
    const [fetching, setFetching] = useState(false);
    const [billingDetails, setBillingDetails] = useState([]);
    const [payment, setPayment] = useState([])
    const [processing, setProcessing] = useState(false);
    const {tableSession} = useStateContext();
    const nav = useNavigate();

    function fetchBillingDetails() {
        if (!session_id) return;
        if (fetching) return
        setFetching(true);

        axiosClient.get(`/cashier/billing/${session_id}/preview`)
        .then(({data}) => {
            setFetching(false);
            setBillingDetails(data)
        })
        .catch((err) => {
            console.error(err);
            setFetching(false);
        });
    }

    function processPayment() {
        if (processing) return;
        setProcessing(true);

        const request1 = axiosClient.post(`cashier/billing/${billingDetails.payment_id}/payment/confirm`).then(({data}) => {
            setProcessing(false);
        }).catch((err) => {
            console.error(err);
            setProcessing(false);
            throw err;
        });

        toast.promise(request1, {
            loading: 'Processing payment...',
            success: `Payment processed successfully!`,
            error: 'Payment processing failed.'
        });
    }

    useEffect(() => {
        fetchBillingDetails();
    }, [session_id]);
    

    return (
        <div className="p-4">
            <div className="flex flex-col gap-4">
                <div>
                    <h1 className="font-bold-custom text-2xl">Biling Details - Table {tableSession?.table?.table_number}</h1>
                    <p className="font-regular-custom text-sm text-day-bg-iron-grey">Session ID: {tableSession.id}</p>
                </div>
                <div className="flex flex-row gap-8">
                    <div>
                        <p className="font-regular-custom text-sm text-day-bg-iron-grey">Open At:</p>
                        <span className="flex flex-row gap-2 font-regular-custom">
                            <Clock/>
                            {new Date(tableSession.opened_at).toLocaleString('en-GB',{
                                day: '2-digit',
                                month: '2-digit',
                                year: 'numeric',
                                hour: '2-digit',
                                minute: '2-digit',
                                hour12: true
                            }).replace(',', ' -')}
                        </span>
                    </div>
                    {
                        fetching ? <p>Loading billing details...</p> :
                        <>
                        <div>
                        <p className="font-regular-custom text-sm text-day-bg-iron-grey">Total Orders:</p>
                        <span className="flex flex-row gap-2 font-regular-custom">
                            <HandPlatter/>
                            <p className="">
                                {billingDetails.total_orders} Served Orders 
                            </p>
                        </span>
                        </div>
                        <div>
                            <p className="font-regular-custom text-sm text-day-bg-iron-grey">Total Items:</p>
                            <span className="flex flex-row gap-2 font-regular-custom">
                                <UtensilsCrossed/>
                                <p className="">
                                    {billingDetails.total_items} Served Items 
                                </p>
                            </span>
                        </div>
                        </>
                    }
                </div>
            </div>
            <div className="flex flex-col justify-between my-4">
                <h1 className="font-regular-custom text-sm text-day-bg-iron-grey">Total Amount</h1>
                <h1 className="font-bold-custom text-2xl">₱{billingDetails?.total_amount}</h1>
            </div>
            <div className="flex flex-row gap-4">
                <button className="btn-white font-regular-custom"  onClick={()=>{nav(-1)}}>Cancel</button>
                <button className={`btn-shadow-grey  font-regular-custom ${processing ? "opacity-50 cursor-not-allowed hover:none":""}`} disabled={processing} onClick={()=>{processPayment()}}>
                    {
                        processing ? 'Processing...' : 'Process Payment'
                    }
                </button>
            </div>
        </div>
    );
}