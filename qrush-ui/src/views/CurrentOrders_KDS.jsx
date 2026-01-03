import { Clock } from "lucide-react";
import axiosClient from "../axios-client";
import { use, useEffect, useRef, useState } from "react";
import { toast } from "sonner";
import UrgencyTimerComponent from "../components/UrgerncyTimerComponent";

export default function CurrentOrders_KDS() 
{
    const [confirmedOrders, setConfirmedOrders] = useState([]);
    const [fetching, setFetching] = useState(false);
    const [updatingOrderId, setUpdatingOrderId] = useState(null);
    const currentOrder = useRef(null);
    const [newOrderFlag, setNewOrderFlag] = useState([]); 

    function getConfirmedOrders() {
        if(fetching) return;
        setFetching(true);

        const request = axiosClient.get('kds/orders')
        .then(({data}) => {
            setConfirmedOrders(data.orders);
            setFetching(false);
        })
        .catch((error) => {
            console.log(error);
        });
    }

    function updateOrderStatus(orderId, newStatus) {
        if(updatingOrderId) return;
        setUpdatingOrderId(orderId);

        const request = axiosClient.patch(`kds/orders/${orderId}/status`, {
            status: newStatus
        }).then(({data}) => {
            getConfirmedOrders();
            setUpdatingOrderId(null);
        }).catch((error) => {
            console.log(error);
            setUpdatingOrderId(null);
            throw error;
        })

        toast.promise(request, {
            loading: 'Updating order status...',
            success: `Order ODR#${orderId} marked as ${newStatus}`,
            error: 'Failed to update order status.'
        });
    }

    useEffect(() => {
        getConfirmedOrders();
        const interval = setInterval(() => {
            getConfirmedOrders();
        }, 60000);
        return () => clearInterval(interval);
    },[])
    useEffect(()=>{
        const currentIds = confirmedOrders.map(order => order.order_id);
        const newIds = currentIds.filter(id => !currentOrder.current?.includes(id));

        if(newIds.length > 0){
            setNewOrderFlag(newIds);
            toast.success(`New order${newIds.length > 1 ? 's' : ''} received!`);
        } 

        setTimeout(() => {
            setNewOrderFlag(prev => prev.filter(id => !newIds.includes(id)));
        }, 10000);
        
        currentOrder.current = currentIds;
    },[confirmedOrders]);

    return (
        <div className="grid md:grid-cols-2 lg:grid-cols-3 p-4 gap-2 w-auto flex-1">
            {
                fetching ? 
                <>
                
                </>
                : confirmedOrders.length === 0 ?
                <div className="col-span-3 row-span-3 flex flex-col justify-center items-center gap-4">
                    <Clock className="w-16 h-16 text-day-bg-iron-grey"/>
                    <p className="text-day-bg-iron-grey text-lg font-regular-custom">
                        No confirmed orders at the moment.
                    </p>
                </div>
                :
                confirmedOrders.map((order, idx) => {
                    const rowspan =  1 + Math.floor(order.order_items.length / 5);
                    const status = order.status.charAt(0).toUpperCase()+order.status.slice(1);
                    const preparing = order.status === 'preparing';
                    const confirmed = order.status === 'confirmed';
                    const ready = order.status === 'ready'; 
                    const toStatus = preparing ? 'ready' : confirmed ? 'preparing' : 'completed';
                    
                    return( 
                    <div key={idx} className={`w-full h-full bg-white rounded-lg flex flex-col row-span-${rowspan} border border-day-bg-pale-slate2 shadow-md snap-center`}>
                        <div className="flex flex-row p-4 justify-between">
                            <div>
                                <h1 className="text-xl font-bold-custom leading-none">Table {order.table_id}</h1>
                                <p className="text-sm font-regular-custom ">ODR#{order.order_id}</p>
                            </div>
                            <div>
                                {
                                    newOrderFlag.includes(order.order_id) ?
                                    <span className="mr-2 px-3 py-1 text-xs font-bold-custom text-white bg-day-bg-gunmetal rounded-full animate-pulse">New Order</span>
                                    : null
                                }
                                <span className={`px-2 py-1 text-xs font-regular-custom rounded-full ${confirmed ? "bg-amber-200 text-amber-700" : preparing ? "bg-blue-200 text-blue-700" : ready ? "bg-green-200 text-green-700" : ""}`}>{status}</span>
                            </div>
                        </div>

                        <div className="flex-1 px-4 pb-4 gap-2 flex flex-col">
                            {
                                order.order_items.map((item, itemIdx) => (
                                <div key={itemIdx} className="border border-day-bg-pale-slate2 rounded-lg p-4 font-regular-custom">
                                    {item.name} x {item.quantity}
                                </div>))
                            }
                        </div>
                        <div className="flex flex-row justify-between items-center p-4 border-t border-day-bg-pale-slate2">
                            {/* action */}
                            <span className="flex flex-row gap-1 items-center font-regular-custom text-day-bg-iron-grey">
                                <Clock/>
                                <UrgencyTimerComponent created_at={order.created_at}/>
                            </span>
                            {
                                !ready ?
                                <button className="btn-shadow-grey font-regular-custom md:text-sm" onClick={()=>{updateOrderStatus(order.order_id , toStatus)}} disabled={updatingOrderId === order.id}>
                                    {
                                        confirmed ? 
                                        "Start Preparing"
                                        : preparing ? 
                                        "Mark as Ready" 
                                        : "Completed"
                                    }
                                </button>
                                : null
                            }
                        </div>
                    </div>)
                })
            }
        </div>
    )
} 