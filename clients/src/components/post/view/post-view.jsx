import React, { useEffect, useState } from 'react';
import { useDispatch, useSelector } from 'react-redux';
import { withRouter } from 'react-router-dom';

import styles from './post-view.module.css';

/** Redux */
import { getPostById } from '../../../store/actions/postActions';

/** Components */
import PostItem from '../item';
import WithNavbar from '../../hoc/with-navbar';
import InitialStatus from '../../utils/initial-status.js';
import PostItemWireframe from '../item/post-item-wireframe';

const PostView = (props) => {
    const { id } = props.match.params;
    const dispatch = useDispatch();
    const { user } = useSelector(state => state.auth);
    const [status, setStatus] = useState(InitialStatus);
    const [post, setPost] = useState({});
    const [profile, setProfile] = useState('');
    const [isShared, setShared] = useState(false);
    /** Only if post is a shared Post */
    const [sharedPost, setSharedPost] = useState({});

    useEffect(() => {
        reloadPost();
        // eslint-disable-next-line react-hooks/exhaustive-deps
    }, [props.match.params]);

    const reloadPost = async () => {
        try {
            setStatus({ ...InitialStatus.LOADING })
            const { isShared, post, ...response} = await dispatch(getPostById(id));
            setPost(post);
            setProfile(post.user);
            setShared(isShared);
            if (isShared) {
                setSharedPost(response.sharedPost);
            }
            setStatus({ ...InitialStatus.POST })
        } catch (e) {
            setStatus({ ...InitialStatus.ERROR })
        }
    }

    const redirectOnSuccess = (link = '/') => {
        props.history.push(link);
    }

    if (status.loading) {
        return (
            <div className={styles.wrapper}>
                <PostItemWireframe />
            </div>
        )
    }

    if (status.error) {
        return <div className="disabled">Oops. Something went wrong</div>
    }

    return (
        <div className={styles.wrapper}>
            <PostItem
                openCommentOnStart={true}
                isShared={isShared}
                sharedPost={isShared ? {
                    userId: Number(sharedPost.user_id),
                    username: sharedPost.user.username,
                    avatarUrl: sharedPost.user.avatar_url,
                    body: sharedPost.body,
                    created: sharedPost.created
                } : null}

                avatarUrl={profile.avatar_url}
                creator={profile.username}

                id={isShared ? Number(sharedPost.id): Number(post.id)}
                title={post.title}
                body={post.body}
                created={post.created}
                imgPath={post.img_path}
                retweet_post_id={isShared ? sharedPost.retweet_post_id : post.retweet_post_id}
                user_id={Number(post.user_id)}

                likes={isShared ? sharedPost.likes: post.likes}
                comments={isShared ? sharedPost.comments: post.comments}
                loggedin_id={Number(user.id)}
                fetchHandler={reloadPost}
                redirectOnSuccess={redirectOnSuccess}
            />
        </div>
    );
}

PostView.propTypes = {
}

PostView.defaultProps = {
}

export default withRouter(WithNavbar(PostView))
