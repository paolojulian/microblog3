import React, { useCallback, useEffect, useState } from 'react';

import { useSelector, useDispatch } from 'react-redux';
import { Link } from 'react-router-dom';

/** Redux  */
import { fetchNotFollowed } from '../../store/actions/profileActions';

/** Components */
import PCard from '../widgets/p-card/p-card';
import ProfileImage from '../widgets/profile-image/profile-image';

const Header = () => (
    <div style={{
        fontSize: '1rem',
        fontStyle: 'italic',
        fontWeight: '400'
    }}>
        People you may know
    </div>
)

const MutualUser = ({
    mutual,
    user
}) => (
    <div style={{
        display: 'flex',
        justifyContent: 'center',
        alignItems: 'center',
        margin: '0.5rem 0',
        padding: '0.375rem 0',
    }}>
        <div style={{ margin: '0 1rem'}}>
            <ProfileImage
                src={user.avatar_url}
                size={32}
            />
        </div>
        <div style={{
            textAlign: 'left',
            flex: '1'
        }}>
            <Link to={`/profiles/${user.username}`}>
                <div style={{
                    textTransform: 'capitalize',
                    lineHeight: '0.75rem',
                }}>
                    {`${user.first_name} ${user.last_name}`}
                </div>
                <span className="username">@{user.username}</span>
            </Link>
            <div className="disabled"
                style={{
                    fontStyle: 'italic',
                    fontSize: '0.85rem',
                    lineHeight: '0.75rem',
                }}
            >
                {mutual > 0 && `Followed by ${mutual} of your followed user${mutual > 1 ? 's' : ''}`}
            </div>
        </div>
    </div>
)

const perPage = 5;

const PeopleYouMayKnow = () => {
    const { list, totalCount } = useSelector(state => state.recommended);
    const dispatch = useDispatch();
    const [page, setPage] = useState(1);

    /**
     * Goes to prev page if is the first page
     * go to the last page instead
     */
    const prevPage = useCallback(() => {
        if (page <= 1) {
            return setPage(Math.ceil(totalCount / perPage))
        }
        setPage(page - 1)
        // eslint-disable-next-line
    }, [page]);

    /**
     * Goes to the next page or if is last page
     * will go to first page
     */
    const nextPage = useCallback(() => {
        if (page * perPage >= totalCount) {
            return setPage(1);
        }
        setPage(page + 1)
        // eslint-disable-next-line
    }, [page]);

    /**
     * Next page every 5 seconds
     */
    useEffect(() => {
        let timeout = setTimeout(nextPage, 5000);
        return () => {
            clearTimeout(timeout)
        }
    }, [nextPage])

    useEffect(() => {
        dispatch(fetchNotFollowed(page));
    }, [page, dispatch])

    return (
        <PCard size="fit"
            header={<Header/>}
        >
            {list.length === 0 && (
                <div className="disabled">
                    No User/s Yet
                </div>
            )}
            {list.map((data, i) => {
                let mutual = data && data.hasOwnProperty('mutual')
                    ? data.mutual
                    : 0
                return (
                    <MutualUser
                        key={i}
                        mutual={mutual}
                        user={data}
                    />
                )
            })}
            {totalCount > 5 &&
                <div style={{
                    fontSize: '1.5rem',
                    userSelect: 'none'
                }}>
                    <span
                        style={{ cursor: 'pointer' }}
                        onClick={prevPage}>
                        &#171;
                    </span>
                    &nbsp;
                    <span
                        style={{ cursor: 'pointer' }}
                        onClick={nextPage}>
                        &#187;
                    </span>
                </div>
            }
        </PCard>
    )
}

export default PeopleYouMayKnow
