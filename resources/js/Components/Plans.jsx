import { 
    Page, 
    Layout,
    ButtonGroup,
    Button,
    BlockStack,
    InlineStack,
    Card,
    Text,
    Divider,
    Bleed,
    Icon,
    Box
} from '@shopify/polaris'
import { useEffect, useState } from 'react';
import {CheckIcon, XIcon} from '@shopify/polaris-icons';
import axios from 'axios';

const Plans = ({page}) => {
    const [billingPeriod, setBillingPeriod] = useState('monthly');
    const [pressedButtonIndex, setPressedButtonIndex] = useState(0);
    const [btnLoading, setBtnLoading] = useState(false);

    const handleChoosePlan = async (plan, buttonIndex) => {
        setBtnLoading(true);
        setPressedButtonIndex(buttonIndex);

        const planData = {
            plan: plan,
            billing_period: billingPeriod
        }

        try {
            const response = await axios.post('/plans/subscription', planData);
            if ( response.status === 200 ) {
                window.open(response.data.confirmation_url, "_top")
            }
        } catch (error) {
            setPressedButtonIndex(null);
            setBtnLoading(false);
        }
    }

    const handleBillingPeriod = ( value ) => {
        setBillingPeriod(value);
    }

    useEffect(() => {
        if (page.billing_interval) {
            setBillingPeriod(page.billing_interval);
        }
    }, [])
    
    return (
        <Page
            title={page.title}
            subtitle={page.subtitle}
            primaryAction={
                <ButtonGroup variant="segmented">
                    <Button
                        pressed={billingPeriod === 'monthly'}
                        onClick={() => handleBillingPeriod('monthly')}
                    >
                        Monthly
                    </Button>
                    <Button
                        pressed={billingPeriod === 'annual'}
                        onClick={() => handleBillingPeriod('annual')}
                    >
                        Yearly
                    </Button>
                </ButtonGroup>
            }
        >
            <Layout>
                {Object.entries(page.plans).map(([key, plan], index) => {
                    return (
                        <Layout.Section variant="oneThird" key={index}>
                            <Card roundedAbove="sm">
                                <BlockStack gap="500">
                                    <InlineStack align="space-between">
                                        <div>
                                            <Text as="h3" variant="headingMd">
                                                {plan.title}
                                            </Text>
                                            <Text as="p">
                                                {plan.description}
                                            </Text>
                                        </div>
                                        { billingPeriod === 'monthly' && 
                                            <InlineStack align="end" direction="column" blockAlign="end">
                                                <Text as="p" fontWeight="medium" textDecorationLine="line-through">
                                                    ${plan.price.regular}
                                                </Text>
                                                <InlineStack blockAlign="end">
                                                    <Text as="p" fontWeight="semibold" variant="headingLg">
                                                        ${plan.price.sale}
                                                    </Text>
                                                    <Text as="span" tone="subdued">/Y</Text>
                                                </InlineStack>
                                            </InlineStack>
                                        }
                                        { billingPeriod === 'annual' &&                                         
                                            <InlineStack align="end" direction="column" blockAlign="end">
                                                <Text as="p" fontWeight="medium" textDecorationLine="line-through">
                                                    ${plan.annualPrice.regular}
                                                </Text>
                                                <InlineStack blockAlign="end">
                                                    <Text as="p" fontWeight="semibold" variant="headingLg">
                                                        ${plan.annualPrice.sale}
                                                    </Text>
                                                    <Text as="span" tone="subdued">/Y</Text>
                                                </InlineStack>
                                            </InlineStack>
                                        }
                                    </InlineStack>
                                    <Bleed marginInline="400">
                                        <Divider />
                                    </Bleed>
                                    <BlockStack gap="200">
                                        { plan.features.map((feature, featureIndex) => {
                                            return (
                                                <InlineStack gap="200" key={featureIndex}>
                                                    <Box>
                                                        { feature.available &&  <Icon source={CheckIcon} color="success" />}
                                                        { !feature.available &&  <Icon source={XIcon} color="success" />}
                                                    </Box>
                                                    <Text as="p">
                                                        {feature.name}
                                                    </Text>
                                                </InlineStack>
                                            )
                                        })}
                                    </BlockStack>
                                    <Button 
                                        fullWidth 
                                        variant="primary" 
                                        disabled={page.current_plan === plan.name}
                                        loading={btnLoading && index === pressedButtonIndex}
                                        onClick={() => handleChoosePlan(plan.name, index)}
                                        >
                                            { page.current_plan === plan.name && <span>Current plan</span>}
                                            { page.current_plan !== plan.name && <span>Choose plan</span>}
                                    </Button>
                                </BlockStack>
                            </Card>
                        </Layout.Section>
                    )
                })}
            </Layout>
        </Page>
    )
}

export default Plans;