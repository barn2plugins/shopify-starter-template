import React, { useState, useEffect, useCallback } from 'react';
import axios from 'axios';
import SkeletonProducts from '../Skeleton/SkeletonProducts';
import {
  Page,
  Card,
  ResourceList,
  ResourceItem,
  Text,
  Thumbnail,
  Badge
} from '@shopify/polaris';

const Products = () => {
  const [products, setProducts] = useState([]);
  const [selectedItems, setSelectedItems] = useState([]);
  const [isLoading, setIsLoading] = useState(true);

  const fetchData = async () => {
    try {
      const response = await axios.get('/products/get');
      console.log(response);
      
      if ( response.status === 200 ) {
        setProducts(response.data.products);
      }
    } catch (error) {
        console.error('Error:', error.response ? error.response.data : error.message);
    } finally {
      setIsLoading(false);
    }
  };

  useEffect(() => {
    fetchData();
  }, []);

  const resourceName = {
    singular: 'product',
    plural: 'products'
  };
  
  // Filter handling
  const handleSelectionChange = useCallback((products) => setSelectedItems(products), []);
 
  const promotedBulkActions = [
    {
      content: 'Edit products',
      onAction: () => console.log('Todo: implement bulk edit'),
    },
  ];

  const bulkActions = [
    {
      content: 'Add tags',
      onAction: () => console.log('Todo: implement bulk add tags'),
    },
    {
      content: 'Remove tags',
      onAction: () => console.log('Todo: implement bulk remove tags'),
    },
    {
      content: 'Delete products',
      onAction: () => console.log('Todo: implement bulk delete'),
    },
  ];

  return (
    <>
      { isLoading && <SkeletonProducts /> }
      { !isLoading && 
        <Page title="Products">
          <Card roundedAbove="sm">
            <ResourceList
              resourceName={resourceName}
              items={products}
              renderItem={renderItem}
              selectedItems={selectedItems}
              onSelectionChange={handleSelectionChange}
              selectable
              loading={isLoading}
              promotedBulkActions={promotedBulkActions}
              bulkActions={bulkActions}
              resolveItemId={resolveItemIds}
            />
          </Card>
        </Page>
      }
    </>
  );

  function renderItem(item) {
    const { id, title, featuredMedia, url, status } = item.node;
    const media = <Thumbnail source={featuredMedia.preview.image.url} alt={featuredMedia.preview.image.altText} />;
    return (
      <ResourceItem id={id} media={media} accessibilityLabel={`View details for ${title}`} url={url}>
        <h3>
          <Text variation="strong">{title}</Text>
        </h3>
        <div className='mt-1'>
          { status === 'ACTIVE' &&  <Badge tone="success">{status}</Badge>}
          { status === 'ARCHIVED' &&  <Badge tone="info">{status}</Badge>}
          { status === 'DRAFT' &&  <Badge>{status}</Badge>}
        </div>
      </ResourceItem>
    );
  }

  function resolveItemIds({id}) {
    return id;
  }
};

export default Products;
