import React, { useState, useRef, useEffect } from 'react';
import { useDispatch, useSelector } from 'react-redux';

/** Redux */
import { CLEAR_ERRORS } from '../../../store/types';
import { sharePost } from '../../../store/actions/postActions';

/** Utils */
import InitialStatus from '../../utils/initial-status';

/** Components */
import PModal from '../../widgets/p-modal';
import FormTextArea from '../../widgets/form/textarea/form-textarea';

const initialState = {
    body: ''
};

const PostShare = ({
    id,
    creator,
    onRequestClose,
    onSuccess,
}) => {

    const [status, setStatus] = useState(InitialStatus);
    const { user } = useSelector(state => state.auth);
    const errors = useSelector(state => state.errors);
    const [state, setState] = useState(initialState);
    const dispatch = useDispatch();

    // Check if post is yours
    const isMine = user.username === creator;

    useEffect(() => {
        return () => {
            dispatch({ type: CLEAR_ERRORS })
        };
        // eslint-disable-next-line react-hooks/exhaustive-deps
    }, [])

    const onChange = e => {
        setState({ ...state, [e.target.name]: e.target.value });
    }

    const handleShare = async (e) => {
        if (e) {
            e.preventDefault();
        }
        if (status.loading) return false;
        try {
            setStatus({ ...InitialStatus, loading: true });
            await dispatch(sharePost(id, state.body))
            onSuccess();
            setStatus({ ...InitialStatus, post: true });
        } catch (e) {
            if (e.response.status !== 422) {
                setStatus({ ...InitialStatus, error: true });
            } else {
                setStatus({ ...InitialStatus });
            }
        }
    }

    if (status.error) {
        return (
            <PModal onRequestClose={onRequestClose}>
                <div className="disabled">Oops. something went wrong</div>
            </PModal>
        )
    }

    if (status.post) {
        let message = '';
        if (isMine) {
            message = `You successfully shared your own post`;
        } else {
            message = `You successfully shared @${creator}'s post`;
        }
        return (
            <PModal onRequestClose={onRequestClose}>
                {message}
            </PModal>
        )
    }

    return (
        <PModal type="submit"
            onRequestSubmit={handleShare}
            isLoading={status.loading}
            onRequestClose={onRequestClose}
            header={isMine ? 'Share own post' : `Share ${creator}'s post`}
        >
            <FormTextArea 
                placeholder="Body"
                name="body"
                info="Say something about the post (Optional)"
                error={errors.body}
                value={state.body}
                onChange={onChange}
                max={140}
                maxLength={140}
            />
        </PModal>
    )
};

export default PostShare;