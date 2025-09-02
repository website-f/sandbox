<!DOCTYPE html>
<html>
<head>
    <title>Import Users</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-5">
    <div class="container">
        <h2>Upload Excel/CSV to Import Users</h2>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <form action="{{ route('users.import') }}" method="POST" enctype="multipart/form-data" class="mt-3">
            @csrf
            <div class="mb-3">
                <label for="file" class="form-label">Select File</label>
                <input type="file" name="file" class="form-control" required>
                <small class="text-muted">Format: Name | Email | Password</small>
            </div>
            <button class="btn btn-primary">Import</button>
        </form>
    </div>
</body>
</html>
