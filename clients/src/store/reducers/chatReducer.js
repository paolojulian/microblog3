import { CHAT } from '../types';

const initialState = {
    users: [
        {id: 1, user: {username: 'chefpipz'}},
        {id: 2, user: {username: 'chefclaire'}},
        {id: 3, user: {username: 'jhonnamae'}},
        {id: 4, user: {username: 'paolovincent'}},
    ],
    userInfo: {
        id: 2,
        user: {
            username: 'chefclaire'
        }
    },
    messages: [
        {id: 5, userId: 26, receiverId: 2, message: 'Akin'},
        {id: 4, userId: 2, receiverId: 26, message: 'Sayo'},
        {id: 3, userId: 26, receiverId: 2, message: 'Mine Again'},
        {id: 2, userId: 2, receiverId: 26, message: 'Yours'},
        {id: 1, userId: 2, receiverId: 26, message: 'Bobo'},
    ],
    newMessagesCount: 0
}

export default function(state = initialState, action) {

    switch (action.type) {

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

        default:
            return state;
    }

}
