import React, { useEffect, useReducer, useState } from 'react';
import { useDispatch, useSelector } from 'react-redux';
import { withRouter } from 'react-router-dom';
import styles from './notifications.module.css';

/** Redux */
import {
    fetchUnreadNotifications,
    readNotification,
    addNotificationCount,
    countUnreadNotifications
} from '../../store/actions/notificationActions';

/** Utils */
import InitialStatus from '../utils/initial-status';
import Pager from '../utils/pager';

/** Components */
import PModal from '../widgets/p-modal';
import VNotificationItem from '../widgets/v-notification/v-notification-item';
import NotificationLoading from './loader';

const InitialPaginator = {
    page: 1,
    totalPage: 0,
    totalLeft: 0
}
function paginatorReducer(state, action) {
    switch (action.type) {
        case 'addPage':
            return {...state, page: state.page + 1}
        default:
            throw new Error('Invalid Action');
    }
}

const EmptyNotifications = () => (
    <div className="disabled">No new notification/s</div>
)

const Notifications = ({
    onRequestClose
}) => {
    const dispatch = useDispatch();
    const [status, setStatus] = useState(InitialStatus.LOADING);
    const { notifications } = useSelector(state => state.notification);
    const [pager, setPager] = useState(Pager);
    const [paginator, dispatchPaginator] = useReducer(paginatorReducer, InitialPaginator);
    const [isLastPage, setIsLastPage] = useState(false);

    useEffect(() => {
        let mounted = true;
        const fetchNotifications = async () => {
            try {
                const res = await dispatch(fetchUnreadNotifications(pager.page, 10));
                if ( ! mounted) return;
                if (res.length === 0) {
                    return setIsLastPage(true);
                }
                setStatus({ ...InitialStatus.POST });
            } catch (e) {
                setStatus({ ...InitialStatus.ERROR });
            }
        }

        if ( ! isLastPage) {
            fetchNotifications();
        }
        return () => {
            mounted = false;
        }
        // eslint-disable-next-line react-hooks/exhaustive-deps
    }, [pager])

    const handleOnRead = (id) => {
        onRequestClose();
        dispatch(readNotification(id))
            .then(() => dispatch(addNotificationCount(-1)))
    }

    const renderNotifications = notifications.map((notification, i) => (
        <div className={styles.item} key={`vnotificationitem_${i}`}>
            <VNotificationItem
                key={`notificationItem_${i}`}
                index={i}
                notificationId={notification.id}
                type={notification.type}
                postId={notification.post_id}
                username={notification.user.username}
                avatarUrl={notification.user.avatar_url}
                onRead={handleOnRead}
                />
        </div>
    ))

    if (status.error) {
        return (
            <PModal
                onRequestClose={onRequestClose}
                header="Notifications"
            >
                <div className="disabled">Oops. Something went wrong</div>
            </PModal>
        );
    }

    if (status.loading) {
        return (
            <PModal
                onRequestClose={onRequestClose}
                header="Notifications"
            >
                <NotificationLoading />
            </PModal>
        );
    }

    return (
        <PModal
            enableScrollPaginate={true}
            pager={pager}
            onScrollPaginate={page => setPager({ ...pager, page })}
            onRequestClose={onRequestClose}
            header="Notifications"
        >
            {notifications.length === 0 ? <EmptyNotifications /> : renderNotifications}
        </PModal>
    );
}

export default withRouter(Notifications);