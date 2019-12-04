import React, { useEffect, useCallback, useState } from 'react';
import styles from './chat.module.css';
import classnames from 'classnames';
import queryString from 'query-string'
import { withRouter } from 'react-router-dom'
import { useDispatch, useSelector } from 'react-redux';

/** Redux */
import {
    addMessageToFirst,
    setUserToFirst,
    fetchUsersToChatAPI,
    fetchMessagesAPI,
    addMessageAPI,
    subscribeToChatAPI,
    unsubscribeToChatAPI,
} from '../../store/actions/chatActions';
import { CHAT } from '../../store/types';

/** HOC */
import withNavbar from '../hoc/with-navbar';

const Users = ({ history }) => {
    const dispatch = useDispatch();
    const { users } = useSelector(state => state.chat);
    const [page, setPage] = useState(1);
    // const [isLast, setIsLast] = useState(false);
    const [isError, setError] = useState(false);

    const handleClick = useCallback(async (id) => {
        history.push(`/chat`);
        history.push(`/chat?id=${id}`);
        // eslint-disable-next-line
    }, []);

    const fetchHandler = useCallback(async () => {
        try {
            await dispatch(fetchUsersToChatAPI(page))
        } catch (e) {
            setError(true);
        }
        // eslint-disable-next-line
    }, [page])

    useEffect(() => {
        fetchHandler()
        setPage(1);
    }, [fetchHandler]);

    if (isError) {
        return ''
    }

    return (
        <div className={styles.usersWrapper}>
            <div className={styles.searchUser}>Search</div>
            <div className={styles.users}>
                {users.map((item, i) => (
                    <div className={classnames(styles.user, {
                        [styles.new]: !!item.new
                    })}
                        key={i}
                        onClick={() => handleClick(item.id)}
                    >
                        {item.username}
                        <div className="disabled">
                            {item.message}
                        </div>
                    </div>
                ))}
            </div>
        </div>
    )
}

const Messages = () => {
    const dispatch = useDispatch();
    const { userInfo, messages, ws } = useSelector(state => state.chat);
    const { user } = useSelector(state => state.auth);
    const [message, setMessage] = useState('');

    useEffect(() => {
        if (ws === null) return;
        if (Object.entries(userInfo).length === 0 && userInfo.constructor === Object) return;

        ws.onmessage = e => {
            const data = JSON.parse(e.data);
            dispatch(setUserToFirst(data.user));
            if (Number(data.user_id) === Number(userInfo.id)) {
                dispatch(addMessageToFirst(data));
            }
        }

        ws.onclose = (e) => {
            setTimeout(() => {
                subscribeToChatAPI(user.id);
            }, 1000);
        };

        ws.onerror = (err) => {
            dispatch({ type: CHAT.unsubscribe });
            dispatch({ type: CHAT.setError });
        };
    }, [ws, userInfo])

    const handleSubmit = useCallback(async e => {
        if (e) {
            e.preventDefault();
        }

        const data = {
            message,
            user_id: user.id,
            receiver_id: userInfo.id
        }
        setMessage('');
        try {
            await dispatch(addMessageAPI(data));
            if (ws !== null) {
                const messageData = {
                    ...data,
                    user
                }
                ws.send(JSON.stringify(messageData));
            }
        } catch (e) {
        }

        // eslint-disable-next-line
    }, [message, userInfo]);

    if (Object.entries(userInfo).length === 0 && userInfo.constructor === Object) {
        return <div className={styles.messagesWrapper}></div>
    }

    return (
        <div className={styles.messagesWrapper}>
            <div className={styles.userInfo}></div>
            <div className={styles.messages}>
                {messages.map((item, i) => (
                    <div
                        className={classnames(styles.message, {
                            [styles.mineMessage]: user.id === item.user_id,
                            [styles.theirMessage]: user.id !== item.user_id
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

const Chat = ({ history, location }) => {
    const { ws, error } = useSelector(state => state.chat);
    const { user } = useSelector(state => state.auth);
    const dispatch = useDispatch();

    useEffect(() => {
        dispatch(subscribeToChatAPI(user.id))
        return () => {
            dispatch(unsubscribeToChatAPI(user.id))
        };
        // eslint-disable-next-line
    }, [])

    useEffect(() => {
        const id = queryString.parse(location.search).id;
        if (id) {
            dispatch(fetchMessagesAPI(id));
        }
        // eslint-disable-next-line
    }, [location.search])

    if (error) {
        return (
            <div className={styles.chat}>
                <div className="disabled">Oops something went wrong</div>
            </div>
        )
    }

    if (ws === null) {
        return <div className={styles.chat}/>
    }

    return (
        <div className={styles.chat}>
            <Users history={history}/>
            <Messages></Messages>
        </div>
    );
}

export default withRouter(withNavbar(Chat));
