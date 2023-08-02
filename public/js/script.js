const toTopBtn = document.querySelector('#toTop');

/***** To Top Button *************************/
toTopBtn.addEventListener('click', (e) => {
    e.preventDefault();
    window.scrollTo(0, 0);
});
if(window.scrollY > 800) {
    toTopBtn.classList.add('show');
}
/*********************************************/
/***** When user scrolls *********************/
window.addEventListener("scroll",debounce(function(e){
    setAfterScrollAttributes();
}));
function setAfterScrollAttributes() {
    let fromTop = window.scrollY;
    if(fromTop > 400) {
        toTopBtn.classList.add('show');
    } else {
        toTopBtn.classList.remove('show');
    }
}
function debounce(func){
    var timer;
    return function(event){
        if(timer) clearTimeout(timer);
        timer = setTimeout(func,10,event);
    };
}
/*********************************************/
