<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <h1>Infracon User: {{Auth:: user()->firstName}} {{Auth:: user()->lastName}}</h1>
    <h2>InfraCon User Role: {{Auth:: user()->role->roleName}}</h2>
    <form action="/logout" method="POST">
        @csrf
        <button>Log Out</button>
    </form>
</body>
</html>