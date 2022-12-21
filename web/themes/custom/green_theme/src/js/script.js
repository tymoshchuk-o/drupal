import '../scss/styles.scss';

openSearchForm();

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

