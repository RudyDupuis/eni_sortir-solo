function showMoreCards(trigger, cardClass) {
  const showMoreButton = document.querySelector(trigger);

  if (showMoreButton) {
    showMoreButton.addEventListener("click", function () {
      const hiddenCards = document.querySelectorAll(`.${cardClass}--hidden`);

      hiddenCards.forEach(function (card) {
        card.classList.remove(`${cardClass}--hidden`);
      });

      showMoreButton.classList.add(`${cardClass}--hidden`);
    });
  }
}

showMoreCards("#next20Outings_show-more", "next20Outings");
showMoreCards(
  "#next20OutingsByUserCampus_show-more",
  "next20OutingsByUserCampus"
);
showMoreCards("#outingsByAuthor_show-more", "outingsByAuthor");
showMoreCards("#outingsByRegistrant_show-more", "outingsByRegistrant");
showMoreCards("#outingsSearch_show-more", "outingsSearch");
