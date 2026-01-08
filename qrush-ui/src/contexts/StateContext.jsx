import { createContext, use, useContext, useState } from "react";

const stateContext = createContext();

export const StateProvider = ({children}) => {
    const [order, _setOrder] = useState(null);
    const [tableSession, _setTableSession] = useState(null);

    function setOrder(orderData) {
        _setOrder(orderData);
    };

    function setTableSession(sessionData) {
        _setTableSession(sessionData);
    }
    

    //values to be provided globally
    const values = {
        order,
        setOrder,
        tableSession,
        setTableSession
    };

    return (
        <stateContext.Provider value={values}>
            {children}
        </stateContext.Provider>
    )
}

export const useStateContext = () => useContext(stateContext);