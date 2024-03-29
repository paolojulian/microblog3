import React, { useState, useEffect } from 'react';
import classnames from 'classnames';
import PropTypes from 'prop-types';
import styles from '../form-component.module.css';

/** Components */
import ErrorMsg from '../error';

const FormInput = ({
    name,
    placeholder,
    refs,
    error,
    info,
    type,
    disabled,
    theme,
    isRequired,
    max,
    ...props
}) => {

    const [stateError, setError] = useState(error);

    useEffect(() => {
        setError(error)
    }, [error]);

    const handleKeyPress = e => {
        if (max !== -1 && e.target.value.length > max) {
            return setError(`Maximum of ${max} characters only`);
        }
        if (stateError) {
            return setError(false);
        }
        if (isRequired && !e.target.value) {
            return setError(true);
        }
    }

    return (
        <div className={styles.form_input}>
            <input
                className={classnames(styles.input, {
                    'is-invalid': stateError,
                    [styles.theme_default]: theme === 'default' && !stateError,
                    [styles.theme_primary]: theme === 'primary' && !stateError,
                    [styles.theme_secondary]: theme === 'secondary' && !stateError,
                })}
                type={type}
                name={name}
                placeholder={placeholder}
                ref={refs}
                disabled={disabled}
                onKeyUp={handleKeyPress}
                {...props}
                />
            {max !== -1 && props.value &&
                <div className={styles.characterCount}>{props.value.length}/{max}</div>
            }
            {info && <div className={styles.formInfo}>{info}</div>}
            <ErrorMsg error={stateError}/>
        </div>
    )
}

FormInput.propTypes = {
    name: PropTypes.string.isRequired,

    placeholder: PropTypes.string,
    info: PropTypes.string,
    type: PropTypes.string,
    error: PropTypes.any,
    disabled: PropTypes.bool,
    theme: PropTypes.string,

    max: PropTypes.number
}

FormInput.defaultProps = {
    type: 'text',
    theme: 'default',
    refs: null,
    disabled: false,
    isRequired: false,
    max: -1
}

export default FormInput;