const DEFAULT_MARK_COLOR = "#cdd5c5";
const ACTIVE_MARK_COLOR = "#3e0d7d";
const HOVER_MARK_COLOR = "#4f87f6";

export default class YandexMapWorker {
  button = null;
  items = null;
  mapWrapper = null;
  serviceCentersData = null;
  serviceCenterList = null;
  isMapVisible = false;
  isMapAdded = false;
  markCollection = null;
  hoveredMark = null;
  hoveredMarkPreviousColor = null;
  activeServiceCenterId = null;

  classes = {
    show: "active",
    hide: "hidden",
  };

  constructor() {
    this.setItems();
    this.mapWrapper = document.getElementById("filter-map");
    this.main = document.querySelector(".main-content");
    this.serviceCenterList = document.getElementById("service-center-list");
    window.onload = () => this.addMap(this.serviceCentersData);

    this.serviceCenterList.addEventListener('ServiceCenterListUpdate', this.updateMarks.bind(this));
    this.serviceCenterList.addEventListener('mouseover', this.highlightServiceCenterMarkerOnHover.bind(this));
    this.serviceCenterList.addEventListener('mouseout', this.restoreServiceCenterMarkerAfterHover.bind(this));
  }

  setItems() {
    this.items = [];
    this.serviceCentersData = [];
    this.items = document.querySelectorAll("[data-service-center-target]");
    this.serviceCentersData = Array.from(document.querySelectorAll('input[name="service_center_coord"]'))
      .map((item) => {
        const coords = this.parseCoords(item.value);
        return coords ? { path: item.dataset.serviceCenterPath, coords } : null;
      })
      .filter(Boolean);
  }

  parseCoords(raw) {
    if (!raw) return null;
    let coords;
    try { coords = JSON.parse(raw); } catch (error) { return null; }
    if (!coords || typeof coords !== 'object') return null;
    if (coords.lat === null || coords.long === null || coords.lat === '' || coords.long === '') return null;
    const lat = Number(coords.lat);
    const long = Number(coords.long);
    return Number.isFinite(lat) && Number.isFinite(long) ? { lat, long } : null;
  }

  async getCityCoord() {
    try {
      const response = await fetch('/api/data/cityInfo');
      if (!response.ok) return null;
      const result = await response.json();
      return this.parseCoords(result?.coord);
    } catch (error) { return null; }
  }

  getMapCenter() {
    if (this.serviceCentersData.length < 1) return this.getCityCoord();
    let sumLat = 0;
    let sumLong = 0;
    for (var i = 0; i < this.serviceCentersData.length; i++) {
      sumLat += this.serviceCentersData[i].coords.lat;
      sumLong += this.serviceCentersData[i].coords.long;
    }

    return {
      lat: sumLat / this.serviceCentersData.length,
      long: sumLong / this.serviceCentersData.length,
    };
  }

  scrollToServiceCenter(id) {
    const serviceCenterItem = document.querySelector(`[data-service-center-target="${id}"]`);
    const serviceCenterListHeight = this.serviceCenterList.offsetHeight;
    const serviceCenterItemHeight = serviceCenterItem.offsetHeight;
    const marginBottom = parseFloat(
      window.getComputedStyle(serviceCenterItem).marginBottom,
    );
    const offsetTop = serviceCenterItem.offsetTop - this.serviceCenterList.offsetTop;
    const scrollToPosition = offsetTop - (serviceCenterListHeight / 2) +
      (serviceCenterItemHeight / 2) + (marginBottom / 2);

    this.serviceCenterList.scrollTo({
      top: scrollToPosition,
      behavior: "smooth",
    });
  }

  async addMap() {
    if (this.isMapAdded) return;
    const average = await this.getMapCenter();
    if (!average) return;

    ymaps.ready(() => {
      this.map = new ymaps.Map("filter-map", {
        center: [average.lat, average.long],
        zoom: 10,
        controls: ["zoomControl"],
      }, {
        searchControlProvider: "yandex#search",
      });

      this.markCollection = new ymaps.GeoObjectCollection(null, {
        iconColor: DEFAULT_MARK_COLOR,
      });

      this.addMarks();
    });

    this.isMapAdded = true;
  }

  addMarks() {
    for (var i = 0, l = this.serviceCentersData.length; i < l; i++) {
      const serviceCenter = this.serviceCentersData[i];
      const mark = new ymaps.Placemark(
        [serviceCenter.coords["lat"], serviceCenter.coords["long"],],
        { path: serviceCenter.path }
      );
      if (String(serviceCenter.path) === this.activeServiceCenterId) {
        mark.options.set("iconColor", ACTIVE_MARK_COLOR);
      }
      mark.events.add("click", ((serviceCenter) => {
        return () => {
          this.setActiveServiceCenter(serviceCenter.path);
          this.scrollToServiceCenter(serviceCenter.path);
          const target = document.querySelector(
            `[data-service-center-target="${serviceCenter.path}"]`
          );
          if (target) target.click();
        };
      })(this.serviceCentersData[i]),
      );
      this.markCollection.add(mark);
      this.map.geoObjects.add(this.markCollection);
    }
  }

  setActiveServiceCenter(id) {
    const activeId = String(id);
    this.activeServiceCenterId = activeId;

    this.items.forEach((card) => {
      if (String(card.dataset.serviceCenterTarget) === activeId) {
        card.classList.add(this.classes.show);
      } else {
        card.classList.remove(this.classes.show);
      }
    });

    if (!this.markCollection) return;

    let selectedMark = null;
    this.markCollection.each((mark) => {
      if (String(mark.properties.get("path")) === activeId) {
        selectedMark = mark;
        mark.options.set("iconColor", ACTIVE_MARK_COLOR);
      } else {
        mark.options.set("iconColor", DEFAULT_MARK_COLOR);
      }
    });

    if (this.hoveredMark === selectedMark) {
      this.hoveredMarkPreviousColor = ACTIVE_MARK_COLOR;
    }
  }

  updateMarks(e) {
    this.setItems();
    this.hoveredMark = null;
    this.hoveredMarkPreviousColor = null;
    if (!this.markCollection || !this.map) return;
    this.markCollection.removeAll();
    this.addMarks();
  }

  findMarkByServiceCenterId(id) {
    if (!this.markCollection) return null;
    let matchingMark = null;
    this.markCollection.each((mark) => {
      if (String(mark.properties.get("path")) === String(id)) matchingMark = mark;
    });
    return matchingMark;
  }

  highlightServiceCenterMarkerOnHover(e) {
    const card = e.target.closest("[data-service-center-target]");
    if (!card || !this.serviceCenterList.contains(card) || !this.markCollection || !this.map) return;
    if (e.relatedTarget && card.contains(e.relatedTarget)) return;

    const mark = this.findMarkByServiceCenterId(card.dataset.serviceCenterTarget);
    if (!mark) return;
    this.hoveredMark = mark;
    this.hoveredMarkPreviousColor = mark.options.get("iconColor");
    mark.options.set("iconColor", HOVER_MARK_COLOR);
  }

  restoreServiceCenterMarkerAfterHover(e) {
    const card = e.target.closest("[data-service-center-target]");
    if (!card || !this.serviceCenterList.contains(card) || !this.markCollection || !this.map) return;
    if (e.relatedTarget && card.contains(e.relatedTarget)) return;

    if (this.hoveredMark) {
      this.hoveredMark.options.set(
        "iconColor",
        this.hoveredMarkPreviousColor || DEFAULT_MARK_COLOR,
      );
    }
    this.hoveredMark = null;
    this.hoveredMarkPreviousColor = null;
  }

  hideMap() {
    this.mapWrapper.classList.remove(this.classes.show);
    this.isMapVisible = false;
  }

  showMap() {
    this.mapWrapper.classList.add(this.classes.show);
    this.isMapVisible = true;
  }

  showServiceCenter(serviceCenterData) {
    const target = document.querySelector(`[data-service-center-target="${serviceCenterData.path}"]`);
    target.classList.remove(this.classes.hide);
    target.classList.add(this.classes.show);
  }

  hideAllItems() {
    this.items.forEach((serviceCenterCard) => serviceCenterCard.classList.add(this.classes.hide));
    this.items.forEach((serviceCenterCard) => serviceCenterCard.classList.remove(this.classes.show));
    this.showMap();
  }

  showAllItems() {
    this.items.forEach((serviceCenterCard) => serviceCenterCard.classList.add(this.classes.show));
    this.items.forEach((serviceCenterCard) => serviceCenterCard.classList.remove(this.classes.hide));
    this.hideMap();
  }
}
