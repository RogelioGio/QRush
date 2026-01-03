import { Outlet } from "react-router-dom";
import logo from '../assets/black-logo.svg';
import LiveClockComponent from "../components/LiveClockComponent";

export default function KDSLayout() {
    return <>
            <div className="w-full h-screen bg-day-bg-pale-slate2 flex flex-col overflow-y-scroll scroll-snap-y">
                <div className="p-4 bg-white border-b border-day-bg-iron-grey flex flex-row justify-between items-center sticky top-0">
                    <img src={logo} alt="QRush Logo" className="h-10" color="import logo from '../assets/white-logo.svg';"/>
                    <LiveClockComponent/>
                </div>
                <Outlet/>
            </div>
        </>
}