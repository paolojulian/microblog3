import React from 'react';
import moment from 'moment';
import PropTypes from 'prop-types';
import { Link } from 'react-router-dom';
import { useSelector } from 'react-redux';
import styles from './post-comment.module.css';

/** Components */
import ProfileImage from '../../widgets/profile-image';
import CommentDelete from './delete';

/** Consumers */
import { ModalConsumer } from '../../widgets/p-modal/p-modal-context';
import Username from '../../widgets/username';

const CommentItem = ({
    id,
    body,
    userId,
    username,
    avatarUrl,
    created,
    reloadPost
}) => {

    const { user: loggedIn } = useSelector(state => state.auth);

    return (
        <div className={styles.commentItem}>
            <div className={styles.itemHeader}>
                <div className={styles.profileImg}>
                    <ProfileImage
                        size={24}
                        src={avatarUrl}
                    />
                </div>
                <Username username={username}/>
                <span className={styles.time}>
                    &nbsp;
                    &#8226;
                    &nbsp;
                    {moment(created).fromNow()}
                </span>
                {Number(loggedIn.id) === userId && <ModalConsumer>
                    {({ showModal, hideModal }) => (
                        <div className={styles.deleteBtn}>
                            <i className="fa fa-trash"
                                onClick={() => showModal(CommentDelete, {
                                    id,
                                    onRequestClose: hideModal,
                                    onRequestSuccess: reloadPost
                                })}
                            />
                        </div>
                    )}
                </ModalConsumer>}
            </div>
            <div className={styles.commentBody}>
                {body}
            </div>
        </div>
    );
}

CommentItem.propTypes = {
    id: PropTypes.number.isRequired,
    body: PropTypes.string.isRequired,
    userId: PropTypes.number.isRequired,
    username: PropTypes.string.isRequired,
    avatarUrl: PropTypes.string,
    created: PropTypes.any.isRequired,
    reloadPost: PropTypes.func.isRequired,
}

CommentItem.defaultProps = {
}

export default CommentItem