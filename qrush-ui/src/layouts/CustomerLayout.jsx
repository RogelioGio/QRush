import { Outlet } from "react-router-dom";

export default function CustomerLayout() {
    return <>
            <div className="w-full h-screen">
                <Outlet/>
            </div>

        </>;
}