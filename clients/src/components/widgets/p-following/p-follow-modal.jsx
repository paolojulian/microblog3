import React, { useEffect, useState } from 'react';
import PropTypes from 'prop-types';
import { useSelector, useDispatch } from 'react-redux';
import styles from './p-follow.module.css';

/** Redux */
import { fetchFollow } from '../../../store/actions/profileActions';

/** Utils */
import Pager from '../../utils/pager';

/** Components */
import PModal from '../../widgets/p-modal';
import PLoader from '../../widgets/p-loader';
import UserItem from '../user';

const availableTypes = ['follower', 'following'];
const PFollowModal = ({
    userId,
    type,
    onRequestClose,
}) => {
    const [isLoading, setLoading] = useState(true);
    const [isError, setError] = useState(false);
    const [isLastPage, setIsLastPage] = useState(false);
    const [users, setUsers] = useState([]);
    const [pager, setPager] = useState(Pager);
    const dispatch = useDispatch();
    const { id: loggedInUser } = useSelector(state => state.auth.user);

    useEffect(() => {
        let mounted = true;
        const init = async (mounted) => {
            if ( ! mounted) return;
            try {
                const res = await dispatch(fetchFollow(userId, type, pager.page))
                if (res.length === 0) {
                    return setIsLastPage(true);
                }
                setUsers([...users, ...res]);
            } catch (e) {
                setError(true);
            } finally {
                setLoading(false);
            }
        }

        if ( ! isLastPage) {
            init(mounted);
        }
        return () => {
            mounted = false;
        }
        // eslint-disable-next-line
    }, [pager]);

    if (availableTypes.indexOf(type) === -1) {
        return onRequestClose();
    }

    const renderBody = () => {
        if (isError) return <div className="disabled">Oops. Something went wrong</div>
        if (isLoading) return <PLoader/>
        if (users.length === 0) return <div className="disabled">No User/s</div>
        return (
            <>
                {users.map(({ user, ...item }, i) => {
                    let isFollowing = true;
                    if (type === 'follower') {
                        isFollowing = !!item.isFollowing
                    }
                    return <UserItem
                        key={i}
                        user={user}
                        isFollowing={isFollowing}
                        showFollow={Number(user.id) !== Number(loggedInUser) && type === 'follower'}
                        onRequestClose={onRequestClose}
                        closeOnClick={true}
                    />
                })}
            </>
        )
    }

    return (
        <PModal
            enableScrollPaginate={true}
            pager={pager}
            onScrollPaginate={page => setPager({ ...pager, page })}
            className={styles.modal}
            onRequestClose={onRequestClose}
            header={type === 'follower' ? 'Followers': 'Following'}
        >
            {isLoading ? <PLoader /> : renderBody()}
        </PModal>
    )
};

PFollowModal.propTypes = {
    userId: PropTypes.number.isRequired,
    type: PropTypes.string.isRequired
}

export default PFollowModal;