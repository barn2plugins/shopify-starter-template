import React from 'react';
import { Head } from '@inertiajs/react';
import { Card, Text, Box } from '@shopify/polaris'

const Sample = () => {
    return (
        <>
            <Head title="Sample" />
            <div>
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
        </>
    );
}

export default Sample;