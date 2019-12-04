import { CHAT } from '../types';

const initialState = {
    users: [],
    userInfo: {},
    messages: [],
    newMessagesCount: 0,

    messageLoading: false,
    error: false
}

export default function(state = initialState, action) {

    switch (action.type) {

        case CHAT.setUserInfo:
            return {
                ...state,
                userInfo: action.payload
            }
        /**
         * Sets the users to be displayed
         */
        case CHAT.setUsers:
            return {
                ...state,
                users: action.payload
            }
        /**
         * Sets the user passed as the first one in the array
         */
        case CHAT.setUserToFirst:
            if ( ! Number.isInteger(action.payload.user.id)) {
                throw new Error("Invalid User ID passed");
            }

            // Remove user from list if he is in the list
            let newUsers = state.users.filter(user => {
                return user.id !== action.payload.user.id
            })
            newUsers = [action.payload.user, ...newUsers];

            return {
                ...state,
                users: newUsers
            }

        /**
         * Sets the messages
         */
        case CHAT.setMessages:
            if ( ! Array.isArray(action.payload)) {
                throw new Error("Passed data is not an array");
            }
            return {
                ...state,
                messages: action.payload
            }

        /**
         * Sets the messages
         */
        case CHAT.addMessages:
            if ( ! Array.isArray(action.payload)) {
                throw new Error("Passed data is not an array");
            }
            return {
                ...state,
                messages: [...state.messages, ...action.payload]
            }

        /**
         * Add a message to the first of array
         */
        case CHAT.addMessageToFirst:
            return {
                ...state,
                messages: [action.payload, ...state.messages]
            }
        
        case CHAT.setError:
            return {
                ...state,
                error: true
            }

        default:
            return state;
    }

}
