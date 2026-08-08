ymaps.ready(function () {
	const coordElement = document.getElementById("shop_coord");
	if (!coordElement) return;
	let coord;
	try { coord = JSON.parse(coordElement.value); } catch (error) { return; }
	if (!coord || typeof coord !== "object" || coord.lat === null || coord.long === null || coord.lat === "" || coord.long === "" || !Number.isFinite(Number(coord.lat)) || !Number.isFinite(Number(coord.long))) return;
	coord.lat = Number(coord.lat);
	coord.long = Number(coord.long);
	var myMap = new ymaps.Map("map", {
		center: [coord.lat, coord.long],
		zoom: 10,
		controls: [],
	}),
		markCollection = new ymaps.GeoObjectCollection(null, {
			iconColor: "#6c757d",
		});

	myMap.controls.add("zoomControl");
	myMap.controls.remove("typeSelector");
	myMap.controls.remove("geolocationControl");
	myMap.controls.remove("trafficControl");
	myMap.controls.remove("FullscreenControl");

	const mark = new ymaps.Placemark([coord.lat, coord.long]);
	markCollection.add(mark);
	myMap.geoObjects.add(markCollection);
});
