import React from 'react';
import Login from './index';
import renderer from 'react-test-renderer';

test('Link changes the class when hovered', () => {
    const component = renderer.create(
        <Login/>
    );
    let tree = component.toJSON();
    expect(tree).toMatchSnapshot();
    console.log(component);
})