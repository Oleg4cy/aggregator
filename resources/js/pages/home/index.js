import YandexMapWorker from "../../modules/YandexMapWorker";

document.addEventListener("DOMContentLoaded", () => {
  new YandexMapWorker();

  const results = document.querySelector("[data-service-center-results]");
  const details = document.querySelector("[data-service-center-details]");

  if (!results || !details) return;

  results.addEventListener("click", async (event) => {
    if (event.target.closest(".service-center-card__open-new-tab")) return;

    const card = event.target.closest("[data-service-center-target]");
    if (!card || !results.contains(card)) return;

    const detailsUrl = card.dataset.serviceCenterDetailsUrl;
    if (!detailsUrl) return;

    let response;
    try {
      response = await fetch(detailsUrl, {
        headers: {
          "X-Requested-With": "XMLHttpRequest",
        },
      });
    } catch (error) {
      return;
    }

    if (!response.ok) return;

    const html = await response.text();
    details.innerHTML = html;
    results.hidden = true;
    details.hidden = false;
    details.scrollTop = 0;
  });

  details.addEventListener("click", (event) => {
    if (!event.target.closest("[data-service-center-details-back]")) return;

    details.hidden = true;
    results.hidden = false;
    details.innerHTML = "";
  });
});
