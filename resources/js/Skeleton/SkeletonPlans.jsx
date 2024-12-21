import React from 'react';

import {
    SkeletonPage,
    SkeletonBodyText,
    Layout,
    Button,
    BlockStack,
    InlineStack,
    Card,
    Text,
    Divider,
    Bleed,
    Icon,
    Box,
    SkeletonDisplayText
} from '@shopify/polaris';

const SkeletonPlans = () => {
    return (
        <SkeletonPage 
            title="Billing Plans"
            primaryAction={
                <SkeletonDisplayText size="small"></SkeletonDisplayText>
            }
        >
            <Layout>
                <Layout.Section variant="oneThird">
                    <Card roundedAbove="sm">
                        <BlockStack gap="500">
                            <SkeletonBodyText></SkeletonBodyText>
                            <Bleed marginInline="400">
                                <Divider />
                            </Bleed>
                            <BlockStack gap="200">
                                <InlineStack gap="200">
                                    <SkeletonBodyText></SkeletonBodyText>
                                </InlineStack>
                                <InlineStack gap="200">
                                    <SkeletonBodyText></SkeletonBodyText>
                                </InlineStack>
                            </BlockStack>
                        </BlockStack>
                    </Card>
                </Layout.Section>
                <Layout.Section variant="oneThird">
                    <Card roundedAbove="sm">
                        <BlockStack gap="500">
                            <SkeletonBodyText></SkeletonBodyText>
                            <Bleed marginInline="400">
                                <Divider />
                            </Bleed>
                            <BlockStack gap="200">
                                <InlineStack gap="200">
                                    <SkeletonBodyText></SkeletonBodyText>
                                </InlineStack>
                                <InlineStack gap="200">
                                    <SkeletonBodyText></SkeletonBodyText>
                                </InlineStack>
                            </BlockStack>
                        </BlockStack>
                    </Card>
                </Layout.Section>
                <Layout.Section variant="oneThird">
                    <Card roundedAbove="sm">
                        <BlockStack gap="500">
                            <SkeletonBodyText></SkeletonBodyText>
                            <Bleed marginInline="400">
                                <Divider />
                            </Bleed>
                            <BlockStack gap="200">
                                <InlineStack gap="200">
                                    <SkeletonBodyText></SkeletonBodyText>
                                </InlineStack>
                                <InlineStack gap="200">
                                    <SkeletonBodyText></SkeletonBodyText>
                                </InlineStack>
                            </BlockStack>
                        </BlockStack>
                    </Card>
                </Layout.Section>
            </Layout>
        </SkeletonPage>
    );
};
export default SkeletonPlans;