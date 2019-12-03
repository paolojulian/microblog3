import React, { useCallback, useState, useRef, useContext } from 'react';
import PropTypes from 'prop-types';
import { useDispatch } from 'react-redux';
import styles from './create.module.css'

/** Context */
import { ModalContext } from '../../widgets/p-modal/p-modal-context'

/** Redux */
import { addComment } from '../../../store/actions/postActions';
import { CLEAR_ERRORS } from '../../../store/types'

/** Components */
import FormTextArea from '../../widgets/form/textarea/form-textarea';

const initialError = {
    body: ''
}

const CommentCreate = ({
    postId,
    onRequestSuccess,
}) => {

    const dispatch = useDispatch();
    const context = useContext(ModalContext);
    const comment = useRef('');
    const [errors, setErrors] = useState(initialError);

    const handleSubmit = useCallback(e => {
        if (e) {
            e.preventDefault();
            e.stopPropagation();
        }
        const form = {
            body: comment.current.value
        }
        dispatch(addComment(postId, form))
            .then(handleSuccess)
            .catch(handleError)
        // eslint-disable-next-line
    }, [comment])

    const handleSuccess = () => {
        comment.current.value = ''
        onRequestSuccess();
        setErrors({ ...initialError });
        dispatch({ type: CLEAR_ERRORS })
    }

    const handleError = e => {
        try {
            if (e.response.status !== 422) {
                throw new Error();
            }
            setErrors(e.response.data.data);
        } catch (err) {
            context.notify.serverError();
        }
    }

    return (
        <div className={styles.wrapper}>
            <form
                onSubmit={handleSubmit}
                className="form"
            >
                <FormTextArea
                    name="comment"
                    placeholder="Write a comment"
                    refs={comment}
                    error={errors.body}
                    rows={2}
                    isRequired={true}
                    submitOnEnter={handleSubmit}
                />
            </form>
        </div>
    );
}

CommentCreate.propTypes = {
    postId: PropTypes.number.isRequired,
    onRequestSuccess: PropTypes.func.isRequired,
}

export default CommentCreate