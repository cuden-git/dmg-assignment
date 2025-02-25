/**
 * Component used to list found posts
 * @since 1.0.0
 */

import { useContext } from 'react';
import { SettingsContext } from './search-settings';

const PostsList = ({ posts, resolved }) => {
  const { setAttributes, attributes } = useContext(SettingsContext);

  return (
    <>
      <h3>Results</h3>
      {resolved && (!posts || posts.length === 0) ? (
        <>
          <p>No posts found.</p>
        </>
      ) : (
        <ul className="dmg-settings__posts">
          {posts.map(({ title: { rendered: postTitle }, id, postURL }, index) => (
            <li key={id} onClick={() => setAttributes({ foundURL: postURL, foundTitle: postTitle })}>
              {postTitle + ' - ' + id}
            </li>
          ))}
        </ul>
      )}
    </>
  )
}

export default PostsList
