import { Outlet } from "react-router-dom";
import logo from "../assets/black-logo.svg";


export default function POSLayout() {
    return (
        <div className="bg-day-bg-pale-slate2 w-screen h-screen grid grid-rows-[min-content_1fr]">
            <div className="col-span-4 pt-4 px-4 h-fit">
                <div className="bg-white w-full h-full p-4 rounded-lg">
                    <img src={logo} alt="QRush Logo" className="h-10"/>
                </div>    
            </div>
            <Outlet/>
        </div>
    );
}