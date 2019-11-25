import React from 'react';

const ErrorMsg = ({ error }) => {
    let message = ''
    if (typeof error === 'string') {
        message = error;
    } else if (typeof error === 'object') {
        try {
            message = Object.values(error)[0];
        } catch (e) {
            message = ''
        }
    }

    if ( ! message) {
        return '';
    }

    return (
        <div className="invalid-feedback">
            * {message}
        </div>
    )
}
export default ErrorMsg;