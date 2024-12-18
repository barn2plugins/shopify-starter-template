import React from 'react';

import {
    SkeletonPage,
    Layout,
    LegacyCard,
    Card,
    SkeletonBodyText,
} from '@shopify/polaris';

const SkeletonProduct = () => {
    return (
        <SkeletonPage title="Products">
            <Layout>
                <Layout.Section>
                    <Card>
                        <SkeletonBodyText lines={3}></SkeletonBodyText>
                        <div className="mt-3">
                            <SkeletonBodyText lines={4}></SkeletonBodyText>
                        </div>
                    </Card>
                </Layout.Section>
            </Layout>
        </SkeletonPage>
    );
};
export default SkeletonProduct;