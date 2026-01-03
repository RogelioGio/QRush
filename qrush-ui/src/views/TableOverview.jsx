import { useEffect, useState } from "react";
import axiosClient from "../axios-client";
import { useNavigate } from "react-router-dom";

export default function TableOverview() {
    const [tables, setTables] = useState([]);
    const [fetching, setFetching] = useState(false);
    const nav = useNavigate();

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

    useEffect(() => {
        fetchTables();
    },[])


    return (
        <div className="flex flex-col gap-4 p-4 h-full w-full col-span-4">        
            <div>

            </div>
            <div className="grid grid-cols-4 grid-rows-2 gap-4 flex-1">
                {
                        fetching ?
                        <p>Loading tables...</p>
                        :
                        tables.length === 0 ?
                        <p>No active tables found.</p>
                        :
                        tables.map((table, index) => {
                            const statusText = table.status === 'open' ? (table.is_billable ? 'Billable'  : 'Active') : 'Closed';
                            return (
                                <div key={index} className="bg-white rounded-lg p-4 shadow-md flex flex-col gap-2">
                                    <div className="flex flex-row justify-between">
                                        <div>
                                            <p className="text-sm font-regular-custom text-day-bg-iron-grey leading-snug">Table Name:</p>
                                            <p className="text-xl font-bold-custom">Table {table.table.table_number}</p>
                                        </div>
                                        <div>
                                            <span className="ml-4 px-2 py-1 text-xs font-regular-custom text-white bg-day-bg-gunmetal rounded-full">{statusText}</span>
                                        </div>
                                    </div>
                                    <div>
                                        <button className="btn-shadow-grey font-regular-custom text-sm" onClick={() => nav(`/pos/billing/${table.id}`)}>Ready to Bill</button>
                                    </div>
                                </div>
                            )
                        })
                }
            </div>
        </div>
    );
}