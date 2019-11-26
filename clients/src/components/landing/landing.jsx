import React, { useEffect, useState } from 'react';
import { useSelector, useDispatch } from 'react-redux';
import { Link } from 'react-router-dom';
import styles from './landing.module.css';
import WithNavbar from '../hoc/with-navbar';

/** Redux */
import { getProfile, fetchFollowCount, fetchNotFollowed } from '../../store/actions/profileActions';
import { getPostsForLanding } from '../../store/actions/postActions';
import { CLEAR_POSTS } from '../../store/types';

/** Components */
import PCard from '../widgets/p-card';
import ServerError from '../widgets/server-error';
import ProfileCard from './profile-card';
import PeopleYouMayKnow from './people-you-may-know';
import Posts from '../post';
import PostCreate from '../post/create';
import LandingLoading from './landing-loading';

const Landing = () => {
    const dispatch = useDispatch()
    const [isLoading, setLoading] = useState(true);
    const [isError, setError] = useState(false);
    const { refreshToken } = useSelector(state => state.refresh);
    const { user: { username } } = useSelector(state => state.auth);

    useEffect(() => {
        window.scrollTo({ top: 0, left: 0 });
        const init = async () => {
            try {
                setLoading(true);
                await dispatch(getProfile(username));
                await fetchHandler();
                dispatch(fetchNotFollowed());
                dispatch(fetchFollowCount(username));
            } catch (e) {
                setError(true);
            } finally {
                setLoading(false);
            }
        }
        init();
        return () => {
            dispatch({ type: CLEAR_POSTS })
        }
    // eslint-disable-next-line react-hooks/exhaustive-deps
    }, [refreshToken])

    const fetchHandler = async (page = 1) => {
        console.log(page);
        await dispatch(getPostsForLanding(page))
    };

    if (isError) {
        return <ServerError/>
    }

    if (isLoading) {
        return <LandingLoading/>
    }

    return (
        <div className={styles.landing}>
            <div className={styles.profile}>
                <ProfileCard size="fit"/>
                <div className={styles.editProfile}>
                    <Link to="/settings/update-profile">
                        <PCard size="fit">
                            <i className="fa fa-gear"></i>
                            &nbsp;Edit Profile
                        </PCard>
                    </Link>
                </div>
            </div>
            <div className={styles.container}>
                <div className={styles.posts}>
                    <PostCreate size="fit"/>
                    <Posts fetchHandler={fetchHandler}/>
                </div>
            </div>
            <div className={styles.right}>
                <PeopleYouMayKnow />
            </div>
        </div>
    )
}

export default WithNavbar(Landing)