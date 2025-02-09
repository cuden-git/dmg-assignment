/**
 * Component that provides the search text field
 * @since 1.0.0
 */
import { useState, useContext, useEffect } from 'react';
import { TextControl, Button, CheckboxControl } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import { SettingsContext } from './search-settings';
import { Icon, closeSmall } from '@wordpress/icons';

const SearchInput = ({ onChange }) => {
  const { searchTerm, setSearchTerm, idSearch, setIDSearch } = useContext(SettingsContext);
  const [inputVal, setInputVal] = useState(searchTerm);

  useEffect(() => {
    setSearchTerm('');
  }, [inputVal]);

  return (
    <>
      <div className='dmg-settings__input-wrap'>
        <TextControl
          value={inputVal}
          help={__("Search for post title or ID")}
          __next40pxDefaultSize
          onChange={val => setInputVal(val)}
        />
        <Icon
          icon={closeSmall}
          onClick={() => setInputVal('')}
        />
      </div>
      <Button
        variant="primary"
        onClick={() => setSearchTerm(inputVal)}
      >
        {__("Find")}
      </Button>
      <CheckboxControl
        label={__("ID search")}
        onChange={() => setIDSearch(!idSearch)}
        className="dmg-settings__input-cb"
      />
    </>
  )
}

export default SearchInput;
