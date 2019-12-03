import React, { useRef, useState, useEffect } from 'react'
import queryString from 'query-string'
import { Link } from 'react-router-dom'
import { useDispatch } from 'react-redux'
import classNames from 'classnames'
import { withRouter } from 'react-router-dom';

/** Utils */
import InitialStatus from '../../utils/initial-status';

/** Redux */
import { apiSearch } from '../../../store/actions/searchActions';

import styles from './navbar.module.css'
import { PostItemMinimal } from '../../widgets/post-item';
import UserItem from '../../widgets/user';

const SearchBar = ({ history, location }) => {

    const dispatch = useDispatch();
    const searchText = useRef('');
    const [users, setUsers] = useState([]);
    const [posts, setPosts] = useState([]);
    const [willShow, setShow] = useState(false);
    const [noData, setNoData] = useState(false);
    const [hasMoreData, setHasMoreData] = useState(false);
    const [status, setStatus] = useState(InitialStatus);

    useEffect(() => {
        if (location.pathname === '/search') {
            searchText.current.value = queryString.parse(location.search).searchText;
        }
        document.body.addEventListener('click', resetState)
        return () => {
            document.body.removeEventListener('click', resetState)
        };
        // eslint-disable-next-line
    }, [])

    const resetState = () => {
        setShow(false);
    }

    const handleSearch = e => {
        if (e) e.preventDefault();
        if (searchText.current.value) {
            history.push(`/search?searchText=${getSearchText()}`)
        }
    }

    const handleChange = async value => {
        if (location.pathname === '/search') {
            // history.push(`/search?searchText=${getSearchText()}`)
            return;
        }
        setShow(value.length !== 0);
        setNoData(false);
        if (value.length === 0) {
            setNoData(true);
            setUsers([]);
            setPosts([]);
            return;
        }
        setStatus({...InitialStatus.LOADING});
        try {
            const data = await dispatch(apiSearch(searchText.current.value))
            if ( ! data) {
                return;
            }

            if (
                data.users.totalCount - data.users.list.length > 0 ||
                data.posts.totalCount - data.posts.list.length > 0
            ) {
                setHasMoreData(true);
            } else {
                setHasMoreData(false);
            }
            if (
                data.users.list.length === 0 &&
                data.posts.list.length === 0
            ) {
                setNoData(true);
            } else {
                setNoData(false);
            }
            setUsers(data.users.list);
            setPosts(data.posts.list);

            setStatus({...InitialStatus.POST});
        } catch (e) {
            setStatus({...InitialStatus.ERROR});
        }
    }

    const handleKeyPress = e => {
        // const re = /^[a-z0-9_ ]*$/i
        // if (!re.test(e.key)) {
        //     e.preventDefault();
        // }
    }

    const getSearchText = () => {
        // return searchText.current.value.replace(/[\W_]+/g," ");
        return searchText.current.value;
    }

    const renderUsers = () => users.map((user, i) => (
        <UserItem
            key={i}
            user={user}
            showFollow={false} />
    )); 
    
    const renderPosts = () => posts.map((post, i) => (
        <PostItemMinimal
            key={i}
            post={post}
        />
    ));

    const stopPropagate = e => {
        if (e) {
            e.stopPropagation();
            e.preventDefault();
        }
        return false;
    }

    return (
        <div className={styles.search}>
            <div className={styles.searchForm}
                onClick={() => setShow(true)}
            >
                <form onSubmit={handleSearch}>
                    <input type="text"
                        placeholder="Search"
                        name="search_bar"
                        ref={searchText}
                        onChange={e => handleChange(e.target.value)}
                        onKeyPress={handleKeyPress}
                        autoComplete="off"
                        />
                </form>
                <div className={classNames(styles.searchList, {
                    [styles.active]: willShow
                })}
                    onClick={stopPropagate}
                >
                    <div className={styles.searchContent}
                        style={{ overflowY: 'auto', maxHeight: '80vh' }}>
                        {status.error && 
                            <div className="alert-disabled">Oops Something went wrong.</div>
                        }

                        {status.loading && 
                            <div className="alert-disabled">
                                <i className="fa fa-spinner fa-spin"></i>
                                &nbsp;Searching..
                            </div>
                        }
                        {noData && <div className="alert-disabled">No data found.</div>}

                        {status.post && users.length > 0 &&
                        <div className={styles.users}>
                            <div className={styles.usersLabel}>Users</div>
                            {renderUsers()}
                        </div>}

                        {status.post && posts.length > 0 &&
                        <div className={styles.posts}>
                            <div className={styles.postsLabel}>Posts</div>
                            {renderPosts()}
                        </div>}
                    </div>
                    {hasMoreData && <Link to={`/search?searchText=${getSearchText()}`}>
                        <div className={styles.viewMore}>
                            View More
                        </div>
                    </Link>}
                </div>
            </div>
        </div>
    );
}

export default withRouter(SearchBar);