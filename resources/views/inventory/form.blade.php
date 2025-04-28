<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Inventory Update</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-4">

<div class="container">
    <h1>Inventory Update</h1>

    @if(session('errors_report'))
        <div class="alert alert-warning">
            <h4>Errors Found:</h4>
            <pre>{{ print_r(session('errors_report'), true) }}</pre>
        </div>
    @endif

    @if(session('download_file'))
        <div class="alert alert-success">
            <p>Your updated inventory file is ready:</p>
            <a href="{{ route('inventory.download', ['filename' => session('download_file')]) }}" class="btn btn-success">
                Download Updated Inventory
            </a>
        </div>
    @endif

    <form action="{{ route('inventory.process') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="mb-3">
            <label for="inventory_file" class="form-label">Inventory Export CSV:</label>
            <input type="file" class="form-control" id="inventory_file" name="inventory_file" required>
        </div>

        <div class="mb-3">
            <label for="stock_file" class="form-label">Stock XLSX File:</label>
            <input type="file" class="form-control" id="stock_file" name="stock_file" required>
        </div>

        <button type="submit" class="btn btn-primary">Process and Update</button>
    </form>
</div>

</body>
</html>
