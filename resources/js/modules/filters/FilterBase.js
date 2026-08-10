import { ServiceCenterListUpdate, FilterFullReset, BeforeServiceCenterListUpdate } from '../../events';

export default class FilterBase {
  api = '/api/filter/service-centers?';
  list = null;

  constructor(fields) {
    this.fields = fields;
    this.list = document.getElementById('service-center-list');
    this.list.addEventListener('filterFullReset', this.reset.bind(this));
  }

  async filterApply() {
    const urlParams = new URLSearchParams(window.location.search);
    this.removeFieldsFromURL(urlParams);
    this.setURLparams(urlParams);
    try {
      const result = await this.getServiceCenterList(urlParams);
      this.updateList(result);
      this.setURL(urlParams);
    } catch (error) {
      console.log(error);
    }
  }

  async getServiceCenterList(urlParams) {
    try {
      const url = `${this.api}${urlParams.toString()}`;
      const response = await fetch(url);
      const result = await response.text();
      return result;
    } catch (error) {
      console.log(error);
    }
  }

  updateList(result) {
    this.list.dispatchEvent(BeforeServiceCenterListUpdate);
    this.list.innerHTML = '';
    this.list.innerHTML = result;
    this.list.dispatchEvent(ServiceCenterListUpdate);
  }

  setURL(urlParams) {
    const url = `${window.location.pathname}?${urlParams.toString()}`;
    window.history.pushState({}, '', url);
  }

  removeFieldsFromURL(urlParams) {
    Object.keys(this.fields).forEach(field => {
      urlParams.delete(this.fields[field]);
    });
  }

  async fullReset() {
    const urlParams = window.location.pathname;
    try {
      const result = await this.getServiceCenterList(urlParams);
      this.updateList(result);
      window.history.pushState({}, '', urlParams);
      this.list.dispatchEvent(FilterFullReset);
    } catch (error) {
      console.log(error);
    }
  }

  setURLparams(urlParams) { }

  reset() { }

}
