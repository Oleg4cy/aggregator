import { SetActiveShopListItem, BeforeShopListUpdate } from '../events';

export default class YandexMapWorker {
  button = null;
  items = null;
  mapWrapper = null;
  shopsData = null;
  shopList = null;
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
    this.shopList = document.getElementById("shop-list");
    window.onload = () => this.addMap(this.shopsData);

    this.shopList.addEventListener('ShopListUpdate', this.updateMarks.bind(this));
  }

  setItems() {
    this.items = [];
    this.shopsData = [];
    this.items = document.querySelectorAll("[data-shop-target]");
    this.shopsData = Array.from(document.querySelectorAll('input[name="shop_coord"]'))
      .map((item) => {
        const coords = this.parseCoords(item.value);
        return coords ? { path: item.dataset.shopPath, coords } : null;
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
    if (this.shopsData.length < 1) return this.getCityCoord();
    let sumLat = 0;
    let sumLong = 0;
    for (var i = 0; i < this.shopsData.length; i++) {
      sumLat += this.shopsData[i].coords.lat;
      sumLong += this.shopsData[i].coords.long;
    }

    return {
      lat: sumLat / this.shopsData.length,
      long: sumLong / this.shopsData.length,
    };
  }

  scrollToShop(id) {
    const shopItem = document.querySelector(`[data-shop-target="${id}"]`);
    const shopListHeight = this.shopList.offsetHeight;
    const shopItemHeight = shopItem.offsetHeight;
    const marginBottom = parseFloat(
      window.getComputedStyle(shopItem).marginBottom,
    );
    const offsetTop = shopItem.offsetTop - this.shopList.offsetTop;
    const scrollToPosition = offsetTop - (shopListHeight / 2) +
      (shopItemHeight / 2) + (marginBottom / 2);

    this.shopList.scrollTo({
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

    this.shopList.addEventListener('click', this.selectShop.bind(this));
    this.isMapAdded = true;
  }

  addMarks() {
    for (var i = 0, l = this.shopsData.length; i < l; i++) {
      const mark = new ymaps.Placemark(
        [this.shopsData[i].coords["lat"], this.shopsData[i].coords["long"],],
        { path: this.shopsData[i].path }
      );
      mark.events.add("click", ((shop) => {
        return () => {
          this.hideAllItems();
          this.showShop(shop);
          this.scrollToShop(shop.path);
          this.markCollection.each(function (placemark) {
            placemark.options.set("iconColor", "#6c757d");
          });
          mark.options.set("iconColor", "#3d39fc");
          this.shopList.dispatchEvent(SetActiveShopListItem);
        };
      })(this.shopsData[i]),
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

  selectShop(e) {
    if (!e.target.hasAttribute('data-shop-view')) return;
    if (!this.markCollection || !this.map) return;
    this.items.forEach((shop) => {
      shop.classList.remove(this.classes.show);
      if (e.target.dataset.shopView == shop.dataset.shopTarget) {
        shop.classList.add(this.classes.show);
      }
    });
    this.markCollection.each((mark) => {
      mark.options.set("iconColor", "#6c757d");
      if (e.target.dataset.shopView == mark.properties.get("path")) {
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

  showShop(shopData) {
    const target = document.querySelector(`[data-shop-target="${shopData.path}"]`);
    target.classList.remove(this.classes.hide);
    target.classList.add(this.classes.show);
  }

  hideAllItems() {
    this.items.forEach((shopCard) => shopCard.classList.add(this.classes.hide));
    this.items.forEach((shopCard) => shopCard.classList.remove(this.classes.show));
    this.showMap();
  }

  showAllItems() {
    this.items.forEach((shopCard) => shopCard.classList.add(this.classes.show));
    this.items.forEach((shopCard) => shopCard.classList.remove(this.classes.hide));
    this.hideMap();
  }
}
