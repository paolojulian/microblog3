import { CHAT } from '../types';
import axios from 'axios';

export const setUsers = (users) => dispatch => {
    dispatch({ type: CHAT.setUsers, payload: users });
}

export const setUserToFirst = (user) => dispatch => {
    dispatch({ type: CHAT.setUserToFirst, payload: user });
}

export const setMessages = (messages) => dispatch => {
    dispatch({ type: CHAT.setMessages, payload: messages });
}

export const addMessages = (messages) => dispatch => {
    dispatch({ type: CHAT.addMessages, payload: messages });
}

export const addMessageToFirst = (message) => dispatch => {
    dispatch({ type: CHAT.addMessageToFirst, payload: message });
}

export const subscribeToChatAPI = (userId) => async dispatch => {
    try {
        const websocket = new WebSocket(`ws://127.0.0.1:4567/chat?id=${userId}`);
        return Promise.resolve(websocket);
    } catch (e) {
        return Promise.reject(e);
    }
}

export const unsubscribeToChatAPI = (userId) => async dispatch => {
    try {
        const websocket = new WebSocket(`ws://127.0.0.1:4567/chat?id=${userId}`);
        return Promise.resolve(websocket);
    } catch (e) {
        return Promise.reject(e);
    }
}

export const fetchUsersToChatAPI = (page = 1) => async dispatch => {
    try {
        const params = { page };
        const res = await axios.get(`/api/chats`, { params });
        if (res.data.status !== 200) {
            throw new Error('Invalid Status');
        }
        if ( ! Array.isArray(res.data.data)) {
            throw new Error('Invalid Data');
        }
        dispatch(setUsers(res.data.data));
        return Promise.resolve(res.data.data);
    } catch (e) {
        dispatch({ type: CHAT.setError });
        // TODO on error display message as error
        return Promise.reject(e);
    }
}

export const fetchMessagesAPI = (userId, page = 1) => async dispatch => {
    try {
        const params = { page };
        const res = await axios.get(`/api/chats/${userId}`, { params });
        const { data } = res.data;
        if (res.data.status !== 200) {
            throw new Error('Invalid Status');
        }
        if ( ! Array.isArray(data.messages)) {
            throw new Error('Invalid Data');
        }
        dispatch(setMessages(data.messages))
        dispatch({ type: CHAT.setUserInfo, payload: data.userInfo });
        return Promise.resolve(data);
    } catch (e) {
        dispatch({ type: CHAT.setError });
        // TODO on error display message as error
        return Promise.reject(e);
    }
}

export const addMessageAPI = (data) => async dispatch => {
    try {
        dispatch(addMessageToFirst(data));
        const res = await axios.post('/api/chats', data);
        if (res.data.status !== 200) {
            throw new Error('Invalid Status');
        }
        // TODO Send message to websocket
        return Promise.resolve(res.data.data);
    } catch (e) {
        // TODO on error display message as error
        return Promise.reject(e);
    }
}

// TODO add listener
