// Small helper for confirmations
document.addEventListener('click', function(e){
  var t = e.target;
  if (t.matches('button[data-confirm]')) {
    if (!confirm(t.getAttribute('data-confirm'))) {
      e.preventDefault();
    }
  }
});
