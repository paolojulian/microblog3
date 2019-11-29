import React, { useRef, useState, useEffect, useCallback } from 'react'
import PropTypes from 'prop-types'

/** Components */
import PLoader from '../widgets/p-loader'

const ModalScrollPaginate = ({
    fetchHandler,
    page,
    ...props
}) => {
    const bodyRef = useRef('');
    const [isLoading, setIsLoading] = useState(false);
    const [isLast, setIsLast] = useState(false);

    const listenOnScroll = useCallback(async e => {
        const element = e.target;
        if (isLast) return;
        if (isLoading) return;
        if ((element.scrollTop + element.clientHeight) !== element.scrollHeight) {
            return;
        }
        try {
            setIsLoading(true)
            await fetchHandler(page + 1);
            setIsLoading(false);
        } catch (e) {
            setIsLast(true);
        }
        // eslint-disable-next-line
    }, [page, isLoading, isLast]);

    useEffect(() => {
        if ( ! isLast && ! isLoading) {
            bodyRef.current.addEventListener('scroll', listenOnScroll);
        } else {
            bodyRef.current.removeEventListener('scroll', listenOnScroll);
        }
    }, [listenOnScroll, isLast, isLoading])

    return (
        <div
            {...props}
            ref={bodyRef}
        >
            {props.children}
            {isLoading && <PLoader/>}
        </div>
    )
}

ModalScrollPaginate.propTypes = {
    /** The function to be runned when scrolled is triggered */
    fetchHandler: PropTypes.func.isRequired,
    /** Current page number */
    page: PropTypes.number,
}

ModalScrollPaginate.defaultProps = {
    page: 1
}

export default ModalScrollPaginate
