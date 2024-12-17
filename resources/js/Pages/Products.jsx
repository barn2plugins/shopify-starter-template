import React, { useState, useCallback } from 'react';
import { Head } from '@inertiajs/react';
import {
  Page,
  Card,
  ResourceList,
  ResourceItem,
  Text,
  Filters,
  Pagination,
  Thumbnail,
} from '@shopify/polaris';

const products = [
  {
    id: '1',
    url: '/products/1',
    title: 'Red Shirt',
    vendor: 'Fashion Hub',
    image: 'https://via.placeholder.com/60',
  },
  {
    id: '2',
    url: '/products/2',
    title: 'Blue Jeans',
    vendor: 'Denim Store',
    image: 'https://via.placeholder.com/60',
  },
  {
    id: '3',
    url: '/products/3',
    title: 'Sneakers',
    vendor: 'Shoe Shop',
    image: 'https://via.placeholder.com/60',
  },
];

const Products = () => {
  const [queryValue, setQueryValue] = useState('');
  const [selectedItems, setSelectedItems] = useState([]);
  const [isLoading, setIsLoading] = useState(false);

  // Filter handling
  const handleQueryChange = useCallback((value) => setQueryValue(value), []);
  const handleQueryClear = useCallback(() => setQueryValue(''), []);
  const handleSelectionChange = useCallback((items) => setSelectedItems(items), []);

  // Pagination (dummy handlers)
  const handlePreviousPage = useCallback(() => console.log('Previous Page'), []);
  const handleNextPage = useCallback(() => console.log('Next Page'), []);

  return (
    <Page title="Products" primaryAction={{ content: 'Add Product' }}>
      <Card>
        <ResourceList
          resourceName={{ singular: 'product', plural: 'products' }}
          items={products}
          renderItem={(item) => {
            const { id, title, vendor, image, url } = item;
            const media = <Thumbnail source={image} alt={title} />;

            return (
              <ResourceItem id={id} media={media} accessibilityLabel={`View details for ${title}`} url={url}>
                <h3>
                  <Text variation="strong">{title}</Text>
                </h3>
                <div>{vendor}</div>
              </ResourceItem>
            );
          }}
          selectedItems={selectedItems}
          onSelectionChange={handleSelectionChange}
          selectable
          filterControl={
            <Filters
              queryValue={queryValue}
              onQueryChange={handleQueryChange}
              onQueryClear={handleQueryClear}
              filters={[
                {
                  key: 'vendor',
                  label: 'Vendor',
                  filter: (
                    <div>
                      <p>Custom Vendor Filter UI Here</p>
                    </div>
                  ),
                },
              ]}
            />
          }
          loading={isLoading}
        />
      </Card>
      <Pagination
        hasPrevious
        onPrevious={handlePreviousPage}
        hasNext
        onNext={handleNextPage}
      />
    </Page>
  );
};

export default Products;