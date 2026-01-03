import { createBrowserRouter, Navigate } from "react-router-dom";
import { QROrderingPage } from "./views/QROrderingPage";
import CustomerLayout from "./layouts/CustomerLayout";
import TokenValidationModule from "./components/TokenValidationModule";
import OrderingMenu from "./views/OrderingMenu";
import ConfirmOrder from "./views/ConfirmOrder";
import KDSLayout from "./layouts/KDSLayout";
import CurrentOrders_KDS from "./views/CurrentOrders_KDS";

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
            },
            {
                path: "confirm_order/:token",
                element: <ConfirmOrder/>,
            }
        ]
    },
    {
        path: "/kds",
        element: <KDSLayout/>,
        children: [
            {
                path: "",
                element: <CurrentOrders_KDS/>,
            }
        ]
    }
]);

export default router;