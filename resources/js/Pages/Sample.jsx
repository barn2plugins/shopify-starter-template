import React from 'react';
import { Card, Text, Box } from '@shopify/polaris'
import Layout from '../Layout';

const Sample = () => {
    return (
        <Layout>
            <div className='w-5/12 mx-auto'>
                <Card roundedAbove="sm">
                    <Text as="h2" variant="headingSm">
                        Sample Page
                    </Text>
                    <Box paddingBlockStart="200">
                        <Text as="p" variant="bodyMd">
                            This is a sample page created for demonstration purposes.
                        </Text>
                        <Text as="p" variant="bodyMd">
                            You can use it, customize it, or maybe delete it.
                        </Text>
                    </Box>
                </Card>
            </div>
        </Layout>
    );
}

export default Sample;