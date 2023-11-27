document.addEventListener('DOMContentLoaded', (e) => {

    let prevButtons = document.querySelectorAll('.slider-control-left');
    let nextButtons = document.querySelectorAll('.slider-control-right');

    prevButtons.forEach( (button) => {
        button.addEventListener('click', clikOnPrev);
    });
    nextButtons.forEach( (button) => {
        button.addEventListener('click', clikOnNext);
    });


    function clikOnPrev(e){
        console.log('On a clické sur btn prev');
        console.log(e);
    }
    function clikOnNext(e){
        console.log('On a clické sur btn next');
        console.log(e);
    }
});