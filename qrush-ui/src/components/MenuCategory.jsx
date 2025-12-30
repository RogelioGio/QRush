import { useEffect, useState } from "react";
import axiosClient from "../axios-client";
import { useParams } from "react-router-dom";

const MenuCategory = () => {
    const {token} = useParams();
    const [categories, setCategories] = useState([]);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState(null);
    const [cartitems, setCartItems] = useState([]);
    const [orderMessage, setOrderMessage] = useState('');
    const [submitting, setSubmitting] = useState(false);

    function addQuantity(item) {
        const existingItem = cartitems.find(cartItem => cartItem.id === item.id);

        if (existingItem) {
            setCartItems(cartitems.map(cartItem =>
                cartItem.id === item.id
                    ? { ...cartItem, quantity: cartItem.quantity + 1 }
                    : cartItem
            ));
        } else {
            setCartItems([...cartitems, { ...item, quantity: 1 }]);
        }
        
    }

    function subtractQuantity(item) {
        const existingItem = cartitems.find(cartItem => cartItem.id === item.id);
        if (existingItem && existingItem.quantity > 1) {
            setCartItems(cartitems.map(cartItem =>
                cartItem.id === item.id
                    ? { ...cartItem, quantity: cartItem.quantity - 1 }
                    : cartItem
            ));
        } else if (existingItem && existingItem.quantity === 1) {
            setCartItems(cartitems.filter(cartItem => cartItem.id !== item.id));
        }
        
    }

    function handleAddToCart(item) {
        const existingItem = cartitems.find(cartItem => cartItem.id === item.id);
        if (existingItem) {
            setCartItems(cartitems.map(cartItem =>
                cartItem.id === item.id
                    ? { ...cartItem, quantity: cartItem.quantity + 1 }
                    : cartItem
            ));
        } else {
            setCartItems([...cartitems, { ...item, quantity: 1 }]);
        }
    }

    function handleRemoveFromCart(item) {
        const existingItem = cartitems.find(cartItem => cartItem.id === item.id);
        if (existingItem) {
            setCartItems(cartitems.filter(cartItem => cartItem.id !== item.id));
        }
    } 

    function handleSubmitOrder() {
        if(cartitems.length === 0) {
            setOrderMessage('No items in cart to submit.');
            return;
        }

        setSubmitting(true);

        const orderPayload = {
            status: 'pending',
            order_items: cartitems.map(items => ({menu_item_id: items.id, quantity: items.quantity}))
        };

        axiosClient.post(`qr/create_order/${token}`, orderPayload)
        .then(({data}) => {
            setOrderMessage('Order submitted successfully!');
            setCartItems([]);
            setSubmitting(false);
        })
        .catch((error) => {
            console.log(error);
            setOrderMessage('Error submitting order: ' + error.message);
            setSubmitting(false);
        });
    }

    useEffect(() =>{
        axiosClient.get('qr/menu').then(({data}) => {
            setCategories(data.data);
            setLoading(false);
        }).catch((error) => {
            console.log(error);
            setError(error.message);
            setLoading(false);
        });
    },[])

    useEffect(() => {
        console.log({
            status: 'pending',
            order_items: cartitems.map(items => ({menu_item_id: items.id, quantity: items.quantity}))
        });
    }, [cartitems]);

    return (
        <div>
            <h5>Menu</h5>
            <div className="space-y-2">
                {
                    categories.map((items, index) => (
                        <div key={index} className="border-white border-2 p-5" >
                            <h6>{items.category}</h6>
                            <ul>
                                {
                                    items.menu_items.map((item, idx) => (
                                        <li className="border-white rounded-2xl border w-fit p-4" key={idx}>
                                            <div onClick={()=>{handleAddToCart(item)}}>
                                                {item.name} - ${item.price}
                                            </div>
                                            <div className="flex flex-row justify-around">
                                                <p className="" onClick={()=>{addQuantity(item)}}>+</p>
                                                <p>{
                                                    cartitems.find(cartItem => cartItem.id === item.id)?.quantity || 0
                                                }</p>
                                                <p className="" onClick={()=>{subtractQuantity(item)}}>-</p>
                                            </div>
                                        </li>
                                    ))
                                }
                            </ul>
                        </div>
                    ))
                }
            </div>
            <div>
                <p>Selected Items Here:</p>
                <ul>
                    {
                        cartitems.map((item, index) => (
                            <li key={index}>
                                <div>
                                    {item.name} - Quantity: {item.quantity} 
                                    <button onClick={() => handleRemoveFromCart(item)}>Remove</button>
                                </div>
                            </li>
                        ))
                    }
                </ul>
            </div>
            <div>
                <button onClick={() => {handleSubmitOrder()}}>Submit Order</button>
                <p>{orderMessage}</p>
            </div>
        </div>
    )
}

export default MenuCategory;