import {
    RECOMMENDED
} from '../types';

const initialState = {
    list: [],
    totalCount: 0
}

export default (state = initialState, action) => {

    switch (action.type) {
        case RECOMMENDED.setList:
            return {
                ...state,
                list: action.payload
            }
        case RECOMMENDED.setTotalCount:
            return {
                ...state,
                totalCount: action.payload
            }
        default:
            return state;
    }

}