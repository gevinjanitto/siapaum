$(function () {
    let content = document.getElementById('main-content'),
        footer = document.getElementById('footer'),
        checkContent = () => {
            if (content.clientHeight <= 1500) {
                footer.style.position = 'absolute';
                footer.style.bottom = '0';
                footer.style.left = '0';
                footer.style.right = '0';
                footer.style.marginTop = '-' + footer.clientHeight;
            }else{
                footer.style.bottom = null;
            }
        };

    checkContent();

    window.onresize = checkContent;
});

