import axios from 'axios';
import { search } from '../../utils/search';
import {
    SET_NOT_FOLLOWED,
    SET_PROFILE,
    TOGGLE_LOADING_PROFILE,
    ADD_FOLLOWING,
    ADD_FOLLOWER,
    FOLLOW
} from '../types';

/**
 * Get profile of current logged in user
 */
export const getProfile = (username) => async dispatch => {
    try {
        dispatch({ type: TOGGLE_LOADING_PROFILE })
        const res = await axios.get(`/api/users/${username}`)
        dispatch({
            type: SET_PROFILE,
            payload: res.data.data
        })
        await dispatch(fetchFollowCount(username));
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
        const res = await axios.put('/users/edit.json', data);
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
        const res = await axios.post('/profiles/uploadimage.json', formData, config)
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
export const fetchFollow = (userId, type, page = 1) => async dispatch => {
    try {
        const res = await axios.get(`/followers.json`, {
            params: {userId, type, page}
        });
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