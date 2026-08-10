import { SetActiveServiceCenterListItem, BeforeServiceCenterListUpdate } from '../events';

export default class YandexMapWorker {
  button = null;
  items = null;
  mapWrapper = null;
  serviceCentersData = null;
  serviceCenterList = null;
  isMapVisible = false;
  isMapAdded = false;
  markCollection = null;

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
        iconColor: "#6c757d",
      });

      this.addMarks();
    });

    this.serviceCenterList.addEventListener('click', this.selectServiceCenter.bind(this));
    this.isMapAdded = true;
  }

  addMarks() {
    for (var i = 0, l = this.serviceCentersData.length; i < l; i++) {
      const mark = new ymaps.Placemark(
        [this.serviceCentersData[i].coords["lat"], this.serviceCentersData[i].coords["long"],],
        { path: this.serviceCentersData[i].path }
      );
      mark.events.add("click", ((serviceCenter) => {
        return () => {
          this.hideAllItems();
          this.showServiceCenter(serviceCenter);
          this.scrollToServiceCenter(serviceCenter.path);
          this.markCollection.each(function (placemark) {
            placemark.options.set("iconColor", "#6c757d");
          });
          mark.options.set("iconColor", "#3d39fc");
          this.serviceCenterList.dispatchEvent(SetActiveServiceCenterListItem);
        };
      })(this.serviceCentersData[i]),
      );
      this.markCollection.add(mark);
      this.map.geoObjects.add(this.markCollection);
    }
  }

  updateMarks(e) {
    this.setItems();
    if (!this.markCollection || !this.map) return;
    this.markCollection.removeAll();
    this.addMarks();
  }

  selectServiceCenter(e) {
    if (!e.target.hasAttribute('data-service-center-view')) return;
    if (!this.markCollection || !this.map) return;
    this.items.forEach((serviceCenter) => {
      serviceCenter.classList.remove(this.classes.show);
      if (e.target.dataset.serviceCenterView == serviceCenter.dataset.serviceCenterTarget) {
        serviceCenter.classList.add(this.classes.show);
      }
    });
    this.markCollection.each((mark) => {
      mark.options.set("iconColor", "#6c757d");
      if (e.target.dataset.serviceCenterView == mark.properties.get("path")) {
        e.target.classList.add(this.classes.show);
        this.map.setCenter(mark.geometry.getCoordinates());
        this.map.setZoom(12);
        mark.options.set("iconColor", "#3d39fc");
      }
    });
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
