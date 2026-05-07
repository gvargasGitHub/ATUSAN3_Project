/**
 * Application
 */
Application.prototype.onOpen = function () {
  if (typeof topbar != "undefined") {
    topbar.onMenuItemClick = function (item) {
      if (item.name == "mi_close") {
        if (confirm('¿Cerrar?')) Module.active().send('/close', { onDone: rs => ats.openModule('/') });
      } else {
        ats.openModule(item.route);
      }
    };
  }
};