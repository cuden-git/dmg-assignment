/**
 * Component used to paginate posts results
 * @since 1.0.0
 */

import { useContext } from "react";
import { Icon, chevronLeft, chevronRight } from "@wordpress/icons";
import { SettingsContext } from "./search-settings";

const Pagination = ({ totalPages, perPage }) => {
  const { attributes, setAttributes, setRequestOffset, requestOffset } = useContext(SettingsContext);
  const maxVisiblePages = 7;

  const currentPage = requestOffset / perPage + 1;
  console.log("component: PostsList");
  function pageItems() {
    let pages = [];

    if (totalPages <= maxVisiblePages) {
      for (let i = 1; i <= totalPages; i++) {
        pages.push(i);
      }
    } else {
      let startPage = Math.max(2, currentPage - 2);
      let endPage = Math.min(totalPages - 1, currentPage + 2);

      pages.push(1);
      if (startPage > 2) pages.push("...");
      for (let i = startPage; i <= endPage; i++) {
        pages.push(i);
      }
      if (endPage < totalPages - 1) pages.push("...");
      pages.push(totalPages);
    }

    return pages;
  }

  if (totalPages <= 1) return null;

  return (
    <ul className="dmg-settings__pagination">
      {currentPage > 1 && (
        <li>
          <Icon
            icon={chevronLeft}
            onClick={() => setRequestOffset(() => Math.max(0, requestOffset - perPage))}
          />
        </li>
      )}

      {pageItems().map((item, index) => (
        <li
          key={index}
          className={currentPage === item ? "active" : ""}
          onClick={() =>
            typeof item === "number" &&
            setRequestOffset(() => (item - 1) * perPage)
          }
        >
          {item}
        </li>
      ))}

      {currentPage < totalPages && (
        <li>
          <Icon
            icon={chevronRight}
            onClick={() => setRequestOffset(() => Math.min((totalPages - 1) * perPage, requestOffset + perPage))}
          />
        </li>
      )}
    </ul>
  );
};

export default Pagination;
