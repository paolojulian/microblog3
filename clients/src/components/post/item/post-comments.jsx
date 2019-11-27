import React, { useEffect, useState } from 'react';
import { useDispatch } from 'react-redux';
import styles from './post-item.module.css';

/** Utils */
import initialStatus from '../../utils/initial-status';

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
    const [status, setStatus] = useState(initialStatus);
    const [comments, setComments] = useState([]);
    const [totalLeft, setTotalLeft] = useState(0);
    const [page, setPage] = useState(1);

    useEffect(() => {
        if (page) {
            getComments(page);
        }
    }, [page, getComments])

    const getComments = async(pageNo = 1) => {
        try {
            setStatus({ ...initialStatus, loading: true })
            const res = await dispatch(getCommentsByPost(postId, pageNo))

            if (pageNo === 1) {
                setComments(res.list)
                setTotalLeft(res.totalCount - res.list.length);
            } else {
                setComments([...comments, ...res.list])
                setTotalLeft(res.totalCount - [...comments, ...res.list].length);
            }

            onUpdateCommentCount(Number(res.totalCount));
            setPage(pageNo);
            setStatus({ ...initialStatus, post: true })
        } catch (e) {
            setStatus({ ...initialStatus, error: true })
        }
    }

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
                    getComments(1)
                    onRequestSuccessCreate(data)
                }}
                onRequestClose={onRequestClose}
            />
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
