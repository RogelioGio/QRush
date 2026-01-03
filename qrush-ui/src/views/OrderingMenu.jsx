import axios from "axios";
import { ArrowBigLeft, ArrowRight, HandPlatter, Plus, Search, UtensilsCrossed } from "lucide-react";
import { use, useEffect, useState } from "react";
import axiosClient from "../axios-client";
import { useNavigate, useParams } from "react-router-dom";
import { useStateContext } from "../contexts/StateContext";

export default function OrderingMenu() {
    const {token} = useParams();
    const [menuList, setMenuList] = useState([]);
    const [loading, setLoading] = useState(false);
    const [selectedCategory, setSelectedCategory] = useState(null);
    const [orderItems, setOrderItems] = useState([]);
    const [submitting, setSubmitting] = useState(false);
    const {setOrder} = useStateContext();
    const nav = useNavigate();
    
    function handleAddAddToOrder(item) {
        const existingItem = orderItems.find(orderItem => orderItem.id === item.id);

        if (existingItem) {
            // setOrderItems(orderItems.map(orderItem =>
            //     orderItem.id === item.id
            //         ? { ...orderItem, quantity: orderItem.quantity + 1 }
            //         : orderItem
            // ));
            setOrderItems(orderItems.filter(orderItem => orderItem.id !== item.id));
        } else {
            setOrderItems([...orderItems, { ...item, quantity: 1 }]);
        }
    }

    // function handleSubmitOrder() {
    //     if(orderItems.length === 0) return;
    //     if (submitting) return;

    //     setSubmitting(true);

    //     const orderPayload = {
    //         status: 'pending',
    //         order_items: orderItems.map(item => ({
    //             menu_item_id: item.id,
    //             quantity: item.quantity
    //         }))
    //     };

    //     // axiosClient.post(`qr/create_order/${token}`, orderPayload)
    //     // .then(({data}) => {
    //     //     console.log('Order submitted successfully:', data);
    //     //     setOrderItems([]);
    //     //     setSubmitting(false);
    //     // })
    //     // .catch((error) => {
    //     //     console.log('Error submitting order:', error);
    //     //     setSubmitting(false);
    //     // });

    //     setTimeout(() => {
    //         setOrder(orderItems);
    //         nav(`/qr/confirm_order/${token}`);
    //     }, 1000);

    // }

    function confirmOrder() {
        setOrder(orderItems);
        nav(`/qr/confirm_order/${token}`);
    }




    function fetchMenuList() {
        if (loading) return;
        setLoading(true);
        
        axiosClient.get('qr/menu')
        .then(({data}) => {
            setMenuList(data.menuList);
            setSelectedCategory(data.menuList[0].id);
            console.log(data);
            setLoading(false);

        })
        .catch((error) => {
            console.log(error);
            setLoading(false);
        });
    }

    useEffect(() => {
        fetchMenuList();
    },[])

    //  7f3a2b1c-8e9d-4a5b-b6c7-d8e9f0a1b2c3


    return (
        <div className="bg-day-bg-shadow-grey h-full">
            <title> QRush | Create an Order</title>
            
            <div className="px-4 pt-4 flex flex-row justify-between items-center">
                <div className="flex flex-row gap-x-2 items-center">    
                    <UtensilsCrossed color="white" size={40} strokeWidth={2}/>
                    <div className="text-white">
                        <h1 className="font-bold-custom text-xl">Table 1</h1>
                        <p className="text-xs font-regular-custom">Create an order:</p>
                    </div>
                </div>
                <div className="p-4 bg-day-bg-gunmetal rounded-lg w-fit text-white">
                    <HandPlatter size={25} strokeWidth={2}/>
                </div>
            </div>

            <div className="p-4 sticky top-0">
                <div className="p-4 bg-day-bg-iron-grey rounded-lg w-full text-white flex flex-row gap-4">
                    <Search size={25} strokeWidth={2} />
                    <input type="text" placeholder="Search for dishes or categories" className="bg-day-bg-iron-grey w-full outline-none placeholder:text-white placeholder:font-regular-custom font-regular-custom"/>
                </div>
            </div>

            <div className="w-full h-[89vh] bg-white rounded-t-lg">
                <div className="py-4 sticky top-0 bg-white border-b border-day-bg-pale-slate2 z-10 rounded-t-lg">
                    <p className="font-regular-custom px-4 pb-2 text-xs text-day-bg-iron-grey">Categories</p>
                    <div className="w-full h-fit flex flex-row gap-2 px-4 overflow-x-auto gap- snap-x snap-mandatory no-scrollbar">
                        {
                            loading ?
                            Array.from({ length: 5 }).map((_, i) => (
                                <div key={i} className="w-40 shrink-0 h-8 bg-day-bg-pale-slate2 rounded-lg animate-pulse snap-center"/>
                            ))
                            : menuList.length === 0 ?
                            <p>No categories found.</p>
                            : menuList.map((item, index) => {
                                const isSelected = String(selectedCategory) === String(item.id);
                                return (
                                    <p key={index} className={`font-regular-custom p-2 border border-day-bg-gunmetal whitespace-nowrap snap-center rounded-lg ${isSelected ? "bg-day-bg-gunmetal text-white" : null} hover:bg-day-bg-gunmetal hover:text-white transition-all ease-in-out hover:cursor-pointer`} 
                                        onClick={() => setSelectedCategory(item.id)}>
                                        {item.category}
                                    </p>
                                )
                                
                            }
                            )
                        }
                    </div>
                </div>

                <div className="pt-4">
                    <p className="font-regular-custom px-4 text-xs text-day-bg-iron-grey">Available Menu Items</p>
                    <div className="pt-4 px-4 flex flex-col gap-4">
                            {
                                loading ?   
                                Array.from({ length: 10 }).map((_, i) => (
                                    <div key={i} className="w-full h-24 flex flex-row gap-4">
                                        <div className="h-24 aspect-square rounded-lg animate-pulse bg-day-bg-pale-slate2"/>
                                        <div className="flex flex-col justify-between py-2 grow">
                                            <div className="h-6 w-1/2 bg-day-bg-pale-slate2 rounded-lg animate-pulse"/>
                                            <div className="h-4 w-full bg-day-bg-pale-slate2 rounded-lg animate-pulse"/>
                                            <div className="h-4 w-3/4 bg-day-bg-pale-slate2 rounded-lg animate-pulse"/>
                                        </div>
                                        <div className="self-center h-12 aspect-square rounded-full bg-day-bg-pale-slate2 animate-pulse"/>
                                    </div>
                                ))
                                : menuList.length === 0 ?
                                <p>No menu items found.</p>
                                : 
                                menuList.find(category => category.id === selectedCategory)?.menu_items.map((item, i) => (
                                    <> 
                                    <div key={i} className="w-full h-24 flex flex-row gap-4">
                                        <div className="h-24 aspect-square border border-amber-400 rounded-lg">{item.name}</div>
                                        <div className="flex-1">
                                            <h6 className="font-bold-custom">{item.name}</h6>
                                            <p className="font-regular-custom text-xs">Food Description</p>
                                        </div>
                                        <div className="justify-self-end my-auto">
                                            <button className="btn-white p-3 rounded-full" onClick={()=>{handleAddAddToOrder(item)}}>
                                                <Plus size={20} strokeWidth={3}/>
                                            </button>
                                        </div>
                                    </div>
                                    </>
                                ))
                            }
                    </div>
                </div>
            </div>

            <div className={`w-full h-fit border flex flex-row gap-4 items-center justify-between border-day-bg-pale-slate2 fixed bottom-0 bg-white p-4 ${orderItems.length === 0 ? 'translate-y-full' : 'translate-y-0'} transition-transform duration-500 ease-in-out`}>
                <div className="flex flex-row gap-2 items-center">
                    <button className="btn-white p-3">
                        <HandPlatter size={25} strokeWidth={2}/>
                    </button>
                    <p className="font-regular-custom text-sm inline-block mr-4">Items Selected: {orderItems.map((items, idx) => (
                        <p>{items.name} - {items.quantity}</p>
                    ))}</p>
                </div>
                <button className="btn-shadow-grey p-3 flex gap-2 whitespace-nowrap" onClick={()=>{confirmOrder()}}>
                    <ArrowRight size={25} strokeWidth={2}/>
                </button>
            </div>


        </div>
    )
}