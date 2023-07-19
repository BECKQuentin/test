document.addEventListener('DOMContentLoaded', function() {


    let toolbar = document.getElementById('toolbar');
    let page = document.getElementById('page');
    let spanNavLink = document.querySelectorAll('.nav-link span');
    let hideToolbar = document.getElementById('hideToolbar');

    if (hideToolbar) {
        hideToolbar.addEventListener('click', (e) => {
            e.preventDefault();
            extendToolbar();
        })
    }

    function extendToolbar() {
        toolbar.classList.toggle('toolbar_retract');
        page.classList.toggle('toolbar_retract_page');

        // hideToolbar.querySelector('i').classList.toggle('fa-bars')
        // hideToolbar.querySelector('i').classList.toggle('fa-xmark')

        // toolbar.classList.add('col-md-2');
        // page.classList.add('offset-md-2');
        // page.classList.add('col-md-10');

        setTimeout(() => {
            spanNavLink.forEach((item) => {
                item.classList.toggle('d-md-inline');
            })
        }, 150)
    }
});

// hideToolbar.addEventListener('mouseleave', () => {
//     setTimeout(() => {
//         retractToolbar();
//     }, 150)
// })

