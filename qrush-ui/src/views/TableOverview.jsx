import { useEffect, useState } from "react";
import axiosClient from "../axios-client";
import { useNavigate } from "react-router-dom";
import no_tables from "../assets/no-tables.svg";
import UrgencyTimerComponent from "../components/UrgerncyTimerComponent";
import { Clock } from "lucide-react";
import { useStateContext } from "../contexts/StateContext";
import { toast } from "sonner";

export default function TableOverview() {
    const [tables, setTables] = useState([]);
    const [fetching, setFetching] = useState(false);
    const nav = useNavigate();
    const [openSession, setOpenSession] = useState(false);
    const [selectedSession, setSelectedSession] = useState({});
    const {setTableSession} = useStateContext();

    function fetchTables() {
        if (fetching) {
            return;
        }   
        setFetching(true);

        axiosClient.get('/cashier/table_session', {
            params: {
                status: 'open'
            }   
        })
        .then(({data}) => {
            setTables(data.data);
            setFetching(false);
        }).catch((err) => {
            console.error(err);
            setFetching(false);
        });
    }

    function processSession(session) {
        if(openSession) {
            setOpenSession(false);
        };
        setOpenSession(true);
        setSelectedSession(session);
    }

    function BillOut() {
        const request =axiosClient.post(`/cashier/billing/${selectedSession.id}/payment`,
            {
                payment_method: 'cash',
                reference_no: `${Math.floor(Math.random() * 1000000000)}`,
            }
        ).then(({data}) => {
            nav(`billing/${selectedSession.id}`);
        }).catch((err) => {
            console.error(err);
            throw err;
        });

        toast.promise(request, {
            loading: 'Creating billing...',
            success: 'Billing created successfully!',
            error: 'Failed to create billing.'
        });
    }

    useEffect(() => {
        fetchTables();
    },[])
    useEffect(() => {
        setSelectedSession(tables[0] || {});
    }, [tables])


    return (
        <div className="grid grid-cols-4 gap-4 h-full w-full overflow-y-scroll no-scrollbar min-h-0 p-4">
            <div className="col-span-3 flex flex-col gap-4 min-h-0">
                <h1 className="font-bold-custom text-xl"> 
                    Tables Overview
                </h1>
                <div className="grid grid-cols-4 gap-4 h-full">
                    {
                        fetching ?
                        Array.from({length: 30}).map((_, index) => (
                            <div key={index} className="bg-white rounded-lg p-4 shadow-md animate-pulse flex flex-col gap-2 h-20">
                            </div>))
                        :
                        tables.length === 0 ? 
                        <>
                            <div className="col-span-4 rounded-lg p-4 flex flex-col justify-center items-center">
                                <img src={no_tables} alt="No Tables" className="h-70 mb-4"/>
                                <div className="mb-4 text-center">
                                    <p className="text-day-bg-iron-grey font-bold-custom text-2xl">No orders in progress</p>
                                    <p className="text-day-bg-iron-grey font-bold-custom">Waiting for the first bite!</p>
                                </div>
                                <p className="font-regular-custom text-day-bg-iron-grey text-sm">No active table found</p>
                            </div>
                        </>
                        : 
                        tables.map((session, idx) => {
                            const sessionStatus = session.statusFlag.charAt(0).toUpperCase() + session.statusFlag.slice(1);
                            const isTakeOut = session.table_id === null;
                            const ordering = session.statusFlag === 'ordering';
                            const active = session.statusFlag === 'active';
                            const billable = session.statusFlag === 'billable';
                            const forPayment = session.statusFlag === 'for_payment';

                            return(
                                <div key={idx} className={`w-full h-fit bg-white rounded-lg p-4 shadow-md flex flex-row justify-between cursor-pointer hover:shadow-lg transition-all ease-in-out ${selectedSession.id === session.id ? "border-day-bg-shadow-grey" : "border-day-bg-pale-slate2"} border`} onClick={()=>{processSession(session)}}>
                                <div>
                                    <h1 className="font-bold-custom text-xl">{!isTakeOut ? `Table ${session.table.table_number}` : `Take out ${session.id}`}</h1>
                                    <p className="text-day-bg-iron-grey text-sm font-regular-custom">Session ID: {session.id}</p>
                                </div>
                                <div className="flex flex-col items-end justify-between">
                                    <span className={`px-2 py-1 text-xs font-regular-custom rounded-full ${ordering ? "bg-blue-200 text-blue-700" : active ? "bg-purple-200 text-purple-700" : billable ? "bg-green-200 text-green-700" : forPayment ? "bg-amber-200 text-amber-700": ""}`}>{sessionStatus}</span>
                                    <span className="flex flex-row gap-2 items-center justify-center font-regular-custom text-day-bg-iron-grey text-xs">
                                        <Clock size={15}/>
                                        <UrgencyTimerComponent created_at={session.created_at}/>
                                    </span>
                                </div>
                            </div>
                            )
                        })
                    }
                </div>
            </div>
            {
                // true ? 
                fetching ?
                <div className="bg-white rounded-lg h-full shadow-md p-4 sticky top-0 animate-pulse"/>                    
                : <div className="bg-white rounded-lg h-full shadow-md p-4 sticky top-0 flex flex-col justify-between">
                    <div className="flex flex-row justify-between ">
                            <div>
                                <h1 className="font-bold-custom text-2xl">Table {selectedSession?.table?.table_number}</h1>
                                <p className="font-regular-custom text-xs text-day-bg-iron-grey">Session ID: {selectedSession?.id}</p>
                            </div>
                            <div>
                                <span className={`px-2 py-1 text-xs font-regular-custom rounded-full ${selectedSession.statusFlag === 'ordering' ? "bg-blue-200 text-blue-700" : selectedSession.statusFlag === 'active' ? "bg-purple-200 text-purple-700" : selectedSession.statusFlag === 'billable' ? "bg-green-200 text-green-700" : selectedSession.statusFlag === 'for_payment' ? "bg-amber-200 text-amber-700": ""}`}>{selectedSession.statusFlag?.charAt(0) + selectedSession.statusFlag?.slice(1)}</span>
                            </div>
                        </div>
                    <div className="py-4 flex-1">
                        <p className="font-regular-custom text-xs text-day-bg-iron-grey">Ordered items:</p>
                    </div>
                    {
                        selectedSession.statusFlag === 'billable'?
                        <button className="btn-shadow-grey font-regular-custom text-sm w-full" onClick={()=>{BillOut(); setTableSession(selectedSession)}}>
                            Bill Out
                        </button> : 
                        selectedSession.statusFlag === 'for_payment' ?
                        <button className="btn-shadow-grey font-regular-custom text-sm w-full" onClick={()=>{nav(`billing/${selectedSession.id}`); setTableSession(selectedSession)}}>
                            View Billing
                        </button>
                        : null 
                    }
                    {/* {JSON.stringify(selectedSession)} */}
                </div>
            }
        </div>
    );
}