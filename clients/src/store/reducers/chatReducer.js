import { CHAT } from '../types';

const initialState = {
    ws: null,
    users: [],
    userInfo: {},
    messages: [],
    newMessagesCount: 0,

    messageLoading: false,
    error: false
}

export default function(state = initialState, action) {

    switch (action.type) {

        case CHAT.subscribe:
            return {
                ...state,
                ws: new WebSocket(action.payload)
            }

        case CHAT.unsubscribe:
            if (state.ws !== null) {
                state.ws.onclose = () => {}
                state.ws.close();
            }
            return { ...initialState }

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
            if ( ! Number.isInteger(action.payload.id)) {
                throw new Error("Invalid User ID passed");
            }

            action.payload.new = true;
            // Remove user from list if he is in the list
            let newUsers = state.users.filter(user => {
                return Number(user.id) !== Number(action.payload.id)
            })
            newUsers = [action.payload, ...newUsers];

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
