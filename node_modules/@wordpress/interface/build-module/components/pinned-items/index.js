/**
 * External dependencies
 */
import clsx from 'clsx';

/**
 * WordPress dependencies
 */
import { Slot, Fill } from '@wordpress/components';
import { jsx as _jsx } from "react/jsx-runtime";
function PinnedItems({
  scope,
  ...props
}) {
  return /*#__PURE__*/_jsx(Fill, {
    name: `PinnedItems/${scope}`,
    ...props
  });
}
function PinnedItemsSlot({
  scope,
  className,
  ...props
}) {
  return /*#__PURE__*/_jsx(Slot, {
    name: `PinnedItems/${scope}`,
    ...props,
    children: fills => fills?.length > 0 && /*#__PURE__*/_jsx("div", {
      className: clsx(className, 'interface-pinned-items'),
      children: fills
    })
  });
}
PinnedItems.Slot = PinnedItemsSlot;
export default PinnedItems;
//# sourceMappingURL=index.js.map