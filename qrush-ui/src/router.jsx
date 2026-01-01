import { createBrowserRouter, Navigate } from "react-router-dom";
import { QROrderingPage } from "./views/QROrderingPage";
import CustomerLayout from "./layouts/CustomerLayout";
import TokenValidationModule from "./components/TokenValidationModule";
import OrderingMenu from "./views/OrderingMenu";

const router = createBrowserRouter([
    
    
    //Customer routes


    {
        path: "/:token?",
        element: <CustomerLayout/>,
        children: [
            {
                path: "",
                element: <TokenValidationModule/>,
            }
        ],
    },
    {
        path: "/qr",
        element: <CustomerLayout/>,
        children: [
            {
                path: "create_order/:token",
                element: <OrderingMenu/>,
            }
        ]
    }
]);

export default router;