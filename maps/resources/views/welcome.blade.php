<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Laravel</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    @vite('resources/css/app.css')
</head>

<body class="font-sans">
    <!-- Modal to show loading while the nearst city is being fetched!  -->

    <div class="flex h-screen">

        <div id="sidebar" class="flex flex-col">
            <div id="searchbar" class="m-4">
                <input id="citySelect" class="w-full border-2 border-neutral-800 rounded text-lg py-2 px-4"
                    oninput="debuouncedFetching(this.value)">
            </div>
        </div>
        <div class="w-3/4 relative">

            <div id="map" class="h-full"></div>
            <div id="loading"
                class="text-xl justify-center items-center absolute z-[1000]  inset-0 bg-[#00000080] text-white  cursor-wait flex hidden">


                <div class="p-4 bg-white text-black rounded flex gap-4 opacity-50">
                    <p>Getting the nearest city</p>
                    <div role="status">
                        <svg aria-hidden="true"
                            class="w-8 h-8 text-gray-200 animate-spin dark:text-gray-600 fill-blue-600"
                            viewBox="0 0 100 101" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z"
                                fill="currentColor" />
                            <path
                                d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z"
                                fill="currentFill" />
                        </svg>
                        <span class="sr-only">Loading...</span>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <script>
        const showNeighbouringCities = (cityName) => {
            fetch(`/query/${cityName}`, {
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                }
            })
                .then(response => response.json())
                .then(neghbouringCities => {
                    document.getElementById('sidebar').querySelectorAll('div.p-2.m-2.bg-gray-200').forEach((neghbouringCitiesDiv) => neghbouringCitiesDiv.remove());
                    for (const city of neghbouringCities) {
                        const cityDiv = document.createElement('div');
                        cityDiv.className = 'p-2 m-2 bg-gray-200';
                        cityDiv.textContent = `${city.city}, ${city.region}, ${city.country}`;
                        cityDiv.onclick = () => {
                            selectCity(city);
                        }
                        document.getElementById('sidebar').appendChild(cityDiv);

                        const marker = L.marker([city.latitude, city.longitude], { icon: L.icon({ iconUrl: 'https://cdn.mapmarker.io/api/v1/pin' }) }).addTo(map).bindPopup(`${city.city}, ${city.country}`);
                        marker.on('click', () => {
                            // console.log(city)
                            // showNeighbouringCities(city.city);
                            selectCity(city);
                        });
                    }
                });
        }

        let timer;
        function debounce(callback, delay) {
            return function () {
                clearTimeout(timer);
                timer = setTimeout(callback, delay);
            }
        }
        function debuouncedFetching(value) {
            debounce(() => getCitiesWithInitials(value), 500)();
        }
        const addSearchButton = (city) => {
            const sidebar = document.getElementById('sidebar');
            const searchButton = document.createElement('button');
            searchButton.className = 'bg-blue-500 text-white p-2 m-4 rounded';
            searchButton.textContent = 'Search';
            searchButton.onclick = () => {
                showNeighbouringCities(city.city);
            }
            sidebar.appendChild(searchButton);
        }

        const removeSearchButton = () => {
            const sidebar = document.getElementById('sidebar');
            const searchButton = document.querySelector('button');
            if (searchButton) {
                sidebar.removeChild(searchButton);
            }
        }

        const getCitiesWithInitials = (city) => {
            fetch(`/get-cities/${city}`)
                .then(response => response.json())
                .then(data => {
                    updateCityOptions(data);
                });
        }


        function updateCityOptions(cities) {
            console.log("Updating!")

            const citySelect = document.getElementById('citySelect');
            const searchbar = document.getElementById('searchbar');
            searchbar.innerHTML = '';

            removeSearchButton();
            document.getElementById('sidebar').querySelectorAll('div.p-2.m-2.bg-gray-200').forEach((neghbouringCitiesDiv) => neghbouringCitiesDiv.remove());
            if (!cities.length) {

                searchbar.innerHTML = `<input id="citySelect" class="w-full border-2 border-neutral-800 rounded text-lg py-2 px-4" oninput="debuouncedFetching(this.value)">`;
                return;
            }
            searchbar.appendChild(citySelect);
            for (let i = 0; i < cities.length; i++) {
                const option = document.createElement('div');
                option.className = 'cursor-pointer p-2 hover:bg-gray-200';
                option.value = cities[i]._id;
                option.onclick = () => {
                    selectCity(cities[i]);
                }
                option.textContent = `${cities[i].city}, ${cities[i].region}, ${cities[i].country}`;
                searchbar.appendChild(option);
            }
            document.getElementById('citySelect').focus();
        }

        function debouncedFetching(value) {
            debounce(() => getCitiesWithInitials(value), 500)();
        }

        function selectCity(city) {
            clearMarkers();
            const searchbar = document.getElementById('searchbar');
            searchbar.innerHTML = `<input id="citySelect" class="w-full border-2 border-neutral-800 rounded text-lg py-2 px-4" oninput="debuouncedFetching(this.value)">`;
            const citySelect = document.getElementById('citySelect');
            citySelect.value = `${city.city}, ${city.region}, ${city.country}`;
            L.marker([city.latitude, city.longitude]).addTo(map).bindPopup(`${city.city}, ${city.country}`).openPopup();
            showNeighbouringCities(city.city);
        }

        // Initialize Leaflet Map
        const map = L.map('map').setView([33.738045, 73.084488], 4);

        function clearMarkers() {
            map.eachLayer((layer) => {
                if (layer instanceof L.Marker) {
                    map.removeLayer(layer);
                }
            });
        }

        // Add Tile Layer
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
        }).addTo(map);

        let newMarker;
        map.on('click', (e) => {
            const { lat, lng } = e.latlng;
            if (newMarker) {
                map.removeLayer(newMarker);
            }

            // remove all markers


            // newMarker = L.marker([lat, lng]).addTo(map);
            const loading = document.getElementById('loading');
            loading.style.display = 'flex';
            fetch(`/get-neariest-cities/${lng}/${lat}`)
                .then(response => response.json())
                .then(city => {
                    console.log(city);
                    loading.style.display = 'none';
                    selectCity(city);
                    // if (newMarker) {
                    //     map.removeLayer(newMarker);
                    // }
                    // newMarker = L.marker([city.latitude, city.longitude]).addTo(map);
                    // newMarker.bindPopup(`${city.city}, ${city.country}`).openPopup();
                    // showNeighbouringCities(city.city);

                });

        });

    </script>
</body>

</html>