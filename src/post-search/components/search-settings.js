/**
 * Component providing the block's settings
 * @since 1.0.0
 */
import { useState, useEffect, createContext } from 'react';
import { InspectorControls } from "@wordpress/block-editor";
import { PanelBody, PanelRow, Spinner } from "@wordpress/components";
import SearchInput from "./search-input";
import PostsList from "./posts-list";
import Pagination from './pagination';
import { useEntityRecords } from '@wordpress/core-data';

export const SettingsContext = createContext();

const SearchSettings = ({ attributes, setAttributes }) => {
  const [requestOffset, setRequestOffset] = useState(0);
  const [searchTerm, setSearchTerm] = useState('');
  const [idSearch, setIDSearch] = useState(false);
  const perPage = 10;
  const postArgs = {
    perPage: perPage,
    orderby: 'date',
    order: 'desc'
  };

  useEffect(() => {
    setRequestOffset(0);
  }, [searchTerm]);

  postArgs.offset = requestOffset;

  if (searchTerm !== "") {
    if (idSearch) {//if true then search IDs
      postArgs.include = searchTerm;//postArgs.search = searchTerm;
    } else {//else search content and title
      postArgs.search = searchTerm;
    }
  }

  console.log('postArgs', postArgs);
  const { hasResolved, records: posts, totalPages, status } = useEntityRecords('postType', 'post', postArgs);
  console.log('useEntityRecords data', status);

  console.log("component: SearchSettings");

  return (
    <SettingsContext.Provider value={{ attributes, setAttributes, setRequestOffset, requestOffset, searchTerm, setSearchTerm, idSearch, setIDSearch }}>
      <InspectorControls>
        <PanelBody title="Search">
          <PanelRow className="dmg-settings__input">
            <SearchInput />
          </PanelRow>
          <PanelRow className="dmg-settings__results">
            {hasResolved ? (
              <>
                <PostsList
                  status={status}
                  resolved={hasResolved}
                  posts={posts}
                />
                <Pagination
                  totalPages={totalPages}
                  perPage={perPage}
                />
              </>
            ) : (
              <Spinner />
            )
            }
          </PanelRow>
        </PanelBody>
      </InspectorControls>
    </SettingsContext.Provider>
  )
}

export default SearchSettings;
