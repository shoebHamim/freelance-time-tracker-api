<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Time Report - {{ $data['client']['name'] }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }
        .client-info {
            margin-bottom: 20px;
        }
        .summary {
            background-color: #f8f9fa;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .footer {
            position: fixed;
            bottom: 0cm;
            left: 0cm;
            right: 0cm;
            height: 1cm;
            text-align: center;
            line-height: 1cm;
            font-size: 10px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Time Tracking Report</h1>
        <h3>{{ $data['client']['name'] }}</h3>
    </div>

    <div class="client-info">
        <strong>Client:</strong> {{ $data['client']['name'] }}<br>
        <strong>Email:</strong> {{ $data['client']['email'] }}<br>
        <strong>Period:</strong> {{ $data['period']['from'] }} to {{ $data['period']['to'] }}
    </div>

    <div class="summary">
        <h3>Summary</h3>
        <p><strong>Total Hours:</strong> {{ $data['summary']['total_hours'] }} hours</p>
        <p><strong>Total Logs:</strong> {{ $data['summary']['total_logs'] }} entries</p>
        <p><strong>Average Hours per Day:</strong> {{ $data['summary']['average_hours_per_day'] }} hours</p>
    </div>

    <h3>Project Breakdown</h3>
    <table>
        <thead>
            <tr>
                <th>Project</th>
                <th class="text-center">Total Hours</th>
                <th class="text-center">Total Logs</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data['project_breakdown'] as $project)
            <tr>
                <td>{{ $project['project_title'] }}</td>
                <td class="text-center">{{ $project['total_hours'] ?? '0' }}</td>
                <td class="text-center">{{ $project['total_logs'] }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <h3>Daily Breakdown</h3>
    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th class="text-center">Total Hours</th>
                <th class="text-center">Total Logs</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data['daily_breakdown'] as $day)
            <tr>
                <td>{{ $day['date'] }}</td>
                <td class="text-center">{{ $day['total_hours'] ?? '0' }}</td>
                <td class="text-center">{{ $day['total_logs'] }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <h3>Detailed Time Logs</h3>
    <table>
        <thead>
            <tr>
                <th>Project</th>
                <th>Start Time</th>
                <th>End Time</th>
                <th class="text-center">Hours</th>
                <th>Description</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data['detailed_logs'] as $log)
            <tr>
                <td>{{ $log['project_title'] }}</td>
                <td>{{ \Carbon\Carbon::parse($log['start_time'])->format('Y-m-d H:i') }}</td>
                <td>{{ $log['end_time'] ? \Carbon\Carbon::parse($log['end_time'])->format('Y-m-d H:i') : 'Ongoing' }}</td>
                <td class="text-center">{{ $log['hours'] ?? '0' }}</td>
                <td>{{ $log['description'] ?? 'No description' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        Generated on {{ now()->format('Y-m-d H:i:s') }}
    </div>
</body>
</html>