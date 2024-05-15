<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Laravel</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />
        @vite('resources/css/app.css')
        <script>
    // let debounceTimer;

    // function debounce(callback, delay) {
    //     clearTimeout(debounceTimer);
    //     debounceTimer = setTimeout(callback, delay);
    // }

    const showNeighbouringCities = (cityName) => {
        fetch(`/query/${cityName}`, {
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            }
        }
        )
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
        const searchbar = document.getElementById('searchbar');
        if (!cities.length) {
            searchbar.innerHTML = "<input id='citySelect' class='w-full' onchange='()=>getCitiesWithInitials(this.value)'>";
            removeSearchButton();
            return;
        }
        const citySelect = document.getElementById('citySelect');
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
</script>
    </head>
    <body class="font-sans">
        <div id="sidebar" class="flex flex-col">
            <div id="searchbar" class="m-4">
                <input id="citySelect" class="w-full" onchange="getCitiesWithInitials(this.value)">
            </div>
        </div>
    </body>
</html>
