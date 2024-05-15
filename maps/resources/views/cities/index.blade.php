<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cities</title>
</head>
<body>

    <h1>Cities</h1>
    <table>
        <thead>
            <tr>
                <th>Location ID</th>
                <th>Country</th>
                <th>Region</th>
                <th>City</th>
                <th>Postal Code</th>
                <th>Latitude</th>
                <th>Longitude</th>
                <th>Metro Code</th>
                <th>Area Code</th>
            </tr>
        </thead>
        <tbody>
            @foreach($cities as $city)
            <tr>
                <td>{{ $city->locId }}</td>
                <td>{{ $city->country }}</td>
                <td>{{ $city->region }}</td>
                <td>{{ $city->city }}</td>
                <td>{{ $city->postalCode }}</td>
                <td>{{ $city->latitude }}</td>
                <td>{{ $city->longitude }}</td>
                <td>{{ $city->metroCode }}</td>
                <td>{{ $city->areaCode }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
