import { ArrowLeft, Hand, HandPlatter, Loader } from "lucide-react";
import { useStateContext } from "../contexts/StateContext";
import { useNavigate, useParams } from "react-router-dom";
import { useState } from "react";
import axiosClient from "../axios-client";
import { Toaster, toast } from 'sonner'

export default function ConfirmOrder() {
    const {order} = useStateContext();
    const {token, take_out} = useParams();
    const [submitting, setSubmitting] = useState(false);
    const nav = useNavigate();
    const isTakeOut = take_out === 'true'; 
    
    function handleSubmitOrder() {
        if(order.length === 0) return;
        if (submitting) return;

        setSubmitting(true);

        const orderPayload = {
            status: 'pending',
            order_items: order.map(item => ({
                menu_item_id: item.id,
                quantity: item.quantity
            }))
        };

        axiosClient.post(`qr/create_order/${token}?take_out=${isTakeOut}`, orderPayload)
        .then(({data}) => {
            toast.success("Order placed successfully!");
            setSubmitting(false);
        })
        .catch((error) => {
            toast.error("Failed to place order. Please try again.");
            setSubmitting(false);
        });

    }

    
    return (
        <div className="">
            <div className="flex flex-row gap-3 border-b border-day-bg-pale-slate2 p-4 items-center">
                <div className="p-3 bg-day-bg-gunmetal rounded-lg w-fit text-white" onClick={()=>{nav(-1)}}>
                    <ArrowLeft size={20} strokeWidth={2}/>
                </div>
                <div>
                    <h1 className="font-bold-custom text-md">Confirm Order</h1>
                    <p className="font-regular-custom text-xs">Please confirm you order before continuing</p>
                </div>
            </div>
            <div className="p-4">
                <p className="font-regular-custom text-xs text-day-bg-iron-grey mb-2">Selected Items</p>
                <div className="flex flex-col gap-y-2">
                    {
                        order.map((item, index) => (
                            <div key={index} className="flex flex-row gap-4 p-4 border border-day-bg-pale-slate2 rounded-lg">
                                <div className="h-15 aspect-square border border-amber-400 rounded-lg">{item.name}</div>
                                <div className="flex-1">
                                    <span className="font-bold-custom text-lg flex flex-row justify-between">
                                        <p>{item.name} </p>
                                        <p>₱{item.price}</p>
                                    </span>
                                    <p className="font-regular-custom text-sm"> Quantity: {item.quantity}x</p>
                                </div>

                            </div>
                        ))
                    }
                </div>
            </div>
            <div className="fixed bottom-0 w-full bg-white p-4 border-t border-day-bg-pale-slate2">
                <button className={`btn-shadow-grey w-full flex gap-2 justify-center items-center ${submitting ? "opacity-80":""}`} disabled={submitting} onClick={()=>{handleSubmitOrder()}}>
                    {

                        submitting ? 
                        <>
                            <Loader size={30} strokeWidth={1} className="animate-spin"/>
                            <p className="font-regular-custom">Placing Order</p>
                        </> :
                        <>
                            <HandPlatter size={30} strokeWidth={1}/>
                            <p className="font-regular-custom">Place Order</p>
                        </>
                    }
                </button>
            </div>
        </div>
    )
}