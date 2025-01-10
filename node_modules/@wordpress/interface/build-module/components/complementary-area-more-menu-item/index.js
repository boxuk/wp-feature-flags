/**
 * WordPress dependencies
 */
import { check } from '@wordpress/icons';
import { MenuItem } from '@wordpress/components';

/**
 * Internal dependencies
 */
import ComplementaryAreaToggle from '../complementary-area-toggle';
import ActionItem from '../action-item';
import { jsx as _jsx } from "react/jsx-runtime";
const PluginsMenuItem = ({
  // Menu item is marked with unstable prop for backward compatibility.
  // They are removed so they don't leak to DOM elements.
  // @see https://github.com/WordPress/gutenberg/issues/14457
  __unstableExplicitMenuItem,
  __unstableTarget,
  ...restProps
}) => /*#__PURE__*/_jsx(MenuItem, {
  ...restProps
});
export default function ComplementaryAreaMoreMenuItem({
  scope,
  target,
  __unstableExplicitMenuItem,
  ...props
}) {
  return /*#__PURE__*/_jsx(ComplementaryAreaToggle, {
    as: toggleProps => {
      return /*#__PURE__*/_jsx(ActionItem, {
        __unstableExplicitMenuItem: __unstableExplicitMenuItem,
        __unstableTarget: `${scope}/${target}`,
        as: PluginsMenuItem,
        name: `${scope}/plugin-more-menu`,
        ...toggleProps
      });
    },
    role: "menuitemcheckbox",
    selectedIcon: check,
    name: target,
    scope: scope,
    ...props
  });
}
//# sourceMappingURL=index.js.map