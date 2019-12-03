import { CHAT } from '../types';

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