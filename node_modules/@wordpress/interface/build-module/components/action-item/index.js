/**
 * WordPress dependencies
 */
import { ButtonGroup, Button, Slot, Fill } from '@wordpress/components';
import { Children } from '@wordpress/element';
import { jsx as _jsx } from "react/jsx-runtime";
const noop = () => {};
function ActionItemSlot({
  name,
  as: Component = ButtonGroup,
  fillProps = {},
  bubblesVirtually,
  ...props
}) {
  return /*#__PURE__*/_jsx(Slot, {
    name: name,
    bubblesVirtually: bubblesVirtually,
    fillProps: fillProps,
    children: fills => {
      if (!Children.toArray(fills).length) {
        return null;
      }

      // Special handling exists for backward compatibility.
      // It ensures that menu items created by plugin authors aren't
      // duplicated with automatically injected menu items coming
      // from pinnable plugin sidebars.
      // @see https://github.com/WordPress/gutenberg/issues/14457
      const initializedByPlugins = [];
      Children.forEach(fills, ({
        props: {
          __unstableExplicitMenuItem,
          __unstableTarget
        }
      }) => {
        if (__unstableTarget && __unstableExplicitMenuItem) {
          initializedByPlugins.push(__unstableTarget);
        }
      });
      const children = Children.map(fills, child => {
        if (!child.props.__unstableExplicitMenuItem && initializedByPlugins.includes(child.props.__unstableTarget)) {
          return null;
        }
        return child;
      });
      return /*#__PURE__*/_jsx(Component, {
        ...props,
        children: children
      });
    }
  });
}
function ActionItem({
  name,
  as: Component = Button,
  onClick,
  ...props
}) {
  return /*#__PURE__*/_jsx(Fill, {
    name: name,
    children: ({
      onClick: fpOnClick
    }) => {
      return /*#__PURE__*/_jsx(Component, {
        onClick: onClick || fpOnClick ? (...args) => {
          (onClick || noop)(...args);
          (fpOnClick || noop)(...args);
        } : undefined,
        ...props
      });
    }
  });
}
ActionItem.Slot = ActionItemSlot;
export default ActionItem;
//# sourceMappingURL=index.js.map