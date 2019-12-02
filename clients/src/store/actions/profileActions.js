import axios from 'axios';
import { search } from '../../utils/search';
import {
    SET_NOT_FOLLOWED,
    SET_PROFILE,
    TOGGLE_LOADING_PROFILE,
    ADD_FOLLOWING,
    ADD_FOLLOWER,
    PROFILES,
    FOLLOW
} from '../types';

/**
 * Get Profile By their username
 * or IF NULL get current profile
 */
export const getProfile = (username = null) => async dispatch => {
    try {
        dispatch({ type: TOGGLE_LOADING_PROFILE })
        let res;
        if (username) {
            res = await axios.get(`/api/users/${username}`)
        } else {
            res = await axios.get(`/api/auth/me`)
        }
        if (res.data.status !== 200) {
            throw new Error("Invalid Status");
        }
        dispatch({
            type: SET_PROFILE,
            payload: res.data.data
        })
        return Promise.resolve(res.data.data)
    } catch (e) {
        return Promise.reject()
    }
}

/**
 * Updates the details of the current user logged in
 */
export const updateProfile = (data) => async dispatch => {
    try {
        const res = await axios.put('/api/users', data);
        if (res.data.status !== 200) {
            throw new Error('Invalid Status');
        }
        return Promise.resolve(res.data.data)
    } catch (e) {
        return Promise.reject(e)
    }
}

/**
 * Uploads the image of the current user logged in
 */
export const uploadProfileImg = (img) => async dispatch => {
    try {
        const config = {
            headers: {
                'content-type': 'multipart/form-data'
            }
        }
        const formData = new FormData();
        formData.append('profile_img', img);
        const res = await axios.post('/api/users/update-image', formData, config)
        return Promise.resolve(res.data.data)
    } catch (e) {
        return Promise.reject(e)
    }
}

/**
 * Uploads the image of the current user logged in
 * @param Number - userId user to follow
 */
export const followUser = (userId) => async dispatch => {
    try {
        const res = await axios.post(`/api/users/${userId}/follow`);
        if (res.data.status !== 201) {
            throw new Error('Invalid Status');
        }
        return Promise.resolve(res.data.data);
    } catch (e) {
        return Promise.reject(e);
    }
}

/**
 * Uploads the image of the current user logged in
 * @param searchText - text to use for matching the desired user
 */
export const searchUser = (searchText) => async dispatch => {
    try {
        const data = await search(`/users/search/${searchText}.json`);
        return Promise.resolve(data);
    } catch (e) {
        return Promise.reject(e);
    }
}

/**
 * Fetch the followers or followed users
 * @param userId - user followers/followed to be seen
 * @param type - [follower/following] only
 */
export const fetchFollow = (userId, type, page = 1) => dispatch => {
    if (type === 'follower') {
        return dispatch(fetchFollowers(userId, page));
    }
    if (type === 'following') {
        return dispatch(fetchFollowing(userId, page));
    }
    return Promise.reject('Wrong type given');
}

/**
 * Fetches the followers of the given user
 * @param userId
 * @param page
 */
export const fetchFollowers = (userId, page = 1) => async dispatch => {
    try {
        const params = { page };
        const res = await axios.get(`/api/users/${userId}/followers`, { params });
        if (res.data.status !== 200) {
            throw new Error("Invalid status")
        }
        if ( ! Array.isArray(res.data.data)) {
            throw new Error("Invalid data type")
        }
        return Promise.resolve(res.data.data);
    } catch (e) {
        return Promise.reject(e);
    }
}

/**
 * Fetches the users being followed by the given user
 * @param userId
 * @param page
 */
export const fetchFollowing = (userId, page = 1) => async dispatch => {
    try {
        const params = { page };
        const res = await axios.get(`/api/users/${userId}/following`, { params });
        if (res.data.status !== 200) {
            throw new Error("Invalid status")
        }
        if ( ! Array.isArray(res.data.data)) {
            throw new Error("Invalid data type")
        }
        return Promise.resolve(res.data.data);
    } catch (e) {
        return Promise.reject(e);
    }
}

/**
 * Fetches the count of followers and following
 */
export const fetchFollowCount = (username) => async dispatch => {
    try {
        const res = await axios.get(`/api/users/${username}/follow/count`)
        dispatch({
            type: FOLLOW.setFollow,
            payload: {
                totalFollowers: res.data.data.followerCount,
                totalFollowing: res.data.data.followingCount,
            }
        })
        return Promise.resolve(res.data.data)
    } catch (e) {
        return Promise.reject()
    }
}

/**
 * Fetch the users who have not yet followed
 * prioritize the ones who have mutual connections
 */
export const fetchNotFollowed = (page = 1) => async dispatch => {
    try {
        const res = await axios.get(`/api/users/follow/recommended?pageNo=${page}`);
        dispatch({
            type: SET_NOT_FOLLOWED,
            payload: res.data.data
        });
        return Promise.resolve(res.data.data);
    } catch (e) {
        return Promise.reject(e);
    }
}

/**
 * Fetch the mutual friends with the given user
 * @param username - user to check mutual friends
 */
export const fetchMutualFriends = (username) => async dispatch => {
    try {
        const res = await axios.get(`/api/users/${username}/mutual`);
        return Promise.resolve(res.data.data);
    } catch (e) {
        return Promise.reject(e);
    }
}

/**
 * Fetch the mutual friends with the given user
 * @param username - user to check mutual friends
 */
export const isFollowing = (username) => async dispatch => {
    try {
        const res = await axios.get(`/api/users/${username}/is-following`);
        dispatch ({
            type: FOLLOW.setIsFollowing,
            payload: !!res.data.data
        })
        return Promise.resolve(res.data.data);
    } catch (e) {
        return Promise.reject(e);
    }
}

/**
 * Adds a follower to count
 * @param n - number to add
 */
export const addFollower = (n = 1) => dispatch => {
    dispatch({ type: ADD_FOLLOWER, payload: n })
}

/**
 * Adds a follower to count
 * @param n - number to add
 */
export const addFollowing = (n = 1) => dispatch => {
    dispatch({ type: ADD_FOLLOWING, payload: n })
}

/**
 * Sets the total follower count
 * @param n - number to add
 */
export const setFollowersCount = (n) => dispatch => {
    dispatch({ type: FOLLOW.setFollowers, payload: n })
}

/**
 * Sets the total following count
 * @param n - number to add
 */
export const setFollowingCount = (n) => dispatch => {
    dispatch({ type: FOLLOW.setFollowing, payload: n })
}

/**
 * Resets the current profile
 */
export const clearProfile = () => dispatch => {
    dispatch({ type: PROFILES.clearProfile })
}