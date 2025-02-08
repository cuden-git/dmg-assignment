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
