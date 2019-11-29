import React, { useCallback } from 'react';
import styles from './v-notification-item.module.css';
import { withRouter, Link } from 'react-router-dom';

/** Components */
import ProfileImage from '../profile-image';
import Username from '../username';

const VNotificationItem = ({
    index,
    notificationId,
    avatarUrl,
    username,
    type,
    postId,
    showCloseBtn,
    onRead,
    onClose,
    ...props
}) => {

    const handleClose = useCallback(e => {
        e.stopPropagation();
        onClose(index);
    }, [index])

    let link;

    switch (type) {
        case 'followed':
            link = `/profiles/${username}`;
            break;
        case 'shared':
        case 'liked':
        case 'commented':
            link = `/posts/${postId}`
            break;
        default:
            link = '';
    }

    const message = () => {
        let text = '';
        switch (type) {
            case 'followed':
                text = 'has followed you';
                break;
            case 'shared':
                text = 'has shared your post';
                break;
            case 'liked':
                text = 'has liked your post';
                break;
            case 'commented':
                text = 'has commented on your post';
                break;
            default:
                return '';
        }
        return (
            <span>{text}</span>
        )
    }

    return (
        <Link to={link} onClick={() => onRead(notificationId, index)}>
            <div className={styles.body}>
                <ProfileImage
                    src={avatarUrl}
                    size={32}
                />
                <div className={styles.info}>
                    <Username username={username}/>
                    <div className={styles.message}>
                        {message()}
                    </div>
                </div>
                {showCloseBtn && <div className={styles.close}
                    type="button"
                    onClick={handleClose}
                >
                    &times;
                </div>}
            </div>
        </Link>
    )
}

VNotificationItem.defaultProps = {
    showCloseBtn: false,
    onClose: () => {}
}

export default withRouter(VNotificationItem);
