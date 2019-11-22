import React from 'react'
import styles from './post.module.css'

/** Redux */
import { useSelector } from 'react-redux'

/** Components */
import PostItem from './item'
import OnScrollPaginate from '../utils/on-scroll-paginate'
import PCard from '../widgets/p-card/p-card'

const Post = ({ fetchHandler }) => {
    const { list: posts, page } = useSelector(state => state.post)
    const { id } = useSelector(state => state.auth.user)
    
    const renderPosts = () => posts.map((post, i) => {
        const sharedPost = {
            userId: Number(post.shared_user_id),
            username: post.shared_username,
            avatarUrl: post.shared_avatar_url,
            body: post.shared_body,
            created: post.shared_created
        }
        return (
            <div key={post.id}>
                <PostItem
                    isShared={!!post.is_shared}
                    sharedPost={sharedPost}
                    id={Number(post.id)}
                    avatarUrl={post.avatar_url}
                    title={post.title}
                    body={post.body}
                    created={post.created}
                    creator={post.username}
                    imgPath={post.img_path}
                    retweet_post_id={Number(post.retweet_post_id)}
                    user_id={Number(post.user_id)}

                    likes={post.likes}
                    comments={post.comments}
                    loggedin_id={Number(id)}
                    fetchHandler={fetchHandler}
                />
            </div>
        )
    })

    const renderEmpty = () => (
        <PCard size="fit" style={{marginTop: '0.5rem'}}>
            <div className="disabled">No Post/s to show</div>
        </PCard>
    )

    return (
        <OnScrollPaginate
            className={styles.posts}
            id="posts"
            fetchHandler={fetchHandler}
            page={page}
        >
            {!posts || posts.length === 0 ? renderEmpty() : renderPosts()}
        </OnScrollPaginate>
    )
}

export default Post
