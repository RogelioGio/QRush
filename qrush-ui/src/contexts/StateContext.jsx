import { createContext, use, useContext, useState } from "react";

const stateContext = createContext();

export const StateProvider = ({children}) => {
    const [order, _setOrder] = useState(null);

    function setOrder(orderData) {
        _setOrder(orderData);
    };
    

    //values to be provided globally
    const values = {
        order,
        setOrder
    };

    return (
        <stateContext.Provider value={values}>
            {children}
        </stateContext.Provider>
    )
}

export const useStateContext = () => useContext(stateContext);