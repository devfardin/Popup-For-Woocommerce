document.addEventListener('DOMContentLoaded', function () {
    var overlay  = document.getElementById('pfwc-overlay');
    var fill     = document.getElementById('pfwc-timer-fill');
    var closeBtn = document.getElementById('pfwc-close');
    var duration = (pfwcData.duration || 7) * 1000;

    if (!overlay) return;

    fill.style.transition = 'transform ' + (duration / 1000) + 's linear';
    requestAnimationFrame(function () {
        requestAnimationFrame(function () {
            fill.style.transform = 'scaleX(0)';
        });
    });

    var timer = setTimeout(closePopup, duration);

    closeBtn.addEventListener('click', function () {
        clearTimeout(timer);
        closePopup();
    });

    function closePopup() {
        overlay.style.transition = 'opacity .3s ease';
        overlay.style.opacity = '0';
        setTimeout(function () { overlay.remove(); }, 300);
    }
});
