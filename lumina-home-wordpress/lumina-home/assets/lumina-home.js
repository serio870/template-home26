(function () {
  function setupLuminaHome(root) {
    var button = root.querySelector('.lumina-contact-toggle');
    var box = root.querySelector('.lumina-contact-box');

    if (!button || !box) {
      return;
    }

    button.addEventListener('click', function () {
      var isOpen = !box.hasAttribute('hidden');
      if (isOpen) {
        box.setAttribute('hidden', '');
        button.setAttribute('aria-expanded', 'false');
      } else {
        box.removeAttribute('hidden');
        button.setAttribute('aria-expanded', 'true');
      }
    });
  }

  document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.lumina-home').forEach(setupLuminaHome);
  });
}());
