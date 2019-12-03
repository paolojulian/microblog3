import { search } from '../../utils/search';

export const apiSearch = (searchText) => async dispatch => {
    try {
        const res = await search(`/api/search?text=${searchText}`);
        return Promise.resolve(res);
    } catch (e) {
        return Promise.reject(e);
    }
}

export const apiSearchUsers = (searchText, page = 1) => async dispatch => {
    try {
        const res = await search(`/api/search/users?text=${searchText}&page=${page}`);
        return Promise.resolve(res);
    } catch (e) {
        return Promise.reject(e);
    }
}

export const apiSearchPosts = (searchText, page = 1) => async dispatch => {
    try {
        const res = await search(`/api/search/posts?text=${searchText}&page=${page}`);
        return Promise.resolve(res);
    } catch (e) {
        return Promise.reject(e);
    }
}
