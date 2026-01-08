import { createBrowserRouter, Navigate } from "react-router-dom";
import { QROrderingPage } from "./views/QROrderingPage";
import CustomerLayout from "./layouts/CustomerLayout";
import TokenValidationModule from "./components/TokenValidationModule";
import OrderingMenu from "./views/OrderingMenu";
import ConfirmOrder from "./views/ConfirmOrder";
import KDSLayout from "./layouts/KDSLayout";
import CurrentOrders_KDS from "./views/CurrentOrders_KDS";
import POSLayout from "./layouts/POSLayout";
import TableOverview from "./views/TableOverView";
import Billing from "./views/Billing";
const router = createBrowserRouter([
    
    
    //Customer routes


    {
        path: "/:token?/:take_out?",
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
                path: "create_order/:token/:take_out?",
                element: <OrderingMenu/>,
            },
            {
                path: "confirm_order/:token/:take_out?",
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
    },
    {
        path: "/pos",
        element:<POSLayout/>,
        children: [
            {
                path: "",
                element: <TableOverview/>,
            },
            {
                path: "billing/:session_id?",
                element: <Billing/>
            }
        ]
    }
    
]);

export default router;