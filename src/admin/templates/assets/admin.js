document.addEventListener('DOMContentLoaded', () => {
    document.getElementById('yolotoggle').addEventListener('click', function() {
        document.getElementsByTagName('body')[0].classList.toggle('yolobar-opened');
    })
});
