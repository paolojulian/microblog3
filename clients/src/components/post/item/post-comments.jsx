import React, { useEffect, useState } from 'react';
import { useDispatch } from 'react-redux';
import styles from './post-item.module.css';

/** Utils */
import InitialStatus from '../../utils/initial-status';

/** Redux */
import { getCommentsByPost } from '../../../store/actions/postActions';

/** Components */
import CommentCreate from '../comment/create';
import PostComment from '../comment';
import PLoader from '../../widgets/p-loader';
import LoadMore from '../../widgets/load-more';

const PostComments = ({
    postId,
    onUpdateCommentCount,
    onRequestClose,
    onRequestSuccessCreate
}) => {

    const dispatch = useDispatch();
    const [status, setStatus] = useState(InitialStatus.LOADING);
    const [comments, setComments] = useState([]);
    const [totalLeft, setTotalLeft] = useState(0);
    const [page, setPage] = useState(1);

    useEffect(() => {
        const fetchComments = async () => {
            try {
                const res = await dispatch(getCommentsByPost(postId, page))

                if (page === 1) {
                    setComments(res.list)
                    setTotalLeft(res.totalCount - res.list.length);
                } else {
                    setComments([...comments, ...res.list])
                    setTotalLeft(res.totalCount - [...comments, ...res.list].length);
                }
                onUpdateCommentCount(Number(res.totalCount));
                setStatus({ ...InitialStatus, post: true })
            } catch (e) {
                setStatus({ ...InitialStatus, error: true })
            }
        }
        if (page && !!postId) {
            fetchComments(page);
        }
        // eslint-disable-next-line
    }, [page, postId])

    const renderStatus = () => {
        if (status.error) {
            return <div className="disabled">Oops. Something went wrong</div>
        }

        if (status.loading) {
            return <PLoader />
        }
        return ''
    }

    return (
        <div className={styles.postComments}>
            <CommentCreate
                postId={Number(postId)}
                onRequestSuccess={(data) => {
                    setPage(false);
                    setPage(1);
                    onRequestSuccessCreate(data)
                }}
                onRequestClose={onRequestClose}
            />
            {comments.length > 0 && <div className={styles.commentsTitle}>
                Comments:
            </div>}
            <div className={styles.comments}>
                <PostComment
                    comments={comments}
                    reloadPost={() => {
                        setPage(false);
                        setPage(1);
                    }}
                />
                <LoadMore
                    totalLeft={totalLeft}
                    onRequestLoad={() => setPage(page + 1)}
                    />
            </div>
            <div className={styles.status}>
                {renderStatus()}
            </div>
            <div className={styles.closeComment}>
                <span onClick={onRequestClose}>
                    Close &times;
                </span>
            </div>
        </div>
    )
}

export default PostComments
