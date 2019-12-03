import React, { useRef, useState, useEffect, useContext } from 'react'
import styles from './post-create.module.css'
import { useDispatch, connect } from 'react-redux'
import { withRouter } from 'react-router-dom';
import classnames from 'classnames';

/** Redux Actions */
import { CLEAR_ERRORS } from '../../../store/types'
import { addPost } from '../../../store/actions/postActions'

/** Components */
import PCard from '../../widgets/p-card'
import PFab from '../../widgets/p-fab'
import PLoader from '../../widgets/p-loader'
import FormInput from '../../widgets/form/input'
import FormTextarea from '../../widgets/form/textarea'
import FormImage from '../../widgets/form/image'

/** Context */
import { ModalContext } from '../../widgets/p-modal/p-modal-context'

const initialError = {
    title: '',
    body: ''
}

const PostCreate = ({
    addPost,
    ...props
}) => {
    const dispatch = useDispatch()
    const context = useContext(ModalContext);
    /**
     * Toggler if component will show create post or display
     * a button that will open a create post card
     */
    const [willCreate, setWillCreate] = useState(false)
    const [errors, setErrors] = useState({ ...initialError })
    const [isLoading, setLoading] = useState(false)
    const form = useRef('')
    const title = useRef('')
    const body = useRef('')
    const img = useRef('')

    useEffect(() => {
        return () => {
            dispatch({ type: CLEAR_ERRORS })
        }
    // eslint-disable-next-line react-hooks/exhaustive-deps
    }, [])

    useEffect(() => {
        if (false === willCreate) {
            setWillCreate(false)
            setErrors({ ...initialError })
            dispatch({ type: CLEAR_ERRORS })
            return;
        }

    }, [willCreate, dispatch])

    const handleSubmit = e => {
        if (e) {
            e.preventDefault();
        }
        setErrors({ ...initialError })
        setLoading(true)
        const form = {
            title: title.current.value,
            body: body.current.value,
            img: img.current.files[0]
        }
        // 10mb
        if (form.img && form.img.size > 1048576) {
            setLoading(false);
            return setErrors({ img: 'Can only upload up to 1 mb' });
        }
        addPost(form, props.history)
            .then(handleSuccess)
            .catch(handleError)
            .then(() => setLoading(false))
    }

    const handleSuccess = () => {
        context.notify.success('Your post was successfully created!');
        setWillCreate(false);
    }

    const handleError = (e) => {
        try {
            switch(e.response.status) {
                case 413:
                    return setErrors({ img: 'Image size should not exceed 25 mb' })
                case 415:
                    return setErrors({ img: 'File is not supported!' })
                case 422:
                    return setErrors(e.response.data.data);
                default:
                    throw new Error(e);
            }
        } catch (e) {
            context.notify.serverError();
        }
    }

    return (
        <div className={willCreate ? styles.wrapper: ''}>
            {willCreate && <div className={styles.overlay}></div>}

            <PCard className={classnames(styles.card, {[styles.focus]: willCreate})}>
                <span className="text-link italic"
                    onClick={() => setWillCreate(!willCreate)}
                >
                    Write a post&nbsp;
                    <i className="fa fa-edit"/>
                </span>
                {willCreate && 
                <form
                    ref={form}
                    className="form"
                    onSubmit={handleSubmit}
                >
                    <FormInput
                        placeholder="Title"
                        name="title"
                        refs={title}
                        info="The title of your post (Optional)"
                        error={errors.title}
                    />
                    <FormTextarea
                        placeholder="Body"
                        name="body"
                        refs={body}
                        info="What's on your mind?"
                        error={errors.body}
                        isRequired={true}
                    />

                    <FormImage
                        name="profile_image"
                        refs={img}
                        error={errors.img}
                        height="15rem"
                    />

                    <br />

                    {isLoading
                        ? <div className={styles.action_btns}><PLoader/></div>
                        : (
                            <div className={styles.action_btns}>
                                <PFab
                                    type="submit"
                                    theme="primary"
                                    className={styles.action_btn}
                                >
                                    <i className="fa fa-check"/>
                                </PFab>

                                <PFab
                                    theme="secondary"
                                    onClick={() => setWillCreate(false)}
                                    className={styles.action_btn}
                                >
                                    &#10006;
                                </PFab>
                            </div>
                        )
                    }

                </form>}
            </PCard>
        </div>
    )
}

export default connect(null, { addPost })(withRouter(PostCreate))
