import React, { useEffect } from 'react';

import styles from './p-alert-notification.module.css';

const types = ['alert', 'danger', 'success'];
const colors = {
    alert: 'var(--blue)',
    danger: 'var(--secondary)',
    success: 'rgba(0, 0, 0, 0.75)'
}

const AlertNotification = ({ 
  onRequestClose,
  type,
  body
}) => {

    useEffect(() => {
        let timeout = setTimeout(onRequestClose, 5000);
        return () => {
            clearTimeout(timeout);
        };
        // eslint-disable-next-line react-hooks/exhaustive-deps
    }, [])

    type = types.indexOf(type) !== -1
        ? type
        : 'alert'

    return (
        <div style={{ backgroundColor: colors[type] }}
            className={styles.wrapper}
            onClick={onRequestClose}
        >
            {body}
        </div>
    )
}
AlertNotification.defaultProps = {
  type: 'alert'
};

export default AlertNotification;