import React from 'react';
import { shallow } from 'enzyme';
import ErrorMsg from '../../widgets/form/error';

describe("Login", () => {
    it('renders form error message', () => {
        const errorMessage = 'Username or Password is incorrect';
        const component = shallow(<ErrorMsg error={errorMessage}/>);
        expect(component).toMatchSnapshot();
    });
});