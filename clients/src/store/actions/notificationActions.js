import axios from 'axios';
import { NOTIFICATION } from '../types'

/**
 * Fetch the unread notifications of the user
 */
export const fetchUnreadNotifications = (page = 1, limit = 5) => async dispatch => {
    try {
        const params = {
            page,
            limit
        };
        const res = await axios.get(`/api/notifications/unread`, { params });
        if (page === 1) {
            dispatch({
                type: NOTIFICATION.set,
                payload: res.data.data
            })
        } else {
            dispatch({
                type: NOTIFICATION.add,
                payload: res.data.data
            })
        }
        return await Promise.resolve(res.data.data);
    } catch (e) {
        return Promise.reject(e);
    }
}

/**
 * Counts the unread notifications of the user
 */
export const countUnreadNotifications = () => async dispatch => {
    try {
        const res = await axios.get('/api/notifications/unread/count');
        dispatch({
            type: NOTIFICATION.setCount,
            payload: res.data.data
        });
        return Promise.resolve(res.data.data);
    } catch (e) {
        dispatch({
            type: NOTIFICATION.setCount,
            payload: 0
        });
        return Promise.resolve(0);
    }
}

/**
 * Set notification as read
 */
export const readNotification = (id) => async dispatch => {
    try {
        const res = await axios.post(`/api/notifications/read/${id}`);
        if (res.data.status !== 200) {
            throw new Error('Invalid status');
        }
        return await Promise.resolve(res.data.data);
    } catch (e) {
        return await Promise.reject(e)
    }
}

/**
 * Sets all notification as read
 */
export const readAllNotification = () => async dispatch => {
    try {
        const res = await axios.post(`/api/notifications/read`);
        if (res.data.status !== 200) {
            throw new Error('Invalid Status');
        }
        return await Promise.resolve(res.data.data);
    } catch (e) {
        return await Promise.reject(e)
    }
}

/**
 * Adds or subtract the number of notifications unread
 */
export const addNotificationCount = (n = 1) => dispatch => {
    dispatch({
        type: NOTIFICATION.addCount,
        payload: n
    })
}

/**
 * Adds or subtract the number of notifications unread
 */
export const clearNotification = () => dispatch => {
    dispatch({ type: NOTIFICATION.clear })
}

/**
 * Adds or subtract the number of notifications unread
 */
export const refreshesNotification = () => dispatch => {
    dispatch({ type: NOTIFICATION.refresh })
}

/**
 * Clears all the popup notification on screen
 */
export const clearPopupNotifications = () => dispatch => {
    dispatch({ type: NOTIFICATION.popup.clear })
}

/**
 * Add a popup notification
 */
export const addPopupNotifications = (notification) => dispatch => {
    dispatch({
        type: NOTIFICATION.popup.add,
        payload: notification
    })
}

/**
 * Remove a certain popup notification
 */
export const removePopupNotifications = (index) => dispatch => {
    dispatch({
        type: NOTIFICATION.popup.remove,
        payload: index
    })
}