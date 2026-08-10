export const BeforeServiceCenterListUpdate = new CustomEvent('BeforeServiceCenterListUpdate', {
  detail: {},
  bubbles: true,
  cancelable: true,
});

export const ServiceCenterListUpdate = new CustomEvent('ServiceCenterListUpdate', {
  detail: {},
  bubbles: true,
  cancelable: true,
});

export const FilterFullReset = new CustomEvent('filterFullReset', {
  detail: {},
  bubbles: true,
  cancelable: true,
});

export const SetActiveServiceCenterListItem = new CustomEvent('SetActiveServiceCenterListItem', {
  detail: {},
  bubbles: true,
  cancelable: true,
});
