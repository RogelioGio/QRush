import { createBrowserRouter } from "react-router-dom";
import { QROrderingPage } from "./views/QROrderingPage";

const router = createBrowserRouter([
    
    
    //Customer routes
    {
        path: "/qr/:token",
        element: <QROrderingPage />,
    }
]);

export default router;