window.onclick = function(event) {
    // Get the modal
    var modals = document.getElementsByClassName('modal');
    Array.prototype.forEach.call(modals, function(element) {
        if ((event.target == element) && (element.style.display = "block")) {
        element.style.display = "none";
        }
    }, this);
}