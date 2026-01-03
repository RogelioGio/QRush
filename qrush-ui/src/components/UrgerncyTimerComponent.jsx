import { useEffect, useState } from "react";

const getTimeAgo = (timestamp) => {
    const now = new Date();
    const past = new Date(timestamp);
    const diffInSeconds = Math.floor((now - past) / 1000);

    if (diffInSeconds < 60) return 'Just now';

    const minutes = Math.floor(diffInSeconds / 60);
    if (minutes < 60) return `${minutes}m ago`;

    const hours = Math.floor(minutes / 60);
    if (hours < 24) return `${hours}h ago`;

    const days = Math.floor(hours / 24);
    if (days === 1) return 'yesterday';
    if (days < 7) return `${days} days ago`;

    return past.toLocaleDateString()
}

const UrgencyTimerComponent = ({created_at}) => {
    const [timeAgo, setTimeAgo] = useState(getTimeAgo(created_at));
    

    useEffect(() => {
        const timer = setInterval(() => {
            setTimeAgo(getTimeAgo(created_at));
        }, 30000);
        return () => clearInterval(timer);
    }, [created_at]);

    return (
        <span>
            <p>
                {timeAgo}
            </p>
        </span>
    );
}

export default UrgencyTimerComponent;