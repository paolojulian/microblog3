import axios from 'axios';
import { SET_CURRENT_USER } from '../types';
import jwtDecode from 'jwt-decode';
import setTokenToAuthHeader from '../../utils/setTokenToAuthHeader';

// Login user
export const loginUser = (userCredentials, history) => async dispatch => {
    const res = await axios.post('/api/auth/login', userCredentials)

    const { token } = res.data.data;
    localStorage.setItem("jwtToken", token);
    setTokenToAuthHeader(token);
    const decoded = jwtDecode(token);
    dispatch(setCurrentUser(decoded));
}

export const registerUser = (user, history) => async dispatch => {
    await axios.post('/api/auth/register', user)
}

export const setCurrentUser = decoded => {
    return {
        type: SET_CURRENT_USER,
        payload: decoded
    };
}

// /**
//  * Logout the user from client
//  */
export const logoutUser = (history = null) => dispatch => {
    localStorage.removeItem("jwtToken");
    // Remove auth header for future requests
    setTokenToAuthHeader(false);

    dispatch(setCurrentUser({}));
    if (history) {
        history.push('/login');
    }
}