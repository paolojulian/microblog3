import React from 'react'
import styles from '../../landing/landing-loading.module.css'

import { TextMock, RoundedMock } from '../../widgets/wireframes/index';

const PostItemWireframe = () => (
    <div className={styles.post}>
        <div className={styles.header}>
            <RoundedMock size="36px"/>
            <div className={styles.postTitle}>
                <TextMock/>
                <TextMock/>
            </div>
        </div>
        <div className={styles.postBody}>
            <TextMock/>
            <TextMock/>
            <TextMock/>
        </div>
    </div>
)

export default PostItemWireframe;