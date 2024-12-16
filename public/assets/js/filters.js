function toggleCloseButton(selectElement) {
    var closeButton = selectElement.parentElement.querySelector('.close-btn');
    if (selectElement.value !== '') {
        closeButton.style.display = 'inline-block'; // Показать кнопку
    } else {
        closeButton.style.display = 'none'; // Скрыть кнопку
    }
}

function handleCloseButtonClick(event) {
    var selectElement = event.target.closest('.select-wrap').querySelector('select');
    selectElement.value = ''; // Сбросить значение селекта
    toggleCloseButton(selectElement); // Скрыть кнопку
}

function initializeSelect(selectId) {
    var selectElement = document.getElementById(selectId);
    toggleCloseButton(selectElement);

    // Добавить обработчик события change для селекта
    selectElement.addEventListener('change', function() {
        toggleCloseButton(selectElement);
    });

    // Добавить обработчик события click для кнопки сброса и элемента span
    var closeButton = selectElement.parentElement.querySelector('.close-btn');
    closeButton.addEventListener('click', handleCloseButtonClick);
    var closeIcon = selectElement.parentElement.querySelector('.close-icon');
    closeIcon.addEventListener('click', handleCloseButtonClick);
}

$(document).ready(function() {
    initializeSelect('year');
    initializeSelect('country');
    initializeSelect('rating');
});