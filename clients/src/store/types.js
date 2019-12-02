
export const GET_ERRORS = "GET_ERRORS";
export const CLEAR_ERRORS = "CLEAR_ERRORS";
export const SET_CURRENT_USER = "SET_CURRENT_USER";

/** Profiles */
export const GET_PROFILE = "GET_PROFILE";
export const SET_PROFILE = "SET_PROFILE";
export const TOGGLE_LOADING_PROFILE = "IS_LOADING_PROFILE";
export const GET_ALL_PROFILES = "GET_ALL_PROFILES";
export const CLEAR_CURRENT_PROFILE = "CLEAR_CURRENT_PROFILE";
export const CLEAR_PROFILES = "CLEAR_PROFILES";
export const SET_NOT_FOLLOWED = "SET_NOT_FOLLOWED";
export const ADD_FOLLOWER = "ADD_FOLLOWER";
export const ADD_FOLLOWING = "ADD_FOLLOWING";

export const PROFILES = {
    clearProfile: "CLEAR_CURRENT_PROFILE"
}

/** TODO Posts */
export const SET_PAGE = "SET_PAGE";
export const SET_POSTS = "SET_POSTS";
export const TOGGLE_LOADING_POST = "IS_LOADING_POST";
export const ADD_POSTS = "ADD_POSTS";
export const CLEAR_POSTS = "CLEAR_POSTS";
export const GET_PROFILE_POSTS = "GET_PROFILE_POSTS";
/** TODO Like */
/** TODO Comment */
export const GET_POST_COMMENTS = "GET_POST_COMMENTS";
/** TODO Follow */
export const FOLLOW = {
    setFollow: "SET_FOLLOW",
    setIsFollowing: "SET_IS_FOLLOWING",
    setFollowers: "SET_FOLLOWERS",
    setFollowing: "SET_FOLLOWING"
}

export const RECOMMENDED = {
    setList: "SET_RECOMMENDED_LIST",
    setTotalCount: "SET_RECOMMENDED_TOTALCOUNT"
}

/** Notifications */
export const NOTIFICATION = {
    set:  "SET_NOTIFICATIONS",
    clear:  "CLEAR_NOTIFICATIONS",
    add:  "ADD_NOTIFICATION",
    setCount: 'SET_COUNT_NOTIFICATION',
    addCount: 'ADD_COUNT_NOTIFICATION',
    refresh: 'REFRESH_NOTIFICATION',

    popup: {
        clear: "CLEAR_POPUP_NOTIFICATIONS",
        add: "ADD_POPUP_NOTIFICATIONS",
        remove: "REMOVE_POPUP_NOTIFICATIONS",
    }
}

/** Refresh */
export const REFRESH = "REFRESH";