import { ServiceCenterListUpdate, FilterFullReset } from '../../events';

export default class Pagination {
  api = '/api/filter/service-centers?';
  main = null;
  list = null;
  page = 1;
  loading = false;
  disable = false;
  start = true;

  constructor() {
    this.init();
  }

  init() {
    this.list = document.getElementById('service-center-list');
    this.main = document.querySelector('.main-content');
    this.startScrollPosition();
    this.list.addEventListener('filterFullReset', this.reset.bind(this));
    this.main.addEventListener('scroll', this.checkPage.bind(this));
    // this.list.addEventListener('scroll', this.checkPage.bind(this));
    this.checkPage();
    this.start = false;
  }

  startScrollPosition() {
    const opt = {
      top: 0,
      left: 0,
      behavior: 'instant'
    };
    this.list.scrollTo(opt);
  }

  async apply(e) {
    const urlParams = new URLSearchParams(window.location.search);
    try {
      const result = await this.getServiceCenterList(urlParams);
      this.updateList(result);
    } catch (error) {
      console.log(error);
    }
  }

  async getServiceCenterList(urlParams) {
    try {
      const urlParamsString = urlParams.toString();
      const filterParams = urlParamsString
        ? urlParamsString + `&page=${this.page}`
        : `page=${this.page}`;
      const url = `${this.api}${filterParams}`;
      const response = await fetch(url);
      const result = await response.text();
      return result;
    } catch (error) {
      console.log(error);
    } finally {
      this.loading = false;
    }
  }

  updateList(result) {
    this.list.innerHTML += result;
    this.list.dispatchEvent(ServiceCenterListUpdate);
  }

  isLastElementVisible() {
    return this.list.scrollTop + this.list.clientHeight === this.list.scrollHeight;
  }

  checkPage() {
    if (this.disable) return;
    console.log('scroll');
    if (this.start) return;
    if (this.loading) return;
    if (!this.isLastElementVisible()) return;
    this.loading = true;
    this.page++;
    this.apply();
  }

  reset() {
    this.page = 1;
    this.apply();
  }

  setDisable() {
    this.disable = true;
  }

  setEnable() {
    this.disable = false;
  }
}
