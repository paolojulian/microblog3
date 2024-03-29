import React, { useState, useEffect } from 'react';
import styles from '../form-component.module.css';
import classnames from 'classnames';
import PropTypes from 'prop-types';
import ErrorMsg from '../error';

const FormTextArea = ({
    name,
    placeholder,
    refs,
    error,
    info,
    type,
    disabled,
    theme,
    rows,
    isRequired,
    submitOnEnter,
    max,
    ...props
}) => {

    const [stateError, setError] = useState(error);

    useEffect(() => {
        setError(error)
    }, [error]);

    const handleKeyPress = e => {
        if (!!submitOnEnter && e.key === 'Enter' && e.shiftKey) {
            e.preventDefault();
            e.stopPropagation();
            submitOnEnter();
        }
    }
    const handleKeyUp = e => {
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
        <div className={styles.form_textarea}>
            <textarea
                className={classnames(styles.input, {
                    'is-invalid': stateError,
                    [styles.theme_default]: theme === 'default' && !stateError,
                    [styles.theme_primary]: theme === 'primary' && !stateError,
                    [styles.theme_secondary]: theme === 'secondary' && !stateError,
                })}
                name={name}
                placeholder={placeholder}
                disabled={disabled}
                ref={refs}
                rows={rows}
                onKeyPress={handleKeyPress}
                onKeyUp={handleKeyUp}
                {...props}
            ></textarea>
            {!!submitOnEnter && <div className={styles.enterToSubmit}>Press Shift + Enter to submit</div>}
            {max !== -1 && props.value &&
                <div className={styles.characterCount}>{props.value.length}/{max}</div>
            }
            {info && <div className={styles.formInfo}>{info}</div>}
            <ErrorMsg error={stateError}/>
        </div>
    )
}

FormTextArea.propTypes = {
    name: PropTypes.string.isRequired,

    placeholder: PropTypes.string,
    info: PropTypes.string,
    type: PropTypes.string,
    error: PropTypes.any,
    disabled: PropTypes.bool,
    theme: PropTypes.string,
    rows: PropTypes.number,
    submitOnEnter: PropTypes.any,
    max: PropTypes.number
}

FormTextArea.defaultProps = {
    type: 'text',
    theme: 'default',
    refs: null,
    rows: 4,
    isRequired: false,
    submitOnEnter: false,
    max: -1
}

export default FormTextArea;