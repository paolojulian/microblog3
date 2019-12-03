import React, { useEffect, useCallback, useState } from 'react';
import styles from './chat.module.css';
import classnames from 'classnames';
import { useDispatch, useSelector } from 'react-redux';

/** Redux */
import {
    fetchUsersToChatAPI,
    fetchMessagesAPI,
    addMessageAPI
} from '../../store/actions/chatActions';

/** HOC */
import withNavbar from '../hoc/with-navbar';

const Users = () => {
    const dispatch = useDispatch();
    const { users } = useSelector(state => state.chat);
    const [page, setPage] = useState(1);
    const [isLast, setIsLast] = useState(false);

    const handleClick = useCallback(async (userId) => {
        dispatch(fetchMessagesAPI(userId));
    });

    useEffect(() => {
        dispatch(fetchUsersToChatAPI(page));
    }, [page]);

    return (
        <div className={styles.usersWrapper}>
            <div className={styles.searchUser}>Search</div>
            <div className={styles.users}>
                {users.map((item, i) => (
                    <div className={styles.user}
                        key={i}
                        onClick={() => handleClick(item.user_id)}
                    >
                        {item.user.username}
                        {/* {`${user.first_name} ${user.last_name}`} */}
                    </div>
                ))}
            </div>
        </div>
    )
}

const Messages = () => {
    const dispatch = useDispatch();
    const { userInfo, messages } = useSelector(state => state.chat);
    const { user } = useSelector(state => state.auth);
    const [message, setMessage] = useState('');

    const handleSubmit = useCallback(async e => {
        if (e) {
            e.preventDefault();
        }
        const data = {
            message,
            user_id: user.id,
            receiver_id: userInfo.user.id
        }
        try {
            await dispatch(addMessageAPI(data));
        } catch (e) {

        }
    }, [message]);

    if ( ! userInfo) {
        return <div className={styles.messagesWrapper}></div>
    }
    return (
        <div className={styles.messagesWrapper}>
            <div className={styles.userInfo}></div>
            <div className={styles.messages}>
                {messages.map((item, i) => (
                    <div
                        className={classnames(styles.message, {
                            [styles.mineMessage]: user.id === item.userId,
                            [styles.theirMessage]: user.id !== item.userId
                        })}
                        key={i}
                    >
                        <span>{item.message}</span>
                    </div>
                ))}
            </div>
            <div className={styles.sendMessage}>
                <form onSubmit={handleSubmit}>
                    <input
                        type="text"
                        placeholder="Enter your message"
                        value={message}
                        onChange={e => setMessage(e.target.value)}
                    />
                </form>
            </div>
        </div>
    )
}

const Chat = () => {
    return (
        <div className={styles.chat}>
            <Users></Users>
            <Messages></Messages>
        </div>
    );
}

export default withNavbar(Chat);
