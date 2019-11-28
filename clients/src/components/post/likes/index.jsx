import React, { useState, useEffect } from 'react';
import PropTypes from 'prop-types';
import { useDispatch } from 'react-redux';

/** Redux */
import { fetchLikesByPost } from '../../../store/actions/postActions';

/** Utils */
import InitialStatus from '../../utils/initial-status';
import Pager from '../../utils/pager';

/** Components */
import PModal from '../../widgets/p-modal';
import PLoader from '../../widgets/p-loader';
import UserItem from '../../widgets/user';

const LikesModal = ({
    postId,
    onRequestClose
}) => {
    const dispatch = useDispatch();
    const [status, setStatus] = useState(InitialStatus.LOADING)
    const [users, setUsers] = useState([]);
    const [pager, setPager] = useState(Pager);
    const [isLastPage, setIsLastPage] = useState(false);

    useEffect(() => {
        let mounted = true;
        const init = async () => {
            if ( ! mounted) return;
            try {
                const data = await dispatch(fetchLikesByPost(postId, pager.page));
                if (data.length === 0) {
                    return setIsLastPage(true);
                }
                setUsers([ ...users, ...data ]);
                setStatus({...InitialStatus.POST });
            } catch (e) {
                setStatus({...InitialStatus.ERROR });
            }
        }
        if ( ! isLastPage) {
            init();
        }
        return () => {
            mounted = false;
        }
        // eslint-disable-next-line react-hooks/exhaustive-deps
    }, [pager])

    const renderLikes = () => users.map((item,  i) =>
        <UserItem
            key={i}
            user={item.user}
            onRequestClose={onRequestClose}/>
    );

    const renderError = () => (<div className="disabled">Oops Something went wrong</div>)
    const renderLoading = () => <PLoader/>

    const renderBody = () => {
        if (true === status.error) {
            return renderError();
        }
        if (true === status.loading) {
            return renderLoading();
        }
        if (true === status.post) {
            return renderLikes();
        }
    }

    return (
        <PModal
            enableScrollPaginate={true}
            onScrollPaginate={page => setPager({ ...pager, page })}
            pager={pager}
            onRequestClose={onRequestClose}
            header="Likes"
        >
            {renderBody()}
        </PModal>
    );
};

LikesModal.propTypes = {
    postId: PropTypes.number.isRequired,
    onRequestClose: PropTypes.func.isRequired
};

export default LikesModal;
