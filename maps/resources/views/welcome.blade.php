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
    <div class="flex h-screen">

        <div id="sidebar" class="flex flex-col">
            <div id="searchbar" class="m-4">
                <input id="citySelect" class="w-full border-2 border-neutral-800 rounded text-lg py-2 px-4"
                    oninput="debuouncedFetching(this.value)">
            </div>
        </div>
        <div id="map" class="w-3/4"></div>
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
                    for(const neghbouringCity of neghbouringCities) {
                        const cityDiv = document.createElement('div');
                        cityDiv.className = 'p-2 m-2 bg-gray-200';
                        cityDiv.textContent = `${neghbouringCity.city}, ${neghbouringCity.region}, ${neghbouringCity.country}`;
                        document.getElementById('sidebar').appendChild(cityDiv);
                    }
                });
        }

        let timer;
        function debounce(callback, delay) {
            return function() {
                clearTimeout(timer);
                timer = setTimeout(callback, delay);
            }
        }
        function debuouncedFetching(value){
            debounce(()=>getCitiesWithInitials(value), 500)();
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

        const getCitiesWithInitials = (city)=> {
            fetch(`/get-cities/${city}`)
                .then(response => response.json())
                .then(data => {
                    updateCityOptions(data);
                });
        }

        function updateCityOptions(cities) {
            const citySelect = document.getElementById('citySelect');
            const searchbar = document.getElementById('searchbar');
            if (!cities.length) {
                searchbar.innerHTML = "<input id='citySelect' class='w-full' onchange='()=>getCitiesWithInitials(this.value)'>";
                removeSearchButton();
                return;
            }
            searchbar.appendChild(citySelect);
            for (let i = 0; i < cities.length; i++) {   
                const option = document.createElement('div');
                option.className = 'cursor-pointer p-2 hover:bg-gray-200';
                option.value = cities[i]._id;
                option.onclick = () => {
                    searchbar.innerHTML = "<input id='citySelect' class='w-full' onchange='()=>getCitiesWithInitials(this.value)'>";
                    const citySelect = document.getElementById('citySelect');
                    citySelect.value = `${cities[i].city}, ${cities[i].region}, ${cities[i].country}`;
                    addSearchButton(cities[i]);
                }
                option.textContent = `${cities[i].city}, ${cities[i].region}, ${cities[i].country}`;
                searchbar.appendChild(option);
            }
        } 

        function debouncedFetching(value){
            debounce(()=>getCitiesWithInitials(value), 500)();
        }

        // Initialize Leaflet Map
        const map = L.map('map').setView([33.738045, 73.084488], 4);

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
            newMarker = L.marker([lat, lng]).addTo(map);
            fetch(`/get-neariest-cities/${lng}/${lat}`)
                .then(response => response.json())
                .then(data => {
                    console.log(data);
                });

        }); 
        
    </script>
</body>

</html>