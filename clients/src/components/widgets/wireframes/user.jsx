import React from 'react'
import styles from './user.module.css'
import { TextMock, RoundedMock } from '../wireframes';

const UserWireframe = () => (
    <div className={styles.user}>
        <RoundedMock size="36px"/>
        <div className={styles.info}>
            <TextMock/>
            <TextMock/>
        </div>
    </div>
)

export default UserWireframe;