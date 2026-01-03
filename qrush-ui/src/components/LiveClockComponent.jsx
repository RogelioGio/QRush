import { useEffect, useState } from "react";

const LiveClockComponent = () => {
    const [date, setDate] = useState(new Date());

    useEffect(() => {
        const timer = setInterval(() => {
            setDate(new Date());
        },1000);
        return () => clearInterval(timer);
    },[]);

    const dayName = date.toLocaleDateString('en-US', { weekday: 'long' });
    const dateStr = date.toLocaleDateString('en-GB');
    const timeStr = date.toLocaleTimeString('en-US', {
        hour: 'numeric',
        minute: '2-digit',
        hour12: true
    });

    return (
        <div className="grid grid-cols-2 gap-4 place-items-en text-right">
            <div>
                <p className="text-sm font-regular-custom text-day-bg-iron-grey">
                    Current Date
                </p>
                <p className="text-sm font-regular-custom"> 
                    {dateStr} - {dayName}
                </p>
            </div>
            <div>
                <p className="text-sm font-regular-custom text-day-bg-iron-grey">
                    Current Time
                </p>
                <p className="text-sm font-regular-custom">
                    {timeStr}
                </p>
            </div>
        </div>
    );


}

export default LiveClockComponent;