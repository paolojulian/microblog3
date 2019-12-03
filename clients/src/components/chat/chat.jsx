import React, { useCallback, useState } from 'react';
import styles from './chat.module.css';
import classnames from 'classnames';
import { useSelector } from 'react-redux';

/** HOC */
import withNavbar from '../hoc/with-navbar';

const Users = () => {
    const { users } = useSelector(state => state.chat);
    return (
        <div className={styles.usersWrapper}>
            <div className={styles.searchUser}>Search</div>
            <div className={styles.users}>
                {users.map((item, i) => (
                    <div className={styles.user}
                        key={i}
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
    const { userInfo, messages } = useSelector(state => state.chat);
    const { user } = useSelector(state => state.auth);
    const [message, setMessage] = useState('');

    const handleSubmit = useCallback(e => {
        if (e) {
            e.preventDefault();
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