/**
 * Component used in the editor to display and save the content
 * @since 1.0.0
 */
import { Icon, external } from '@wordpress/icons';

const Output = ({ url, title }) => {
  console.log("component: Output");
  return (
    <p className="dmg-read-more">
      Read More: <a href={url} target="_blank" title={title}>{title} <Icon icon={external} /></a>
    </p>
  )
}

export default Output;
