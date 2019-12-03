import { combineReducers } from 'redux';
import auth from './authReducer';
import errors from './errorReducer';
import profile from './profileReducer';
import post from './postReducer';
import notification from './notificationReducer';
import refresh from './refreshReducer';
import recommended from './recommendedReducer';
import chat from './chatReducer';

export default combineReducers({
    auth,
    errors,
    profile,
    post,
    notification,
    refresh,
    recommended,
    chat
});