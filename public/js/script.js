const toTopBtn = document.querySelector('#toTop');
const dietTextarea = document.querySelector('textarea[name=diet_anders]');
const dietAndersRadio = document.querySelector('input#form-diet_anders');

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
/***** Check corresponding radio button when texarea is focused ******/
if(dietTextarea) {
    dietTextarea.addEventListener('focus', () => {
        dietAndersRadio.checked = true;
    });
}
/*********************************************************************/