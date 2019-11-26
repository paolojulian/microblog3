import React from 'react'
import { useDispatch, useSelector } from 'react-redux'
import { withRouter } from 'react-router-dom';

/** Redux */
import { refreshHome } from '../../../store/actions/refreshAction'
import { logoutUser } from '../../../store/actions/authActions'

/** Components */
import SearchBar from './search-bar'

import styles from './navbar.module.css'
import NotificationBell from './notification-bell';

const Navbar = ({
    history,
    location
}) => {

    const dispatch = useDispatch();

    const { notificationCount } = useSelector(state => state.notification);

    const handleLogout = e => {
        if (e) e.preventDefault();
        dispatch(logoutUser(history));
    }

    const reloadOrNavigate = () => {
        if (location.pathname === '/') {
            return dispatch(refreshHome())
        }
        history.push('/')
    };

    return (
        <>
            <nav className={styles.top}>
                <SearchBar/>
                <div className={styles.logo}>LaCosina</div>
                <ul className={styles.container}>
                    <li
                        className={styles.home}
                        onClick={reloadOrNavigate}>
                        Home
                    </li>
                    <li className={styles.notification}>
                        <NotificationBell notificationCount={notificationCount}/>
                    </li>
                    <li className={styles.logout}
                        onClick={handleLogout}>
                        Logout
                    </li>
                </ul>
            </nav>
            <nav className={styles.bottom}>
                <ul className={styles.container}>
                    <li
                        className={styles.home}
                        onClick={reloadOrNavigate}>
                        <i className="fa fa-home fa-lg"></i>
                    </li>
                    <li className={styles.notification}>
                        <NotificationBell notificationCount={notificationCount}/>
                    </li>
                    <li className={styles.logout}
                        onClick={handleLogout}>
                        <i className="fa fa-sign-out fa-lg"/>
                    </li>
                </ul>
            </nav>
        </>
    )
}

export default withRouter(Navbar);