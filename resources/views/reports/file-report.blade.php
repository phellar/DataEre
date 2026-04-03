<!DOCTYPE html>
<html>
<head>
    <title>File Report</title>
    <style>
        body { font-family: Arial; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #000; padding: 8px; }
        th { background: #f0f0f0; }
    </style>
</head>
<body>

<h2>File Report</h2>

<p><strong>File ID:</strong> {{ $file->id }}</p>
<p><strong>Status:</strong> {{ $file->status }}</p>

<h3>Data Preview</h3>

<table>
    <thead>
        <tr>
            <th>#</th>
            <th>Data</th>
        </tr>
    </thead>
    <tbody>
        @foreach($rows as $index => $row)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>
                    @if(is_array($row))
                        {{ implode(' | ', $row) }}
                    @else
                        {{ $row }}
                    @endif
                </td>
            </tr>
        @endforeach
    </tbody>
</table>

</body>
</html>