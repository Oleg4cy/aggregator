import YandexMapWorker from "../../modules/YandexMapWorker";
import "../../components/tabs";

document.addEventListener("DOMContentLoaded", () => {
  const mapWorker = new YandexMapWorker();

  const results = document.querySelector("[data-service-center-results]");
  const details = document.querySelector("[data-service-center-details]");

  if (!results || !details) return;

  const fadeOut = (element) =>
    new Promise((resolve) => {
      const handleTransitionEnd = (event) => {
        if (event.target !== element || event.propertyName !== "opacity") return;

        element.removeEventListener("transitionend", handleTransitionEnd);
        resolve();
      };

      element.addEventListener("transitionend", handleTransitionEnd);
      element.classList.add("filter__view--transparent");
    });

  const fadeIn = (element) => {
    element.classList.add("filter__view--transparent");
    element.hidden = false;

    requestAnimationFrame(() => {
      requestAnimationFrame(() => {
        element.classList.remove("filter__view--transparent");
      });
    });
  };

  results.addEventListener("click", async (event) => {
    if (event.target.closest(".service-center-card__open-new-tab")) return;

    const card = event.target.closest("[data-service-center-target]");
    if (!card || !results.contains(card)) return;

    const detailsUrl = card.dataset.serviceCenterDetailsUrl;
    if (!detailsUrl) return;

    mapWorker.setActiveServiceCenter(card.dataset.serviceCenterTarget);

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

    if (!details.hidden) {
      await fadeOut(details);
      details.hidden = true;
      details.classList.remove("filter__view--transparent");
    }

    details.innerHTML = html;
    details.scrollTop = 0;
    fadeIn(details);
  });

  details.addEventListener("click", async (event) => {
    if (!event.target.closest("[data-service-center-details-close]")) return;

    await fadeOut(details);
    details.hidden = true;
    details.classList.remove("filter__view--transparent");
    details.innerHTML = "";
  });
});
