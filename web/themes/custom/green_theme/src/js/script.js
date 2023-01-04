import '../scss/styles.scss';

openSearchForm();
scrollTop();


function openSearchForm() {
  const searchBTN = document.querySelector('.search-block-form.block-search .search-form__submit');
  const searchQueryFields = document.querySelector('.search-block-form.block-search .form-search');

  searchBTN.addEventListener('click', (e) => {

    if (searchQueryFields.value.length == 0) {
      e.preventDefault();
      searchQueryFields.classList.toggle('form-search--open');
    }

  });
}


//  Scroll Top Script
function scrollTop() {
  // Get the button:
  let btn = document.getElementById("scroll-top-btn");

  btn.addEventListener('click', ()=>{
    topFunction();
  });

// When the user scrolls down 20px from the top of the document, show the button
  window.onscroll = function () {
    scrollFunction(btn)
  };

}

function scrollFunction(btn) {
  if (document.body.scrollTop > 300 || document.documentElement.scrollTop > 300) {
    btn.style.display = "block";
  } else {
    btn.style.display = "none";
  }
}

// When the user clicks on the button, scroll to the top of the document
function topFunction() {
  document.body.scrollTop = 0; // For Safari
  document.documentElement.scrollTop = 0; // For Chrome, Firefox, IE and Opera
}
