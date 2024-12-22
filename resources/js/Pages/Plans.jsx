import { useEffect, useState } from 'react';
import SkeletonPlans from '../Skeleton/SkeletonPlans';
import axios from 'axios';
import Plans from '../Components/Plans';
import Layout from '../Layout';

const Sample = () => {
    const [loading, setLoading] = useState(true);
    const [pageContent, setPageContent] = useState([]);
    
    const fetchPageContent = async () => {
        try {
            const response = await axios.get('/plans/content');
            if ( response.status === 200 ) {
                setPageContent(response.data);
            }
        } catch (error) {
            console.log('caught errors', error);
        } finally {
            setLoading(false);
        }
    };

    useEffect(() => {
        fetchPageContent();
    }, []);

    return (
        <Layout>
            { loading && <SkeletonPlans /> }
            { !loading && <Plans page={pageContent} /> }
        </Layout>
    );
}

export default Sample;