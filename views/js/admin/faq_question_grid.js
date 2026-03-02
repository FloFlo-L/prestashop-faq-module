$(document).ready(function () {
  const grid = new window.prestashop.component.Grid('faq');
  grid.addExtension(
    new window.prestashop.component.GridExtensions.SortingExtension(),
  );
  grid.addExtension(
    new window.prestashop.component.GridExtensions.LinkRowActionExtension(),
  );
  grid.addExtension(
    new window.prestashop.component.GridExtensions.SubmitRowActionExtension(),
  );
  grid.addExtension(
    new window.prestashop.component.GridExtensions.AsyncToggleColumnExtension(),
  );
  grid.addExtension(
    new window.prestashop.component.GridExtensions.PositionExtension(),
  );
  grid.addExtension(
    new window.prestashop.component.GridExtensions.BulkActionCheckboxExtension(),
  );
  grid.addExtension(
    new window.prestashop.component.GridExtensions.SubmitBulkActionExtension(),
  );
  grid.addExtension(
    new window.prestashop.component.GridExtensions.FiltersResetExtension(),
  );
  grid.addExtension(
    new window.prestashop.component.GridExtensions.FiltersSubmitButtonEnablerExtension(),
  );
});
