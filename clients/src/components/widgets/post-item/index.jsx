import React from 'react'
import { withRouter } from 'react-router-dom';

import PCard from '../p-card';
import ProfileImage from '../profile-image';
import PostImage from '../post-image';
import Username from '../username';
import SharedPost from '../../post/item/shared-post';
import PostHeader from '../../post/header';

const postStyle = {
    post: {
        width: '100%',
        padding: '1rem 0',
    },
    header: {
        display: 'flex',
        justifyContent: 'center',
        alignItems: 'center',
        textAlign: 'left'
    },
    img: {
        margin: '0 0.5rem',
        marginLeft: '1rem'
    },
    info: {
        flex: '1'
    },
    title: {
        fontSize: '1.1rem',
        color: 'var(--green-primary)',
        letterSpacing: '1px',
        fontWeight: 500
    },
    body: {
        padding: '0.5rem 1rem',
        textAlign: 'left',
        wordWrap: 'break-word',
    }
}

const Minimal = ({
    post,
    history
}) => (
    <div 
       style={{ cursor: 'pointer' }}
        onClick={() => {
            history.push(`/posts/${post.id}`)
        }}>
        <div style={postStyle.post} className="hover-grey">
            <div 
                style={postStyle.header}>
                <div style={postStyle.img}>
                    <ProfileImage
                        src={post.user.avatar_url}
                        size={24}
                    />
                </div>
                <div style={postStyle.info}>
                    <span style={postStyle.title}>
                        {post.title ? post.title : 'Untitled'}
                    </span>
                    &nbsp;
                    &#8226;
                    &nbsp;
                    <Username username={post.user.username}/>
                </div>
            </div>
        </div>
    </div>
)

export const PostItem = ({ post }) => {
    // const { Post, User, isShared } = post;
    // let sharedPost = null;
    // if (isShared) {
    //     sharedPost = post.SharedPost;
    // }

    return (
        <PCard size="fit">
            {/* {isShared && <SharedPost
                postId={Number(sharedPost.Post.id)}
                userId={Number(sharedPost.Post.user_id)}
                originalUserId={Number(post.Post.user_id)}
                body={sharedPost.Post.body}
                avatarUrl={sharedPost.User.avatar_url}
                username={sharedPost.User.username}
                created={sharedPost.Post.created}
            />} */}

            <PostHeader
                postId={Number(post.id)}
                title={post.title}
                username={post.user.username}
                avatarUrl={post.user.avatar_url}
                created={post.created}
            />

            <div style={postStyle.body}>
                {post.body}
            </div>

            {!!post.img_path && <PostImage imgPath={post.img_path} title={post.title}/>}
        </PCard>
    )
}

export const PostItemMinimal = withRouter(Minimal);
export default PostItem;
