import YandexMapWorker from "../../modules/YandexMapWorker";
import Location from "../../modules/filters/Location";
import EquipmentTypes from "../../modules/filters/EquipmentTypes";
import Rating from "../../modules/filters/Rating";
import Options from "../../modules/filters/Options";
import FilterBase from "../../modules/filters/FilterBase";
import "../../components/tabs";
import "../../layouts/carousel";

document.addEventListener("DOMContentLoaded", () => {
  const mapWorker = new YandexMapWorker();

  new Location({
    area: "areas[]",
    subway: "subways[]",
  });

  new EquipmentTypes({
    brands: "brands[]",
  });

  new Rating({
    rating: "rating",
  });

  new Options({
    workNow: "work_now",
    open24Hours: "open_24_hours",
    onlineEstimate: "online_estimate",
    warranty: "warranty",
    onsiteRepair: "onsite_repair",
    courier: "courier",
    originalParts: "original_parts",
    buyback: "buyback",
    tradeIn: "trade_in",
    buyForParts: "buy_for_parts",
  });

  const filterBase = new FilterBase({});
  const filterClearAll = document.getElementById("filter-clear-all");

  if (filterClearAll) {
    filterClearAll.addEventListener("click", () => {
      filterBase.fullReset();
    });
  }

  const filterPanel = document.querySelector("[data-filter-panel]");
  const filterToggle = document.querySelector("[data-filter-panel-toggle]");
  const filterClose = document.querySelector("[data-filter-panel-close]");

  const setFilterPanelOpen = (isOpen) => {
    if (!filterPanel || !filterToggle) return;

    filterPanel.classList.toggle("open", isOpen);
    filterToggle.classList.toggle("active", isOpen);
    filterToggle.setAttribute("aria-expanded", String(isOpen));
    filterPanel.setAttribute("aria-hidden", String(!isOpen));
  };

  if (filterPanel && filterToggle && filterClose) {
    filterToggle.addEventListener("click", () => {
      setFilterPanelOpen(!filterPanel.classList.contains("open"));
    });

    filterClose.addEventListener("click", () => {
      setFilterPanelOpen(false);
    });
  }

  const filterContainer = document.querySelector(".filter__container");
  const mobileMapToggle = document.querySelector("[data-mobile-map-toggle]");

  if (filterContainer && mobileMapToggle) {
    mobileMapToggle.addEventListener("click", () => {
      const isMapOpen = filterContainer.classList.toggle(
        "filter__container--map-open",
      );

      mobileMapToggle.setAttribute("aria-pressed", String(isMapOpen));
      mobileMapToggle.setAttribute(
        "aria-label",
        isMapOpen ? "Показать список" : "Показать карту",
      );

      if (isMapOpen) {
        setFilterPanelOpen(false);
      }
    });
  }

  const results = document.querySelector("[data-service-center-results]");
  const details = document.querySelector("[data-service-center-details]");
  const modalRoot = document.getElementById("modal-window");
  const photoModalSelector = '[data-modal-target="carousel_photos"]';

  if (!results || !details) return;

  const removeServiceCenterPhotoModal = () => {
    if (!modalRoot) return;

    const currentModal = modalRoot.querySelector(photoModalSelector);

    if (currentModal) {
      currentModal.remove();
    }
  };

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

    removeServiceCenterPhotoModal();
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
    removeServiceCenterPhotoModal();
  });
});
