import React, { useRef, useState, useEffect, useContext } from 'react'
import { useDispatch, useSelector, connect } from 'react-redux'

/** Utils */
import InitialStatus from '../../utils/initial-status.js';

/** Redux Actions */
import { CLEAR_ERRORS } from '../../../store/types'
import { updatePost } from '../../../store/actions/postActions'

/** Components */
import FormInput from '../../widgets/form/input'
import FormTextarea from '../../widgets/form/textarea'
import FormImage from '../../widgets/form/image'

/** Context */
import { ModalContext } from '../../widgets/p-modal/p-modal-context'
import PModal from '../../widgets/p-modal/p-modal'

const PostEdit = ({
    updatePost,
    id,
    isShared,
    onSuccess,
    onRequestClose,
    ...props
}) => {

    const dispatch = useDispatch()
    const context = useContext(ModalContext)
    const { errors } = useSelector(state => state)
    const [imgError, setImgError] = useState('');
    const imgRef = useRef()
    const [didChangeImg, setDidChangeImg] = useState(false);
    const [status, setStatus] = useState(InitialStatus)
    const [state, setState] = useState({
        title: props.title,
        body: props.body
    });
    const [title, setTitle] = useState(props.title);
    const [body, setBody] = useState(props.body);
    useEffect(() => {
        return () => {
            dispatch({ type: CLEAR_ERRORS })
        }
        // eslint-disable-next-line react-hooks/exhaustive-deps
    }, [])

    const handleSubmit = async (e) => {
        if (e) {
            e.preventDefault();
        }
        let form = {
            title,
            body,
        }
        if (didChangeImg) {
            form.img = imgRef.current.files[0] ? imgRef.current.files[0] : -1
        }
        // 10mb
        if (form.img && form.img.size > 1048576) {
            return setImgError('Can only upload up to 1 mb');
        }
        try {
            setStatus({ ...InitialStatus, loading: true });
            await updatePost(id, form)
            context.notify.success("Updated Successfully!");
            onSuccess();
            setStatus({ ...InitialStatus, post: true });
        } catch (e) {
            handleError(e);
        }
    }

    const handleError = (e) => {
        try {
            if (e.response.status !== 422) {
                setStatus({ ...InitialStatus, error: true });
                throw new Error();
            }
            setStatus({ ...InitialStatus, loading: false });
        } catch (e) {
            context.notify.serverError();
        }
    }

    if (status.error) {
        return (
            <PModal onRequestClose={onRequestClose}>
                <div className="disabled">Oops. Something went wrong</div>
            </PModal>
        )
    }

    return (
        <PModal
            type="submit"
            size="30rem"
            header="Edit your post"
            isLoading={status.loading}
            onRequestClose={onRequestClose}
            onRequestSubmit={handleSubmit}>
            { ! isShared && <FormInput
                placeholder="Title"
                name="title"
                info="The title of your post (Optional)"
                error={errors.title}
                value={title}
                onChange={e => setTitle(e.target.value)}
                max={30}
                autoComplete="off"
            />}
            <FormTextarea
                placeholder="Body"
                name="body"
                info="What's on your mind?"
                error={errors.body}
                value={body}
                onChange={e => setBody(e.target.value)}
                isRequired={true}
                max={140}
            />

            { ! isShared && <FormImage
                name="profile_image"
                refs={imgRef}
                error={imgError}
                initSrc={props.imgPath ? props.imgPath + 'x256.png' : ''}
                onChangeImg={() => setDidChangeImg(true)}
            />}
        </PModal>
    )
}

PostEdit.defaultProps = {
    isShared: false
}

export default connect(null, { updatePost })(PostEdit)
