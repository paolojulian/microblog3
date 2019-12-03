import React, { useEffect, useState, useRef } from 'react'
import { Link } from 'react-router-dom'
import { connect, useSelector } from 'react-redux'
import { withRouter } from 'react-router-dom';

/** Redux */
import { loginUser } from '../../../store/actions/authActions'

/** Components */
import PCard from '../../widgets/p-card'
import PButton from '../../widgets/p-button'
import FormInput from '../../widgets/form/input'
import ErrorMsg from '../../widgets/form/error';

const Login = ({
    loginUser,
    history
}) => {
    const { isAuthenticated } = useSelector(state => state.auth);
    const [isLoading, setLoading] = useState(false);
    const username = useRef('');
    const password = useRef('');
    const [errors, setErrors] = useState({
        form: '',
        username: false,
        password: false
    });

    useEffect(() => {
        if (isAuthenticated) {
            history.push('/')
        }
    }, [history, isAuthenticated]);

    const handleSubmit = async e => {
        e.preventDefault();
        if (isLoading) {
            return;
        }
        setLoading(true);
        setErrors({
            form: '',
            username: false,
            password: false
        })
        const User = {
            username: username.current.value,
            password: password.current.value,
        }
        try {
            await loginUser(User, history);
        } catch (e) {
            handleError(e);
        } finally {
            setLoading(false);
        }
    }

    const handleError = e => {
        try {
            if (e.response.status !== 422) {
                throw new Error();
            }
            return setErrors({
                form: e.response.data.data,
                username: true,
                password: true
            })
        } catch (e) {
            return setErrors({
                ...errors,
                form: 'Oops. Something went wrong'
            })
        }
    }

    return (
        <div className="center-absolute">
            <PCard size="sm" header="LaCosina">
                <form
                    className="form"
                    onSubmit={handleSubmit}
                >
                    <ErrorMsg error={errors.form}/>
                    <FormInput
                        placeholder="Username"
                        name="username"
                        refs={username}
                        error={errors.username}
                    />
                    <FormInput
                        type="password"
                        placeholder="Password"
                        name="password"
                        refs={password}
                        error={errors.password}
                    />

                    <br />

                    <PButton
                        type="submit"
                        theme="primary"
                        isLoading={isLoading}
                    >
                        SUBMIT
                    </PButton>

                    <Link to="/register">
                        <div className="text-link italic">
                            Create an account?
                        </div>
                    </Link>

                </form>
            </PCard>
        </div>
    )
}

export default connect(null, { loginUser })(withRouter(Login))