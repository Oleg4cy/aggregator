import "./hero";
import "../../layouts/similar-equipment-types";
import "../../layouts/similar-locations";
import FiltersUIController from "./FiltersUIController";
import Location from "../../modules/filters/Location";
import YandexMapWorker from "../../modules/YandexMapWorker";
import EquipmentTypes from "../../modules/filters/EquipmentTypes";
import Rating from "../../modules/filters/Rating";
import Options from "../../modules/filters/Options";
import FilterBase from "../../modules/filters/FilterBase";
import Pagination from "../../modules/filters/Pagination";

document.addEventListener("DOMContentLoaded", () => {
  new YandexMapWorker();
  new Location({area: 'areas[]', subway: 'subways[]'});
  new EquipmentTypes({brands: 'brands[]'});
  new Rating({rating: 'rating'});
  new Options({
    workNow: 'work_now',
    open24Hours: 'open_24_hours',
    onlineEstimate: 'online_estimate',
    warranty: 'warranty',
    onsiteRepair: 'onsite_repair',
    courier: 'courier',
    originalParts: 'original_parts',
    buyback: 'buyback',
    tradeIn: 'trade_in',
    buyForParts: 'buy_for_parts',
  });
  new FiltersUIController();

  // const pagination = new Pagination();
  // pagination.setDisable();

  const filteBase = new FilterBase();
  const fullClear = document.getElementById('filter-clear-all');
  fullClear.addEventListener('click', () => filteBase.fullReset());
});
