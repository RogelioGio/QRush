import { Outlet } from "react-router-dom";
import logo from "../assets/black-logo.svg";


export default function POSLayout() {
    return (
        <div className="bg-day-bg-pale-slate2 w-screen h-screen grid grid-rows-[min-content_1fr]">
            <div className="p-4 bg-white border-b border-day-bg-iron-grey">
                <img src={logo} alt="QRush Logo" className="h-10"/>
            </div>
            <Outlet/>
        </div>
    );
}