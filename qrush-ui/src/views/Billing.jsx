import { useParams } from "react-router-dom";
import axiosClient from "../axios-client";
import { useEffect, useState } from "react";
import { toast } from "sonner";
import axios from "axios";

export default function Billing() {
    const {session_id} = useParams();
    const [fetching, setFetching] = useState(false);
    const [billingDetails, setBillingDetails] = useState([]);
    const [payment, setPayment] = useState([])

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

    function handlePayment() {
        axiosClient.post(`/cashier/billing/${sess}/payment`,
            {
                payment_method: 'cash',
                reference_no: '#0912331211233',
            }
        ).then(({data}) => {
            toast.info('Open a Payment!');
            setPayment(data);
        }).catch((err) => {
            console.error(err);
            alert('Payment failed. Please try again.');
        });
        
        const confirmed = window.confirm(
            `Confirm payment of ₱${billingDetails.total_amount}?`
        );

        if (!confirmed){
            return;
        }
    }

    function processPayment() {
        const confirmed = window.confirm(
            `Confirm payment of ₱${billingDetails.total_amount}?`
        );
        axiosClient.post(`cashier/billing/3/payment/confirm`).then(({data}) => {
            toast.success('Payment successful!');
        }).catch((err) => {
            console.error(err);
            alert('Payment confirmation failed. Please try again.');
        });
        if (!confirmed){
            return;
        }
    }

    useEffect(() => {
        fetchBillingDetails();
    }, [session_id]);

    console.log(payment);
    

    return (
        <div className="p-4">
            {
                billingDetails.length === 0 ?
                <p>No billing details found.</p>
                :
                <div>
                    <p>Table {billingDetails.table_id}</p>
                    <p>Session ID: #{billingDetails.table_session_id}</p>
                    <p>Total Amount: ${billingDetails.total_amount}</p>
                    <p>Items:</p>
                </div>
            }
            <div>
                {
                    payment.length > 0 ?
                    <button className="btn-shadow-grey" onClick={()=>{handlePayment()}}>Proceed to Payment</button>
                    : 
                <button className="btn-shadow-grey" onClick={()=>{processPayment()}}>finalize to Payment</button>

                }
            </div>
        </div>
    );
}